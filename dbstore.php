<?php
include_once 'sendmail.php';
include_once 'initialize.php';
include_once 'fields.php';

function storeSignup($email, $first, $last, $birth, $city, $gender, $phone, $nr_visits, $editions, $partner, $motivation, $familiar, $contrib0, $contrib1, $contrib0_desc, $contrib1_desc, $contrib0_need, $contrib1_need, $preparations, $terms0, $terms1, $terms2, $terms3, $signupround) {
    global $db_person_email, $db_person_first, $db_person_last, $db_person_birth, $db_person_city, $db_person_gender, $db_person_phone, $db_person_visits, $db_person_editions, $db_person_partner, $db_person_motivation, $db_person_familiar, $db_person_contrib0, $db_person_contrib1, $db_contrib_id, $db_contrib_type, $db_contrib_desc, $db_contrib_needs, $db_person_preparations, $db_person_terms0, $db_person_terms1, $db_person_terms2, $db_person_terms3, $db_person_date, $db_person_round;
    global $db_table_person, $db_table_contrib;
    global $db_host, $db_user, $db_pass, $db_name;
    global $mailtolink;
	$returnVal = "";

	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
        $returnVal .= "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ";
    }

    $contrib0_insert_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`) VALUES ('%s', '%s', '%s')",
    	$db_table_contrib,
    	$db_contrib_type,
        $db_contrib_desc,
        $db_contrib_needs,
        $mysqli->real_escape_string($contrib0),
        $mysqli->real_escape_string($contrib0_desc),
        $mysqli->real_escape_string($contrib0_need));
    $contrib1_insert_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`) VALUES ('%s', '%s', '%s')",
    	$db_table_contrib,
    	$db_contrib_type,
        $db_contrib_desc,
        $db_contrib_needs,
		$mysqli->real_escape_string($contrib1),
        $mysqli->real_escape_string($contrib1_desc),
        $mysqli->real_escape_string($contrib1_need));

    $contrib0_select_query = sprintf("SELECT `%s` FROM `%s` WHERE `%s` = '%s' AND `%s` = '%s' AND `%s` = '%s'",
        $db_contrib_id,
        $db_table_contrib,
        $db_contrib_type, $mysqli->real_escape_string($contrib0),
        $db_contrib_desc, $mysqli->real_escape_string($contrib0_desc),
        $db_contrib_needs, $mysqli->real_escape_string($contrib0_need));

    $contrib1_select_query = sprintf("SELECT `%s` FROM `%s` WHERE `%s` = '%s' AND `%s` = '%s' AND `%s` = '%s'",
        $db_contrib_id,
        $db_table_contrib,
        $db_contrib_type, $mysqli->real_escape_string($contrib1),
        $db_contrib_desc, $mysqli->real_escape_string($contrib1_desc),
        $db_contrib_needs, $mysqli->real_escape_string($contrib1_need));

	$contrib0_id = $contrib1_id = 0;
    if( $mysqli->query($contrib0_insert_query) ) {
    	$contrib0_id = $mysqli->insert_id;
    } else {
    	$returnVal .= "Failed to store contribution0: (" . $mysqli->error . ") ";
    }
    if( $mysqli->query($contrib1_insert_query) ) {
        $contrib1_id = $mysqli->insert_id;
    } else {
        $returnVal .= "Failed to store contribution1: (" . $mysqli->error . ") ";
    }
    
    $first = ucfirst($first);
    $city = ucfirst(strtolower($city));

    $person_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', '%s', '%s', '%s', %s, %s, '%s', '%s', '%s', '%s', '%s', %s, '%s')",
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
        $db_person_motivation,
        $db_person_familiar,
        $db_person_contrib0,
        $db_person_contrib1,
        $db_person_preparations,
        $db_person_terms0,
        $db_person_terms1,
        $db_person_terms2,
        $db_person_terms3,
        $db_person_round,
        $db_person_date,
        $mysqli->real_escape_string($email),
        $mysqli->real_escape_string($first),
        $mysqli->real_escape_string($last),
        $mysqli->real_escape_string($birth),
        $mysqli->real_escape_string($city),
        $mysqli->real_escape_string($gender),
        $mysqli->real_escape_string($phone),
        $mysqli->real_escape_string($nr_visits),
        $mysqli->real_escape_string($editions),
        $mysqli->real_escape_string($partner),
        $mysqli->real_escape_string($motivation),
        $mysqli->real_escape_string($familiar),
        $mysqli->real_escape_string($contrib0_id),
        $mysqli->real_escape_string($contrib1_id),
        $mysqli->real_escape_string($preparations),
        $mysqli->real_escape_string($terms0),
        $mysqli->real_escape_string($terms1),
        $mysqli->real_escape_string($terms2),
        $mysqli->real_escape_string($terms3),
        $mysqli->real_escape_string($signupround),
        date( 'Y-m-d H:i:s')
    );
	
	if( !$mysqli->query($person_query) ) {
        if($mysqli->errno == 1062) {
            global $mailtolink;
            $returnVal = "Zo te zien heb je je al ingeschreven. Als je denkt dat deze observatie fout is kun je even mailen naar: " . $mailtolink;
        } else {
            $email_content = $person_query . "<br>";
            $email_content .= $contrib0_insert_query . "<br>";
            $email_content .= $contrib1_insert_query . "<br>";
            $email_content .= $returnVal . "<br><br>";
            $email_content .= "<br>" . $mysqli->error;
            send_mail('info@stichtingfamiliarforest.nl', 'Web Familiar Forest', 'Found ERROR!', $email_content);
            
            $returnVal = "Er is helaas iets fout gegaan. Probeer het inschrijven later nog eens of stuur een mail naar: " . $mailtolink;
            if( $contrib0_id != 0 ) {
                $mysqli->query(sprintf("DELETE FROM `%s` WHERE `%s` = '%s';", $db_table_contrib, $db_contrib_id, $contrib0_id));    
            }
            if( $contrib1_id != 0 ) {
                $mysqli->query(sprintf("DELETE FROM `%s` WHERE `%s` = '%s';", $db_table_contrib, $db_contrib_id, $contrib1_id));
            }
        }
	}
    $mysqli->close();
	return $returnVal;
}

