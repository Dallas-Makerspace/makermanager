<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class BadgeHistoriesController extends AppController {
  public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
  }
  
  public function isAuthorized($user) {
    return parent::isAuthorized($user);
  }

  public function index() {
    $this->paginate = ['limit' => 50];
    
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
          'Badges.description REGEXP' => $query,
          'BadgeHistories.badge_number REGEXP' => $query,
          'BadgeHistories.changed_to REGEXP' => $query,
          'BadgeHistories.reason REGEXP' => $query,
          'BadgeHistories.created REGEXP' => $query,
        ]
      ];
      if (strtolower($_GET['search']) == 'system') {
        $conditions = [
          'OR' => [
            'BadgeHistories.modified_by IS NULL'
          ]
        ];
      }
    }
    
    $badge_histories = $this->BadgeHistories->find()
      ->where($conditions)
      ->contain(['Badges.Users', 'Users'])
      ->order(['BadgeHistories.created' => 'DESC']);
    $this->set('badge_histories', $this->paginate($badge_histories));
  }
}
