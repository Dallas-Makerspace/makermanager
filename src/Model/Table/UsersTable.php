<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table {
  public function initialize(array $config) {
    $this->addBehavior('Timestamp');
    $this->hasOne('Badges');
    $this->hasOne('Parent', [
      'className' => 'Users'
    ]);
    $this->hasMany('BadgeHistories', [
      'foreignKey' => 'changed_by'
    ]);
    $this->hasMany('Children', [
      'className' => 'Users',
      'dependent' => true
    ]);
  }

  public function validationDefault(Validator $validator) {
    return $validator
      ->requirePresence('first_name', 'create')
      ->notEmpty('first_name')
      ->requirePresence('last_name', 'create')
      ->notEmpty('last_name')
      ->requirePresence('username', 'create')
      ->notEmpty('username')
      ->add('username', [
        'validUsername' => [
          'rule' => function($value, $context) {
            $validExtras = array('-', '_'); 
            if(!ctype_alnum(str_replace($validExtras, '', $value))) { 
              return false;
            }
            return true;
          },
          'message' => 'Username can contain only lowercase letters, numbers, underscores and dashes.'
        ],
        'unique' => [
          'rule' => 'validateUnique',
          'provider' => 'table'
        ]
      ])
      ->requirePresence('email', 'create')
      ->notEmpty('email')
      ->add('email', [
        'email' => [
          'rule' => 'email',
          'message' => 'Valid email address required.'
        ]
      ])
      ->requirePresence('phone', 'create')
      ->notEmpty('phone')
      ->requirePresence('address_1', 'create')
      ->notEmpty('address_1')
      ->requirePresence('city', 'create')
      ->notEmpty('city')
      ->requirePresence('state', 'create')
      ->notEmpty('state')
      ->requirePresence('zip', 'create')
      ->notEmpty('zip')
      ->requirePresence('whmcs_user_id', 'create')
      ->notEmpty('whmcs_user_id')
      ->add('whmcs_user_id', [
        'numeric' => [
          'rule' => 'numeric',
          'message' => 'whmcs_user_id relation must be an integer.'
        ]
      ]);
  }
}
