<?php session_start();
include "../functions.php";
include "../fields.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

// Assemble menu:
if( $user_info_permissions & PERMISSION_DISPLAY ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id ='showstats' href='index'>Main</a></li>";
    $menu_html .= "<li><a class='menulink' id='displaysignup' href='signups'>Inschrijvingen tonen</a></li>";
    $menu_html .= "<li><a class='menulink' id='displayraffle' href='displayraffle'>Loting tonen</a></li>";
    $menu_html .= "<li><a class='menulink' id='displaybuyers' href='buyers'>Verkochte tickets tonen</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_RAFFLE ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='raffle' href='raffle'>Loting</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_CALLER) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='callerview' href='callerview''>Bellen</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_EDIT ) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='editsignup' href='#''>Wijzigingen</a></li>";
    $menu_html .= "<li><a class='menulink' id='removesignup' href='#''>Verwijderen</a></li>";
    $menu_html .= "</ul>";
}
if( $user_info_permissions & PERMISSION_USER) {
    $menu_html .= "<ul class='nav nav-sidebar'>";
    $menu_html .= "<li><a class='menulink' id='usermanage' href='users''>Gebruikers<span class='sr-only'>(current)</span></a></li>";
    $menu_html .= "</ul>";
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
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="Teun van Dingenen">
        <link rel="icon" href="../favicon.ico">

        <title>Familiar Forest Dashboard</title>

        <!-- Bootstrap core CSS -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../css/main.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">Familiar Forest Festival</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
                <li><a class='menulink' href='logout.php'>Logout</a></li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                  <?php echo $menu_html ?>
                </div>
            </div>
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
                        </div>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
                </form>
            </div>
        </div>

    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="../js/vendor/bootstrap.min.js"></script>

        <script src="../js/plugins.js"></script>
        <script src="../js/main.js"></script>
        <script src="js/secure.js"></script>
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
