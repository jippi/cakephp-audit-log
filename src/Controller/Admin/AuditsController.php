<?php
namespace AuditLog\Controller\Admin;

use App\Controller\AppController;

/**
 * Audits Controller
 *
 * @property \AuditLog\Model\Table\AuditsTable $Audits
 */
class AuditsController extends AppController
{

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Search.Prg', [
            'actions' => [
                'index'
            ]
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Audits
            ->find('search', [
                'search' => $this->getRequest()->getQueryParams(),
            ]);

        $audits = $this->paginate($query);

        $this->set(compact('audits'));
        $this->set('_serialize', ['audits']);
    }

    /**
     * View method
     *
     * @param string|null $id Audit id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $audit = $this->Audits->get($id, [
            'contain' => ['AuditDeltas']
        ]);

        $this->set('audit', $audit);
        $this->set('_serialize', ['audit']);
    }

}
