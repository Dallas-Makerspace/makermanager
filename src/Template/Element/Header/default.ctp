<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <?= $this->Html->link(__('Maker Manager 3'), '/', ['class' => 'navbar-brand']); ?>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <?php
        $auth_user = $this->request->session()->read('Auth.User');
        $left_nav = [];
        $dropdown_nav = [];
        $admin_nav = [];
        $right_nav = [];

        if (!empty($auth_user)) {
          $right_nav[] = $this->Html->link(__('Logout'), [
            'controller' => 'Users',
            'action' => 'logout'
          ]);

          if (isset($auth_user['id'])) {
            $my_badge = $this->Html->link(__('My Badge'), [
              'controller' => 'Badges',
              'action' => 'users',
              'user_id' => $auth_user['id']
            ]);
          }

          // Primary users are directed to WHMCS for account changes while family members are directed
          // to the internal account editing page. All account badges can be managed by the primary
          // user, while family members are limited to their own badge controls.
          if (isset($auth_user['id'])) {
            if ($auth_user['is_primary']) {
              $left_nav[] = $this->Html->link(__('Billing Account'), [
                'controller' => 'Users',
                'action' => 'billing'
              ]);

              if (count($auth_user['badges']) > 0) {
                $dropdown_nav[] = $my_badge;

                foreach ($auth_user['children'] as $child) {
                  foreach ($auth_user['badges'] as $badge) {
                    if ($child->id == $badge->user_id) {
                      $dropdown_nav[] = $this->Html->link($child->first_name . ' ' . $child->last_name . __('\'s Badge'), [
                        'controller' => 'Badges',
                        'action' => 'users',
                        'user_id' => $child->id
                      ]);
                    }
                  }
                }

                foreach ($auth_user['badges'] as $badge) {
                  if ($badge->user_id != $auth_user['id']) {
                    if (empty($badge->user_id)) {
                      $dropdown_nav[] = $this->Html->link(__('Assign Family Badge'), [
                        'controller' => 'Badges',
                        'action' => 'assign',
                        $badge->id
                      ]);
                      // Only need to show the assign family badge once
                      break;
                    }
                  }
                }
              } else {
                $left_nav[] = $my_badge;
              }
            } else {
              $left_nav[] = $this->Html->link(__('Edit Account'), [
                'controller' => 'Users',
                'action' => 'edit',
                $auth_user['id']
              ]);
              $left_nav[] = $my_badge;
            }
          }

          // Admin users have access to badge management areas
          if (array_key_exists('is_admin', $auth_user) && $auth_user['is_admin']) {
            $admin_nav = [
              $this->Html->link(__('All Badges'), [
                'controller' => 'Badges',
                'action' => 'index'
              ]),
              $this->Html->link(__('Activity Log'), [
                'controller' => 'BadgeHistories',
                'action' => 'index'
              ]),
              $this->Html->link(__('Active Badges'), [
                'controller' => 'Badges',
                'action' => 'index',
                'active'
              ]),
              $this->Html->link(__('Suspended Badges'), [
                'controller' => 'Badges',
                'action' => 'index',
                'suspended'
              ]),
              $this->Html->link(__('Unassigned Badges'), [
                'controller' => 'Badges',
                'action' => 'index',
                'unassigned'
              ]),
              $this->Html->link(__('One Off Badges'), [
                'controller' => 'Badges',
                'action' => 'index',
                'oneoff'
              ]),
              $this->Html->link(__('Create One Off Badge'), [
                'controller' => 'Badges',
                'action' => 'add'
              ]),
              $this->Html->link(__('User List'), [
                'controller' => 'users',
                'action' => 'index'
              ]),
              $this->Html->link(__('Download Users CSV'), [
                'controller' => 'users',
                'action' => 'export'
              ])
            ];
          }
        } else {
          $right_nav[] = $this->Html->link(__('Login'), [
            'controller' => 'Users',
            'action' => 'login'
          ]);
        }
      ?>
      <ul class="nav navbar-nav">
        <?php foreach ($left_nav as $item): ?>
          <li><?= $item ?></li>
        <?php endforeach; ?>
        <?php if (!empty($admin_nav)): ?>
          <li class="dropdown">
            <?= $this->Html->link(__('Badge Admin') . ' <span class="caret"></span>', '#', [
              'class' => 'dropdown-toggle',
              'data-toggle' => 'dropdown',
              'role' => 'button',
              'aria-haspopup' => 'true',
              'aria-expanded' => 'false',
              'escape' => false
            ]) ?>
            <ul class="dropdown-menu">
              <?php foreach ($admin_nav as $item): ?>
                <li><?= $item ?></li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>
        <?php if (!empty($dropdown_nav)): ?>
          <li class="dropdown">
            <?= $this->Html->link(__('My Badges') . ' <span class="caret"></span>', '#', [
              'class' => 'dropdown-toggle',
              'data-toggle' => 'dropdown',
              'role' => 'button',
              'aria-haspopup' => 'true',
              'aria-expanded' => 'false',
              'escape' => false
            ]) ?>
            <ul class="dropdown-menu">
              <?php foreach ($dropdown_nav as $item): ?>
                <li><?= $item ?></li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <?php foreach ($right_nav as $item): ?>
          <li><?= $item ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>
