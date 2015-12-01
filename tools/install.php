<?php

include "../fields.php";

try {
    $person_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(256) NOT NULL,
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(8) NOT NULL default '',
        `%s` varchar(16) NOT NULL default '',
        `%s` int(8) NOT NULL default 0,
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(16) NOT NULL default '',
        `%s` tinyint(1) NOT NULL default 0,
        `%s` tinyint(1) NOT NULL default 0,
        `%s` tinyint(1) NOT NULL default 0,
        PRIMARY KEY (`%s`),
        UNIQUE KEY (`%s`),
        UNIQUE KEY (`%s`),
        UNIQUE KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Personal information';",
        $db_table_person,
        $db_person_email,
        $db_person_first,
        $db_person_last,
        $db_person_birth,
        $db_person_city,
        $db_person_gender,
        $db_person_phone,
        $db_person_visits,
        $db_person_prev,
        $db_person_partner,
        $db_person_contrib0,
        $db_person_contrib1,
        $db_person_terms0,
        $db_person_terms1,
        $db_person_terms2,
        $db_person_email,
        $db_person_partner
        $db_person_contrib0,
        $db_person_contrib1 );

    $contrib_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(16) NOT NULL,
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        PRIMARY KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contributions';",
        $db_table_contrib,
        $db_contrib_id,
        $db_contrib_type,
        $db_contrib_discr,
        $db_contrib_needs,
        $db_contrib_id );

    $raffle_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(16) NOT NULL,
        `%s` varchar(256) NOT NULL default '',
        PRIMARY KEY (`%s`),
        UNIQUE KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Raffle codes';",
        $db_table_raffle,
        $db_raffle_code,
        $db_person,
        $db_raffle_code,
        $db_person );

    $buyer_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(256) NOT NULL,
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(256) NOT NULL default '',
        PRIMARY KEY (`%s`),
        UNIQUE KEY (`%s`),
        UNIQUE KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bought tickets';",
        $db_table_buyer,
        $db_buyer_id,
        $db_buyer_raffle,
        $db_buyer_person,
        $db_buyer_id,
        $db_buyer_raffle,
        $db_buyer_person );

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db);
    if( $mysqli->connect_errno ) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
        :

?>

