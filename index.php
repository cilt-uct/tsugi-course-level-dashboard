<?php
require_once "../config.php";
include 'tool-config.php';

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;

$launch = LTIX::requireData();
$app = new \Tsugi\Silex\Application($launch);

$app['debug'] = $tool['debug'];
$app['script-add'] = $tool['script-add'];
$app['script-remove'] = $tool['script-remove'];

$app->get('/', 'AppBundle\\Home::get')->bind('main');
$app->post('/', 'AppBundle\\Home::post');

$app->run();
