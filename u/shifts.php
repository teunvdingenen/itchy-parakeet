<?php
include "../functions.php";

include("checklogin.php");
if( ($user_permissions & PERMISSION_VOLUNTEERS) != PERMISSION_VOLUNTEERS ) {
        header('Location: oops');
}

$tasks = array("", "keuken", "bar", "other", "interiour", "thee", "camping", "afbouw", "act", "game", "schmink", "other_act", "perform", "install", "crew");

$nrrequired = $name = $taskselect = $returnVal = $startdate_output = $enddate_output = "";
$startdate = "Friday, 05/05/2017 21:00";
$enddate = "Friday, 05/05/2017 21:00";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["name"])) {
        $name = test_input($_POST["name"]);
    } else {
        addError("Geen kenmerk opgegeven");
    }
    if( !empty($_POST["nrrequired"])) {
        $nrrequired = test_input($_POST["nrrequired"]);
    } else {
        addError("Geen vrijwilligers aantal opgegeven");
    }
    if( !empty($_POST["taskselect"])) {
        $task = test_input($_POST["taskselect"]);
    } else {
        addError("Geen taak opgegeven");
    }
    if( !empty($_POST["startdate"])) {
        $startdate = $_POST["startdate"];
        $sdate = DateTime::createFromFormat('dddd, DD/MM/YYYY HH:mm', $startdate);
        if( $sdate === FALSE ) {
            if( ($timestamp = strtotime($startdate)) == FALSE ) {
                addError("De opgegeven startdatum klopt niet.");
            } else {
                $startdate_output = date( 'Y-m-d H:i:s', $timestamp );
            }
        } else {
            $startdate_output = $sdate->format('Y-m-d H:i:s');
        }
    } else {
        addError("Geen startdatum/tijd opgegeven");
    }
    if( !empty($_POST["enddate"])) {
        $enddate = $_POST["enddate"];
        $edate = DateTime::createFromFormat('dddd, DD/MM/YYYY HH:mm', $enddate);
        if( $edate === FALSE ) {
            if( ($timestamp = strtotime($enddate)) == FALSE ) {
                addError("De opgegeven einddatum klopt niet.");
            } else {
                $enddate_output = date( 'Y-m-d H:i:s', $timestamp );
            }
        } else {
            $enddate_output = $edate->format('Y-m-d H:i:s');
        }
    } else {
        addError("Geen einddatum/tijd opgegeven");
    }
    if( $returnVal == "" ) {
        $sqlresult = $mysqli->query(sprintf("INSERT INTO `shifts` (`name`, `task`, `startdate`, `enddate`, `nrrequired`) VALUES 
            ('%s','%s','%s','%s',%s);",
            $mysqli->real_escape_string($name),
            $mysqli->real_escape_string($task),
            $mysqli->real_escape_string($startdate_output),
            $mysqli->real_escape_string($enddate_output),
            $mysqli->real_escape_string($nrrequired)));
        if( $sqlresult === FALSE ) {
            addError("Er is iets fout gegaan met het opslaan van de shift. Is het kenmerk wel uniek?");
        } else {
            $nrrequired = $name = $taskselect = $startdate = $enddate = "";
        }
    }
}

$sqlresult = $mysqli->query("SELECT * FROM shifts WHERE 1");
if( $sqlresult === FALSE ) {
    email_error("Error on get shifts: ".$mysqli->error);
}

$mysqli->close();

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <link href="../css/bootstrap-datetimepicker.css" rel="stylesheet">
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
                        <?php echo $returnVal ?>
                        <table class='table table-striped table-bordered table-hover table-condensed'>
                            <thead>
                                <tr class='header-row'><th></th><th>Kenmerk</th><th>Taak</th><th>Startdatum</th><th>Dag</th><th>Starttijd</th><th>Einddatum</th><th>Dag</th><th>Eindtijd</th><th>Aantal vrijwilligers</th><th>Verwijderen</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC)) {
                                echo "<tr>";
                                echo "<td class='status'><span class='glyphicon'></span></td>";
                                echo "<td class='name'>".$row['name']."</td>";
                                echo "<td>".translate_task($row['task'])."</td>";
                                $startdate = DateTime::createFromFormat('Y-m-d H:i:s', $row['startdate']);
                                $enddate = DateTime::createFromFormat('Y-m-d H:i:s', $row['enddate']);
                                echo "<td>".$startdate->format('d-m-Y')."</td>";
                                echo "<td>".$startdate->format('l')."</td>";
                                echo "<td>".$startdate->format('H:i:s')."</td>";
                                echo "<td>".$enddate->format('d-m-Y')."</td>";
                                echo "<td>".$enddate->format('l')."</td>";
                                echo "<td>".$enddate->format('H:i:s')."</td>";
                                //TOOD button -> popup -> editable
                                echo "<td class='input-group nrrequired'>
                                        <input type='text' class='form-control changenr' value='".$row['nrrequired']."'/>
                                    </td>";
                                //echo "<td>".$row['nrrequired']."</td>";
                                echo "<td><a class='btn btn-danger btn-sm btn-block removeshift'>Verwijderen</a></td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <div>
                            <h3>Shift toevoegen</h3>
                            <form class="" method="post" id="shift-form" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                                <div class="form-group">
                                    <label class="sr-only" for="taskselect">Taak</label>
                                    <select class='form-control taskselect' id="taskselect" name='taskselect'>
                                        <?php
                                        foreach( $tasks as $task ) {
                                            echo "<option name='".$task."' value='".$task."' ".($task == $taskselect ? " selected='selected'" : "").">".translate_task($task)."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="startdate">Start</label>
                                    <div class='input-group date' id='startdate'>
                                        <input type='text' class="form-control" name="startdate" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="enddate">Einde</label>
                                    <div class='input-group date' id='enddate'>
                                        <input type='text' class="form-control" name="enddate" id="enddate_input" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="name">Kenmerk</label>
                                    <div class='input-group'>
                                        <input class="form-control" type="text" id="name" placeholder="Kenmerk" value="<?php echo $name;?>" name="name">
                                        <span class="working input-group-addon">
                                            <span class="glyphicon glyphicon-file"></span>
                                        </span>
                                    </div>
                                    <div class="checkbox hidden">
                                      <label>
                                        <input type="checkbox" id="autoname" checked> Kenmerk automatisch genereren
                                      </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="nrrequired">Aantal vrijwilligers</label>
                                    <input class="form-control" type="text" id="nrrequired" placeholder="Aantal" value="<?php echo $nrrequired;?>" name="nrrequired">
                                </div>
                                <button class="btn btn-sm btn-primary" type="submit">Toevoegen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("form-js.html"); ?>
        <script src="../js/vendor/moment-with-locales.js"></script>
        <script src="../js/vendor/bootstrap-datetimepicker.min.js"></script>
        <script src="js/shifts.js"></script>
        </body>
</html>
