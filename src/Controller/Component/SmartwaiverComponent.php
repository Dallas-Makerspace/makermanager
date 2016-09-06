<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class SmartwaiverComponent extends Component {
  private function _queryApi($options = array()) {
    $authorized_call = 'https://www.smartwaiver.com/api/v3/?rest_request=' . Configure::read('Smartwaiver.authorization');
    
    foreach ($options as $key => $value) {
      $authorized_call .= '&' . $key . '=' . $value;
    }
    
    $api_result = simplexml_load_file($authorized_call);
    
    if (isset($api_result->participants)) {
      return $api_result->participants;
    }
    
    return false;
  }
  
  public function check($last_name = '', $email = '') {
    if (!empty($last_name) && !empty($email)) {
      $results = $this->_queryApi(['rest_request_lastname' => $last_name]);
      
      if ($results) {  
        foreach ($results->participant as $result) {
          if ($result->primary_email == $email) {
            return (string) $result->waiver_id;
          }
        }
      }
    }
    
    return false;
  }
}