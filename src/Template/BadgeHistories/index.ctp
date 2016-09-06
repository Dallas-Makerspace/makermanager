<?php $this->assign('title', 'Activity Log'); ?>

<div class="col-xs-12">
  <div class="page-header">
    <h1><?= __('Activity Log') ?></h1>
  </div>
  
  <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row']) ?>
    <div class="col-xs-8 col-sm-7 col-md-5" style="padding-right:0;">
      <?= $this->Form->input('search', [
        'value' => (!empty($_GET['search']) ? $_GET['search'] : ''),
        'label' => false,
        'placeholder' => 'Search across fields (except name/email)'
      ]) ?>
    </div>
    <div class="col-xs-4 col-sm-5 col-md-7" style="padding-left:0;">
      <?= $this->Form->button(__('Search')); ?>
    </div>
  <?= $this->Form->end() ?>
  
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?= __('Name') ?></th>
        <th><?= __('Email') ?></th>
        <th><?= __('Changed By') ?></th>
        <th><?= __('Description') ?></th>
        <th><?= __('Badge') ?></th>
        <th><?= __('Changed To') ?></th>
        <th><?= __('Reason') ?></th>
        <th><?= $this->Paginator->sort('created', 'Date') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($badge_histories as $badge_history): ?>
        <tr>
          <td>
            <?php if (!empty($badge_history->badge->user)): ?>
              <?php $name = h($badge_history->badge->user->first_name . ' ' . $badge_history->badge->user->last_name) ?>
              <?php if (empty($badge_history->badge->user->user_id)): ?>
                <?= $this->Html->link($name, [
                  'controller' => 'Users',
                  'action' => 'view',
                  $badge_history->badge->user->id
                ]) ?>
              <?php else: ?>
                <?= $this->Html->link($name, [
                  'controller' => 'Users',
                  'action' => 'edit',
                  $badge_history->badge->user->id
                ]) ?>
              <?php endif; ?>
            <?php elseif ($badge_history->badge->whmcs_user_id != 0): ?>
              <?= __('Open Family Badge'); ?>
            <?php else: ?>
              <?= $this->Html->link(__('One Off Badge'), [
                'controller' => 'Badges',
                'action' => 'edit',
                $badge_history->badge->id
              ]) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($badge_history->badge->user)): ?>
              <?= h($badge_history->badge->user->email) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($badge_history->user)): ?>
              <?= h($badge_history->user->first_name) ?> <?= h($badge_history->user->last_name) ?>
            <?php else: ?>
              <?= __('System') ?>
            <?php endif; ?>
          </td>
          <td><?= h($badge_history->badge->description) ?></td>
          <td>
            <?php $badge_number = (!empty($badge_history->badge_number) ? h($badge_history->badge_number) : '[none]'); ?>
            <?php if (!empty($badge_history->badge->user)): ?>
              <?= $this->Html->link($badge_number, [
                'controller' => 'Badges',
                'action' => 'users',
                $badge_history->badge->user->id
              ]) ?>
            <?php else: ?>
              <?= $this->Html->link($badge_number, [
                'controller' => 'Badges',
                'action' => 'edit',
                $badge_history->badge->id
              ]) ?>
            <?php endif; ?>
          </td>
          <td><?= h($badge_history->changed_to) ?></td>
          <td><?= h($badge_history->reason) ?></td>
          <td><?= $this->Time->format(
            $badge_history->created,
            'yyyy-MM-dd HH:mm:ss',
            null,
            'America/Chicago'
          ) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?= $this->Paginator->numbers() ?>
</div>