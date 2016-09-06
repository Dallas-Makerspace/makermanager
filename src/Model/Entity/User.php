<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Entity;

/**
 * Active Directory Integrations Live Here
 * ---------------------------------------
 * User Entity methods act as liaisons between Maker Manager and Active Directory.
 * These are handled here and not as behaviors due to the more standalone nature that
 * some AD updates have which aren't necessarily directly tied to an update of the
 * user's data in the database (such as user password), but are still synonymous with
 * the user as far as the entire environment is concerned.
 */
class User extends Entity {
  protected $_accessible = [
    '*' => true,
    'id' => false
  ];
  
  protected function encode8Bit(&$item, $key) {
    $encode = false;
    if (is_string($item)) {
      for ($i=0; $i<strlen($item); $i++) {
        if (ord($item[$i]) >> 7) {
          $encode = true;
        }
      }
    }
    if ($encode === true && $key != 'password') {
      $item = utf8_encode($item);   
    }
  }
  
  protected function _ldapSlashes($str){
    return preg_replace('/([\x00-\x1F\*\(\)\\\\])/e',
                        '"\\\\\".join("",unpack("H2","$1"))',
                        $str);
  }
  
  // Bind to Active Directory over LDAP
  protected function _ldapBind() {
    //$ldap = ldap_connect('ldap://' . Configure::read('ActiveDirectory.domain'), 389);
    $ldap = ldap_connect('ldaps://' . Configure::read('ActiveDirectory.domain'), 636);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    $ldap_rdn = Configure::read('ActiveDirectory.admin_user') . Configure::read('ActiveDirectory.suffix');

    $bind = @ldap_bind($ldap, $ldap_rdn, Configure::read('ActiveDirectory.admin_password'));
    if ($bind) {
      return $ldap;
    }
    
    return false;
  }
  
