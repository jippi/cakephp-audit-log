<?php

namespace AuditLog\Model\Table;

use Cake\ORM\Table;

/**
 * AuditDeltas Model
 *
 * @property \AuditLog\Model\Table\AuditsTable $Audits
 *
 * @method \AuditLog\Model\Entity\AuditDelta get($primaryKey, $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta newEntity($data = null, array $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta[] newEntities(array $data, array $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta[] patchEntities($entities, array $data, array $options = [])
 * @method \AuditLog\Model\Entity\AuditDelta findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\CounterCacheBehavior
 * @mixin \Search\Model\Behavior\SearchBehavior
 */
class AuditDeltasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('audit_deltas');
        $this->setDisplayField('property_name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Audits', [
            'foreignKey' => 'audit_id',
            'joinType' => 'INNER',
            'className' => 'AuditLog.Audits',
        ]);

        $this->addBehavior('CounterCache', [
            'Audits' => [
                'delta_count',
            ],
        ]);

        $this->addBehavior('Search.Search');

        $this->searchManager()
            ->add('source_id', 'Search.Value', [
                'field' => 'Audits.source_id',
                'model' => 'Audits',
                'fields' => [
                    'id' => 'source_id',
                    'label' => 'source_id',
                    'value' => 'source_id',
                ],
            ])
            ->add('model', 'Search.Value', [
                'field' => 'Audits.model',
                'model' => 'Audits',
                'fields' => [
                    'id' => 'model',
                    'label' => 'model',
                    'value' => 'model',
                ],
            ])
            ->add('entity_id', 'Search.Value', [
                'field' => 'Audits.entity_id',
                'model' => 'Audits',
                'fields' => [
                    'id' => 'entity_id',
                    'label' => 'entity_id',
                    'value' => 'entity_id',
                ],
            ])
            ->add('property_name', 'Search.Value', [
                'field' => 'id',
            ])
            ->add('old_value', 'Search.Value', [
                'field' => 'id',
            ])
            ->add('new_value', 'Search.Value', [
                'field' => 'id',
            ]);
    }

}
