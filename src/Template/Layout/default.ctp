<?php

use Cake\Core\Configure;

// Prepend top level tags and assets to their respective tag blocks
$this->prepend('meta', $this->Html->meta('favicon.png', '/favicon.png', ['type' => 'icon']));
$this->prepend('css', $this->Html->css(['bootstrap/bootstrap']));
$this->append('css', $this->Html->css(['app']));
$this->prepend('script', $this->Html->script(['jquery/jquery', 'bootstrap/bootstrap']));

// Bootstrap 3 Shims for IE < IE9
$html5Shim =
<<<HTML

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

HTML;
$this->append('css', $html5Shim);

// Generic body class generator
$this->prepend('body_attrs', ' class="' . strtolower(implode(' ', [$this->request->controller, $this->request->action])) . '" ');

?>
<!DOCTYPE html>
<?php printf('<html lang="%s" class="no-js">', Configure::read('App.htmlLang')); ?>
  <head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?> | <?= __('Maker Manager 3') ?></title>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
  </head>

  <body <?= $this->fetch('body_attrs') ?>>
    <?= $this->element('Header/default') ?>
    
    <div class="container">
      <?= $this->fetch('content') ?>
    </div>

    <?= $this->fetch('script') ?>
<?= $this->element('Static/mascot') ?>
  </body>
</html>