  // Active Directory LDAP schema translations
  // Originally from adLDAP by Scott Barnett, Richard Hyland. http://adldap.sourceforge.net/
  protected function _ldapSchema($attributes) {
    $mod=array();
    array_walk($attributes, array($this, 'encode8bit'));
    if (!empty($attributes["address_city"])){ $mod["l"][0]=$attributes["address_city"]; }
    if (!empty($attributes["address_code"])){ $mod["postalCode"][0]=$attributes["address_code"]; }
    if (!empty($attributes["address_country"])){ $mod["c"][0]=$attributes["address_country"]; }
    if (!empty($attributes["address_pobox"])){ $mod["postOfficeBox"][0]=$attributes["address_pobox"]; }
    if (!empty($attributes["address_state"])){ $mod["st"][0]=$attributes["address_state"]; }
    if (!empty($attributes["address_street"])){ $mod["streetAddress"][0]=$attributes["address_street"]; }
    if (!empty($attributes["company"])){ $mod["company"][0]=$attributes["company"]; }
    if (!empty($attributes["change_password"])){ $mod["pwdLastSet"][0]=0; }
    if (!empty($attributes["department"])){ $mod["department"][0]=$attributes["department"]; }
    if (!empty($attributes["description"])){ $mod["description"][0]=$attributes["description"]; }
    if (!empty($attributes["display_name"])){ $mod["displayName"][0]=$attributes["display_name"]; }
    if (!empty($attributes["email"])){ $mod["mail"][0]=$attributes["email"]; }
    if (!empty($attributes["employee_id"])){ $mod["employeeID"][0]=$attributes["employee_id"]; }
    if (!empty($attributes["expires"])){ $mod["accountExpires"][0]=$attributes["expires"]; }
    if (!empty($attributes["firstname"])){ $mod["givenName"][0]=$attributes["firstname"]; }
    if (!empty($attributes["home_directory"])){ $mod["homeDirectory"][0]=$attributes["home_directory"]; }
    if (!empty($attributes["home_drive"])){ $mod["homeDrive"][0]=$attributes["home_drive"]; }
    if (!empty($attributes["initials"])){ $mod["initials"][0]=$attributes["initials"]; }
    if (!empty($attributes["logon_name"])){ $mod["userPrincipalName"][0]=$attributes["logon_name"]; }
    if (!empty($attributes["manager"])){ $mod["manager"][0]=$attributes["manager"]; }
    if (!empty($attributes["office"])){ $mod["physicalDeliveryOfficeName"][0]=$attributes["office"]; }
    if (!empty($attributes["password"])){
      $password="\"".$attributes["password"]."\"";
      $encoded="";
      for ($i=0; $i <strlen($password); $i++){ $encoded.="{$password{$i}}\000"; }
      $mod["unicodePwd"][0] = $encoded;
    }
    if (!empty($attributes["profile_path"])){ $mod["profilepath"][0]=$attributes["profile_path"]; }
    if (!empty($attributes["script_path"])){ $mod["scriptPath"][0]=$attributes["script_path"]; }
    if (!empty($attributes["surname"])){ $mod["sn"][0]=$attributes["surname"]; }
    if (!empty($attributes["title"])){ $mod["title"][0]=$attributes["title"]; }
    if (!empty($attributes["telephone"])){ $mod["telephoneNumber"][0]=$attributes["telephone"]; }
    if (!empty($attributes["mobile"])){ $mod["mobile"][0]=$attributes["mobile"]; }
    if (!empty($attributes["pager"])){ $mod["pager"][0]=$attributes["pager"]; }
    if (!empty($attributes["ipphone"])){ $mod["ipphone"][0]=$attributes["ipphone"]; }
    if (!empty($attributes["web_page"])){ $mod["wWWHomePage"][0]=$attributes["web_page"]; }
    if (!empty($attributes["fax"])){ $mod["facsimileTelephoneNumber"][0]=$attributes["fax"]; }
    if (!empty($attributes["enabled"])){ $mod["userAccountControl"][0]=$attributes["enabled"]; }
    if (!empty($attributes["homephone"])){ $mod["homephone"][0]=$attributes["homephone"]; }
    if (!empty($attributes["group_sendpermission"])){ $mod["dlMemSubmitPerms"][0]=$attributes["group_sendpermission"]; }
    if (!empty($attributes["group_rejectpermission"])){ $mod["dlMemRejectPerms"][0]=$attributes["group_rejectpermission"]; }
    if (!empty($attributes["exchange_homemdb"])){ $mod["homeMDB"][0]=$attributes["exchange_homemdb"]; }
    if (!empty($attributes["exchange_mailnickname"])){ $mod["mailNickname"][0]=$attributes["exchange_mailnickname"]; }
    if (!empty($attributes["exchange_proxyaddress"])){ $mod["proxyAddresses"][0]=$attributes["exchange_proxyaddress"]; }
    if (!empty($attributes["exchange_usedefaults"])){ $mod["mDBUseDefaults"][0]=$attributes["exchange_usedefaults"]; }
    if (!empty($attributes["exchange_policyexclude"])){ $mod["msExchPoliciesExcluded"][0]=$attributes["exchange_policyexclude"]; }
    if (!empty($attributes["exchange_policyinclude"])){ $mod["msExchPoliciesIncluded"][0]=$attributes["exchange_policyinclude"]; }       
    if (!empty($attributes["exchange_addressbook"])){ $mod["showInAddressBook"][0]=$attributes["exchange_addressbook"]; }    
    if (!empty($attributes["exchange_altrecipient"])){ $mod["altRecipient"][0]=$attributes["exchange_altrecipient"]; } 
    if (!empty($attributes["exchange_deliverandredirect"])){ $mod["deliverAndRedirect"][0]=$attributes["exchange_deliverandredirect"]; }    
    if (!empty($attributes["exchange_hidefromlists"])){ $mod["msExchHideFromAddressLists"][0]=$attributes["exchange_hidefromlists"]; }
    if (!empty($attributes["contact_email"])){ $mod["targetAddress"][0]=$attributes["contact_email"]; }
    if (count($mod)==0){ return (false); }
    return ($mod);
  }
  
