<?php
require('../fpdf/fpdf.php');

include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_PARTICIPANT) != PERMISSION_PARTICIPANT ) {
    header('Location: oops');
}

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('../img/logo_small.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'Familiar Forest 2018 : Kleurenrevolutie',0,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,4,'Voor meer informatie kun je altijd mailen naar info@stichingfamiliarforest.nl',0,0,'C');
    }
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    $email_error("Database connectie is kapot: " . $mysqli->error);
    header('Location: forest');
}
$query = "";
if( $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ticket'])) {
    $query = $query = sprintf("SELECT p.firstname, p.lastname, p.street, p.postal, p.city, s.motivation, s.rafflecode, s.transactionid, s.ticket from person p join
        $current_table s on p.email = s.email where s.ticket = '%s' and s.complete = 1",$mysqli->real_escape_string($_GET['ticket']));
} else {
    $query = sprintf("SELECT p.firstname, p.lastname, p.street, p.postal, p.city, s.motivation, s.rafflecode, s.transactionid, s.ticket from person p join
        $current_table s on p.email = s.email where s.email = '%s' and s.complete = 1",$mysqli->real_escape_string($user_email));
}
$result = $mysqli->query($query);

if( !$result || $result->num_rows != 1) {
    header('Location: forest');
}

//$QRURL = 'http://stichtingfamiliarforest.nl/u/genqrcode?hash=';
$QRURL = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=';
$row = $result->fetch_array(MYSQLI_ASSOC);
$mysqli->close();

$firstname = $row['firstname'];
$lastname = $row['lastname'];
$motivation = $row['motivation'];
$street = $row['street'];
$adres = $row['postal'].' '.$row['city'];
$code = $row['rafflecode'];
$id = $row['transactionid'];
$url = $QRURL.$row['ticket'];

// Instanciation of inherited class
$pdf = new PDF();
$pdf->addPage("P", "A4");
$pdf->SetFont('Arial','',12);
$pdf->Image($url,100,25,90,0,'PNG');
$pdf->Cell(30,30);
$pdf->Cell(80,10,htmlspecialchars_decode($firstname).' '.htmlspecialchars_decode($lastname),0,1);
$pdf->Cell(30);
$pdf->Cell(80,10,$street,0,1);
$pdf->Cell(30);
$pdf->Cell(80,10,$adres,0,1);
$pdf->Ln();
$pdf->Cell(30);
$pdf->Cell(80,10,'Ticketcode: '.$code,0,1);
$pdf->Cell(30);
$pdf->Cell(80,10,'Transactie: '.$id,0,1);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(30);
$pdf->SetFont('times','I',10);
$pdf->MultiCell(130,5,'"'.htmlspecialchars_decode($motivation).'"  - '.htmlspecialchars_decode($firstname).' '.htmlspecialchars_decode($lastname),0,1);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$pdf->Cell(30);
$pdf->Output();
?>