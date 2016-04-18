<?php

require 'mailer/PHPMailerAutoload.php';
include 'initialize.php';

function get_email_header() {
	return "<head>

    <title>Familiar Forest Festival 2016</title>

    <!-- Custom styles for this template -->
    <style>
        body {
            background-color: #eee;
        }
    </style>
    </head>";
}

function get_email_footer() {
	return "De High Fives zijn gratis, de knuffels oprecht en de liefde oneindig.
        <br><br>
        Familiar Forest
        <br><br>
        <a href='mailto:info@stichtingfamiliarforest.nl' target='_top'>info@stichtingfamiliarforest.nl</a>
        <br>
        <img src='http://stichtingfamiliarforest.nl/img/logo_small.png' alt='Stichting Familiar Forest'>";
}

function send_mail($email, $fullname, $subject, $content) {
	global $email_pass;

	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'smtp02.hostnet.nl';
	$mail->SMTPAuth = true;
	$mail->Username = 'info@stichtingfamiliarforest.nl';
	$mail->Password = $email_pass;
	$mail->Port = 587;

	$mail->setFrom('info@stichtingfamiliarforest.nl','Stichting Familiar Forest');
	$mail->addAddress($email, $fullname);
	$mail->addReplyTo('info@stichtingfamiliarforest.nl','Stichting Familiar Forest');

	$mail->isHTML(true);

	$mail->Subject = $subject;
	$mail->Body = $content;

	$mail->AltBody = "Om deze mail te lezen heb je een email programma nodig die HTML kan tonen.";

	return $mail->send();
}