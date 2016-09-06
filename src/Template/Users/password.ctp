<?php $this->assign('title', 'Change Password'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Change Password &raquo; <?= $user->first_name ?> <?= $user->last_name ?></h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <?= $this->Form->create($user) ?>
        <fieldset>
          <?= $this->Form->input('password') ?>
        </fieldset>
        <?= $this->Form->button(__('Update Password')) ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>