<?php session_start();
include "../functions.php";
include "../fields.php";

$user_email = $user_firstname = $user_permissions = "";

if(!isset($_SESSION['email'])) {
    header('Location: ../login');
} else {
    $user_email = $_SESSION['email'];
}
if(!isset($_SESSION['firstname'])) {
    header('Location: ../login');
} else {
    $user_firstname = $_SESSION['firstname'];
}
if(!isset($_SESSION['permissions'])) {
    header('Location: ../login');
} else {
    $user_permissions = $_SESSION['permissions'];
}

if( $user_permissions & PERMISSION_USER != PERMISSION_USER ) {
    header('Location: oops.php');
}

$returnVal = "";
$fullname = $username = $password = $repeat = "";
$permissions = [];
$permission_code = 0;

$usertable="<table class='table table-striped table-bordered table-hover table-condensed'>";
$usertable.="<thead><tr class='header-row'>";
$usertable.="<th>Verwijderen</th>";
$usertable.="<th>Name</th>";
$usertable.="<th>Gebruikersnaam</th>";
$usertable.="<th>Permissions</th>";
$usertable.="</th></thead>";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if( $mysqli->connect_errno ) {
    addError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ");
} if ( $returnVal == "" ) {
    $row = array();
    $query = "SELECT name, username, permissions FROM `$db_table_users` WHERE 1";
    $results = $mysqli->query($query);
    if( $results === FALSE ) {
        addError("Failed to get users<br>" . $mysqli->error);
    } else {
        while($row = mysqli_fetch_array($results,MYSQLI_ASSOC)) {
            $usertable.="<tr>";
            $usertable.="<td>";
            if( $row['username'] != "admin" ) {
                $usertable.="<button class='btn btn-sm removeuser' type='button'>Verwijderen</button>";
            }
            foreach($row as $key=>$value) {
                $usertable.= "<td><div class='table-cell' id='".$key."''>" . $value . "</div></td>";
            }
            $usertable.="</td>";
            $usertable .="</tr>";
        }
    }
}
$mysqli->close();

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["fullname"]) ) {
        $fullname = test_input($_POST["fullname"]);
    } else {
        $fullname = "";
        addError("Je hebt je naam niet opgegeven.");
    }
    if( !empty($_POST["username"]) ) {
        $username = test_input($_POST["username"]);
    } else {
        $username = "";
        addError("Je hebt je gebruikersnaam niet opgegeven.");
    }
    if( !empty($_POST["password"]) ) {
        $password = test_input($_POST["password"]);
    } else {
        $password = "";
        addError("Je hebt je wachtwoord niet opgegeven.");
    }
    if( !empty($_POST["repeat"]) ) {
        $repeat = test_input($_POST["repeat"]);
    } else {
        $repeat = "";
        addError("Je hebt je herhaling niet opgegeven.");
    }

    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : array();
    foreach($permissions as $permission) {
        $permission_str = test_input($permission);
        if( $permission_str == "display") {
            $permission_code |= PERMISSION_DISPLAY;
        } else if( $permission_str == "edit") {
            $permission_code |= PERMISSION_EDIT;
        } else if( $permission_str == "raffle") {
            $permission_code |= PERMISSION_RAFFLE;
        } else if( $permission_str == "user") {
            $permission_code |= PERMISSION_USER;
        } else if( $permission_str == "caller") {
            $permission_code |= PERMISSION_CALLER;
        } else if( $permission_str == "acts") {
            $permission_code |= PERMISSION_ACTS;
        } else if( $permission_str == "volunteers") {
            $permission_code |= PERMISSION_VOLUNTEERS;
        } else if( $permission_str == 'buyers') {
            $permission_code |= PERMISSION_BUYERS;
        } else if( $permission_str |= "nacht") {
            $permission_code |= PERMISSION_NACHT;
        }
    }
    if( $repeat != $password ) {
        addError( "De opgegeven wachtwoorden komen niet overeen");
    }
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        addError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error. " ");
    } else if( $returnVal == "" ) {
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_add_query = sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`) VALUES ('%s', '%s','%s','%s')",
        $db_table_users,
        $db_user_username,
        $db_user_password,
        $db_user_name,
        $db_user_permissions,
        $mysqli->real_escape_string($username), 
        $mysqli->real_escape_string($pw_hash), 
        $mysqli->real_escape_string($fullname), 
        $mysqli->real_escape_string($permission_code)
        );
        if( $mysqli->query($user_add_query)) {
            $returnVal = '<div class="alert alert-success" role="alert">Gebruiker toegevoegd</div>';
        } else {
            addError("Gebruiker niet toegevoegd " . $mysqli->error);
        }
    }
    $mysqli->close();
}

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert">' . $value . '</div>';
}

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>

        <div class="container-fluid">
            <?php include("navigation.php"); ?>
            <div id="content" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php echo $usertable ?>
                <?php echo $returnVal ?>
                <form id="user-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                    <div class="form-group row">
                        <label for="fullname" class="col-sm-2 form-control-label">Naam</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="fullname" placeholder="Naam" value="<?php echo $fullname;?>" name="fullname" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="username" class="col-sm-2 form-control-label">Username</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="username" placeholder="Username" value="<?php echo $username;?>" name="username" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 form-control-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" id="password" class="form-control" placeholder="Paswoord" name="password" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password-repeat" class="col-sm-2 form-control-label">Repeat Password</label>
                        <div class="col-sm-10">
                            <input type="password" id="repeat" class="form-control" placeholder="Paswoord Herhaling" name="repeat" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Permissions</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="display" value="display" <?php if(in_array("display", $permissions)) echo( "checked"); ?> > Tonen
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="raffle" value="raffle" <?php if(in_array("raffle", $permissions)) echo( "checked"); ?> > Loten
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="edit" value="edit" <?php if(in_array("edit", $permissions)) echo( "checked"); ?> > Wijzigen
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="user" value="user" <?php if(in_array("user", $permissions)) echo( "checked"); ?> > Gebruikers
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="caller" value="caller" <?php if(in_array("caller", $permissions)) echo( "checked"); ?> > Bellen
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="volunteers" value="volunteers" <?php if(in_array("volunteers", $permissions)) echo( "checked"); ?> > Vrijwilligers
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="acts" value="acts" <?php if(in_array("acts", $permissions)) echo( "checked"); ?> > Acts
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="buyers" value="buyers" <?php if(in_array("buyers", $permissions)) echo( "checked"); ?> > Verkochte tickets
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="permissions[]" id="nacht" value="nacht" <?php if(in_array("nacht", $permissions)) echo( "checked"); ?> > Nachtprogramma
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
                </form>
            </div>
        </div>

    	<?php include("default-js.html"); ?>
        <script>
            $('.removeuser').click(function() {
                var remove = $(this).closest('tr').children().children('#username').text();
                $.post("removeUser.php", {"remove":remove}, function(response){
                });
                location.reload();
            });
        </script>
    </body>
</html>
