<?php
include "initialize.php";
try
{
    include "mollie_api_init.php";
}
catch (Mollie_API_Exception $e) {
    //email error
    email_error("API call failed: " . htmlspecialchars($e->getMessage()));
}

function create_hashes($mysqli, $seed) {
	$query = sprintf("SELECT b.code, b.id FROM buyer b WHERE b.complete=1 && b.ticket=''");
	$sqlresult = $mysqli->query($query);
	$created = 0;
	while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC)) {
		if( !hasPaid($mysqli, $id, $code)) {
		    $email_error("Er gaat iets fout met hasPaid voor code: " . $code);
		    header('Location: index');	
		} else {
			$code = $mysqli->real_escape_string($row['code']);
			$id = $mysqli->real_escape_string($row['id']);
			$hash = md5($seed.$code.$id);

			$query = sprintf("UPDATE buyer set ticket='%s' where code = '%s'",$hash,$code);
			$sqlresult = $mysqli->query($query);
			if( $sqlresult === FALSE ) {
			    email_error("Failed to add ticket hash for : ".$code);
			} else {
				$created += 1;
			}
		}
	}
	return $created;
}
function hasPaid($mysqli, $id, $code) {
    global $mollie;
    if( $mollie->payments->get($row['id'])->isPaid() ) {
        return TRUE;
    } else if( $mollie->payments->get($row['id'])->isRefunded()) {
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