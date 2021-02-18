<?php

$REGISTER_LTI2 = array(
    "name" => "Course Level Dashboard"
    ,"FontAwesome" => "fa-chart-bar"
    ,"short_name" => "Course Level Dashboard"
    ,"description" => "Shows the Course Level Dashboard - limited to 2020"
    ,"messages" => array("launch") // By default, accept launch messages..
    ,"privacy_level" => "public" // anonymous, name_only, public
    ,"license" => "Apache"
    ,"languages" => array(
        "English",
    )
    ,"source_url" => "https://github.com/cilt-uct/tsugi-course-level-dashoard"
    // For now Tsugi tools delegate this to /lti/store
    ,"placements" => array(
        /*
        "course_navigation", "homework_submission",
        "course_home_submission", "editor_button",
        "link_selection", "migration_selection", "resource_selection",
        "tool_configuration", "user_navigation"
        */
    )
    ,"screen_shots" => array(
        /* no screenshots */
    )
);
