
<?php


require('app/bootstrap.php');

use Framework\Application;
use Framework\BaseManager;

$app = (new Application());
var_dump($app);
echo "_____________________________________________________";
$datasource = $app->getDatasource();
var_dump($datasource);
require ('./commands/fill.php');
