<?php
require_once "../../config.php";
require_once("../dao/CourseDAO.php");

include '../tool-config.php';

use \Tsugi\Core\LTIX;
use \Course\DAO\CourseDAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$real_weeks = isset($tool['api']['real_weeks']) ? ($tool['api']['real_weeks'] == '1') : false;
$real_weeks = ($LAUNCH->ltiRawParameter('custom_real_week_no','false') == "true") | $real_weeks;

$course_obj = new CourseDAO($PDOX, $p, $tool['api']['url'], $tool['api']['username'], $tool['api']['password'], $real_weeks);

// $data = $course_obj->getJSON($LAUNCH->ltiRawParameter('context_id','none')), true);

// Testing: 
// $data = $course_obj->getJSON('2dda0bd3-9100-4034-a404-ff0e34b1887c', true); // MAM1000W (2020)
// $data = $course_obj->getJSON('996b25c5-9d5f-4dba-9c7a-507e4862c578', true); // loadtest 2012
// $data = $course_obj->getJSON('ac4e7899-7600-4516-a6c2-702513cb0230', true); // ISFAP Students
$data = $course_obj->getJSON('0cd090cc-77f1-47ba-b342-0f79c328114e', true); // POL3038S (2020)

if ($data['success'] == 1) {
    $now = new DateTime();
    $now->setTimezone(new DateTimeZone('Africa/Johannesburg'));


    header('Cache-Control: private');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Analytics '. $data['title'] . ' '. $now->format('Y-m-d_H-i'). '.csv"');

    // use Keys For Header Row
    array_unshift($data['result'], array_keys(reset($data['result'])));
    
    $outputBuffer = fopen("php://output", 'w');
    foreach($data['result'] as $v) {
        fputcsv($outputBuffer, $v);
    }
    fclose($outputBuffer);
} else {
    
    $config = array();
    $config['styles']  = [ 'static/user.css' ];

    // Start of the output
    $OUTPUT->header();

    Template::view('templates/header.html', $config);

    $OUTPUT->bodyStart();

    $OUTPUT->topNav($menu);

    Template::view('templates/error.html', $config);

    $OUTPUT->footerStart();
    $OUTPUT->footerEnd();
}

exit;