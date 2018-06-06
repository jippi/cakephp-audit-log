<?php

namespace AuditLog\Test\App\Model\Table;

use Cake\ORM\Table;

/**
 * Tags Model
 */
class TagsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('tags');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Articles', [
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'article_id',
            'joinTable' => 'articles_tags',
        ]);
    }
}
