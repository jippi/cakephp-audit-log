<?php
namespace AuditLog\Test\TestCase\Model\Table;

use AuditLog\Model\Table\AuditsTable;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AuditLog\Model\Table\AuditsTable Test Case
 */
class AuditsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \AuditLog\Model\Table\AuditsTable
     */
    public $Audits;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.audit_log.audits',
        'plugin.audit_log.audit_deltas'
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
        $config = $tableLocator->exists('Audits') ? [] : ['className' => 'AuditLog\Model\Table\AuditsTable'];
        $this->Audits = $tableLocator->get('Audits', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Audits);

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
