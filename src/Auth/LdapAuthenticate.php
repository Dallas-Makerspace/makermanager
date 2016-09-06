<?php
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;

class LdapAuthenticate extends BaseAuthenticate {
  public function authenticate(Request $request, Response $response) {
    if (!empty($request->data['username']) && !empty($request->data['password'])) {
      $username = $request->data['username'];
      $password = $request->data['password'];

      // Query active directory for authorized user
      //$ldap = ldap_connect('ldap://' . Configure::read('ActiveDirectory.domain'), 389);
      $ldap = ldap_connect('ldaps://' . Configure::read('ActiveDirectory.domain'), 636);
      ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
      $ldap_rdn = $username . Configure::read('ActiveDirectory.suffix');

      $bind = @ldap_bind($ldap, $ldap_rdn, $password);
      if ($bind) {
        $filter = "(sAMAccountName=$username)";
        $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter);
        $data = ldap_get_entries($ldap, $result);

        if ($data['count'] == 1) {
          $auth_user = [
            'username' => $data[0]['samaccountname'][0],
            'is_admin' => false,
            'is_primary' => false
          ];

          if (isset($data[0]['memberof'])) {
            unset($data[0]['memberof']['count']);
            foreach ($data[0]['memberof'] as $group) {
              if (strpos($group, 'CN=MakerManager Admins') > -1) {
                $auth_user['is_admin'] = true;
                break;
              }
            }
          }
        }

        // Check username from active directory against local maker manager database
        $users = TableRegistry::get('Users');
        $user = $users->find()
          ->where(['username' => $auth_user['username']])
          ->select(['id', 'email', 'whmcs_user_id', 'waiver_id', 'user_id'])
          ->contain(['Children' => ['fields' => ['id', 'first_name', 'last_name', 'user_id']]])
          ->first();

        // Assign local user id, if it exists
        if (!empty($user)) {
          // Get account family members and all open badges
          if (empty($user->user_id)) {
            $auth_user['email'] = $user->email;
            if (empty($user->user_id)) {
              $auth_user['is_primary'] = true;
            }
            $auth_user['children'] = $user->children;
            $auth_user['badges'] = [];

            $badges = TableRegistry::get('Badges');
            $user_badges = $badges->find()
              ->select(['id', 'user_id', 'number'])
              ->where(['whmcs_user_id' => $user->whmcs_user_id]);

            foreach ($user_badges as $badge) {
              $auth_user['badges'][] = $badge;
            }
          }

          $auth_user['id'] = $user->id;
        }

        return $auth_user;
      }
    }

    return false;
  }
}
