
<?php

require 'app/bootstrap.php';

use Framework\Application;
use Framework\BaseManager;
use Framework\Config;

$app = (new Application());
$datasource = Config::getDatasource();
require './commands/fill.php';