  // Active Directory account control conversions
  // Originally from adLDAP by Scott Barnett, Richard Hyland. http://adldap.sourceforge.net/
  protected function _accountControl($options) {
    $val=0;
    if (is_array($options)) {
      if (in_array("SCRIPT",$options)){ $val=$val+1; }
      if (in_array("ACCOUNTDISABLE",$options)){ $val=$val+2; }
      if (in_array("HOMEDIR_REQUIRED",$options)){ $val=$val+8; }
      if (in_array("LOCKOUT",$options)){ $val=$val+16; }
      if (in_array("PASSWD_NOTREQD",$options)){ $val=$val+32; }
      //PASSWD_CANT_CHANGE Note You cannot assign this permission by directly modifying the UserAccountControl attribute.
      //For information about how to set the permission programmatically, see the "Property flag descriptions" section.
      if (in_array("ENCRYPTED_TEXT_PWD_ALLOWED",$options)){ $val=$val+128; }
      if (in_array("TEMP_DUPLICATE_ACCOUNT",$options)){ $val=$val+256; }
      if (in_array("NORMAL_ACCOUNT",$options)){ $val=$val+512; }
      if (in_array("INTERDOMAIN_TRUST_ACCOUNT",$options)){ $val=$val+2048; }
      if (in_array("WORKSTATION_TRUST_ACCOUNT",$options)){ $val=$val+4096; }
      if (in_array("SERVER_TRUST_ACCOUNT",$options)){ $val=$val+8192; }
      if (in_array("DONT_EXPIRE_PASSWORD",$options)){ $val=$val+65536; }
      if (in_array("MNS_LOGON_ACCOUNT",$options)){ $val=$val+131072; }
      if (in_array("SMARTCARD_REQUIRED",$options)){ $val=$val+262144; }
      if (in_array("TRUSTED_FOR_DELEGATION",$options)){ $val=$val+524288; }
      if (in_array("NOT_DELEGATED",$options)){ $val=$val+1048576; }
      if (in_array("USE_DES_KEY_ONLY",$options)){ $val=$val+2097152; }
      if (in_array("DONT_REQ_PREAUTH",$options)){ $val=$val+4194304; } 
      if (in_array("PASSWORD_EXPIRED",$options)){ $val=$val+8388608; }
      if (in_array("TRUSTED_TO_AUTH_FOR_DELEGATION",$options)){ $val=$val+16777216; }
    }
    return $val;
  }
  
