<?php
include "initialize.php";
require('fpdf/fpdf.php');

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
    $this->Cell(0,10,'Dit ticket is persoonlijk en niet overdraagbaar',0,0,'C');
}
}

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

$code = $mysqli->real_escape_string($_GET['code']);

$query = sprintf("SELECT firstname, lastname, motivation from person where ");
$person_result

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->Cell(30);
$pdf->Cell(80,30,'Content',0,1);
//$pdf->Image('genqrcode.php', 50,50,30,30,'PNG');
$pdf->Image('http://macintosh.local/inschrijf/genqrcode.php?id=1',100,30,90,0,'PNG');
$pdf->Cell(80);
$pdf->Cell(30,180,'Voor meer informatie kun je altijd mailen naar info@stichingfamiliarforest.nl',0,0,'C');
$pdf->Output();
?>