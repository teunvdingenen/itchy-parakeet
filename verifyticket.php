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
	$sqlresult = $mysqli->query(sprintf("SELECT p.firstname, p.lastname, p.birthdate, s.transactionid, s.rafflecode, s.attending, s.task
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
		$taskresult = $mysqli->query(sprintf("SELECT task, startdate, enddate FROM shifts WHERE name = '%s'",
			$mysqli->real_escape_string($row['task'])));
		if( !$taskresult || $taskresult->num_rows == 0 ) {
			$row['startdate'] = "";
			$row['enddate'] = "";
			$row['task'] = "Onbekend";
		} else {
			$taskrow = $taskresult->fetch_array(MYSQL_ASSOC);
			$row['startdate'] = $taskrow['startdate'];
			$row['enddate'] = $taskrow['enddate'];
			$row['task'] = translate_task($taskrow['task']);
		}
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