  public function assignMembersGroup() {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        // Add Active Directory account to members group        
        $filter = '(&(objectCategory=group)(name=' . $this->_ldapSlashes('Members') . '))';
        $fields = ['cn'];
        $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, $fields);
        $group_data = ldap_get_entries($ldap, $result);        
        $add_user = ['member' => $data[0]['dn']];
        $result = @ldap_mod_add($ldap, $group_data[0]['dn'], $add_user);
      }
    }
  }

  public function createActiveDirectoryAccount($password = null, $silent = false) {
    if (!empty($password)) {
      $ldap = $this->_ldapBind();
            
      if ($ldap != false) {
        $filter = "(sAMAccountName=$this->username)";
        $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter);
        $data = ldap_get_entries($ldap, $result);
        
        if ($data['count'] == 0) {
          $new_user = [
            'description' => $this->whmcs_user_id,
            'change_password' => 0,
            'enabled' => 0,
            'display_name' => $this->first_name . ' ' . $this->last_name,
            'firstname' => $this->first_name,
        	'surname' => $this->last_name,
        	'email' => $this->email,
        	'address_street' => $this->address_1 . ' ' . $this->address_2,
        	'address_city' => $this->city,
        	'address_state' => $this->state,
        	'address_code' => $this->zip,
        	'telephone' => $this->phone,
        	'container' => array('Members'),
        	'username' => $this->username,
        	'logon_name' => $this->username . "@dms.local",
        	'password' => $password
          ];
          
          if (!empty($this->whmcs_addon_id)) {
            $update_user['description'] .= '-' . $this->whmcs_addon_id;
          }
          
          $adding_user = $this->_ldapSchema($new_user);
          $adding_user['cn'][0] = $new_user['display_name'];
          $adding_user['samaccountname'][0] = $new_user['username'];
          $adding_user['objectclass'][0] = 'top';
          $adding_user['objectclass'][1] = 'person';
          $adding_user['objectclass'][2] = 'organizationalPerson';
          $adding_user['objectclass'][3] = 'user';
    
          $control_options = ['NORMAL_ACCOUNT', 'ACCOUNTDISABLE'];
          $adding_user['userAccountControl'][0] = $this->_accountControl($control_options);
          
          $new_user['container'] = array_reverse($new_user['container']);
          $container = 'OU=' . implode(', OU=', $new_user['container']);
          
          $result = @ldap_add($ldap, 'CN=' . $adding_user['cn'][0] . ', ' . $container . ',' . Configure::read('ActiveDirectory.dcString'), $adding_user);
          
          if ($result != true && !$silent) {
            Log::error('Active Directory account creation failed. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
          } else {
            $this->assignMembersGroup();
            return true;
          }
        } else {
          if (!$silent) {
            Log::error('Unable to create Active Directory account for member with username ' . $this->username . '. Username already exists in Active Directory. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
          }
        }
      } else {
        if (!$silent) {
          Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
        }
      }
    } else {
      if (!$silent) {
        Log::error('Active Directory account could not be created due to missing password. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    }
    
    return false;
  }

  public function enableActiveDirectoryAccount() {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        // Add Active Directory account to members group        
        /*$filter = '(&(objectCategory=group)(name=' . $this->_ldapSlashes('Members') . '))';
        $fields = ['cn'];
        $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, $fields);
        $group_data = ldap_get_entries($ldap, $result);        
        $add_user = ['member' => $data[0]['dn']];
        $result = @ldap_mod_add($ldap, $group_data[0]['dn'], $add_user);*/
        
        // Enable account
        $updated_user = $this->_ldapSchema(['enabled' => 1]);
        $control_options = ['NORMAL_ACCOUNT'];
        $updated_user['userAccountControl'][0] = $this->_accountControl($control_options);
        $result = @ldap_modify($ldap, $data[0]['dn'], $updated_user);
        
        if ($result != true) {
          Log::error('Unable to enable Active Directory account for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
        } else {
          $this->ad_active = 1;
        }
      } else {
        Log::error('Unable to enable Active Directory account for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    } else {
      Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
    }
  }

  public function disableActiveDirectoryAccount() {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        // Remove Active Directory account to members group        
        /* $filter = '(&(objectCategory=group)(name=' . $this->_ldapSlashes('Members') . '))';
        $fields = ['cn'];
        $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, $fields);
        $group_data = ldap_get_entries($ldap, $result);        
        $add_user = ['member' => $data[0]['dn']];
        $result = @ldap_mod_del($ldap, $group_data[0]['dn'], $add_user); */
        
        // Disable account
        $updated_user = $this->_ldapSchema(['enabled' => 1]);
        $control_options = ['NORMAL_ACCOUNT', 'ACCOUNTDISABLE'];
        $updated_user['userAccountControl'][0] = $this->_accountControl($control_options);
        
        $result = @ldap_modify($ldap, $data[0]['dn'], $updated_user);
        
        if ($result != true) {
          Log::error('Unable to disable Active Directory account for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
        } else {
          $this->ad_active = 0;
        }
      } else {
        Log::error('Unable to disable Active Directory account for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    } else {
      Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
    }
  }

  public function updateActiveDirectoryAccount() {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        $update_user = [
          'description' => $this->whmcs_user_id,
          'display_name' => $this->first_name . ' ' . $this->last_name,
          'firstname' => $this->first_name,
      		'surname' => $this->last_name,
      		'email' => $this->email,
      		'address_street' => $this->address_1 . ' ' . $this->address_2,
      		'address_city' => $this->city,
      		'address_state' => $this->state,
      		'address_code' => $this->zip,
      		'telephone' => $this->phone,
        ];
        
        if (!empty($this->whmcs_addon_id)) {
          $update_user['description'] .= '-' . $this->whmcs_addon_id;
        }
        
        $updated_user = $this->_ldapSchema($update_user);
        $result = @ldap_modify($ldap, $data[0]['dn'], $updated_user);
        
        if ($result != true) {
          Log::error('Unable to edit Active Directory account information for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
        }
      } else {
        Log::error('Unable to edit Active Directory account information for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    } else {
      Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
    }
  }

  public function changeActiveDirectoryPassword($password = null) {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        $updated_user = $this->_ldapSchema(['password' => $password]);
        $result = @ldap_modify($ldap, $data[0]['dn'], $updated_user);
        
        if ($result != true) {
          Log::error('Unable to update Active Directory account password for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
        } else {
          $this->ad_active = 0;
        }
      } else {
        Log::error('Unable to update Active Directory account password for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    } else {
      Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
    }
  }
  
  public function associateBadgeNumberWithActiveDirectory($badge_number) {
    $ldap = $this->_ldapBind();
    
    if ($ldap != false) {
      $filter = "(sAMAccountName=$this->username)";
      $result = ldap_search($ldap, Configure::read('ActiveDirectory.dcString'), $filter, ['cn']);
      $data = ldap_get_entries($ldap, $result);
      
      if ($data['count'] > 0) {
        $updated_user = $this->_ldapSchema(['employee_id' => $badge_number]);
        $result = @ldap_modify($ldap, $data[0]['dn'], $updated_user);
        
        if ($result != true) {
          Log::error('Unable to add badge number to Active Directory account information for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
        } else {
          $this->ad_active = 0;
        }
      } else {
        Log::error('Unable to add badge number to Active Directory account information for member with username ' . $this->username . '. App id: ' . $this->id . ', WHMCS id: ' . $this->whmcs_user_id, ['scope' => ['activeDirectory']]);
      }
    } else {
      Log::error('Unable to bind to Active Directory LDAP. Admin account information is likely incorrect within the app config.', ['scope' => ['activeDirectory']]);
    }
  }
}
