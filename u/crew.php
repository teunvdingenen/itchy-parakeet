<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_EDIT) != PERMISSION_EDIT ) {
    header('Location: oops.php');
}
$firstname = $lastname = "";
$permission_texts = ["Gegevens inzien", "Loten", "Wijzigen", "Bellen", "Vrijwilligers", "Acts", "Bar"];
$permission_values = [PERMISSION_DISPLAY, PERMISSION_RAFFLE, PERMISSION_EDIT, PERMISSION_CALLER, PERMISSION_VOLUNTEERS, PERMISSION_ACTS, PERMISSION_BAR];

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    email_error("Database connectie is kapot: " . $mysqli->error);
}
$result = $mysqli->query(sprintf("SELECT p.firstname, p.lastname, p.email, p.phone, s.share, s.note, u.permissions, s.complete
    FROM person p join $current_table s on s.email = p.email join users u on u.email = p.email
    WHERE s.task = 'crew'"));
if(!$result) {
    email_error("Crew page kapot: " . $mysqli->error);
}
$sqlresult = 0;
if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["firstname"]) ) {
        $firstname = test_input($_POST["firstname"]);
    }
    if( !empty($_POST["lastname"]) ) {
        $lastname = test_input($_POST["lastname"]);
    }
    $sqlresult = $mysqli->query(sprintf("SELECT p.email, p.firstname, p.lastname FROM person p WHERE p.firstname = '%s' or p.lastname = '%s'",
        $mysqli->real_escape_string($firstname),
        $mysqli->real_escape_string($lastname)
        ));
} //end post 

$mysqli->close();

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
                    <div class="col-xs-12 col-sm-9">
                        <table class='table table-striped table-bordered table-hover table-condensed'>
                            <thead>
                                <tr class='header-row'><th>Status</th><th>Betaald</th><th>Voornaam</th><th>Achternaam</th><th>Email</th><th>Telefoon</th><th>Ticket</th><th>Opmerking</th>
                                    <?php
                                    foreach ($permission_texts as $value) {
                                        echo "<th>".$value."</th>";
                                    }
                                    ?>
                            </thead>
                            <tbody>
                            <?php
                            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
                            {
                                echo "<tr>";
                                echo "<td class='working'></td>";
                                echo "<td>". (($row['complete'] == 1 || $row['share'] == "FREE" ) ? "<i class='glyphicon glyphicon-ok'></i>" : "<i class='glyphicon glyphicon-remove'></i>")."</td>"; 
                                echo "<td>" . $row['firstname'] . "</td>";
                                echo "<td>" . $row['lastname'] . "</td>";
                                echo "<td class='email'>" . $row['email'] . "</td>";
                                echo "<td>" . $row['phone'] . "</td>";
                                echo "<td><select class='share' name='share'>";
                                echo "<option name='share' value='FULL' ".($row["share"] == "FULL" ? " selected='selected'" : "").">Betaald ticket</option>";
                                echo "<option name='share' value='HALF' ".($row["share"] == "HALF" ? " selected='selected'" : "").">Half ticket</option>";
                                echo "<option name='share' value='FREE' ".($row["share"] == "FREE" ? " selected='selected'" : "").">Heel ticket</option>";
                                echo "</select></td>";
                                echo "<td><div class='table-cell'><textarea class='note' cols='60' rows='4'>".$row['note']."</textarea></div></td>";
                                $i = 0;
                                foreach ($permission_texts as $value) {
                                    echo '<td><input class="permission" type="checkbox" name="'.$value.'" value='.$permission_values[$i]." ".($row['permissions'] & $permission_values[$i] ? "checked=checked" : "").' ></td>';
                                    $i++;
                                }
                                echo "<td><a class='btn btn-info btn-sm btn-block removecrew'>Verwijderen van crew</a>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <form class="form-inline" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                            <div class="form-group">
                                <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname">
                            </div>
                            <button class="btn btn-sm btn-primary" type="submit">Zoeken</button>
                        </form>
                        <table class='table table-striped table-bordered table-hover table-condensed'>
                        <?php
                        if( $_SERVER["REQUEST_METHOD"] == "POST" ) {
                            while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
                            {
                                echo "<tr>";
                                echo "<td>" . $row['firstname'] . "</td>";
                                echo "<td>" . $row['lastname'] . "</td>";
                                echo "<td class='email'>" . $row['email'] . "</td>";
                                echo "<td><a class='btn btn-info btn-sm btn-block addcrew'>Aan crew toevoegen</a>";
                                echo "</tr>";
                            }
                        }
                        ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        <script src="js/crew.js"></script>
        </body>
</html>
