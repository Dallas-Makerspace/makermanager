<?php
namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class BadgesTable extends Table {
  public function initialize(array $config) {
    $this->addBehavior('BadgeControl');
    $this->addBehavior('BadgeLog');
    $this->addBehavior('Timestamp');
    $this->belongsTo('Users');
    $this->hasMany('BadgeHistories');
  }

  public function validationDefault(Validator $validator) {
    return $validator
      ->requirePresence('whmcs_user_id', 'create')
      ->notEmpty('whmcs_user_id')
      ->add('whmcs_user_id', [
        'numeric' => [
          'rule' => 'numeric',
          'message' => 'whmcs_user_id relation must be an integer.'
        ]
      ])
      ->requirePresence('whmcs_service_id', 'create')
      ->notEmpty('whmcs_service_id')
      ->add('whmcs_service_id', [
        'numeric' => [
          'rule' => 'numeric',
          'message' => 'whmcs_service_id relation must be an integer.'
        ],
        'emptyOrUnique' => [
          'rule' => function($value, $context){
            if (empty($value)) { return true; }
            $count = $this->find()
              ->where(['whmcs_service_id' => $value, 'whmcs_addon_id' => $context['data']['whmcs_addon_id']])
              ->count();
            if ($count > 0) { return false; }
            return true;
          },
          'message' => 'whmcs_service_id already exists in Maker Manager badges table with the given whmcs_addon_id.'
        ]
      ])
      ->requirePresence('whmcs_addon_id', 'create')
      ->notEmpty('whmcs_addon_id')
      ->add('whmcs_addon_id', [
        'numeric' => [
          'rule' => 'numeric',
          'message' => 'whmcs_addon_id relation must be an integer.'
        ]
      ])
      ->add('number', [
        'numeric' => [
          'rule' => 'numeric',
          'message' => 'Badge number can only include numbers.'
        ],
        'emptyOrUnique' => [
          'rule' => function($value, $context){
            if (empty($value)) { return true; }
            $count = $this->find()
              ->where(['number' => $value])
              ->count();
            if ($count > 0) { return false; }
            return true;
          },
          'message' => 'Badge number already assigned.'
        ]
      ])
      ->add('status', [
        'inList' => [
          'rule' => ['inList', ['unassigned', 'active', 'suspended']],
          'message' => 'Status must be one of unassigned, active or suspended.'
        ]
      ]);
  }
  
  public function afterSave(Event $event, EntityInterface $entity) {
    if ($entity->status == 'unassigned' && $entity->user_id != 0) {
      $query = $this->query();
      $query->update()
          ->set(['number' => null])
          ->where(['id' => $entity->id])
          ->execute();
    }
  }
}
