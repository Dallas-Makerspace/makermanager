<?php $this->assign('title', 'Assign Family Badge'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Assign Family Badge</h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p><?= __('Select a family member from the dropdown below or create a new family member to assign this badge to.') ?></p>
      <?= $this->Form->create(); ?>
        <fieldset style="margin-bottom:15px;">
          <?= $this->Form->select('user_id',
            $family_members,
            ['empty' => __('Select a Family Member'), 'required' => true]
          ); ?>
        </fieldset>
        <?= $this->Form->button(__('Assign Badge')); ?>
        <?= $this->Html->link(__('Create New Family Member'), [
          'controller' => 'Users',
          'action' => 'add',
          $badge->id
        ], [
          'class' => 'btn btn-warning'
        ]); ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>