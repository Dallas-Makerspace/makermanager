<?php $this->assign('title', 'Add One Off Badge'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Add One Off Badge</h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <p><?= __('One off badges are not tied to any specific user in the system. These are intended to be used for employees, contractors and other people who need access to DMS, but don\'t otherwise hold a membership.') ?></p>
      <?= $this->Form->create($badge); ?>
        <fieldset>
          <?= $this->Form->input('description') ?>
        </fieldset>
        <p class="small"><?= __('You\'ll be able to assign a badge number for this badge in the following step.') ?></p>
        <?= $this->Form->button(__('Create One Off Badge')); ?>
      <?= $this->Form->end() ?>
    </div>
  </div>
</div>