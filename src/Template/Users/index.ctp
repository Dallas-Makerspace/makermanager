<?php $this->assign('title', 'User List'); ?>

<div class="col-xs-12">
  <div class="page-header">
    <h1><?= __('User List') ?></h1>
  </div>
  
  <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row']) ?>
    <div class="col-xs-8 col-sm-7 col-md-5" style="padding-right:0;">
      <?= $this->Form->input('search', [
        'value' => (!empty($_GET['search']) ? $_GET['search'] : ''),
        'label' => false,
        'placeholder' => 'Search across first name, last name, username and email'
      ]) ?>
    </div>
    <div class="col-xs-4 col-sm-5 col-md-7" style="padding-left:0;">
      <?= $this->Form->button(__('Search')); ?>
      
      <div class="pull-right">
        <?= $this->Html->link(__('Import from WHMCS'), [
          'controller' => 'Users',
          'action' => 'locate'
        ], ['class' => 'btn btn-primary']) ?>
      </div>
    </div>
  <?= $this->Form->end() ?>
  
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?= $this->Paginator->sort('last_name', 'Name') ?></th>
        <th><?= $this->Paginator->sort('username', 'Username') ?></th>
        <th><?= $this->Paginator->sort('email', 'Email') ?></th>
        <th><?= __('Badge') ?></th>
        <th><?= __('Account') ?></th>
        <th><?= $this->Paginator->sort('ad_active', 'Active') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <?php $name = h($user->first_name . ' ' . $user->last_name) ?>
            <?php if (empty($user->user_id)): ?>
              <?= $this->Html->link($name, [
                'controller' => 'Users',
                'action' => 'view',
                $user->id
              ]) ?>
            <?php else: ?>
              <?= $this->Html->link($name, [
                'controller' => 'Users',
                'action' => 'edit',
                $user->id
              ]) ?>
            <?php endif; ?>
          </td>
          <td><?= h($user->username) ?></td>
          <td><?= h($user->email) ?></td>
          <td>
            <?php $badge_number = (!empty($user->badge->number) ? h($user->badge->number) : '[none]'); ?>
            <?= $this->Html->link($badge_number, [
              'controller' => 'Badges',
              'action' => 'users',
              $user->id
            ]) ?>
          </td>
          <td>
            <?php if (empty($user->user_id)): ?>
              <?= __('Primary') ?>
            <?php else: ?>
              <?= __('Family') ?>
            <?php endif; ?>
          </td>
          <td>
            <?= ($user->ad_active == 1 ? __('Yes') : __('No')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?= $this->Paginator->numbers() ?>
</div>