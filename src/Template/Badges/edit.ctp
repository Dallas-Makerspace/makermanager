<?php $this->assign('title', 'Edit One Off Badge'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Edit One Off Badge</h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p><?= __('One off badges are not tied to any specific user in the system. These are intended to be used for employees, contractors and other people who need access to DMS, but don\'t otherwise hold a membership.') ?></p>
      <?= $this->Form->create($badge); ?>
        <fieldset>
          <?= $this->Form->input('description') ?>
          <?= $this->Form->input('number') ?>
        </fieldset>
        <?= $this->Form->button(__('Update Badge')); ?>
        <?php if ($badge->status != 'active'): ?>
          <?= $this->Html->link(__('Enable This Badge'), [
            'controller' => 'Badges',
            'action' => 'enable',
            $badge->id
          ], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
          <?= $this->Html->link(__('Disable This Badge'), [
            'controller' => 'Badges',
            'action' => 'suspend',
            $badge->id
          ], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        <hr/>
        <?= $this->Html->link(__('Delete This Badge'), [
          'controller' => 'Badges',
          'action' => 'delete',
          $badge->id
        ], ['class' => 'btn btn-warning']) ?>
      <?= $this->Form->end() ?>
    </div>
  </div>
</div>