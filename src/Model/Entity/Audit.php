<?php

namespace AuditLog\Model\Entity;

use Cake\ORM\Entity;

/**
 * Audit Entity
 *
 * @property string $id
 * @property string $event
 * @property string $model
 * @property string $entity_id
 * @property string $json_object
 * @property string $description
 * @property string $source_id
 * @property \Cake\I18n\Time $created
 * @property int $delta_count
 * @property string $source_ip
 * @property string $source_url
 *
 * @property \AuditLog\Model\Entity\AuditDelta[] $audit_deltas
 */
class Audit extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
