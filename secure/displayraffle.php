<?php session_start();
include "../functions.php";
include "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: ../login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions & PERMISSION_DISPLAY != PERMISSION_DISPLAY ) {
    return false;
}

// Assemble menu:
$menu_html = get_menu_html();

$statistic_string = "";
$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Uitloten</th>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Code</th>";
$resultHTML.="<th>Status</th>";
$resultHTML.="</th></thead>";

//Statistics

$email = $firstname = $lastname = $rafflecode = $notcontacted = "";
$filtersql = array();
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
        if( $email != "" ) {
            $filtersql[] = "p.email = '" . $mysqli->real_escape_string($email)."'";
        }
    }
    if( !empty($_POST["firstname"]) ) {
        $firstname = test_input($_POST["firstname"]);
        if( $firstname != "" ) {
            $filtersql[] = "p.firstname = '" . $mysqli->real_escape_string($firstname)."'";
        }
    }
    if( !empty($_POST["lastname"]) ) {
        $lastname = test_input($_POST["lastname"]);
        if( $lastname != "" ) {
            $filtersql[] = "p.lastname = '" . $mysqli->real_escape_string($lastname)."'";
        }
    }
    if( !empty($_POST["rafflecode"]) ) {
        $rafflecode = test_input($_POST["rafflecode"]);
        if( $rafflecode != "" ) {
            $filtersql[] = "r.code = '" . $mysqli->real_escape_string($rafflecode)."'";
        }
    }
    if( !empty($_POST["notcontacted"]) ) {
        $notcontacted = test_input($_POST["notcontacted"]);
        if( $notcontacted == 'Y') {
            $filtersql[] = "r.called != 1";
        }
    }
}
$filterstr = "1";
foreach($filtersql as $filter) {
    $filterstr .= " AND " . $filter;
}

$sqlresult = "";
$query = "SELECT p.firstname, p.lastname, r.email, p.phone, r.code, r.called FROM person p join raffle r on r.email = p.email WHERE ".$filterstr;

if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}
$mysqli->close();

while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
{
    $resultHTML.="<tr>";
    $resultHTML.="<td>";
    if( $user_info_permissions & PERMISSION_RAFFLE ) {
        $resultHTML.="<button class='btn btn-sm unraffle' type='button'>Uitloten</button>";
    }
    $resultHTML.="</td>";
    foreach($row as $key=>$value) {
        if( $key == 2 ) {
            $resultHTML.= "<td><div id='email' class='table-cell'>" . $value . "</div></td>";
        } else if ($key == 4 ) {
            $resultHTML.= "<td><div id='code' class='table-cell'>" . $value . "</div></td>";
        } else if( $key == 5 ) {
            $Bvalue = '';
            if( $value == 0 ) $Bvalue = 'Nee';
            else if( $value == 1 ) $Bvalue = 'Ja';
            else if( $value == 2 ) $Bvalue = 'In behandeling'; 
            else if( $value == 3 ) $Bvalue = 'Niet opgenomen'; 
            $resultHTML.= "<td><div class='table-cell'>" . $Bvalue . "</div></td>";
        } else {
            $resultHTML.= "<td><div class='table-cell'>" . $value . "</div></td>";
        }
    }
    $resultHTML.= "</tr>";
}

$resultHTML.="</table>";

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
                <div id='statcontent' class="container-fluid">

                </div>
                <form id="user-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 form-control-label">Email</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="firstname" class="col-sm-2 form-control-label">Voornaam</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lastname" class="col-sm-2 form-control-label">Achternaam</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rafflecode" class="col-sm-2 form-control-label">Loting Code</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="rafflecode" placeholder="Loting Code" value="<?php echo $rafflecode;?>" name="rafflecode">
                        </div>
                    </div>
                    <div class = "form-group row">
                        <label for = "notcontacted" class="col-sm-2 form-control-label">Gecontact?</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notcontacted" id="notcontacted" value="Y" <?php if($notcontacted == 'Y') echo( "checked"); ?> >
                                    Nog niet bereikt
                                </label>
                            </div>
                        </div>
                    <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
                </form>

                <div><?php echo $resultHTML ?></div>
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
        <script src="js/removeraffle.js"></script>
    </body>
</html>

