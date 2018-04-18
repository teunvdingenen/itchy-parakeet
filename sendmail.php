<?php
require_once 'mailer/PHPMailerAutoload.php';
include_once('mailer/class.smtp.php');

include 'initialize.php';

function get_email_header() {
	return "<head>

    <title>Back to the FFFuture: '95</title>

    <!-- Custom styles for this template -->
    <style>
        body {
            background-color: #eee;
        }
    </style>
    </head>";
}

function get_email_footer() {
	return "<br><br>De High Fives zijn gratis, de knuffels oprecht en de liefde oneindig.
        <br><br>
        Familiar Forest
        <br><br>
        <a href='mailto:info@stichtingfamiliarforest.nl' target='_top'>info@stichtingfamiliarforest.nl</a>
        <br>
        <img src='http://stichtingfamiliarforest.nl/img/logo_small.png' alt='Stichting Familiar Forest'>";
}

function send_mail($email, $fullname, $subject, $content) {
	$mail = new PHPMailer(true);
	$mail->CharSet="UTF-8";
	$mail->Encoding="base64";
	$mail->setFrom('info@stichtingfamiliarforest.nl','Stichting Familiar Forest');
	$mail->addAddress($email, $fullname);
	$mail->addReplyTo('info@stichtingfamiliarforest.nl','Stichting Familiar Forest');

	$mail->isHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $content;

	$mail->AltBody = "Om deze mail te lezen heb je een email programma nodig die HTML kan tonen.";

	return $mail->send();
}
?>