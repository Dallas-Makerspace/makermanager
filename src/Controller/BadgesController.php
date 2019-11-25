<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class BadgesController extends AppController {
  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
  }

  public function isAuthorized($user) {
    if (in_array($this->request->action, ['users'])) {
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

    if (in_array($this->request->action, ['assign', 'revoke'])) {
      if (isset($user['badges'])) {
        foreach ($user['badges'] as $badge) {
          if ($badge['id'] == $this->request->pass[0]) {
            return true;
          }
        }
      }
    }

    return parent::isAuthorized($user);
  }

  public function add() {
    $badge = $this->Badges->newEntity();
    if ($this->request->is('post')) {
      if (empty($this->request->data['description'])) {
        $this->Flash->error(__('A description is required for one off badges.'));
      } else {
        $this->request->data['whmcs_user_id'] = 0;
        $this->request->data['whmcs_service_id'] = 0;
        $this->request->data['whmcs_addon_id'] = 0;
        $this->request->data['user_id'] = 0;
        $this->request->data['status'] = 'unassigned';
        $badge = $this->Badges->patchEntity($badge, $this->request->data);

        if ($this->Badges->save($badge)) {
          return $this->redirect(['controller' => 'Badges', 'action' => 'edit', $badge->id]);
        } else {
          $this->Flash->error(__('Badge could not be created. Please try again.'));
        }
      }
    }

    $this->set('badge', $badge);
  }

  public function assign($id = null) {
    $badge = $this->Badges->get($id);
    if (!empty($badge->user_id)) {
      $this->Flash->error(__('Badge is already assigned.'));
      return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    $this->loadModel('Users');
    if ($this->request->is('post')) {
      $user = $this->Users->find()
        ->where(['id' => $this->request->data['user_id'], 'whmcs_user_id' => $badge->whmcs_user_id])
        ->first();

      if (!empty($user)) {
        $badge = $this->Badges->patchEntity($badge, ['user_id' => $user->id]);
        $this->Badges->save($badge);
        $user->enableActiveDirectoryAccount();
        $this->Users->save($user);
        $this->refreshAuthUser();
        $this->Flash->success(__('Badge assigned to family member.'));
        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
      } else {
        $this->Flash->success(__('Badge could not be assigned to user, WHMCS data mismatch.'));
      }
    }

    $family_query = $this->Users->find()
      ->where(['Users.whmcs_user_id' => $badge->whmcs_user_id, 'Users.user_id IS NOT NULL'])
      ->contain('Badges');

    $family_members = [];
    foreach ($family_query as $family_member) {
      if (empty($family_member->badge)) {
        $family_members[$family_member->id] = $family_member->first_name . ' ' . $family_member->last_name;
      }
    }

    $this->set('badge', $badge);
    $this->set('family_members', $family_members);
  }

  public function delete($id = null) {
    $badge = $this->Badges->get($id);

    if (empty($badge) || $badge->whmcs_user_id != 0) {
      $this->Flash->error(__('Requested one off badge was not found.'));
      return $this->redirect($this->referer());
    }

    $this->loadModel('BadgeHistories');
    $associated_id = $badge->id;

    $this->Badges->patchEntity($badge, ['status' => 'unassigned']);
    $this->Badges->save($badge);
    $this->Badges->delete($badge);
    $this->BadgeHistories->deleteAll(['badge_id' => $associated_id]);

    $this->Flash->success(__('Badge deleted.'));
    return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
  }

  public function disable($id = null, $reason = 0) {
    $reason = min(max($reason, 0), 3);

    $reasons = [
      0 => 'Disabled by admin',
      1 => 'Lost',
      2 => 'Damaged',
      3 => 'Other'
    ];

    $badge = $this->Badges->get($id);

    if (empty($badge)) {
      $this->Flash->error(__('Requested badge was not found.'));
      return $this->redirect($this->referer());
    }

    if ($badge->whmcs_user_id == 0) {
      $badge->disable($reasons[$reason]);
      $this->Badges->save($badge);
      $this->Flash->success(__('Badge disabled.'));
    } else {

    }

    return $this->redirect($this->referer());
  }

  public function edit($id = null) {
    $badge = $this->Badges->get($id);

    if (empty($badge) || $badge->whmcs_user_id != 0) {
      $this->Flash->error(__('Requested one off badge was not found.'));
      return $this->redirect($this->referer());
    }

    if ($this->request->is('put')) {
      if (empty($this->request->data['description'])) {
        $this->Flash->error(__('A description is required for one off badges.'));
      } else {
        $badge = $this->Badges->patchEntity($badge, $this->request->data);
        $badge->enable('New Badge Number Assigned');
        if ($this->Badges->save($badge)) {
          $this->Flash->success(__('Badge updated.'));
        } else {
          $this->Flash->error(__('Badge could not be created. Please try again.'));
        }
      }
    }

    $this->set('badge', $badge);
  }

  public function enable($id = null) {
    $badge = $this->Badges->get($id);

    if (empty($badge)) {
      $this->Flash->error(__('Requested badge was not found.'));
      return $this->redirect($this->referer());
    }

    if ($badge->whmcs_user_id == 0) {
      $badge->enable('Enabled by admin');
      $this->Badges->save($badge);
      $this->Flash->success(__('Badge enabled.'));
    } else {

    }

    return $this->redirect($this->referer());
  }

  public function index($list = null) {
    $this->paginate = [
      'contain' => ['Users'],
      'sortWhitelist' => ['Users.first_name', 'Users.last_name', 'Users.username', 'Users.email', 'description', 'number', 'status'],
      'order' => ['Users.last_name' => 'asc']
    ];

    $type = 'all';
    $conditions = ['Badges.user_id IS NOT NULL'];
    if (!empty($list)) {
      if ($list == 'active' || $list == 'suspended' || $list == 'unassigned') {
        $conditions['Badges.status'] = $list;
        $type = $list;
      }
      if ($list == 'oneoff') {
        $conditions['Badges.user_id'] = 0;
        $type = 'One Off';
      }
    }

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
          'Badges.description REGEXP' => $query,
          'Badges.number REGEXP' => $query,
          'Badges.status REGEXP' => $query,
        ]
      ];
    }

    $badges = $this->Badges->find()
      ->where($conditions)
      ->contain('Users');
    $this->set('badges', $this->paginate($badges));
    $this->set('type', ucwords($type));
  }

  // Revoke badge privledge from a family member account
  public function revoke($id = null) {
    $badge = $this->Badges->get($id);

    if (!empty($badge)) {
      $this->loadModel('Users');
      $user = $this->Users->get($badge->user_id);
      $user->disableActiveDirectoryAccount();
      $this->Users->save($user);
      $badge = $this->Badges->patchEntity($badge, ['user_id' => null]);
      $this->Badges->save($badge);
      $this->refreshAuthUser();
      $this->Flash->success(__('Badge successfully revoked.'));
      return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    } else {
      $this->Flash->error(__('Badge could not be revoked. Requested badge was not found.'));
      return $this->redirect($this->referer());
    }
  }

  public function suspend($id = null) {
    $badge = $this->Badges->get($id);

    if (empty($badge)) {
      $this->Flash->error(__('Requested badge was not found.'));
      return $this->redirect($this->referer());
    }

    if ($badge->whmcs_user_id == 0) {
      $badge->suspend('Suspended by admin');
      $this->Badges->save($badge);
      $this->Flash->success(__('Badge suspended.'));
    } else {

    }

    return $this->redirect($this->referer());
  }

  // Known user badge administration
  public function users($user_id = null) {
    if ($this->request->is('put')) {
      $badge = $this->Badges->find()
        ->where(['user_id' => $user_id])
        ->first();

      if (!empty($this->request->data['disable'])) {
        // Disabling badge
        $badge->disable($this->request->data['disable']);
        if ($this->Badges->save($badge)) {
          $this->Flash->success(__('Badge has been disabled.'));
        } else {
          $this->Flash->error(__('Unable to disable badge. Please try again.'));
        }
      } else {
        // Enabling badge
        $badge = $this->Badges->patchEntity($badge, $this->request->data);
        $badge->enable('New Badge Number Assigned');
        if ($this->Badges->save($badge)) {
          $this->Flash->success(__('Badge has been activated.'));
        } else {
          $this->Flash->error(__('Badge activation failed. Double check your badge number and try again.'));
        }
      }
    }

    $this->loadModel('Users');
    $user = $this->Users->find()
      ->where(['Users.id' => $user_id])
      ->contain('Badges')
      ->first();

    // No waiver on file for user, look it up or forward to more info
    if (empty($user->waiver_id)) {
      $this->loadComponent('Smartwaiver');
      $waiver_id = $this->Smartwaiver->check($user->first_name, $user->last_name, $user->email);

      if (!$waiver_id) {
        return $this->redirect(['controller' => 'Users', 'action' => 'waiver', $user_id]);
      }

      $user->waiver_id = $waiver_id;
      $this->Users->save($user);
    }

    $this->set('user', $user);
  }
}
