<?php

namespace AuditLog\Test\App\Model\Entity;

use Cake\ORM\Entity;

class Article extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'author_id' => true,
        'title' => true,
        'body' => true,
        'published' => true,
        'ignored_field' => true,
        'author' => true,
        'tags' => true,
    ];
}
