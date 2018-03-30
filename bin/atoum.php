  <?php
  //.atoum.php

  use mageekguy\atoum;
  use mageekguy\atoum\reports;

  $coveralls = new reports\asynchronous\coveralls('src', 'myCoverallsProjectToken');
  $coveralls->addWriter();
  $runner->addReport($coveralls);

  $script->addDefaultReport();
  ?>
