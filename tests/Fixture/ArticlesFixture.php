<?php

namespace AuditLog\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArticlesFixture
 *
 */
class ArticlesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'author_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'body' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'published' => ['type' => 'string', 'fixed' => true, 'length' => 1, 'null' => false, 'default' => 'N', 'comment' => '', 'precision' => null],
        'ignored_field' => ['type' => 'integer', 'length' => 1, 'unsigned' => true, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],

        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'First Article',
            'body' => 'First Article Body',
            'published' => 'Y',
            'created' => '2007-03-18 10:39:23',
            'updated' => '2007-03-18 10:41:31',
        ],
        [
            'user_id' => 3,
            'author_id' => 3,
            'title' => 'Second Article',
            'body' => 'Second Article Body',
            'published' => 'Y',
            'created' => '2007-03-18 10:41:23',
            'updated' => '2007-03-18 10:43:31',
        ],
        [
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'Third Article',
            'body' => 'Third Article Body',
            'published' => 'Y',
            'created' => '2007-03-18 10:43:23',
            'updated' => '2007-03-18 10:45:31',
        ],
    ];
}
