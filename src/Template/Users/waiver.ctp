<?php $this->assign('title', 'Waiver'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Waiver &raquo; <?= $user->first_name ?> <?= $user->last_name ?></h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p>We were unable to find a waiver that matched the name and email address for this account. If you used a different email address when filling out the waiver then enter it below and we'll attempt to look up your waiver in the system.</p>
      <?= $this->Form->create(); ?>
        <fieldset>
          <?= $this->Form->input('email', [
            'type' => 'email',
            'required' => true
          ]); ?>
        </fieldset>
        <?= $this->Form->button(__('Look Up Waiver')); ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>