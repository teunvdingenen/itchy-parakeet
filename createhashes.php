<?php
include_once "functions.php";

try
{
    include "u/mollie_api_init.php";
}
catch (Mollie_API_Exception $e) {
    //email error
    email_error("API call failed: " . htmlspecialchars($e->getMessage()));
}

function get_hash($code,$id) {
	return md5($ticketseed.$code.$id);
}

function set_hash($mysqli, $id, $code) {
	global $current_table;
	if( !hasPaid($mysqli, $id, $code) ) {
	    $email_error("Er gaat iets fout met hasPaid voor code: " . $code);
	    return false;
	} else {
		$hash = get_hash($code,$id);
		$query = sprintf("UPDATE $current_table set ticket='%s' where rafflecode = '%s'",
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

function hasPaid($mysqli, $id, $code) {
    global $mollie;
    if( $mollie->payments->get($id)->isPaid() ) {
        return TRUE;
    }
    return FALSE;
}

?>