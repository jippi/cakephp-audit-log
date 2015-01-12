<?php

App::uses('Hash', 'Utility');
App::uses('Model', 'Model');
App::uses('ModelBehavior', 'Model');

/**
 * Records changes made to an object during save operations.
 */
class AuditableBehavior extends ModelBehavior {

/**
 * A copy of the object as it existed prior to the save. We're going
 * to store this off so we can calculate the deltas after save.
 *
 * @var array
 */
	protected $_original = [];

/**
 * Initiate behavior for the model using specified settings.
 *
 * Available settings:
 *   - ignore array, optional
 *            An array of property names to be ignored when records
 *            are created in the deltas table.
 *   - habtm  array, optional
 *            An array of models that have a HABTM relationship with
 *            the acting model and whose changes should be monitored
 *            with the model.
 *   - on     array, optional
 *   					events to audit on, can be either/any of
 *   					'create' 'update' or 'delete'
 *
 * @param   Model  $model      Model using the behavior
 * @param   array   $settings   Settings overrides.
 */
	public function setup(Model $model, $settings = []) {
		if (empty($this->settings[$model->alias])) {
			$this->settings[$model->alias] = [
				'on' 		 => ['delete', 'create', 'update'],
				'ignore' => ['created', 'updated', 'modified'],
				'habtm'  => [],
				'json_object' => true
			];
		}

		if (!is_array($settings)) {
			$settings = [];
		}

		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], $settings);

		/*
		 * Ensure that no HABTM models which are already auditable
		 * snuck into the settings array. That would be bad. Same for
		 * any model which isn't a HABTM association.
		 */
		foreach ($this->settings[$model->alias]['habtm'] as $index => $modelName) {
			if (array_key_exists($modelName, $model->hasAndBelongsToMany)) {
				continue;
			}

			if (!is_array($model->{$modelName}->actsAs)) {
				continue;
			}

			if (array_search('Auditable', $model->{$modelName}->actsAs) === false) {
				unset($this->settings[$model->alias]['habtm'][$index]);
			}
		}
	}

/**
 * Executed before a save() operation.
 *
 * @param Model $model
 * @param array $options
 * @return boolean
 */
	public function beforeSave(Model $model, $options = []) {
		if (!$this->_shouldProcess('create', $model) && !$this->_shouldProcess('update', $model)) {
			return;
		}

		if (!empty($model->id)) {
			$this->_original[$model->alias] = $this->_getModelData($model);
		}

		return true;
	}

/**
 * Executed before a delete() operation.
 *
 * @param 	Model 	$model
 * @param  	boolean $cascade
 * @return	boolean
 */
	public function beforeDelete(Model $model, $cascade = true) {
		if (!$this->_shouldProcess('delete', $model)) {
			return;
		}

		$original = $model->find('first', [
			'contain'    => false,
			'conditions' => [$model->escapeField() => $model->getID()],
		]);

		$this->_original[$model->alias] = $original[$model->alias];
		return true;
	}

/**
 * Executed after a save operation completes.
 *
 * @param  	Model 	$model
 * @param   boolean $created
 * @param   array   $options
 * @return  void
 */
	public function afterSave(Model $model, $created, $options = []) {
		if ($created && !$this->_shouldProcess('create', $model)) {
			return;
		}

		if (!$created && !$this->_shouldProcess('update', $model)) {
			return;
		}

		$audit = [$model->alias => $this->_getModelData($model)];
		$audit[$model->alias][$model->primaryKey] = $model->id;

		$source = $this->_getSource($model);

		$data = [
			'Audit' => [
				'event' => $created ? 'CREATE' : 'EDIT',
				'model' => $model->alias,
				'entity_id' => $model->id,
				'source_id' => $source['id'],
				'source_ip' => $source['ip'],
				'source_url' => $source['url'],
				'description' => $source['description']
			]
		];

		if (!empty($this->_settings[$model->alias]['json_object'])) {
			$data['Audit']['json_object'] = json_encode($audit);
		}

		$updates = [];
		foreach ($audit[$model->alias] as $property => $value) {
			// Don't create delta for new records
			if ($created) {
				continue;
			}

			// Don't create delta for virtual fields
			if ($model->hasMethod('isVirtualField') && $model->isVirtualField($property)) {
				continue;
			}

			// Don't create fields for those that are ignored
			if (in_array($property, $this->settings[$model->alias]['ignore'])) {
				continue;
			}

			// Don't create delta for new values
			if (!array_key_exists($property, $this->_original[$model->alias])) {
				continue;
			}

			// Don't create delta for unchanged values
			if ($this->_original[$model->alias][$property] == $value) {
				continue;
			}

			$delta = [
				'AuditDelta' => [
					'property_name' => $property,
					'old_value'     => $this->_original[$model->alias][$property],
					'new_value'     => $value
				]
			];

			array_push($updates, $delta);
		}

		$audit = ClassRegistry::init('AuditLog.Audit');
		if ($created || count($updates)) {
			$audit->create();
			$audit->save($data);

			if ($created && $model->hasMethod('afterAuditCreate')) {
				$model->afterAuditCreate($model);
			}

			if (!$created && $model->hasMethod('afterAuditUpdate')) {
				$model->afterAuditUpdate($model, $this->_original, $updates, $audit->id);
			}
		}

		foreach ($updates as $delta) {
			$delta['AuditDelta']['audit_id'] = $audit->id;

			$audit->AuditDelta->create();
			$audit->AuditDelta->save($delta);

			if (!$created && $model->hasMethod('afterAuditProperty')) {
				$model->afterAuditProperty(
					$model,
					$delta['AuditDelta']['property_name'],
					$this->_original[$model->alias][$delta['AuditDelta']['property_name']],
					$delta['AuditDelta']['new_value']
				);
			}
		}

		if (!empty($this->_original[$model->alias])) {
			unset($this->_original[$model->alias]);
		}

		return true;
	}

