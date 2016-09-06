<?php $this->assign('title', 'Create New Family Member'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Create New Family Member</h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p><?= __('Complete all the fields below to create a new family member account. The new account will automatically be assigned a badge.') ?></p>
      <?= $this->Form->create($user); ?>
        <fieldset>
          <?= $this->Form->input('first_name') ?>
          <?= $this->Form->input('last_name') ?>
          <?= $this->Form->input('phone') ?>
          <?= $this->Form->input('email') ?>
          <?= $this->Form->input('username', [
            'help' => __('Must be all lowercase. Automatically converted to all lowercase if capitals are used. This username is for access to Maker Manager, the wiki, virtual machines and more.')
          ]) ?>
          <?= $this->Form->input('password', [
            'required' => true,
            'minlength' => 6,
            'help' => __('A minimum of 6 characters are required.')
          ]) ?>
        </fieldset>
        <?= $this->Form->button(__('Create Account')); ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>