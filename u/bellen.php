<?php
include "../functions.php";

include("checklogin.php");

if(( $user_permissions & PERMISSION_CALLER ) != PERMISSION_CALLER ) {
    header("oops.php");
}
$show_already_called = FALSE;
if( !empty($_GET['done'])) {
    $show_already_called = ($_GET['done'] == 1);
}
$required_permissions = PERMISSION_CALLER;
$request_for = "";
if( $show_already_called ) {
    $request_for = 'called_done';
} else {
    $request_for = 'caller';
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10"> 
                        <div style='margin-top: 5px;'>
                        <?php include("generic_filter.php"); ?>
                        <?php include("stats.php");
                            if( $show_already_called ) {
                                echo "<a href='bellen' class='btn btn-default btn-block'>Nog te doen tonen</a>";
                            } else {
                                echo "<a href='bellen?done=1' class='btn btn-default btn-block'>Gebeld/Geen Gehoor tonen</a>";
                            }
                        ?>
                        <?php include("pagination.php"); ?>
                            <table class='table table-striped table-bordered table-hover table-condensed'>
                                <thead>
                                    <tr class='header-row'>
                                        <?php
                                        if( $show_already_called ) {
                                            echo "<th>Toch niet gebeld</th>";
                                        } else {
                                            echo "<th>Gebeld</th><th>Geen Gehoor</th>";
                                        }
                                        ?>
                                        <th>Stalk</th><th>Achternaam</th><th>Voornaam</th><th>Telefoon</th><th>Code</th><th>Leeftijd</th><th>Woonplaats</th><th>Bezoeken</th><th>Motivatie</th><th>Vraag</th>
                                </thead>
                                <tbody>
                                <?php
                                while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
                                {
                                    echo "<tr>";
                                    if( $show_already_called ) {
                                        echo "<td><a href='#' class='btn btn-success btn-sm btn-block notcalled'>Terug naar bellijst</td>";    
                                    } else {
                                        echo "<td><a href='#' class='btn btn-success btn-sm btn-block called'>Gebeld</td>";
                                        echo "<td><a href='#' class='btn btn-info btn-sm btn-block nocall'>Geen Gehoor</td>";
                                    }
                                    echo "<td><a href='https://www.facebook.com/search/top/?q=".$row['firstname']." ".$row['lastname']. "' class='btn btn-primary btn-sm btn-block' target='_blank'><i class='fa fa-facebook-square'></i></td>";
                                    echo "<td>" . $row['lastname'] . "</td>";
                                    echo "<td>" . $row['firstname'] . "</td>";
                                    echo "<td>" . $row['phone'] . "</td>";
                                    echo "<td>" . $row['rafflecode'] . "</td>";
                                    $age = (new DateTime($row['birthdate']))->diff(new DateTime('now'))->y;
                                    echo "<td>" . $age . "</td>"; 
                                    echo "<td>" . $row['city'] . "</td>";
                                    echo "<td>" . $row['visits'] . "</td>";
                                    echo "<td>" . $row['motivation'] . "</td>";
                                    echo "<td>" . $row['question'] . "</td>";
                                    echo "<td class='email hidden'>" . $row['email'] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <?php include("pagination.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include("form-js.html"); ?>
        <script src="js/called.js"></script>

        </body>
</html>
