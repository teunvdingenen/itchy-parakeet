<?php

#include $_SERVER['DOCUMENT_ROOT'].'/initialize.php';
#include $_SERVER['DOCUMENT_ROOT'].'/fields.php';
include '../initialize.php';
include '../fields.php';

try {
    $user_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(255) NOT NULL,
        `%s` varchar(255) NOT NULL default '',
        `%s` varchar(64) NOT NULL default '',
        `%s` int(8) NOT NULL default 0,
        PRIMARY KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Familiar Users';",
        $db_table_users,
        $db_user_username,
        $db_user_password,
        $db_user_name,
        $db_user_permissions,
        $db_user_username
    );

    $person_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(255) NOT NULL,
        `%s` varchar(255) NOT NULL default '',
        `%s` varchar(255) NOT NULL default '',
        `%s` date NOT NULL default 0,
        `%s` varchar(255) NOT NULL default '',
        `%s` varchar(8) NOT NULL default '',
        `%s` varchar(32) NOT NULL default '',
        `%s` int(8) NOT NULL default 0,
        `%s` varchar(255) NOT NULL default '',
        `%s` varchar(255) default NULL,
        `%s` int(8) NOT NULL default 0,
        `%s` int(8) NOT NULL default 0,
        `%s` varchar(8) NOT NULL default 0,
        `%s` varchar(8) NOT NULL default 0,
        `%s` varchar(8) NOT NULL default 0,
        `%s` date NOT NULL default 0,
        PRIMARY KEY (`%s`),
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
        $db_person_editions,
        $db_person_partner,
        $db_person_contrib0,
        $db_person_contrib1,
        $db_person_date,
        $db_person_terms0,
        $db_person_terms1,
        $db_person_terms2,
        $db_person_email,
        $db_person_partner
        );

    $contrib_sql = sprintf("CREATE TABLE `%s` (
        `%s` int(8) NOT NULL AUTO_INCREMENT,
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(255) NOT NULL default '',
        `%s` varchar(255) NOT NULL default '',
        PRIMARY KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contributions';",
        $db_table_contrib,
        $db_contrib_id,
        $db_contrib_type,
        $db_contrib_desc,
        $db_contrib_needs,
        $db_contrib_id );

    $raffle_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(16) NOT NULL,
        `%s` varchar(255) NOT NULL default '',
        `%s` BOOLEAN NOT NULL DEFAULT FALSE,
        PRIMARY KEY (`%s`),
        UNIQUE KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Raffle codes';",
        $db_table_raffle,
        $db_raffle_code,
        $db_raffle_email,
        $db_raffle_called,
        $db_raffle_code,
        $db_raffle_email );

    $buyer_sql = sprintf("CREATE TABLE `%s` (
        `%s` varchar(255) NOT NULL,
        `%s` varchar(16) NOT NULL default '',
        `%s` varchar(255) NOT NULL default '',
        `%s` BOOLEAN NOT NULL default FALSE,
        PRIMARY KEY (`%s`),
        UNIQUE KEY (`%s`),
        UNIQUE KEY (`%s`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bought tickets';",
        $db_table_buyer,
        $db_buyer_id,
        $db_buyer_raffle,
        $db_buyer_email,
        $db_buyer_complete,
        $db_buyer_id,
        $db_buyer_raffle,
        $db_buyer_email );

    $person_keys_sql_0 = sprintf("ALTER TABLE `%s`
        ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s);",
            $db_table_person,
            $db_fk_person_contrib0,
            $db_person_contrib0,
            $db_table_contrib,
            $db_contrib_id);
    $person_keys_sql_1 = sprintf("ALTER TABLE `%s`
        ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s);",
            $db_table_person,
            $db_fk_person_contrib1,
            $db_person_contrib1,
            $db_table_contrib,
            $db_contrib_id);
    $raffle_keys_sql = sprintf("ALTER TABLE `%s`
        ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s);",
            $db_table_raffle,
            $db_fk_raffle_person,
            $db_raffle_email,
            $db_table_person,
            $db_person_email);
    $buyer_keys_sql_0 = sprintf("ALTER TABLE `%s`
        ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s);",
            $db_table_buyer,
            $db_fk_buyer_person,
            $db_buyer_email,
            $db_table_person,
            $db_person_email );
    $buyer_keys_sql_1 = sprintf("ALTER TABLE `%s`
        ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s);",
            $db_table_buyer,
            $db_fk_buyer_raffle,
            $db_buyer_raffle,
            $db_table_raffle,
            $db_raffle_code );


    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
        create_tables($mysqli, $person_sql, $contrib_sql, $raffle_sql, $buyer_sql, $user_sql);
    }
    if( $mysqli->query($person_keys_sql_0)) {
        printf("Added person contraints (1 of 2).\n");
    } else {
        printf($mysqli->error . ") . \n" . $person_keys_sql_0."\n");
    }
    if( $mysqli->query($person_keys_sql_1)) {
        printf("Added person contraints (2 of2).\n");
    } else {
        printf($mysqli->error . ") . \n" . $person_keys_sql_1."\n");
    }
    if( $mysqli->query($raffle_keys_sql) ) {
        printf("Added raffle contraints.\n");
    } else {
        printf($mysqli->error . ") . \n" . $raffle_keys_sql."\n");
    }
    if( $mysqli->query($buyer_keys_sql_0) && $mysqli->query($buyer_keys_sql_1)) {
        printf("Added buyer contraints.\n");
    } else {
        printf($mysqli->error . ") . \n" . $buyer_keys_sql."\n");
    }

    //add root user
    $pw_hash = password_hash("password", PASSWORD_DEFAULT);
    $user_add_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`) VALUES ('%s', '%s','%s','%s')",
        $db_table_users,
        $db_user_username,
        $db_user_password,
        $db_user_name,
        $db_user_permissions,
        "root", $pw_hash, "root", 0xFFFF
        );
    if( $mysqli->query($user_add_query)) {
        printf("Added root user");
    } else {
        printf("Failed to add root user" . $mysqli->error);
    }

    $mysqli->close();
} catch (Exception $e) {
    echo "Caucht exception: ", $e->getMessage(), "\n";
}
    
function create_tables($mysqli, $person_sql, $contrib_sql, $raffle_sql, $buyer_sql, $user_sql) {
    if( $mysqli->query($user_sql) ) {
        printf("Created User Table.\n");
    } else {
        printf($mysqli->error . ") . \n" . "Failed to create User Table.\n");
    }
    if( $mysqli->query($person_sql) ) {
        printf("Created Person Table.\n");
    } else {
        printf($mysqli->error . ") . \n" . "Failed to create Person Table.\n");
    }
    if( $mysqli->query($contrib_sql) ) {
        printf("Created Contributions Table.\n");
    } else {
        printf($mysqli->error . ") . \n" . "Failed to create Contributions Table.\n");
    }
    if( $mysqli->query($raffle_sql) ) {
        printf("Created Raffle Table.\n");
    } else {
        printf($mysqli->error . ") . \n" . "Failed to create Raffle Table.\n");
    }
    if( $mysqli->query($buyer_sql) ) {
        printf("Created buyers Table.\n");
    } else {
        printf($mysqli->error . ") . \n" . "Failed to create Buyers Table.\n");
    }
}
?>

