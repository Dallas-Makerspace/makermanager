<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class EndpointsController extends AppController {
  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);

    // Allow access to all endpoints in the controller for whmcs communication
    $this->Auth->allow();

    // Access to endpoints is protected by a valid mm_auth value in the post array
    if (empty($this->request->data['mm_auth']) || $this->request->data['mm_auth'] != Configure::read('Endpoint.authorization')) {
      echo 'Unauthorized';
      exit;
    }

    $this->autoRender = false;
  }

  protected function _sendEmail($message = '', $subject = 'Maker Manager Error Reported', $to_name = 'DMS Back Office', $to_email = 'backoffice@dallasmakerspace.org',  $from_name = 'DMS Maker Manager', $from_email = 'admin@dallasmakerspace.org') {
    Email::configTransport('sparkpost', [
        'className' => 'SparkPost.SparkPost',
        'apiKey' => Configure::read('SparkPost.Api.key')
    ]);

    if (!empty($message)) {
      $email = new Email();
      $email->transport('sparkpost');
      $email->from([$from_email => $from_name]);
      $email->to([$to_email => $to_name]);
      $email->subject($subject);
      $email->send($message);
    }
  }

  public function addonActivate() {
    // Create a new family member empty badge and associate it with its parent user via whmcs_user_id
    $badgesTable = TableRegistry::get('Badges');
    $badge = $badgesTable->newEntity();

    // Assigning a 0 for uniquness across multiple fields, null causes this check to fail
    $badge_data = [
      'whmcs_user_id' => $this->request->data['userid'],
      'whmcs_service_id' => $this->request->data['serviceid'],
      'whmcs_addon_id' => $this->request->data['addonid'],
      'status' => 'unassigned'
    ];

    $badge = $badgesTable->patchEntity($badge, $badge_data);
    if (!$badgesTable->save($badge)) {
      $this->_sendEmail('WHMCS module creation failed to seed a family member user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ', service id ' . $this->request->data['serviceid'] . ' and addon id ' . $this->request->data['addonid']);
      Log::error('WHMCS module creation failed to seed a family member user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ', service id ' . $this->request->data['serviceid'] . ' and addon id ' . $this->request->data['addonid'] . '. Errors in next logged message.', ['scope' => ['users']]);
      Log::error(var_export($badge->errors(), true), ['scope' => ['users']]);
    } else {
      Log::error('WHMCS module creation successfully seeded a family member user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ', service id ' . $this->request->data['serviceid'] . ' and addon id ' . $this->request->data['addonid'], ['scope' => ['users']]);
    }
  }

  public function addonCancel() {
    // Disable addon badge, delete the immediate badge record and disable active directory account
    $badgesTable = TableRegistry::get('Badges');
    $badge = $badgesTable->find()
      ->where(['whmcs_service_id' => $this->request->data['serviceid']])
      ->andWhere(['whmcs_addon_id' => $this->request->data['addonid']])
      ->contain(['Users'])
      ->first();

    if (!empty($badge)) {
      $usersTable = TableRegistry::get('Users');
      $badge->suspend('WHMCS Addon Cancelled');
      $badgesTable->save($badge);
      if (!empty($badge->user)) {
        $badge->user->disableActiveDirectoryAccount();
        $usersTable->save($badge->user);
      }

      $badgeHistories = TableRegistry::get('BadgeHistories');
      $associated_id = $badge->id;
      $badgesTable->delete($badge);
      $badgeHistories->deleteAll(['badge_id' => $associated_id]);
    }

    // Badge not found, but since this addon was cancelled it's inconsequential
    Log::error('WHMCS addon cancellation successfully removed a family member user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ', service id ' . $this->request->data['serviceid'] . ' and addon id ' . $this->request->data['addonid'], ['scope' => ['users']]);
  }
    public function userAdd(){
        $usersTable = TableRegistry::get('Users');
        if($existing_user = $usersTable->find()->where(['whmcs_user_id' => $this->request->data['user_id']])->first())
        {
            if (!empty($existing_user)) {
                // Create a new active directory account, if one didn't previously exist
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $password = substr(str_shuffle($chars), 0, 16);
                $existing_user->createActiveDirectoryAccount($password, true);

                $existing_user->changeActiveDirectoryPassword($this->request->data['password']);

                Log::error('WHMCS client successfully changed Active Directory password for WHMCS user id ' . $this->request->data['userid'] . '.', ['scope' => ['users']]);
            } else {
                $this->_sendEmail('WHMCS client failed Active Directory password changing in Maker Manager for WHMCS user id ' . $this->request->data['user_id'] . '. WHMCS user not found in Maker Manager.');
                Log::error('WHMCS client failed Active Directory password changing in Maker Manager for WHMCS user id ' . $this->request->data['user_id'] . '. WHMCS user not found in Maker Manager.', ['scope' => ['users']]);
            }
        }

    }
  public function clientAdd() {
    // Create user in App
    $usersTable = TableRegistry::get('Users');

    $existing_user = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']])
      ->first();

    if (empty($existing_user)) {
      $user = $usersTable->newEntity();
      $user_data = [
        'first_name' => $this->request->data['firstname'],
        'last_name' => $this->request->data['lastname'],
        'username' => strtolower($this->request->data['username']),
        'email' => $this->request->data['email'],
        'phone' => $this->request->data['phonenumber'],
        'address_1' => $this->request->data['address1'],
        'address_2' => $this->request->data['address2'],
        'city' => $this->request->data['city'],
        'state' => $this->request->data['state'],
        'zip' => $this->request->data['postcode'],
        'whmcs_user_id' => $this->request->data['userid']
];
  //Attempt to log variable - Freddy
      Log::error(var_export($this->request->data, true));
$user = $usersTable->patchEntity($user, $user_data);
      if ($usersTable->save($user)) {
        $user->createActiveDirectoryAccount($this->request->data['password']);
        Log::error('New WHMCS client successfully created in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '.', ['scope' => ['users']]);
      } else {
        $this->_sendEmail('New WHMCS client failed creation in Maker Manager for WHMCS user id ' . $this->request->data['userid']);
        Log::error('New WHMCS client failed creation in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. Errors in next logged message.', ['scope' => ['users']]);
        Log::error(var_export($user->errors(), true), ['scope' => ['users']]);
      }
    } else {
      Log::error('Possible Conflict: New WHMCS client skipped creation in Maker Manager (whmcs_user_id already exists in MM) for WHMCS user id ' . $this->request->data['userid'] . '. Errors in next logged message.', ['scope' => ['users']]);
    }
  }

  public function clientChangePassword() {
    // Update user's password in active directory
    $usersTable = TableRegistry::get('Users');
    $user = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']])
      ->andWhere(['user_id IS' => null])
      ->first();

    if (!empty($user)) {
      // Create a new active directory account, if one didn't previously exist
      $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $password = substr(str_shuffle($chars), 0, 16);
      $user->createActiveDirectoryAccount($password, true);

      $user->changeActiveDirectoryPassword($this->request->data['password']);

      Log::error('WHMCS client successfully changed Active Directory password for WHMCS user id ' . $this->request->data['userid'] . '.', ['scope' => ['users']]);
    } else {
      $this->_sendEmail('WHMCS client failed Active Directory password changing in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. WHMCS user not found in Maker Manager.');
      Log::error('WHMCS client failed Active Directory password changing in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. WHMCS user not found in Maker Manager.', ['scope' => ['users']]);
    }
  }

  public function clientEdit() {
    // Update user in App
    $usersTable = TableRegistry::get('Users');
    $user = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']])
      ->andWhere(['user_id IS' => null])
      ->first();

    if (!empty($user)) {
      // Not all data was present during WHMCS endpoint testing, this is why the posted data is tested
      // for the presence of each field before assigning it to the patch array
      $relevant_data = [
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'email' => 'email',
        'phonenumber' => 'phone',
        'address1' => 'address_1',
        'address2' => 'address_2',
        'city' => 'city',
        'state' => 'state',
        'postcode' => 'zip'
      ];
      $user_data = [];

      foreach ($relevant_data as $search => $assign) {
        if (!empty($this->request->data[$search])) {
          $user_data[$assign] = $this->request->data[$search];
        }
      }

      $user = $usersTable->patchEntity($user, $user_data);
      if ($usersTable->save($user)) {
        // Create a new active directory account, if one didn't previously exist
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = substr(str_shuffle($chars), 0, 16);
        $user->createActiveDirectoryAccount($password, true);

        $user->updateActiveDirectoryAccount();

        Log::error('WHMCS client successfully edited in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '.', ['scope' => ['users']]);
      } else {
        Log::error('WHMCS client failed editing in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. Errors in next logged message.', ['scope' => ['users']]);
        Log::error(var_export($user->errors(), true), ['scope' => ['users']]);
      }
    } else {
      $this->_sendEmail('WHMCS client failed editing in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. WHMCS user not found in Maker Manager.');
      Log::error('WHMCS client failed editing in Maker Manager for WHMCS user id ' . $this->request->data['userid'] . '. WHMCS user not found in Maker Manager.', ['scope' => ['users']]);
    }
  }

  public function invoicePaid() {
    /**
     * The purpose of this method is to set the WHMCS domain status for this invoice as
     * active. Whether or not that has any real affect on the operation of WHMCS in
     * regards to accounts and payments isn't clear. We (Brooks/Eric) think this won't
     * have any ramifications if ditched, but the skeleton method is left commented out
     * below in case it needs to be revived and finished in the future.
     */

    /*$post_fields = [
      'action' => 'getinvoice',
      'invoiceid' => $this->request->data['invoiceid']
    ];

    $invoice_data = $this->_whmcsApiCall($post_fields);
    if ($invoice_data['WHMCSAPI']['STATUS'] == 'Paid') {
      $post_fields = [
        'action' => 'getclientsdomains',
        'clientid' => 1212
      ];

      $client_domains = $this->_whmcsApiCall($post_fields);
    }*/
  }

  public function moduleCreate() {
    // Modules are primary account bound, incoming data creates a badge directly tied to the account holder
    $usersTable = TableRegistry::get('Users');
    $user = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']])
      ->first();

    if (empty($user)) {
      $this->_sendEmail('WHMCS module creation failed to seed a user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ' and service id ' . $this->request->data['serviceid'] . '. A Maker Manager user with the given WHMCS user id could not be found.');
      Log::error('WHMCS module creation failed to seed a user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ' and service id ' . $this->request->data['serviceid'] . '. A Maker Manager user with the given WHMCS user id could not be found.', ['scope' => ['users']]);
      exit;
    }

    // Create a new active directory account, if one didn't previously exist
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = substr(str_shuffle($chars), 0, 16);
    $user->createActiveDirectoryAccount($password, true);

    // Create a new empty badge and associate it with its user via whmcs_user_id
    $badgesTable = TableRegistry::get('Badges');
    $badge = $badgesTable->newEntity();

    // Assigning a 0 for uniquness across multiple fields, null causes this check to fail
    $badge_data = [
      'user_id' => $user->id,
      'whmcs_user_id' => $this->request->data['userid'],
      'whmcs_service_id' => $this->request->data['serviceid'],
      'whmcs_addon_id' => 0,
      'status' => 'unassigned'
    ];

    $badge = $badgesTable->patchEntity($badge, $badge_data);
    if (!$badgesTable->save($badge)) {
      $this->_sendEmail('WHMCS module creation failed to seed a user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ' and service id ' . $this->request->data['serviceid']);
      Log::error('WHMCS module creation failed to seed a user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ' and service id ' . $this->request->data['serviceid'] . '. Errors in next logged message.', ['scope' => ['users']]);
      Log::error(var_export($badge->errors(), true), ['scope' => ['users']]);
    }

    $user->enableActiveDirectoryAccount();
    $usersTable->save($user);

    Log::error('WHMCS module creation successfully seeded a user badge in Maker Manager for user with WHMCS user id ' . $this->request->data['userid'] . ' and service id ' . $this->request->data['serviceid'] . '.', ['scope' => ['users']]);
  }

  public function moduleSuspend() {
    // Disable all badges and active directory accounts associated with the service
    $badgesTable = TableRegistry::get('Badges');
    $badges = $badgesTable->find()
      ->where(['whmcs_service_id' => $this->request->data['serviceid']]);
    foreach ($badges as $badge) {
      $badge->suspend('WHMCS Module Suspended');
      $badgesTable->save($badge);
    }

    $usersTable = TableRegistry::get('Users');
    $users = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']]);

    foreach ($users as $user) {
      $user->disableActiveDirectoryAccount();
      $usersTable->save($user);
    }

    Log::error('WHMCS module data suspended in AD and MM for WHMCS service id ' . $this->request->data['serviceid'] . '.', ['scope' => ['users']]);
  }

  public function moduleTerminate() {
    // Disable all badges and active directory accounts associated with the service and delete all immediate badge records
    $badgesTable = TableRegistry::get('Badges');
    $badges = $badgesTable->find()
      ->where(['whmcs_service_id' => $this->request->data['serviceid']]);

    $badgeHistories = TableRegistry::get('BadgeHistories');
    foreach ($badges as $badge) {
      $badge->suspend('WHMCS Module Terminated');
      $badgesTable->save($badge);
      $associated_id = $badge->id;
      $badgesTable->delete($badge);
      $badgeHistories->deleteAll(['badge_id' => $associated_id]);
    }

    $usersTable = TableRegistry::get('Users');
    $users = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']]);

    foreach ($users as $user) {
      $user->disableActiveDirectoryAccount();
      $usersTable->save($user);
    }

    Log::error('WHMCS module data terminated in AD and MM for WHMCS service id ' . $this->request->data['serviceid'] . '.', ['scope' => ['users']]);
  }

  public function moduleUnsuspend() {
    // Enable all badges and active directory accounts associated with the service
    $badgesTable = TableRegistry::get('Badges');
    $badges = $badgesTable->find()
      ->where(['whmcs_service_id' => $this->request->data['serviceid']]);
    foreach ($badges as $badge) {
      $badge->enable('WHMCS Module Unsuspended');
      $badgesTable->save($badge);
    }

    $usersTable = TableRegistry::get('Users');
    $users = $usersTable->find()
      ->where(['whmcs_user_id' => $this->request->data['userid']]);

    foreach ($users as $user) {
      $user->enableActiveDirectoryAccount();
      $usersTable->save($user);
    }

    Log::error('WHMCS module data unsuspended in AD and MM for WHMCS service id ' . $this->request->data['serviceid'] . '.', ['scope' => ['users']]);
  }

  private function _whmcsApiCall($post_fields) {
    $post_fields['username'] = Configure::read('Whmcs.username');
    $post_fields['password'] = md5(Configure::read('Whmcs.password'));
    $post_fields['responsetype'] = 'xml';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, Configure::read('Whmcs.url'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));

    $response = curl_exec($ch);
    curl_close($ch);

    $xml_parser = xml_parser_create();
 	  xml_parse_into_struct($xml_parser, $response, $vals, $index);
 	  xml_parser_free($xml_parser);
 	  $params = [];
 	  $level = [];
 	  $alreadyused = [];
 	  $x = 0;

    foreach ($vals as $xml_elem) {
 	    if ($xml_elem['type'] == 'open') {
        if (in_array($xml_elem['tag'],$alreadyused)) {
          $x++;
 		 	    $xml_elem['tag'] = $xml_elem['tag'].$x;
        }

        $level[$xml_elem['level']] = $xml_elem['tag'];
        $alreadyused[] = $xml_elem['tag'];
      }

      if ($xml_elem['type'] == 'complete') {
        $start_level = 1;
        $php_stmt = '$params';

        while ($start_level < $xml_elem['level']) {
          $php_stmt .= '[$level['.$start_level.']]';
          $start_level++;
        }

        $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
        @eval($php_stmt);
      }
    }

    return($params);
  }
}
