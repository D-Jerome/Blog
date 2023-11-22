
<?php

require 'app/bootstrap.php';

use Framework\Application;
use Framework\BaseManager;

$app = (new Application());
$datasource = $app->getDatasource();
require './commands/fill.php';