<?php

namespace AuditLog\Test\App\Model\Table;

use Cake\ORM\Table;

class ArticlesTable extends Table
{
    /**
     * Initialize method
     *
     * @param  array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Authors', [
            'foreignKey' => 'author_id',
        ]);

        $this->belongsToMany('Tags', [
            'className' => 'AuditLog.Tags',
            'foreignKey' => 'article_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'articles_tags',
        ]);

        $this->addBehavior('AuditLog.Auditable', [
            'ignore' => ['ignored_field'],
            'habtm' => ['Tags'],
        ]);
    }

    public function currentUser()
    {
        return [
            'id' => '15',
            'ip' => '127.0.0.1',
            'url' => 'http://127.0.0.1/articles',
            'description' => 'Testing audit log',
        ];
    }
}
