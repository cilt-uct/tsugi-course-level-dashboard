<?php
require_once('../config.php');

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Settings;

$launch = LTIX::requireData();

if ( $USER->instructor ) {
    header( 'Location: '.addSession('instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('student-home.php') ) ;
}
