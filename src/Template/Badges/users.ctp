<?php $this->assign('title', 'Badge Status'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Badge Status &raquo; <?= $user->first_name ?> <?= $user->last_name ?></h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <?php if(!empty($user->badge)): ?>
        <?php if ($user->badge->status == 'unassigned'): ?>
          <div class="alert alert-success" role="alert">
            <strong><?= __('Badge available.') ?></strong> <?= __('You have an available badge activation. Enter the badge number below to enable your access to the building.') ?>
          </div>
          <?= $this->Form->create($user->badge); ?>
            <fieldset>
              <?= $this->Form->input('number', [
                'help' => __('Note: If it\'s a card, it\'s only the first number on the left. (usually starts with a zero)')
              ]); ?>
            </fieldset>
            <?= $this->Form->button(__('Activate Badge')); ?>
          <?= $this->Form->end(); ?>
        <?php elseif ($user->badge->status == 'active'): ?>
          <strong><?= __('Badge Number:') ?></strong> <?= $user->badge->number ?>
          <p class="small"><?= ('Your badge is enabled and working without issue. Should your badge become lost, damaged or otherwise unable to be used then you can use the form below to disable your current badge. Once disabled, you can assign yourself a new badge.') ?></p>
          <hr/>
          <?= $this->Form->create($user->badge); ?>
            <fieldset style="margin-bottom:15px;">
              <?= $this->Form->select('disable',
                ['Lost' => __('Lost'), 'Damaged' => __('Damaged'), 'Other' => __('Other')],
                ['empty' => __('Select a Reason for Disabling'), 'required' => true]
              ); ?>
            </fieldset>
            <?= $this->Form->button(__('Disable Badge')); ?>
          <?= $this->Form->end(); ?>
        <?php else: ?>
          <div class="alert alert-warning" role="alert">
            <strong><?= __('Badge suspended.') ?></strong> <?= __('Your badge is currently suspended. This is most likely due to a suspended DMS membership or a missed payment. Once your account is unsuspended your badge will be automatically reenabled.') ?>
          </div>
        <?php endif; ?>
        <?php if ($user->badge->whmcs_addon_id != 0): ?>
          <hr/>
          <p><?= __('You can also edit this badge user\'s account information or revoke this badge, which unassigns it from this user and allows it to be reassigned to a different user under the same account.') ?></p>
          <?= $this->Html->link(__('Edit Family Member'), [
            'controller' => 'Users',
            'action' => 'edit',
            $user->id
          ], ['class' => 'btn btn-default']) ?>
          <?= $this->Html->link(__('Revoke Badge'), [
            'controller' => 'Badges',
            'action' => 'revoke',
            $user->badge->id
          ], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
      <?php else: ?>
        <div class="alert alert-warning" role="alert">
          <strong><?= __('No badge available.') ?></strong> <?= __('No badge assignments are available for your account.') ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>