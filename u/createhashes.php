<?php
include_once "initialize.php";
include "../functions.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_EDIT  != PERMISSION_EDIT ) {
    header('Location: oops.php');
}

try
{
    include "mollie_api_init.php";
}
catch (Mollie_API_Exception $e) {
    //email error
    email_error("API call failed: " . htmlspecialchars($e->getMessage()));
}

function get_hash($code,$id) {
	return md5($ticketseed.$code.$id);
}

function set_hash($mysqli, $id, $code) {
	if( !hasPaid($mysqli, $id, $code) ) {
	    $email_error("Er gaat iets fout met hasPaid voor code: " . $code);
	    return false;
	} else {
		$hash = get_hash($code,$id);

		$query = sprintf("UPDATE buyer set ticket='%s' where code = '%s'",
			$mysqli->real_escape_string($hash),
			$mysqli->real_escape_string($code));
		$sqlresult = $mysqli->query($query);
		if( $sqlresult === FALSE ) {
		    email_error("Failed to add ticket hash for : ".$code);
		    return false;
		}
	}
	return true;
}

function set_all_hashes($mysqli) {
	$query = sprintf("SELECT b.code, b.id FROM buyer b WHERE b.complete=1 && b.ticket=''");
	$sqlresult = $mysqli->query($query);
	$created = 0;
	while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC)) {
		$code = $mysqli->real_escape_string($row['code']);
		$id = $mysqli->real_escape_string($row['id']);
		if( set_hash($mysqli, $id, $code) ) {
			$created += 1;
		}
	}
	return $created;
}
function hasPaid($mysqli, $id, $code) {
    global $mollie;
    if( $mollie->payments->get($id)->isPaid() ) {
        return TRUE;
    } else if( $mollie->payments->get($id)->isRefunded()) {
        $sqlresult = $mysqli->query(sprintf("SELECT * FROM halfticket WHERE code = '%s'", 
            $mysqli->real_escape_string($code)));
        if( $sqlresult === FALSE) {
            return FALSE;
        }
        if( $sqlresult->num_rows != 1 ) {
            return false;
        }
        return true;
    }
    return FALSE;
}

?>