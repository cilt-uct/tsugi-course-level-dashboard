<?php

# The configuration file for this tool - copy to tool-config.php
$tool = array(
    "debug" => false,
    "api" => array(
        "production" => true,
        "url"        => "https://api.server.com/v.1.0/",
        "username"   => "username",
        "password"   => "password",
        "real_weeks" => false
    ),
    "config" => basename(__FILE__, '.php')
);

if (file_exists("tool-config.php") && basename(__FILE__, '.php') != "tool-config") {
    include 'tool-config.php';
}
