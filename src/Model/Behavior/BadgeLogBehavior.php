<?php
namespace App\Model\Behavior;

use App\Model\Entity\BadgeHistory;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;

class BadgeLogBehavior extends Behavior {
  public function beforeSave(Event $event, EntityInterface $entity) {
    if ($entity->status != $entity->getOriginal('status') || $entity->number != $entity->getOriginal('number')) {
      $history = new BadgeHistory([
        'badge_number' => $entity->number,
        'changed_to' => $entity->status,
        'reason' => $entity->reason
      ]);
      $entity->badge_histories = [$history];
    }
  }
}
