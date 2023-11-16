
<?php
require 'app/bootstrap.php';
define('DEBUG_TIME', microtime(true));

use Framework\Application;

(new Application())->run();