/**
 * Executed after a model is deleted.
 *
 * @param 	Model $model
 * @return	void
 */
	public function afterDelete(Model $model) {
		if (!$this->_shouldProcess('delete', $model)) {
			return;
		}

		$source = $this->_getSource($model);
		$audit = [$model->alias => $this->_original[$model->alias]];

		$data  = [
			'Audit' => [
				'event' => 'DELETE',
				'model' => $model->alias,
				'entity_id' => $model->id,
				'source_id' => $source['id'],
				'source_ip' => $source['ip'],
				'source_url' => $source['url'],
				'description' => $source['description']
			]
		];

		if (!empty($this->_settings[$model->alias]['json_object']) && $this->_settings[$model->alias]['json_object']) {
			$data['Audit']['json_object'] = json_encode($audit);
		}

		$audit = ClassRegistry::init('AuditLog.Audit');
		$audit->create();
		$audit->save($data);
	}

/**
 * Should a model event be processed by AuditLog ?
 *
 * @param  string $event
 * @param  Model $model
 * @return boolean
 */
	protected function _shouldProcess($event, Model $model) {
		return in_array($event, $this->settings[$model->alias]['on']);
	}

/**
 * Get the source for the actor CRUD'ing the resource
 *
 * @param  Model  $model
 * @return array
 */
	protected function _getSource(Model $model) {
		$defaults = [
			'id' => null,
			'ip' => null,
			'url' => null,
			'description' => null
		];

		if ($source = Configure::read('AuditSource')) {
			return $source + $defaults;
		}

		if ($model->hasMethod('currentUser')) {
			return $model->currentUser() + $defaults;
		}

		if ($model->hasMethod('current_user')) {
			return $model->current_user() + $defaults;
		}

		return $defaults;
	}

/**
 * Retrieves the entire set model data contained to the primary
 * object and any/all HABTM associated data that has been configured
 * with the behavior.
 *
 * Additionally, for the HABTM data, all we care about is the IDs,
 * so the data will be reduced to an indexed array of those IDs.
 *
 * @param   Model $model
 * @return  array
 */
	protected function _getModelData(Model $model) {
		$data = $model->find('first', [
			'contain' 		=> !empty($this->settings[$model->alias]['habtm']) ? array_values($this->settings[$model->alias]['habtm']) : [],
			'conditions' 	=> [$model->alias . '.' . $model->primaryKey => $model->id],
		]);

		$audit_data = [
			$model->alias => isset($data[$model->alias]) ? $data[$model->alias] : []
		];

		foreach ($this->settings[$model->alias]['habtm'] as $habtmModel) {
			if (!array_key_exists($habtmModel, $model->hasAndBelongsToMany)) {
				continue;
			}

			if (!isset($data[$habtmModel])) {
				continue;
			}

			$ids = Hash::extract($data[$habtmModel], '{n}.id');
			sort($ids);
			$audit_data[$model->alias][$habtmModel] = implode(',', $ids);
		}

		return $audit_data[$model->alias];
	}

}