function updateSignup($email, $first, $last, $birth, $city, $gender, $phone, $nr_visits, $editions, $partner, $motivation, $familiar, $contrib0, $contrib1, $contrib0_desc, $contrib1_desc, $contrib0_need, $contrib1_need, $preparations, $terms0, $terms1, $terms2, $terms3, $signupround) {
    global $db_person_email, $db_person_first, $db_person_last, $db_person_birth, $db_person_city, $db_person_gender, $db_person_phone, $db_person_visits, $db_person_editions, $db_person_partner, $db_person_motivation, $db_person_familiar, $db_person_contrib0, $db_person_contrib1, $db_contrib_id, $db_contrib_type, $db_contrib_desc, $db_contrib_needs, $db_person_preparations, $db_person_terms0, $db_person_terms1, $db_person_terms2, $db_person_terms3, $db_person_date, $db_person_round;
    global $db_table_person, $db_table_contrib;
    global $db_host, $db_user, $db_pass, $db_name;
    global $mailtolink;
    $returnVal = "";

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        $returnVal .= "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ";
    }

    $contrib0_id = $contrib1_id = 0;
    $sqlresult = $mysqli->query(sprintf("SELECT contrib0 FROM person WHERE email = '%s'", $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE ) {
        $returnVal .= "Failed to get contrib0";
    } else if ($sqlresult->num_rows == 0) {
        $returnVal .= "No contrib1 for email: ".$email;
    } else {
        $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
        $contrib0_id = $row['contrib0'];
    }
    $sqlresult = $mysqli->query(sprintf("SELECT contrib1 FROM person WHERE email = '%s'", $mysqli->real_escape_string($email)));
    if( $sqlresult === FALSE ) {
        $returnVal .= "Failed to get contrib1";
    } else if ($sqlresult->num_rows == 0) {
        $returnVal .= "No contrib1 for email: ".$email;
    } else {
        $row = $sqlresult->fetch_array(MYSQLI_ASSOC);
        $contrib1_id = $row['contrib1'];
    }

    $contrib0_update_query = sprintf("UPDATE `%s` SET `%s` = '%s', `%s` = '%s', `%s` = '%s' WHERE `%s` = %s",
        $db_table_contrib,
        $db_contrib_type,
        $mysqli->real_escape_string($contrib0),
        $db_contrib_desc,
        $mysqli->real_escape_string($contrib0_desc),
        $db_contrib_needs,        
        $mysqli->real_escape_string($contrib0_need),
        $db_contrib_id,
        $mysqli->real_escape_string($contrib0_id));
    $contrib1_update_query = sprintf("UPDATE `%s` SET `%s` = '%s', `%s` = '%s', `%s` = '%s' WHERE `%s` = %s",
        $db_table_contrib,
        $db_contrib_type,
        $mysqli->real_escape_string($contrib1),
        $db_contrib_desc,
        $mysqli->real_escape_string($contrib1_desc),
        $db_contrib_needs,        
        $mysqli->real_escape_string($contrib1_need),
        $db_contrib_id,
        $mysqli->real_escape_string($contrib1_id));

    if( !$mysqli->query($contrib0_update_query) ) {
        $returnVal .= "Failed to update contribution0: (" . $mysqli->error . ") ";
    }
    if( !$mysqli->query($contrib1_update_query) ) {
        $returnVal .= "Failed to update contribution1: (" . $mysqli->error . ") ";
    }
    
    $first = ucfirst($first);
    $city = ucfirst(strtolower($city));

    $person_query = sprintf("UPDATE `%s` SET `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = %s, `%s` = '%s',
        `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = '%s', `%s` = %s, `%s` = '%s'
        WHERE `%s` = '%s';",
        $db_table_person,
        $db_person_first,
        $mysqli->real_escape_string($first),
        $db_person_last,
        $mysqli->real_escape_string($last),
        $db_person_birth,
        $mysqli->real_escape_string($birth),
        $db_person_city,
        $mysqli->real_escape_string($city),
        $db_person_gender,
        $mysqli->real_escape_string($gender),
        $db_person_phone,
        $mysqli->real_escape_string($phone),
        $db_person_visits,
        $mysqli->real_escape_string($nr_visits),
        $db_person_editions,
        $mysqli->real_escape_string($editions),
        $db_person_partner,
        $mysqli->real_escape_string($partner),
        $db_person_motivation,
        $mysqli->real_escape_string($motivation),
        $db_person_familiar,
        $mysqli->real_escape_string($familiar),
        $db_person_preparations,
        $mysqli->real_escape_string($preparations),
        $db_person_terms0,
        $mysqli->real_escape_string($terms0),
        $db_person_terms1,
        $mysqli->real_escape_string($terms1),
        $db_person_terms2,
        $mysqli->real_escape_string($terms2),
        $db_person_terms3,
        $mysqli->real_escape_string($terms3),
        $db_person_round,
        $mysqli->real_escape_string($signupround),
        $db_person_date, 
        date( 'Y-m-d H:i:s'),
        $db_person_email,
        $mysqli->real_escape_string($email)
    );
    
    if( !$mysqli->query($person_query) ) {
        echo $mysqli->error;
        $email_content = $person_query . "<br>";
        $email_content .= $contrib0_update_query . "<br>";
        $email_content .= $contrib1_update_query . "<br>";
        $email_content .= $returnVal . "<br><br>";
        $email_content .= "<br>" . $mysqli->error;
        send_mail('info@stichtingfamiliarforest.nl', 'Web Familiar Forest', 'Found ERROR!', $email_content);
        
        $returnVal = "Er is helaas iets fout gegaan. Probeer het inschrijven later nog eens of stuur een mail naar: " . $mailtolink;
    }
    $mysqli->close();
    return $returnVal;
}
?>