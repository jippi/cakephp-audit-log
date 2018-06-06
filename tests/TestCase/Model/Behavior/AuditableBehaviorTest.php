<?php

namespace AuditLog\Test\TestCase\Model\Behavior;

use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AuditLog\Model\Behavior\AuditableBehavior Test Case
 */
class AuditableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.audit_log.audit_deltas',
        'plugin.audit_log.audits',
        'plugin.audit_log.authors',
        'plugin.audit_log.articles',
        'plugin.audit_log.tags',
        'plugin.audit_log.articles_tags',
    ];

    /**
     * @var \AuditLog\Test\App\Model\Table\ArticlesTable
     */
    public $Articles;

    /**
     * @var \AuditLog\Test\App\Model\Table\AuthorsTable
     */
    public $Authors;

    /**
     * @var \AuditLog\Test\App\Model\Table\TagsTable
     */
    public $Tags;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        $tableLocator = new TableLocator();

        $this->Articles = $tableLocator->get('Articles', [
            'className' => 'AuditLog\Test\App\Model\Table\ArticlesTable',
        ]);
        $this->Authors = $tableLocator->get('Authors', [
            'className' => 'AuditLog\Test\App\Model\Table\AuthorsTable',
        ]);
        $this->Tags = $tableLocator->get('Tags', [
            'className' => 'AuditLog\Test\App\Model\Table\TagsTable',
        ]);

        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Articles, $this->Authors, $this->Tags);
        parent::tearDown();
    }

    /**
     * Test the action of creating a new record.
     *
     * @todo  Test HABTM save
     * @return void
     */
    public function testCreate()
    {
        $new_article = $this->Articles->newEntity([
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'First Test Article',
            'body' => 'First Test Article Body',
            'published' => 'N',
        ]);

        $result = $this->Articles->save($new_article);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($new_article->id);

        $tableLocator = new TableLocator();
        $audit = $tableLocator->get('AuditLog.Audits')->find('all', [
            'conditions' => [
                'event' => 'CREATE',
                'model' => 'Articles',
                'entity_id' => $new_article->id,
            ],
        ])->firstOrFail();

        $this->assertEquals('15', $audit->source_id);
        $this->assertEquals('127.0.0.1', $audit->source_ip);
        $this->assertEquals('http://127.0.0.1/articles', $audit->source_url);
        $this->assertEquals('Testing audit log', $audit->description);

        $article = json_decode($audit->json_object, true);

        $deltas = $tableLocator->get('AuditLog.AuditDeltas')->find('all', [
            'conditions' => [
                'AuditDeltas.audit_id' => $audit->id,
            ],
        ])->toArray();

        # Verify the audit record
        $this->assertEquals(1, $article['user_id']);
        $this->assertEquals('First Test Article', $article['title']);
        $this->assertEquals('N', $article['published']);

        #Verify that no delta record was created.
        $this->assertTrue(empty($deltas));
    }

    /**
     * Test saving associated data
     *
     * @return void
     */
    public function testSaveAssociated()
    {
        # TEST A MODEL AND A SINGLE ASSOCIATED MODEL
        $data = [
            'user_id' => 1,
            'title' => 'Rob\'s Test Article',
            'body' => 'Rob\'s Test Article Body',
            'published' => 'Y',
            'author' => [
                'first_name' => 'Rob',
                'last_name' => 'Wilkerson',
            ],
            'tags' => [
                ['id' => 1],
                ['title' => 'news'],
                ['title' => 'test'],
            ],
        ];
        $entity = $this->Articles->newEntity($data, [
            'associated' => [
                'Authors',
                'Tags',
            ],
        ]);

        $this->Articles->save($entity);

        $tableLocator = new TableLocator();
        $article_audit = $tableLocator->get('AuditLog.Audits')->find('all', [
            'conditions' => [
                'Audits.event' => 'CREATE',
                'Audits.model' => 'Articles',
                'Audits.entity_id' => $entity->id,
            ],
            'contain' => ['AuditDeltas'],
        ])->firstOrFail()->toArray();
        $article = json_decode($article_audit['json_object'], true);

        # Verify the audit record
        $this->assertEquals(1, $article['user_id']);
        $this->assertEquals('Rob\'s Test Article', $article['title']);
        $this->assertEquals('Y', $article['published']);

        $this->assertEquals([
            1 => [
                'tag_id' => 1,
                'article_id' => 4,
            ],
            2 => [
                'tag_id' => 2,
                'article_id' => 4,
            ],
            3 => [
                'tag_id' => 3,
                'article_id' => 4,
            ],
        ], $article['tags']);

        # Verify that no delta record was created.

        $this->assertTrue(empty($article_audit['audit_deltas']));

        $author_audit = $tableLocator->get('AuditLog.Audits')->find('all', [
            'conditions' => [
                'Audits.event' => 'CREATE',
                'Audits.model' => 'Authors',
                'Audits.entity_id' => $entity->author->id,
            ],
            'contain' => ['AuditDeltas'],
        ])->firstOrFail();

        $author = json_decode($author_audit['json_object'], true);

        # Verify the audit record
        $this->assertEquals($article['author_id'], $author['id']);
        $this->assertEquals('Rob', $author['first_name']);

        # Verify that no delta record was created.
        $this->assertTrue(empty($author_audit['audit_deltas']));

    }

    /**
     * Test saving multiple data
     *
     * @return void
     */
    public function testSaveMultiple()
    {

        # TEST MULTIPLE RECORDS OF ONE MODEL

        $data = [
            [
                'user_id' => 1,
                'author_id' => 1,
                'title' => 'Multiple Save 1 Title',
                'body' => 'Multiple Save 1 Body',
                'published' => 'Y',

            ],
            [
                'user_id' => 2,
                'author_id' => 2,
                'title' => 'Multiple Save 2 Title',
                'body' => 'Multiple Save 2 Body',
                'published' => 'N',
                'ignored_field' => 1,
            ],
            [
                'user_id' => 3,
                'author_id' => 3,
                'title' => 'Multiple Save 3 Title',
                'body' => 'Multiple Save 3 Body',
                'published' => 'Y',
            ],
        ];
        $entities = $this->Articles->newEntities($data);
        foreach ($entities as $entity) {
            $this->Articles->save($entity);
        }

        $tableLocator = new TableLocator();
        # Retrieve the audits for the last 3 articles saved
        $audits = $tableLocator->get('AuditLog.Audits')->find('all', [
            'conditions' => [
                'Audits.event' => 'CREATE',
                'Audits.model' => 'Articles',
            ],
            'order' => [
                'Audits.entity_id' => 'DESC',
            ],
            'limit' => 3,
            'contain' => ['AuditDeltas'],
        ])->all()->toArray();

        $article_1 = json_decode($audits[2]['json_object'], true);
        $article_2 = json_decode($audits[1]['json_object'], true);
        $article_3 = json_decode($audits[0]['json_object'], true);

        # Verify the audit records
        $this->assertEquals(1, $article_1['user_id']);
        $this->assertEquals('Multiple Save 1 Title', $article_1['title']);
        $this->assertEquals('Y', $article_1['published']);

        $this->assertEquals(2, $article_2['user_id']);
        $this->assertEquals('Multiple Save 2 Title', $article_2['title']);
        $this->assertEquals('N', $article_2['published']);

        $this->assertEquals(3, $article_3['user_id']);
        $this->assertEquals('Multiple Save 3 Title', $article_3['title']);
        $this->assertEquals('Y', $article_3['published']);

        # Verify that no delta records were created.
        $this->assertTrue(empty($audits[0]['audit_deltas']));
        $this->assertTrue(empty($audits[1]['audit_deltas']));
        $this->assertTrue(empty($audits[2]['audit_deltas']));
    }

    /**
     * Test editing an existing record.
     *
     * @todo  Test change to ignored field
     * @todo  Test HABTM save
     * @return void
     */
    public function testEdit()
    {
        $tableLocator = new TableLocator();
        $this->Audit = $tableLocator->get('AuditLog.Audits');
        $this->AuditDelta = $tableLocator->get('AuditLog.AuditDeltas');

        $new_article = $this->Articles->newEntity([
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'First Test Article',
            'body' => 'First Test Article Body',
            'ignored_field' => 1,
            'published' => 'N',
        ]);

        # TEST SAVE WITH SINGLE PROPERTY UPDATE

        $result = $this->Articles->save($new_article);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($new_article->id);
        $new_article->title = 'First Test Article (Edited)';
        $this->Articles->save($new_article);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($new_article->id);

        $audit_records = $this->Audit->find('all', [
            'conditions' => [
                'Audits.model' => 'Articles',
                'Audits.entity_id' => $new_article->id,
            ],
        ])->all();

        $auditsIds = $audit_records->extract('id')->toArray();

        $delta_records = $this->AuditDelta->find('all', [
            'conditions' => [
                'AuditDeltas.audit_id IN' => $auditsIds,
            ],
        ])->all()->toArray();

        $count = $audit_records->countBy(function ($item) {
            return $item->event;
        })->toArray();

        # There should be 1 CREATE and 1 EDIT record
        $this->assertEquals(2, $audit_records->count());

        # There should be one audit record for each event.
        $this->assertEquals(1, $count['CREATE']);
        $this->assertEquals(1, $count['EDIT']);

        # Only one property was changed
        $this->assertEquals(1, count($delta_records));

        $delta = array_shift($delta_records);
        $this->assertEquals('First Test Article', $delta['old_value']);
        $this->assertEquals('First Test Article (Edited)', $delta['new_value']);

        # TEST UPDATE OF MULTIPLE PROPERTIES
        # Pause to simulate a gap between edits
        # This also allows us to retrieve the last edit for the next set
        # of tests.
        $anotherArticle = $this->Articles->newEntity([
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'Second Test Article',
            'body' => 'Second Test Article Body',
            'ignored_field' => 1,
            'published' => 'N',
        ]);
        $result = $this->Articles->save($anotherArticle);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($anotherArticle->id);
        $articleId = $anotherArticle->id;
        $this->Articles->patchEntity($anotherArticle, [
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'Second Test Article (Newly Edited)',
            'body' => 'Second Test Article Body (Also Edited)',
            'ignored_field' => 0,
            'published' => 'Y',
        ]);

        $result = $this->Articles->save($anotherArticle);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($anotherArticle->id);
        $this->assertSame($articleId, $anotherArticle->id);

        $last_audit = $this->Audit->find('all', [
            'conditions' => [
                'event' => 'EDIT',
                'model' => 'Articles',
                'entity_id' => $articleId,
            ],
            'contain' => [
                'AuditDeltas' => [
                    'strategy' => 'select',
                    'queryBuilder' => function ($q) {
                        return $q->order([
                            'AuditDeltas.property_name ' => 'ASC',
                        ]);
                    },
                ],
            ],
            'order' => 'Audits.created DESC',
        ])->firstOrFail();
        $this->assertEquals(3, count($last_audit->audit_deltas));
        $actual = array_map(function ($item) {
            return [
                'property_name' => $item->property_name,
                'old_value' => $item->old_value,
                'new_value' => $item->new_value,
            ];
        }, $last_audit->audit_deltas);

        # There are 4 changes, but one to an ignored field
        $expected = [
            [
                'property_name' => 'body',
                'old_value' => 'Second Test Article Body',
                'new_value' => 'Second Test Article Body (Also Edited)',
            ],
            [
                'property_name' => 'published',
                'old_value' => 'N',
                'new_value' => 'Y',
            ],
            [
                'property_name' => 'title',
                'old_value' => 'Second Test Article',
                'new_value' => 'Second Test Article (Newly Edited)',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test saving ignored field
     *
     * @return void
     */
    public function testIgnoredField()
    {
        $tableLocator = new TableLocator();
        $this->Audit = $tableLocator->get('AuditLog.Audits');
        $this->AuditDelta = $tableLocator->get('AuditLog.AuditDeltas');

        $new_article = $this->Articles->newEntity([
            'user_id' => 1,
            'author_id' => 1,
            'title' => 'First Test Article',
            'body' => 'First Test Article Body',
            'ignored_field' => 1,
            'published' => 'N',
        ]);

        # TEST NO AUDIT RECORD IF ONLY CHANGE IS IGNORED FIELD

        $result = $this->Articles->save($new_article);
        $this->assertNotEquals($result, false);
        $this->assertNotNull($new_article->id);
        $articleId = $new_article->id;

        $new_article->ignored_field = '5';
        $result = $this->Articles->save($new_article);
        $this->assertNotEquals($result, false);

        $last_audit = $this->Audit->find('all', [
            'contain' => ['AuditDeltas'],
            'conditions' => [
                'Audits.event' => 'EDIT',
                'Audits.model' => 'Articles',
                'Audits.entity_id' => $articleId,
            ],
            'order' => 'Audits.created DESC',
        ]);

        $this->assertEquals(0, $last_audit->count());
    }

    /**
     * Test deleting
     *
     * @return voida
     */
    public function testDelete()
    {
        $tableLocator = new TableLocator();
        $this->Audit = $tableLocator->get('AuditLog.Audits');
        $this->AuditDelta = $tableLocator->get('AuditLog.AuditDeltas');
        $article = $this->Articles->find('all', [
            'order' => ['rand()'],
        ])->first();

        $id = $article->id;

        $this->Articles->delete($article);

        $last_audit = $this->Audit->find('all', [
            //'contain'    => array('AuditDelta'), <-- What does this solve?
            'conditions' => [
                'Audits.event' => 'DELETE',
                'Audits.model' => 'Articles',
                'Audits.entity_id' => $id,
            ],
            'order' => 'Audits.created DESC',
        ])->all();
        $this->assertEquals(1, count($last_audit));
    }
}
