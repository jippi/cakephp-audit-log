<?php

namespace AuditLog\Test\App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ArticlesTag Entity.
 */
class ArticlesTag extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'article' => true,
        'tag' => true,
    ];
}
