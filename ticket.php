<?php
require('fpdf/fpdf.php');

include "initialize.php";

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Logo
        $this->Image('img/logo_small.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'Familiar Forest 2016 - Nieuw Babylon',0,0,'C');
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
        $this->Cell(0,4,'Familiar Forest geeft geen "kutfeest, geld terug"-garantie',0,1,'C');
        $this->Cell(0,4,'Voor meer informatie kun je altijd mailen naar info@stichingfamiliarforest.nl',0,0,'C');
    }
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    $email_error("Database connectie is kapot: " . $mysqli->error);
    header('Location: index');
}

$hash = "";
if( isset($_GET['ticket'])) {
    $hash = $mysqli->real_escape_string($_GET['ticket']);
} else {
    header('Location: index');
}

$query = sprintf("SELECT p.firstname, p.lastname, p.street, p.postal, p.city, p.motivation, b.code, b.id from person p join
    buyer b on p.email = b.email where b.ticket = '%s'",$hash);
$result = $mysqli->query($query);

if( !$result ) {
    $email_error("Fout bij zoeken naar: " . $hash." ".$mysqli->error);
    header('Location: index');
} else if ($result->num_rows != 1) {
    header('Location: index');
}

$QRURL = 'http://stichtingfamiliarforest.nl/genqrcode.php?hash=';
//$QRURL = 'http://macintosh.local/inschrijf/genqrcode.php?hash=';
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$firstname = $row['firstname'];
$lastname = $row['lastname'];
$motivation = $row['motivation'];
$street = $row['street'];
$adres = $row['postal'].' '.$row['city'];
$code = $row['code'];
$id = $row['id'];
$url = $QRURL.$hash;

$mysqli->close();

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
$pdf->Ln();
$pdf->Cell(30);
$pdf->SetFont('times','I',10);
$pdf->MultiCell(130,6,'"'.htmlspecialchars_decode($motivation).'"  - '.htmlspecialchars_decode($firstname).' '.htmlspecialchars_decode($lastname),0,1);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$pdf->Cell(30);
$pdf->MultiCell(130,6,'Lieve '.htmlspecialchars_decode($firstname).',',0,'C');
$pdf->Cell(30);
$pdf->MultiCell(130,6,'Met dit ticket in de hand ben jij helemaal klaar om naar Familiar Forest 2016 te gaan. Denk er dus wel even aan het uit te printen.',0,'C');
$pdf->Ln();
$pdf->Cell(30);
$pdf->MultiCell(130,6,'Daarnaast willen we je ook eraan herinneren dat je dit ticket niet kan doorverkopen en het alleen door jou gebruikt kan worden samen met een geldig legitimatiebewijs.',0,'C');
$pdf->Ln();
$pdf->Cell(30);
$pdf->MultiCell(130,6,'De high fives zijn gratis, de knuffels oprecht en de liefde oneindig,',0,'C');
$pdf->Cell(30);
$pdf->MultiCell(130,6,'Familiar Forest',0,'C');
$pdf->Output();
?>