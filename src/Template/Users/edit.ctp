<?php $this->assign('title', 'Edit Account'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Edit Account &raquo; <?= $user->first_name ?> <?= $user->last_name ?></h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p><?= __('Need to update your password?') ?> <?= $this->Html->link(__('Update password here.'), [
        'controller' => 'Users',
        'action' => 'password',
        $user->id
      ]) ?></p>
      <p><strong><?= __('Username:') ?></strong> <?= $user->username ?></p>
      <?= $this->Form->create($user) ?>
        <fieldset>
          <?= $this->Form->input('first_name') ?>
          <?= $this->Form->input('last_name') ?>
          <?= $this->Form->input('phone') ?>
          <?= $this->Form->input('email') ?>
          <?= $this->Form->input('address_1') ?>
          <?= $this->Form->input('address_2') ?>
          <?= $this->Form->input('city') ?>
          <?= $this->Form->input('state') ?>
          <?= $this->Form->input('zip') ?>
        </fieldset>
        <?= $this->Form->button(__('Update Account')) ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>