<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_DISPLAY) != PERMISSION_DISPLAY ) {
    header('Location: oops.php');
}

$resultHTML="<table class='table table-striped table-bordered table-hover table-condensed'>";
$resultHTML.="<thead><tr class='header-row'>";
$resultHTML.="<th>Achternaam</th>";
$resultHTML.="<th>Voornaam</th>";
$resultHTML.="<th>Geboortedag</th>";
$resultHTML.="<th>Geslacht</th>";
$resultHTML.="<th>Woonplaats</th>";
$resultHTML.="<th>Email</th>";
$resultHTML.="<th>Telefoon</th>";
$resultHTML.="<th>Bekend door</th>";
$resultHTML.="<th>Motivatie</th>";
$resultHTML.="<th>Vraag</th>";
$resultHTML.="<th>Voorgaande Edities</th>";
$resultHTML.="<th>Partner</th>";
$resultHTML.="<th>Eerste keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Tweede keus</th>";
$resultHTML.="<th></th>";
$resultHTML.="<th></th>";
$resultHTML.="<th>Voorbereiding</th>";
$resultHTML.="<th>Aantal bezoeken</th>";
$resultHTML.="<th>Leeftijd</th>";
$resultHTML.="</tr></thead>";
$resultHTML.="<tbody>";

$cell_keys = ['lastname', 'firstname', 'birthdate', 'gender', 'city', 'email', 'phone', 'familiar', 'motivation', 'question', 'editions', 'partner', 'contrib0','type0','needs0', 'contrib1','type1','needs1', 'visits', 'preparations'];
$email = $firstname = $lastname = $gender = $contrib = $contribnr = $requestedage = $agetype = $visits = $visitstype = "";

$round = -1; //get round
$limit = 50;
$page = 0;

if( $user_permissions & PERMISSION_DISPLAY ) {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if( $mysqli->connect_errno ) {
        return false;
    }

    $filtersql = array();
    if( !empty($_GET['p'])) {
        $page = $mysqli->real_escape_string($_GET['p']);
    }
    
    $query = "SELECT COUNT(*) FROM person p join $current_table s on p.email = s.email";

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
    
    $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.familiar, s.motivation, s.familiar, p.editions, s.partner, s.contrib0_type, s.contrib0_desc, s.contrib0_need, s.contrib1_type, s.contrib1_desc, s.contrib1_need, s.preparations, p.visits
        FROM person p join $current_table s on p.email = s.email LIMIT %s OFFSET %s", 
        $mysqli->real_escape_string($limit), 
        $mysqli->real_escape_string($offset));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult === FALSE ) {
        echo $mysqli->error;
        echo $query;
        return;
    }
    $mysqli->close();
    while($row = mysqli_fetch_array($sqlresult,MYSQLI_NUM))
    {
        $resultHTML.="<tr>";
        $i = 0;
        foreach($row as $value) {
            $resultHTML .= "<td><div id='".$cell_keys[$i]."' class='table-cell'>".$value."</div></td>";
            $i++;
        }
        $age = (new DateTime($row[2]))->diff(new DateTime('now'))->y;
        $resultHTML .= "<td><div id='age' class='table-cell'>".$age."</div></td>";
        $resultHTML.= "</tr>";
    }
    $resultHTML.="</tbody></table>";
} else {
    $resultHTML="You do not have the necessary permissions to view this page";
}
?>
<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="page-container">
            <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-12 col-sm-9"> 
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
    </body>
</html>
