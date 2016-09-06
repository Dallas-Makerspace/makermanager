<?php
namespace App\Model\Behavior;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class BadgeControlBehavior extends Behavior {
  public function beforeSave(Event $event, EntityInterface $entity) {
    if ($entity->status != $entity->getOriginal('status')) {
      $responses = [
        'User Added Successfully',
        'User Removed Successfully'
      ];
      
      $response = $this->_badgeController($entity->status, $entity->number, $entity->user_id);

      if ($response['status'] != 200) {
        $entity->errors('badgeController', ['Unexpected Error Code: ' . $response['status']]);
        $event->stopPropagation();
      }

      if (!in_array($response['message'], $responses)) {
        $entity->errors('badgeController', ['Unexpected Response: ' . $response['message']]);
        $event->stopPropagation();
      }
    }
  }

  private function _badgeController($status = 'unassigned', $badge = null, $user_id = null) {
    $map_actions = [
      'active' => 'add',
      'unassigned' => 'remove',
      'suspended' => 'remove'
    ];
    
    $badge_api = Configure::read('Badges.url');
    $badge_api .= '?apiKey=' . Configure::read('Badges.authorization');
    $badge_api .= '&action=' . $map_actions[$status];
    $badge_api .= '&badge=' . $badge;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $badge_api);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!empty($user_id)) {
      $pass_badge = ($map_actions[$status] == 'add' ? $badge : '');
      $users = TableRegistry::get('Users');
      $user = $users->get($user_id);
      $user->associateBadgeNumberWithActiveDirectory((!empty($pass_badge) ? $pass_badge : ''));
    }

    return [
      'status' => $http_status,
      'message' => $response
    ];
  }
}
