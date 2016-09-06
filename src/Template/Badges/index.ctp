<?php $this->assign('title', 'Badge Management'); ?>

<div class="col-xs-12">
  <div class="page-header">
    <h1><?= h($type) ?> <?= __('Badges') ?></h1>
  </div>
  
  <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row']) ?>
    <div class="col-xs-8 col-sm-7 col-md-5" style="padding-right:0;">
      <?= $this->Form->input('search', [
        'value' => (!empty($_GET['search']) ? $_GET['search'] : ''),
        'label' => false,
        'placeholder' => 'Search across fields'
      ]) ?>
    </div>
    <div class="col-xs-4 col-sm-5 col-md-7" style="padding-left:0;">
      <?= $this->Form->button(__('Search')); ?>
    </div>
  <?= $this->Form->end() ?>
  
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?= $this->Paginator->sort('Users.last_name', 'Name') ?></th>
        <th><?= $this->Paginator->sort('Users.username', 'Username') ?></th>
        <th><?= $this->Paginator->sort('Users.email', 'Email') ?></th>
        <th><?= $this->Paginator->sort('description', 'Description') ?></th>
        <th><?= $this->Paginator->sort('number', 'Badge') ?></th>
        <th><?= $this->Paginator->sort('status', 'Status') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($badges as $badge): ?>
        <tr>
          <td>
            <?php if (!empty($badge->user)): ?>
              <?php $name = h($badge->user->first_name . ' ' . $badge->user->last_name) ?>
              <?php if (empty($badge->user->user_id)): ?>
                <?= $this->Html->link($name, [
                  'controller' => 'Users',
                  'action' => 'view',
                  $badge->user->id
                ]) ?>
              <?php else: ?>
                <?= $this->Html->link($name, [
                  'controller' => 'Users',
                  'action' => 'edit',
                  $badge->user->id
                ]) ?>
              <?php endif; ?>
            <?php else: ?>
              <?= $this->Html->link(__('One Off Badge'), [
                'controller' => 'Badges',
                'action' => 'edit',
                $badge->id
              ]) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($badge->user)): ?>
              <?= h($badge->user->username) ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($badge->user)): ?>
              <?= h($badge->user->email) ?>
            <?php endif; ?>
          </td>
          <td><?= h($badge->description) ?></td>
          <td>
            <?php $badge_number = (!empty($badge->number) ? h($badge->number) : '[none]'); ?>
            <?php if (!empty($badge->user)): ?>
              <?= $this->Html->link($badge_number, [
                'controller' => 'Badges',
                'action' => 'users',
                $badge->user->id
              ]) ?>
            <?php else: ?>
              <?= $this->Html->link($badge_number, [
                'controller' => 'Badges',
                'action' => 'edit',
                $badge->id
              ]) ?>
            <?php endif; ?>
          </td>
          <td><?= h($badge->status) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?= $this->Paginator->numbers() ?>
</div>