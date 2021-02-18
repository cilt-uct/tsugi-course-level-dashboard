<?php
require_once('../config.php');
include 'tool-config-dist.php';
include 'src/Template.php';

use \Tsugi\Util\U;
use \Tsugi\Core\Cache;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\UI\SettingsForm;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$menu = false;

$config = [
    'instructor' => $USER->instructor, 
    'styles'     => [ 'static/tooltipster.bundle.min.css', 'static/user.css' ],
    'scripts'    => [ $CFG->staticroot .'/js/moment.min.js', 'static/tooltipster.bundle.min.js' ],
    'getUrl'     => addSession('actions/GetInfo.php'),
    'tool' => $tool
];

$provider = $LAUNCH->ltiRawParameter('lis_course_offering_sourcedid','none') != $LAUNCH->ltiRawParameter('context_id','');
$provider_st = $LAUNCH->ltiRawParameter('lis_course_offering_sourcedid','none');

$has_provider = ($provider && (preg_match_all('/[A-Z]{3}[\d]{4}[A-Z]?,20[\d]{2}/i', $provider_st, $matches) > 0));
$force_download = $LAUNCH->ltiRawParameter('custom_download','none') == "enable";
$force_download = true; // test

if ($force_download) {
    $config['downloadUrl'] = addSession('actions/GetCSV.php');
} else {
    $context['downloadUrl'] = ($provider && (preg_match_all('/[A-Z]{3}[\d]{4}[A-Z]?,20[\d]{2}/i', $provider_st, $matches) > 0)) ? addSession('actions/GetCSV.php') : '';
}

$config['result'] = array( 
    'siteid' => $LAUNCH->ltiRawParameter('context_id','none')
    ,'download' => $force_download ? 1 : 0
    ,'has_provider' => $has_provider ? 1 : 0
);

// Start of the output
$OUTPUT->header();

Template::view('templates/header.html', $config);

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

if ($tool['debug']) {
    echo '<pre>'; print_r($config); echo '</pre>';
}

Template::view('templates/body.html', $config);

$OUTPUT->footerStart();

Template::view('templates/footer.html', $config);
include('templates/tmpl.html');

$OUTPUT->footerEnd();
