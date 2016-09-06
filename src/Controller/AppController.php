<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use \Ceeram\Blame\Controller\BlameTrait;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    use BlameTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Ldap'
            ],
            'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'redirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ],
        ]);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }   
    
    public function isAuthorized($user)
    {
        // Full system admin access
        if (isset($user['is_admin']) && $user['is_admin']) {
            return true;
        }

        return false;
    }
    
    public function refreshAuthUser()
    {
        $data = $this->Auth->User();
        if (!empty($data)) {
            $users = TableRegistry::get('Users');
            $user = $users->find()
                ->where(['id' => $data['id']])
                ->select(['id', 'email', 'whmcs_user_id', 'waiver_id', 'user_id'])
                ->contain(['Children' => ['fields' => ['id', 'first_name', 'last_name', 'user_id']]])
                ->first();

            if (!empty($user)) {
                if (empty($user->user_id)) {
                    $data['email'] = $user->email;
                    $data['children'] = $user->children;
                    $data['badges'] = [];

                    $badges = TableRegistry::get('Badges');
                    $user_badges = $badges->find()
                        ->select(['id', 'user_id', 'number'])
                        ->where(['whmcs_user_id' => $user->whmcs_user_id]);

                    foreach ($user_badges as $badge) {
                        $data['badges'][] = $badge;
                    }
                }
                $data['id'] = $user->id;
            }
            
            $this->Auth->setUser($data);
        }
    }
}
