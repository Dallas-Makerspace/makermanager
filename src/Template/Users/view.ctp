<?php $this->assign('title', 'View Account Details'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><?= __('View Account Details &raquo;') ?> <?= $user->first_name ?> <?= $user->last_name ?></h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      <div class="row">
        <div class="col-sm-6">
          <ul class="list-unstyled">
            <li><strong><?= __('Email:') ?></strong> <?= h($user->email) ?></li>
            <li><strong><?= __('Username:') ?></strong> <?= h($user->username) ?></li>
            <li><strong><?= __('Phone:') ?></strong> <?= h($user->phone) ?></li>
            <li><strong><?= __('Created:') ?></strong>
              <?= $this->Time->format(
                $user->created,
                'yyyy-MM-dd HH:mm:ss',
                null,
                'America/Chicago'
              ) ?>
            </li>
            <li><strong><?= __('Updated:') ?></strong>
              <?= $this->Time->format(
                $user->modified,
                'yyyy-MM-dd HH:mm:ss',
                null,
                'America/Chicago'
              ) ?>
            </li>
          </ul>
        </div>
        <div class="col-sm-6">
          <ul class="list-unstyled">
            <li><strong><?= __('Address 1:') ?></strong> <?= h($user->address_1) ?></li>
            <li><strong><?= __('Address 2:') ?></strong> <?= h($user->address_2) ?></li>
            <li><strong><?= __('City:') ?></strong> <?= h($user->city) ?></li>
            <li><strong><?= __('State:') ?></strong> <?= h($user->state) ?></li>
            <li><strong><?= __('Zip:') ?></strong> <?= h($user->zip) ?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><?= __('Account Badges'); ?></h3>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?= __('Badge') ?></th>
          <th><?= __('Assigned To') ?></th>
          <th><?= __('Status') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($associated_badges as $badge): ?>
          <tr>
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
                  'action' => 'assign',
                  $badge->id
                ]) ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($badge->user)): ?>
                <?= h($badge->user->first_name) ?> <?= h($badge->user->last_name) ?>
              <?php else: ?>
                <?= $this->Html->link(__('[no one]'), [
                  'controller' => 'Badges',
                  'action' => 'assign',
                  $badge->id
                ]) ?>
              <?php endif; ?>
            </td>
            <td><?= h($badge->status) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><?= __('Family Members'); ?></h3>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?= __('Name') ?></th>
          <th><?= __('Username') ?></th>
          <th><?= __('Active') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($family_accounts as $account): ?>
          <tr>
            <td>
              <?= $this->Html->link(h($account->first_name) . ' ' . $account->last_name, [
                'controller' => 'Users',
                'action' => 'edit',
                $account->id
              ]) ?>
            </td>
            <td><?= h($account->username) ?></td>
            <td><?= ($account->ad_active == 1 ? __('Yes') : __('No')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <div class="text-center">
    <p>Is account data missing for this user?</p>
    <?= $this->Html->link(__('Synchronize with WHMCS'), [
      'controller' => 'Users',
      'action' => 'sync',
      $user->id
    ], ['class' => 'btn btn-primary']) ?>
  </div>
</div>