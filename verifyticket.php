<?php
include "functions.php";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	if( $mysqli->connect_errno ) {
		$array = array('connect_err' => $mysqli->connect_errno );
		echo json_encode($array);
		return;
	}
	$ticketId = $mysqli->real_escape_string($_POST['ticket']);
	$sqlresult = $mysqli->query(sprintf("SELECT p.firstname, p.lastname, p.birthdate, s.task, s.transactionid, s.rafflecode, s.attending
		FROM $current_table s join person p on p.email = s.email where s.ticket = '%s' and s.complete = 1",$ticketId));
	if( $sqlresult === FALSE ) {
		$array = array('message' => $mysqli->error);
		$array['status'] = 'ERR';
		echo json_encode($array);
	} else if ( $sqlresult->num_rows != 1 ) {
		$array = array('message' => 'Kon ticket niet vinden, niet valide');
		$array['status'] = 'ERR';
		echo json_encode($array);
	} else {
		$row = $sqlresult->fetch_array(MYSQL_ASSOC);
		$row['task'] = translate_task($row['task']);
		if( $row['attending'] == 0 ) {
			$updateresult = $mysqli->query("UPDATE $current_table set attending = 1 where ticket = '$ticketId'");
			if( $updateresult === FALSE ) {
				$row['status'] = 'ERR';
				$row['message'] = $mysqli->error;
			} else if ( $mysqli->affected_rows != 1 ) {
				$row['status'] = 'WARN';
				$row['message'] = "Er gaat iets fout bij het registreren van aanwezigheid. Ticket wel OK";
			} else {
				$row['status'] = 'OK';
				$row['message'] = '';
			}
		} else {
			$row['status'] = 'WARN';
			$row['message'] = 'Stond al op aanwezig! Twee keer gescand of dubbel ticket? Check gegevens!';
		}
		echo json_encode($row);
	}
	$mysqli->close();
}


?>