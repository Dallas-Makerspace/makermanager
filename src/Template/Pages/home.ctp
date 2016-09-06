<?php $this->assign('title', 'Welcome'); ?>

<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
  <?= $this->Flash->render() ?>
  
  <?php $auth_user = $this->request->session()->read('Auth.User'); ?>
  <?php if (!isset($auth_user['id'])): ?>
    <div class="alert alert-warning" role="alert">
      <strong><?= __('Heads up!') ?></strong> <?= __('Your account was recognized by Active Directory, but no data pertaining to your account was found in Maker Manager. We\'ve gone ahead and logged you in with limited access based on your Active Directory permissions.') ?>
    </div>
  <?php endif; ?>
  
  <?= $this->element('Static/intro') ?>
</div>