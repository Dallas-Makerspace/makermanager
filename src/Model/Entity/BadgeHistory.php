<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class BadgeHistory extends Entity {
  protected $_accessible = [
    '*' => true,
    'id' => false
  ];
}
