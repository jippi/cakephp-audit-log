<?php
namespace AuditLog\Test\TestCase\Model\Table;

use AuditLog\Model\Table\AuditDeltasTable;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AuditLog\Model\Table\AuditDeltasTable Test Case
 */
class AuditDeltasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \AuditLog\Model\Table\AuditDeltasTable
     */
    public $AuditDeltas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.audit_log.audit_deltas',
        'plugin.audit_log.audits'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $tableLocator = new TableLocator();
        $config = $tableLocator->exists('AuditDeltas') ? [] : ['className' => 'AuditLog\Model\Table\AuditDeltasTable'];
        $this->AuditDeltas = $tableLocator->get('AuditDeltas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AuditDeltas);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test setupSearchPlugin method
     *
     * @return void
     */
    public function testSetupSearchPlugin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
