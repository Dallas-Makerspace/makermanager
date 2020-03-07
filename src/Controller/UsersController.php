<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class UsersController extends AppController {
  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    $this->Auth->allow(['logout']);
  }

  public function isAuthorized($user) {
    if (in_array($this->request->action, ['edit', 'password', 'sync', 'view', 'waiver'])) {
      if (isset($user['id']) && $user['id'] == $this->request->pass[0]) {
        return true;
      }

      if (isset($user['children'])) {
        foreach ($user['children'] as $child) {
          if ($child['id'] == $this->request->pass[0]) {
            return true;
          }
        }
      }
    }

    if (in_array($this->request->action, ['add'])) {
      if (isset($user['badges'])) {
        foreach ($user['badges'] as $badge) {
          if ($badge['id'] == $this->request->pass[0]) {
            return true;
          }
        }
      }
    }

    if (in_array($this->request->action, ['billing'])) {
      if (!empty($user)) {
        return true;
      }
    }

    return parent::isAuthorized($user);
  }

  public function add($badge_id = null) {
    if (empty($badge_id)) {
      $this->Flash->error(__('An available badge is required to create a new family member account.'));
      return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $this->loadModel('Badges');
    $badge = $this->Badges->get($badge_id);
    if (!empty($badge->user_id)) {
      $this->Flash->error(__('An available badge is required to create a new family member account.'));
      return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $user = $this->Users->newEntity();
    if ($this->request->is('post')) {
      if (strpos($this->request->data['password'], ' ') > -1 || strlen($this->request->data['password']) < 6) {
        $this->Flash->error(__('Account creation failed. Passwords must be 6 characters or more and not include spaces.'));
      } else {
        $primary_user = $this->Users->find()
          ->where(['whmcs_user_id' => $badge->whmcs_user_id, 'user_id IS NULL'])
          ->first();

        $this->request->data['username'] = strtolower($this->request->data['username']);
        $merged_account_data = array_merge($this->request->data, [
          'address_1' => $primary_user->address_1,
          'address_2' => $primary_user->address_2,
          'city' => $primary_user->city,
          'state' => $primary_user->state,
          'zip' => $primary_user->zip,
          'user_id' => $primary_user->id,
          'whmcs_user_id' => $primary_user->whmcs_user_id
        ]);

        $user = $this->Users->patchEntity($user, $merged_account_data);
        if ($this->Users->save($user)) {
          $success = $user->createActiveDirectoryAccount($this->request->data['password']);
          if ($success == true) {
            $user->enableActiveDirectoryAccount();
            $this->Users->save($user);
            $badge = $this->Badges->patchEntity($badge, ['user_id' => $user->id]);
            $this->Badges->save($badge);
            $this->refreshAuthUser();
            $this->Flash->success(__('New family member account created.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
          } else {
            $this->Users->delete($user);
            $this->Flash->error(__('Account creation failed. This is most likely due to the chosen Username already being used within Active Directory. Choose a different Username and try again.'));
          }
        } else {
          $this->Flash->error(__('Account creation failed. Check the errors below and resubmit.'));
        }
      }
    }

    $this->set('user', $user);
  }

  public function billing() {
    // Redirect user straight to their WHMCS billing account
    $timestamp = time();
    $login_url = Configure::read('Whmcs.login');
    $auth = Configure::read('Whmcs.login_authorization');
    $hash = sha1($this->Auth->User('email') . $timestamp . $auth);
    return $this->redirect($login_url . '?email=' . $this->Auth->User('email') . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=clientarea.php');
  }

  public function edit($id = null) {
    $user = $this->Users->get($id);

    if (empty($user)) {
      $this->Flash->error(__('User not found.'));
      return $this->redirect($this->referer());
    }

    if (empty($user->user_id)) {
      $this->Flash->error(__('Primary account holders must edit their accounts through WHMCS.'));
      return $this->redirect($this->referer());
    }

    if ($this->request->is('put')) {
      $user = $this->Users->patchEntity($user, $this->request->data);
      if ($this->Users->save($user)) {
        $this->Flash->success(__('Account information updated.'));
        $user->updateActiveDirectoryAccount();
        $this->Users->save($user);
      } else {
        $this->Flash->error(__('Update failed.'));
      }
    }

    $this->set('user', $user);
  }

  public function export() {
    $users = $this->Users->find()
      ->contain(['Badges'])
      ->order(['Users.last_name' => 'ASC', 'Users.first_name' => 'ASC']);

    $data = [['First Name', 'Last Name', 'Username', 'Email', 'Phone', 'Address', 'Locale', 'Badge', 'Account']];
    foreach ($users as $user) {
      $user_data = [
        $user->first_name,
        $user->last_name,
        $user->username,
        $user->email,
        $user->phone,
        $user->address_1 . ' ' . $user->address_2,
        $user->city . ', ' . $user->state . ' ' . $user->zip,
      ];

      if (!empty($user->badge)) {
        if (!empty($user->badge->number)) {
          if ($user->badge->status != 'active') {
            $user_data[] = 'No Badge';
          } else {
            $user_data[] = $user->badge->number;
          }
        } else {
          $user_data[] = 'No Badge';
        }
      } else {
        $user_data[] = 'No Badge';
      }

      if (empty($user->user_id)) {
        $user_data[] = 'Primary';
      } else {
        $user_data[] = 'Addon';
      }

      $data[] = $user_data;
    }

    $_serialize = 'data';

    $this->viewBuilder()->className('CsvView.Csv');
    $this->set(compact('data', '_serialize'));
  }

  public function index() {
    $conditions = [];

    if (!empty($_GET['search'])) {
      $terms = explode(' ', $_GET['search']);
      $query = '';
      foreach ($terms as $term) {
        $query .= $term . '|';
      }
      $query = rtrim($query, '|');
      $conditions = [
        'OR' => [
          'Users.first_name REGEXP' => $query,
          'Users.last_name REGEXP' => $query,
          'Users.username REGEXP' => $query,
          'Users.email REGEXP' => $query,
        ]
      ];
    }

    $this->paginate = ['limit' => 50, 'order' => ['Users.last_name' => 'asc']];
    $users = $this->Users->find()
      ->where($conditions)
      ->contain('Badges');
    $this->set('users', $this->paginate($users));
  }

  public function locate() {
    $results = [];

    if ($this->request->is('post')) {
      $connection = ConnectionManager::get('whmcs');
      $results = $connection
        ->execute("SELECT id, firstname, lastname, email FROM tblclients WHERE firstname = :first AND lastname = :last", [
          'first' =>  $this->request->data['first_name'],
          'last' =>  $this->request->data['last_name']
        ])
        ->fetchAll('assoc');
    }

    $this->set('results', $results);
  }

  public function login() {
    if ($this->request->is('post')) {
      $user = $this->Auth->identify();


      if ($user) {
        $this->Auth->setUser($user);
        return $this->redirect($this->Auth->redirectUrl());
      }
      $this->Flash->error(__('Invalid username or password, try again.'));
    }
  }

  public function logout() {
    return $this->redirect($this->Auth->logout());
  }

  public function migrate($id = null) {
    $connection = ConnectionManager::get('whmcs');
    $whmcsData = $connection
      ->execute("SELECT * FROM tblclients WHERE id = :id", [
        'id' =>  $id
      ])
      ->fetchAll('assoc');

    if (count($whmcsData) > 0) {
      $username = $connection
        ->execute("SELECT value FROM tblcustomfieldsvalues WHERE relid = :id AND fieldid = 2", [
          'id' =>  $id
        ])
        ->fetchAll('assoc');
      $username = $username[0]['value'];

      $user = $this->Users->newEntity();
      $user_data = [
        'first_name' => $whmcsData[0]['firstname'],
        'last_name' => $whmcsData[0]['lastname'],
        'username' => strtolower($username),
        'email' => $whmcsData[0]['email'],
        'phone' => $whmcsData[0]['phonenumber'] ? $whmcsData[0]['phonenumber'] : 'NO PHONE WHMCS',
        'address_1' => $whmcsData[0]['address1'],
        'address_2' => $whmcsData[0]['address2'],
        'city' => $whmcsData[0]['city'],
        'state' => $whmcsData[0]['state'],
        'zip' => $whmcsData[0]['postcode'],
        'whmcs_user_id' => $whmcsData[0]['id']
      ];

      $user = $this->Users->patchEntity($user, $user_data);
      $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $password = substr(str_shuffle($chars), 0, 16);

      if ($this->Users->save($user)) {
        $user->createActiveDirectoryAccount($password);
        $this->Flash->success(__('WHMCS data imported for user. User will need to reset their password in WHMCS in order to access Active Directory as a temporary password has been used to facilitate initial account creation.'));
      } else {
        $user = $this->Users->find()
          ->where(['whmcs_user_id' => $whmcsData[0]['id']])
          ->first();
        $this->Flash->error(__('Data already exist in Maker Manager for requested client id. Note: In the event that this user had a missing Active Director account the account should now exist. User will need to reset their password in WHMCS in order to access Active Directory as a temporary password has been used to facilitate initial account creation.'));
        $user->createActiveDirectoryAccount($password);
      }
    } else {
      $this->Flash->error(__('No data found in WHMCS for requested client id.'));
    }

    return $this->redirect(['controller' => 'Users', 'action' => 'locate']);
  }

  public function password($id = null) {
    $user = $this->Users->get($id);

    if (empty($user)) {
      $this->Flash->error(__('User not found.'));
      return $this->redirect($this->referer());
    }

    if (empty($user->user_id)) {
      $this->Flash->error(__('Primary account holders must change their passwords through WHMCS.'));
      return $this->redirect($this->referer());
    }

    if ($this->request->is('put')) {
      if (strpos($this->request->data['password'], ' ') > -1 || strlen($this->request->data['password']) < 6) {
        $this->Flash->error(__('Password change failed. Passwords must be 6 characters or more and not include spaces.'));
      } else {
        $user->changeActiveDirectoryPassword($this->request->data['password']);
        $this->Flash->success(__('Password change successful.'));
      }
    }

    $this->set('user', $user);
  }

  public function sync($id = null) {
    $user = $this->Users->get($id, [
      'contain' => ['Badges', 'Children']
    ]);

    if (empty($user)) {
      $this->Flash->error(__('User not found.'));
      return $this->redirect($this->referer());
    }

    $connection = ConnectionManager::get('whmcs');
    $subscriptions = $connection->execute("SELECT * FROM tblhosting WHERE domainstatus = 'active' AND userid = $user->whmcs_user_id")->fetchAll('assoc');

    if (count($subscriptions) > 0) {
      // Synchronize primary subscription data
      if (empty($user->badge)) {
        $user_data['badge'] = [
          'user_id' => $user->id,
          'whmcs_user_id' => $user->whmcs_user_id,
          'whmcs_service_id' => $subscriptions[0]['id'],
          'whmcs_addon_id' => 0,
          'status' => 'unassigned'
        ];
        $user = $this->Users->patchEntity($user, $user_data);
      } else {
        $user->badge->enable('WHMCS Manual Synchronization');
      }
      $user->enableActiveDirectoryAccount();
      $this->Users->save($user);

      // Synchronize family addon data
      $subscriptionId = $subscriptions[0]['id'];
      $addons = $connection->execute("SELECT * FROM tblhostingaddons WHERE status = 'active' AND hostingid = $subscriptionId")->fetchAll('assoc');

      if (count($addons) > 0) {
        $badgesTable = TableRegistry::get('Badges');
        $badgesQuery = $badgesTable->find()
          ->where(['whmcs_user_id' => $user->whmcs_user_id])
          ->find('all');
        $badges = $badgesQuery->toArray();

        print_r($addons);
        foreach ($addons as $addon) {
          $exists = false;
          foreach ($badges as $badge) {
            if ($badge->whmcs_addon_id == $addon['addonid']) {
              $exists = true;
              break;
            }
          }

          if (!$exists) {
            $badge = $badgesTable->newEntity();
            $badge_data = [
              'whmcs_user_id' => $user->whmcs_user_id,
              'whmcs_service_id' => $subscriptions[0]['id'],
              'whmcs_addon_id' => $addon['addonid'],
              'status' => 'unassigned'
            ];
            $badge = $badgesTable->patchEntity($badge, $badge_data);
            $badgesTable->save($badge);
          }
        }
      }

      $this->Flash->success(__('Subscription found. Data successfully synced from WHMCS.'));
    } else {
      $this->Flash->error(__('No active subscription found to sync from WHMCS.'));
    }

    return $this->redirect(['controller' => 'Users', 'action' => 'view', $id]);
  }

  public function view($id = null) {
    $user = $this->Users->get($id);

    if (empty($user)) {
      $this->Flash->error(__('User not found.'));
      return $this->redirect($this->referer());
    }

    $family_accounts = $this->Users->find()
      ->where(['user_id' => $user->id]);

    $this->loadModel('Badges');
    $associated_badges = $this->Badges->find()
      ->where(['Badges.whmcs_user_id' => $user->whmcs_user_id])
      ->contain('Users');

    $this->set('user', $user);
    $this->set('family_accounts', $family_accounts);
    $this->set('associated_badges', $associated_badges);
  }

  public function waiver($id = null) {
    $user = $this->Users->get($id);

    if (!empty($user->waiver_id)) {
        $this->Flash->success(__('Waiver verified.'));
        return $this->redirect(['controller' => 'Badges', 'action' => 'users', $id]);
    }

    $waiver_id = $this->Smartwaiver->check($user->first_name, $user->last_name, $user->email);
    if (!empty($waiver_id)) {
        $user->waiver_id = $waiver_id;
        $this->Users->save($user);
        $this->Flash->success(__('Waiver verified.'));
        return $this->redirect(['controller' => 'Badges', 'action' => 'users', $id]);
    }

    if ($this->request->is('post')) {
      $this->loadComponent('Smartwaiver');
      $waiver_id = $this->Smartwaiver->check($user->first_name, $user->last_name, $this->request->data['email']);

      if (!empty($waiver_id)) {
        $user->waiver_id = $waiver_id;
        $this->Users->save($user);
        $this->Flash->success(__('Waiver verified.'));
        return $this->redirect(['controller' => 'Badges', 'action' => 'users', $id]);
      } else {
        $this->Flash->error(__('No waiver found that matches this user and email address') . $this->request->data['email'] . '. ' . __('You can fill out a waiver again at any of the kiosks and try again.'));
      }
    }

    $this->set('user', $user);
  }
}
