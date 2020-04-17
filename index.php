<?php
require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;

$launch = LTIX::requireData();
$app = new \Tsugi\Silex\Application($launch);

if (file_exists('config.cfg')) {
    $app['config'] = parse_ini_file("config.cfg");
} else {
    $app['config'] = parse_ini_file("config-dist.cfg");
}

$app->get('/', 'AppBundle\\Home::getPage')->bind('main');
$app->get('info', 'AppBundle\\Home::getInfo');
$app->get('static/{file}', 'AppBundle\\Home::getFile')->assert('file', '.+');

$app->run();
