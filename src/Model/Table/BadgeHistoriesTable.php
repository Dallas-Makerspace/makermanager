<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class BadgeHistoriesTable extends Table {
  public function initialize(array $config) {
    $this->addBehavior('Ceeram/Blame.Blame');
    $this->addBehavior('Timestamp');
    $this->belongsTo('Users', [
      'foreignKey' => 'modified_by'
    ]);
    $this->belongsTo('Badges');
  }

  public function validationDefault(Validator $validator) {
    return $validator;
  }
}
