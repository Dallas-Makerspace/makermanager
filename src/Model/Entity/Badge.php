<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

class Badge extends Entity {
  protected $_accessible = [
    '*' => true,
    'id' => false
  ];

  public function enable($reason = null) {
    if (!empty($this->number)) {
      $this->status = 'active';
    } else {
      $this->status = 'unassigned';
    }
    
    $this->reason = $reason;
  }

  public function suspend($reason = null) {
    $this->status = 'suspended';
    $this->reason = $reason;
  }

  public function disable($reason = null) {
    $this->status = 'unassigned';
    $this->reason = $reason;
  }
}
