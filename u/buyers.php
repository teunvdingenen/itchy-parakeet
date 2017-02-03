<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_DISPLAY) != PERMISSION_DISPLAY ) {
    header('Location: oops.php');
}

$statistic_string = "";
$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Motivatie</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Code</th>";
$resultHTML.="<th>Transactie ID</th>";
$resultHTML.="<th>Ticketlink</th>";
$resultHTML.="</th></thead>";

$limit = 50;
$page = 0;
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
$email = $firstname = $lastname = $rafflecode = $transactionid = "";
$filtersql = array();

if( !empty($_GET['p'])) {
    $page = $mysqli->real_escape_string($_GET['p']);
}

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
            $filtersql[] = "b.code = '" . $mysqli->real_escape_string($rafflecode)."'";
        }
    }
    if( !empty($_POST["transactionid"]) ) {
        $transactionid = test_input($_POST["transactionid"]);
        if( $transactionid != "" ) {
            $filtersql[] = "b.id = '" . $mysqli->real_escape_string($transactionid)."'";
        }
    }
}

$filterstr = "b.complete = 1";
foreach($filtersql as $filter) {
    $filterstr .= " AND " . $filter;
}

$query = "SELECT COUNT(*) FROM person p join buyer b on p.email = b.email WHERE " . $filterstr;

$sqlresult = $mysqli->query($query);
if( $sqlresult === FALSE ) {
    echo $mysqli->error;
}
$row = mysqli_fetch_array($sqlresult, MYSQLI_NUM);
$entries = $row[0];

$pages = $entries / $limit;
if( $page >= $pages ) {
        $page = $pages - 1;
    }
    if( $page < 0 ) {
        $page = 0;
    }
$offset = $page * $limit;

$sqlresult = "";
$query = sprintf("SELECT p.firstname, p.lastname, p.motivation, p.email, p.phone, b.code, b.id, b.ticket FROM person p join buyer b on p.email = b.email WHERE " . $filterstr. " LIMIT %s OFFSET %s", $mysqli->real_escape_string($limit), $mysqli->real_escape_string($offset));
        $sqlresult = $mysqli->query($query);

if( $mysqli->connect_errno ) {
    return false;
} else {
    $sqlresult = $mysqli->query($query);
}

if( $sqlresult === FALSE ) {
    echo $mysqli->error;
}
$mysqli->close();


while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
{
    $resultHTML.="<tr>";
    foreach($row as $key=>$value) {
        if( $key == 'ticket' ) {
            $resultHTML.= "<td><div id='".$key."' class='table-cell'><a href=http://stichtingfamiliarforest.nl/ticket.php?ticket=" . $value . ">link</a></div></td>";
        } else {
            $resultHTML.= "<td><div id='".$key."' class='table-cell'>" . $value . "</div></td>";
        }

    }
    $resultHTML.= "</tr>";
}

$resultHTML.="</table>";

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <?php include("header.php"); ?>

        <div class="page-container">
            <?php include("header.php"); ?>
            
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">

                <?php include("navigation.php");?>

                <div class="col-xs-12 col-sm-9"> 
                <a id="togglebutton" class="btn btn-info btn-sm btn-block" role="button" data-toggle="collapse" data-target="#stat-panel"><span class='glyphicon glyphicon-refresh spinning'></span></a>
                <div class="row">
                    <div id="stat-panel" class="collapse stat-panel">
                        <div class="panel panel-default">
                            <div id="statcontent" class="panel-body">
                                
                            </div>
                        </div>
                    </div>
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
                        <label for="transactionid" class="col-sm-2 form-control-label">Transactie ID</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="transactionid" placeholder="Transactie ID" value="<?php echo $transactionid;?>" name="transactionid">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rafflecode" class="col-sm-2 form-control-label">Loting Code</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="rafflecode" placeholder="Loting Code" value="<?php echo $rafflecode;?>" name="rafflecode">
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
                </form>
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href=<?php echo "?p=".($page-1) ?> aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                            for($i = 0; $i < $pages; $i++ ) {
                                printf("<li><a href='?p=%s''>%s</a></li>",$i,$i+1);
                            }
                        ?>
                        <li>
                            <a href=<?php echo "?p=".($page+1) ?> aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div style='margin-top: 5px;'>
                    <?php echo $resultHTML ?>
                </div>
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href=<?php echo "?p=".($page-1) ?> aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                            for($i = 0; $i < $pages; $i++ ) {
                                printf("<li><a href='?p=%s''>%s</a></li>",$i,$i+1);
                            }
                        ?>
                        <li>
                            <a href=<?php echo "?p=".($page+1) ?> aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            </div>
        </div>
        </div>

        <?php include("default-js.html"); ?>
        <script>
        $(document).ready(function() {
            $.post("signupstats.php", {"type":"buyer"}, function(response){
                $("#statcontent").html($(response).find('table'));
                $("#togglebutton").html("Statistieken <i class='glyphicon glyphicon-chevron-right'>");
            });
            $('#togglebutton').on('click', function(){
                $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
            });
        });
        </script>
    </body>
</html>
