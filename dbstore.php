<?php
include 'initialize.php';
include 'fields.php';
//include 'email.php' TODO send email on error

function storeSignup($email, $first, $last, $birth, $city, $gender, $phone, $nr_visits, $editions, $partner, $contrib0, $contrib1, $contrib0_desc, $contrib1_desc, $contrib0_need, $contrib1_need, $terms0, $terms1, $terms2) {
    global $db_person_email, $db_person_first, $db_person_last, $db_person_birth, $db_person_city, $db_person_gender, $db_person_phone, $db_person_visits, $db_person_editions, $db_person_partner, $db_person_contrib0, $db_person_contrib1, $db_contrib_id, $db_contrib_type, $db_contrib_desc, $db_contrib_needs, $db_person_terms0, $db_person_terms1, $db_person_terms2, $db_person_date;
    global $db_table_person, $db_table_contrib;
    global $db_host, $db_user, $db_pass, $db_name;
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
        $db_contrib_needs, $mysqli->real_escape_string($contrib0_need)
        );
    $contrib1_select_query = sprintf("SELECT `%s` FROM `%s` WHERE `%s` = '%s' AND `%s` = '%s' AND `%s` = '%s'",
        $db_contrib_id,
        $db_table_contrib,
        $db_contrib_type, $mysqli->real_escape_string($contrib1),
        $db_contrib_desc, $mysqli->real_escape_string($contrib1_desc),
        $db_contrib_needs, $mysqli->real_escape_string($contrib1_need)
        );

	$contrib0_id = $contrib1_id = 0;
    $result = $mysqli->query($contrib0_select_query);
    if( $result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $contrib0_id = $row[$db_contrib_id];
    } else if( $mysqli->query($contrib0_insert_query) ) {
    	$result =  $mysqli->query($contrib0_select_query);
        if( $result->num_rows > 0 ) {
            var_dump($row);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $contrib0_id = $row[$db_contrib_id];    
        } else {
            //FATAL ERROR NIGH IMPOSSIBLE
        }
    } else {
    	$returnVal .= "Failed to store contribution0: (" . $mysqli->error . ") ";
    }
    $result = $mysqli->query($contrib1_select_query);
    if( $result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $contrib1_id = $row[$db_contrib_id];
    } else if( $mysqli->query($contrib1_insert_query) ) {
        $result =  $mysqli->query($contrib1_select_query);
        if( $result->num_rows > 0 ) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $contrib1_id = $row[$db_contrib_id];    
        } else {
            //FATAL ERROR NIGH IMPOSSIBLE
        }
    } else {
        $returnVal .= "Failed to store contribution1: (" . $mysqli->error . ") ";
    }
    
    $first = ucfirst($first);
    $city = ucfirst(strtolower($city));

    // SUPER UGLY! YOU CAN DO BETTER!!
    if( $partner != '') {
        $person_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', '%s', %s, %s, '%s', '%s', '%s', '%s')",
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
            $db_person_terms0,
            $db_person_terms1,
            $db_person_terms2,
            $db_person_date,
            $mysqli->real_escape_string($email),
            $mysqli->real_escape_string($first),
            $mysqli->real_escape_string($last),
            $birth,
            $mysqli->real_escape_string($city),
            $mysqli->real_escape_string($gender),
            $mysqli->real_escape_string($phone),
            $nr_visits,
            $mysqli->real_escape_string($editions),
            $mysqli->real_escape_string($partner),
            $contrib0_id,
            $contrib1_id,
            $mysqli->real_escape_string($terms0),
            $mysqli->real_escape_string($terms1),
            $mysqli->real_escape_string($terms2),
            date( 'Y-m-d H:i:s'));
    } else {
        $person_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`, `%s`,`%s`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', %s, %s, '%s', '%s', '%s', '%s')",
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
            $db_person_contrib0,
            $db_person_contrib1,
            $db_person_terms0,
            $db_person_terms1,
            $db_person_terms2,
            $db_person_date,
            $mysqli->real_escape_string($email),
            $mysqli->real_escape_string($first),
            $mysqli->real_escape_string($last),
            $birth,
            $mysqli->real_escape_string($city),
            $mysqli->real_escape_string($gender),
            $mysqli->real_escape_string($phone),
            $nr_visits,
            $mysqli->real_escape_string($editions),
            $contrib0_id,
            $contrib1_id,
            $mysqli->real_escape_string($terms0),
            $mysqli->real_escape_string($terms1),
            $mysqli->real_escape_string($terms2),
            date( 'Y-m-d H:i:s'));
    }
	
	if( !$mysqli->query($person_query) ) {
        if($mysqli->errno == 1062) {
            global $mailtolink;
            $returnVal = "Zo te zien heb je je al ingeschreven. Als je denkt dat deze observatie fout is kun je even mailen naar: " . $mailtolink . $mysqli->error;
        } else {
		    //TODO SEND ME AN EMAIL
            //$returnVal .= "Failed to add person: " . $mysqli->error . ") \n" . $person_query;
            $returnVal = "Er is helaas iets fout gegaan. Er is een mail verstuurd hierover en zal snel worden opgepakt. Probeer het inschrijven later nog eens!" . $mysqli->error . $person_query;
        }
	}
    $mysqli->close();
	return $returnVal;
}

?>

