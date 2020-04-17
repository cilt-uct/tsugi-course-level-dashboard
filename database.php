<?php

// // The SQL to uninstall this tool
// $DATABASE_UNINSTALL = array(
// "drop table if exists {$CFG->dbprefix}Orientation_Questions"
// );

// // The SQL to create the tables if they don't exist
// $DATABASE_INSTALL = array(
// array( "{$CFG->dbprefix}Orientation_Questions",
// "create table {$CFG->dbprefix}Orientation_Questions (
//     `link_id` int(11) NOT NULL,
//     `user_id` int(11) NOT NULL,
//     `EID` varchar(45) NOT NULL,
//     `answer` tinyint(1) NOT NULL,
//     `ipaddr` varchar(64) DEFAULT NULL,
//     `created` datetime DEFAULT CURRENT_TIMESTAMP,
//     `updated` datetime DEFAULT CURRENT_TIMESTAMP,
//     PRIMARY KEY (`EID`),
//     KEY `IDX_ANSWER` (`answer`)
// ) ENGINE = InnoDB DEFAULT CHARSET=utf8")
// );
