<?php
require_once "../../config.php";
require_once("../dao/CourseDAO.php");

include '../tool-config.php';

use \Tsugi\Core\LTIX;
use \Course\DAO\CourseDAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

header('Content-Type: application/json');
ini_set('max_execution_time', '3600');

$real_weeks = isset($tool['api']['real_weeks']) ? ($tool['api']['real_weeks'] == '1') : false;
$real_weeks = ($LAUNCH->ltiRawParameter('custom_real_week_no','false') == "true") | $real_weeks;

$course_obj = new CourseDAO($PDOX, $p, $tool['api']['url'], $tool['api']['username'], $tool['api']['password'], $real_weeks);
$SITE_ID = $LAUNCH->ltiRawParameter('context_id','none');

// Testing: 
// $SITE_ID = '2dda0bd3-9100-4034-a404-ff0e34b1887c'; // MAM1000W (2020)
// $SITE_ID = '1f718456-7261-43b4-8e40-1dfbf8bdce23'; // PACA Orientation
// $SITE_ID = '4f6abcc6-84f1-4c5c-9df2-08712ea669df'; // CILT LT Team - Dev
// $SITE_ID = '996b25c5-9d5f-4dba-9c7a-507e4862c578'; // loadtest 2012
// $SITE_ID = 'a30edd67-9678-45cd-92de-7559c7e6a944'; // Sociology Courses
// $SITE_ID = 'ac4e7899-7600-4516-a6c2-702513cb0230'; // ISFAP Students
// $SITE_ID = '0cd090cc-77f1-47ba-b342-0f79c328114e'; // POL3038S (2020)

echo json_encode($course_obj->getJSON($SITE_ID));
exit;