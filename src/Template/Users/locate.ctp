<?php $this->assign('title', 'WHMCS User Import'); ?>

<div class="col-sm-12 col-sm-offset-0 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">WHMCS User Import</h3>
    </div>
    <div class="panel-body">
      <?= $this->Flash->render() ?>
      
      <?php if (count($results) > 0): ?>
        <h4>Search Results</h4>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $result): ?>
              <tr>
                <td><?= $result['firstname'] ?></td>
                <td><?= $result['lastname'] ?></td>
                <td><?= $result['email'] ?></td>
                <td><?= $this->Html->link(__('Import'), [
                  'controller' => 'Users',
                  'action' => 'migrate',
                  $result['id']
                ]) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <hr/>
      <?php endif; ?>
      
      <p>Search WHMCS Users. Results shown in the next step for import selection.</p>
      <?= $this->Form->create() ?>
        <fieldset>
          <?= $this->Form->input('first_name', ['required' => true]) ?>
          <?= $this->Form->input('last_name', ['required' => true]) ?>
        </fieldset>
        <?= $this->Form->button(__('Search WHMCS')) ?>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>