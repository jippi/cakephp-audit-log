<?php

namespace AuditLog\Model\Table;

use Cake\Http\ServerRequestFactory;

/**
 * Trait CurrentUserTrait
 *
 * @package AuditLog\Model\Table
 */
trait CurrentUserTrait
{

    /**
     * Returns data about the current logged user
     *
     * @return array
     */
    public function currentUser()
    {
        $request = ServerRequestFactory::fromGlobals();
        $session = $request->getSession();

        return [
            'id' => $session->read('Auth.User.id'),
            'ip' => $request->clientIp(),
            'url' => $request->getRequestTarget(),
            'description' => $session->read('Auth.User.username'),
        ];
    }

}
