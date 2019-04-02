<?php
include "../functions.php";

include("checklogin.php");

$tasks = array();
$team = '';
if( !isset($_GET['t'])) {
    $team = 'vrijwilligers';
} else {
    $team = $_GET['t'];
}
if( $team == 'vrijwilligers' && ($user_permissions & PERMISSION_VOLUNTEERS) == PERMISSION_VOLUNTEERS ) {
    $tasks = array("keuken", "bar", "other", "iv", "thee", "camping", "afb", "jip", "silent", "vuur", "techniek", "reserve");
} else if ( $team == 'acts' && ( $user_permissions & PERMISSION_ACTS) == PERMISSION_ACTS ) {
    $tasks = array("act", "game", "schmink", "other_act", "perform", "install", "workshop", "acteren");
} else {
    header('Location: oops');
}

//$tasks = array("keuken", "bar", "other", "iv", "thee", "camping", "afbouw", "act", "game", "schmink", "other_act", "perform", "install", "crew");

$nrrequired = $name = $taskselect = $returnVal = $startdate_output = $enddate_output = "";
$startdate = "Friday, 07/09/2019 11:00";
$enddate = "Friday, 08/09/2019 13:00";

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
        $sdate = DateTime::createFromFormat('D, d/m/Y G:i', $startdate);
        if( $sdate === FALSE ) {
            addError("De opgegeven startdatum klopt niet.");
        } else {
            $startdate_output = $sdate->format('Y-m-d H:i:s');
        }
    } else {
        addError("Geen startdatum/tijd opgegeven");
    }
    if( !empty($_POST["enddate"])) {
        $enddate = $_POST["enddate"];
        $edate = DateTime::createFromFormat('D, d/m/Y G:i', $enddate);
        if( $edate === FALSE ) {
            addError("De opgegeven einddatum klopt niet.");
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

$shiftcount = 0;
$sqlresult = $mysqli->query("SELECT COUNT(*) AS 'count' FROM shifts");
if( $sqlresult === FALSE ) {
    echo $mysqli->error;
} else {
    $shiftcount = $sqlresult->fetch_array(MYSQLI_ASSOC)['count'];
}

$sqlresult = $mysqli->query("SELECT * FROM shifts WHERE `task` IN ('".implode("','",$tasks)."')");
if( $sqlresult === FALSE ) {
    echo $mysqli->error;
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
            <div class='shiftcount' style='display:none'><?php echo $shiftcount ?></div>
            <div id='editmodal' class="modal fade" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Tijden wijzigen</h4>
                  </div>
                  <div class="modal-body">
                    <div id='hiddenname' style='display:none'></div>
                    <p id="edit-modal-content"></p>
                    <div class="form-group">
                        <label class="sr-only" for="startdate">Start</label>
                        <div class='input-group date'>
                            <input id='startdate_modal' type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="enddate">Eind</label>
                        <div class='input-group date'>
                            <input id='enddate_modal' type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                    <button id='saveedit' type="button" class="btn btn-primary">Wijzigen</button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10">
                        <?php echo $returnVal ?>
                        <table class='table table-striped table-bordered table-hover table-condensed'>
                            <thead>
                                <tr class='header-row'><th></th><th>Kenmerk</th><th>Taak</th><th>Startdatum</th><th>Dag</th><th>Starttijd</th><th>Einddatum</th><th>Dag</th><th>Eindtijd</th><th>Aantal vrijwilligers</th><th>Tijden wijzigen</th><th>Verwijderen</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC)) {
                                echo "<tr>";
                                echo "<td class='status'><span class='glyphicon'></span></td>";
                                echo "<td class='name'>".$row['name']."</td>";
                                echo "<td>".translate_task($row['task'])."</td>";
                                $tstartdate = DateTime::createFromFormat('Y-m-d H:i:s', $row['startdate']);
                                $tenddate = DateTime::createFromFormat('Y-m-d H:i:s', $row['enddate']);
                                echo "<td>".$tstartdate->format('d-m-Y')."</td>";
                                echo "<td>".$tstartdate->format('l')."</td>";
                                echo "<td>".$tstartdate->format('H:i:s')."</td>";
                                echo "<td>".$tenddate->format('d-m-Y')."</td>";
                                echo "<td>".$tenddate->format('l')."</td>";
                                echo "<td>".$tenddate->format('H:i:s')."</td>";
                                echo "<td class='hiddenstartdate' style='display:none'>".$tstartdate->format('D, d/m/Y G:i')."</td>";
                                echo "<td class='hiddenenddate' style='display:none'>".$tenddate->format('D, d/m/Y G:i')."</td>";
                                echo "<td class='input-group nrrequired'>
                                        <input type='text' class='form-control changenr' value='".$row['nrrequired']."'/>
                                    </td>";
                                echo "<td><a class='btn btn-info btn-sm btn-block editshift'>Tijden wijzigen</a></td>";
                                echo "<td><a class='btn btn-danger btn-sm btn-block removeshift'>Verwijderen</a></td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                        <div>
                            <h3>Shift toevoegen</h3>
                            <form class="" method="post" id="shift-form" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4).'?t='.$team;?>" target="_top">
                                <div class="form-group">
                                    <label class="sr-only" for="taskselect">Taak</label>
                                    <select class='form-control taskselect' id="taskselect" name='taskselect'>
                                        <?php
                                        foreach( $tasks as $t ) {
                                            echo "<option name='".$t."' value='".$t."' ".($t == $task ? " selected='selected'" : "").">".translate_task($t)."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="startdate">Start</label>
                                    <div class='input-group date' id='startdate'>
                                        <input type='text' class="form-control" name="startdate" value="<?php echo $startdate;?>">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="enddate">Einde</label>
                                    <div class='input-group date' id='enddate'>
                                        <input type='text' class="form-control" name="enddate" id="enddate_input" value="<?php echo $enddate;?>">
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
