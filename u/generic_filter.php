<?php

include_once("../functions.php");
include_once("checklogin.php");

if( !isset($required_permissions) ) {
    exit;
}
if( !isset($request_for) ) {
    exit;
}

if( ($user_permissions & $required_permissions) != $required_permissions ) {
    exit;
}

$email = $firstname = $lastname = $gender = $contrib = $contribnr = $requestedage = $agetype = $visits = $visitstype = "";

$round = -1; //TODO get current round
$limit = 50;
$page = 0;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
$filtersql = array();
if( $_SERVER["REQUEST_METHOD"] == "GET") {
    if( !empty($_GET['p'])) {
        $page = $_GET['p'];
    }
    if( !empty($_GET["email"]) ) {
        $email = test_input($_GET["email"]);
        if( $email != "" ) {
            $filtersql[] = "p.email = '" . $mysqli->real_escape_string($email)."'";
        }
    }
    if( !empty($_GET["firstname"]) ) {
        $firstname = test_input($_GET["firstname"]);
        if( $firstname != "" ) {
            $filtersql[] = "p.firstname = '" . $mysqli->real_escape_string($firstname)."'";
        }
    }
    if( !empty($_GET["lastname"]) ) {
        $lastname = test_input($_GET["lastname"]);
        if( $lastname != "" ) {
            $filtersql[] = "p.lastname = '" . $mysqli->real_escape_string($lastname)."'";
        }
    }
    if( !empty($_GET["gender"]) ) {
        if( $_GET["gender"] == 'male') {
            $filtersql[] = "p.gender = 'male'";    
        } else if( $_GET["gender"] == 'female') {
            $filtersql[] = "p.gender = 'female'";
        }
        $gender = $_GET["gender"];
    }
    if( !empty($_GET["contrib"]) ) {
        $contrib = $_GET["contrib"];
        $contribnr = "contrib0";
        if( !empty($_GET["contribnr"])) {
            $contribnr = $_GET["contribnr"];
        }
        if( $contrib == '' || $contrib == 'all') {
            //nothing
        } else if( $contrib == 'act') {
            $filtersql[] = "s.".$contribnr."_type IN ('workshop', 'game', 'lecture', 'schmink', 'other', 'perform', 'install')";    
        } else {
            $filtersql[] = "s.".$contribnr."_type = '" . $mysqli->real_escape_string($contrib)."'";
        }
    }
    if( !empty($_GET["requestedage"]) && !empty($_GET["agetype"])) {
        $requestedage = test_input($_GET["requestedage"]);
        $agetype = test_input($_GET["agetype"]);
        $operator = "";
        if( $agetype == "min") { 
            $operator = ">=";
        } else if( $agetype == "max") {
            $operator = "<=";
        } else if( $agetype == "exact") {
            $operator = "=";
        }
        $filtersql[] = "FLOOR(DATEDIFF (NOW(), p.birthdate)/365) ".$operator." '".$mysqli->real_escape_string($requestedage)."'";
    }
    if( !empty($_GET["visits"])) {
        $visits = test_input($_GET["visits"]);
        $visitstype = test_input($_GET["visitstype"]);
        $operator = "";
        if( $visitstype == "min") { 
            $operator = ">=";
        } else if( $visitstype == "max") {
            $operator = "<=";
        } else if( $visitstype == "exact") {
            $operator = "=";
        }
        $filtersql[] = "p.visits ".$operator." '".$mysqli->real_escape_string($visits)."'";
    }
    if( !empty($_GET["round"])) {
        $roundstr = test_input($_GET["round"]);
        if( $roundstr == "all") {
            $round = -1;
        } else if ($roundstr == "first") {
            $round = 0;
        } else if ($roundstr == "second") {
            $round = 1;
        } else if ($roundstr == "third") {
            $round = 2;
        }
    } 
}
if( $round != -1 ) {
    $filtersql[] = sprintf("s.round = %s", $mysqli->real_escape_string($round));
}
$filterstr = "";
foreach($filtersql as $filter) {
    $filterstr .= " AND " . $filter;
}

$restriction = "1";
if( $request_for == 'raffle' ) {
    $restriction = 's.valid != 1 AND s.complete != 1';
} else if( $request_for == 'buyer' ) {
    $restriction = 's.complete = 1 ';
} else if( $request_for == 'signups' ) {
    $restriction = '1';
} else if( $request_for == 'showraffle' ) {
    $restriction = 's.valid = 1 AND s.complete != 1 AND s.share = "FULL"';
} else if( $request_for == 'caller' ) {
    $restriction = "s.valid = 1 AND s.called = 0 AND s.task != 'crew'";
} else if( $request_for == 'called_done' ) {
    $restriction = 's.valid = 1 AND s.called != 0';
} else if( $request_for == 'volunteers' ) {
    if(!isset($task) || $task = "" ) {
        $restriction = 's.complete = 1';
    } else {
        $restriction = "s.complete = 1 and s.task = '".$mysqli->real_escape_string($task)."'";
    }
} else {
    exit;
}

$query = "SELECT COUNT(*) FROM person p join $current_table s on p.email = s.email
        WHERE $restriction" . $filterstr;

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
if( $mysqli->connect_errno ) {
    return false;
} else {
    $query = sprintf("SELECT p.lastname, p.firstname, p.birthdate, p.gender, p.city, p.email, p.phone, p.familiar, p.visits, p.editions, s.motivation, s.question, s.partner, s.called, s.rafflecode, s.contrib0_type, s.contrib0_desc, s.contrib0_need, s.contrib1_type, s.contrib1_desc, s.contrib1_need, s.preparations, t.task, t.startdate, t.enddate, s.ticket
        FROM person p join $current_table s on s.email = p.email left join shifts t on s.task = t.name 
        WHERE $restriction" . $filterstr . " LIMIT %s OFFSET %s", $mysqli->real_escape_string($limit), $mysqli->real_escape_string($offset));
    $sqlresult = $mysqli->query($query);
    if( $sqlresult === FALSE ) {
         echo $mysqli->error;
    }
}
$mysqli->close();

$url = substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);
$first = true;
if (!empty($_GET)) {
    foreach ($_GET as $parameter => $value) {
        if( $parameter != 'p' && $parameter != 'show_names' ) {
            $url .= ($first ? "?" : "&") . $parameter . "=" . urlencode($value);
            $first = false;
        }
    }
}

?>
<a id="formtoggle" class="btn btn-info btn-sm btn-block" role="button" data-toggle="collapse" data-target="#form-panel">Filteren <span class='glyphicon glyphicon-chevron-right'></span></a>
<div id="form-panel" class="collapse form-panel">
    <div class="panel panel-default">
        <div id="formcontent" class="panel-body">
            <form id="user-form" method="get" action=
                <?php 
                    $link = '"'.substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4).'?';
                    $link .= !empty($_GET['p']) ? "p=".$_GET['p'] : "";
                    $link .= !empty($_GET['show_names']) ? "show_names=".$_GET['show_names'] : '';
                    echo $link.'"';
                ?>
                target="_top">
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
                    <label class="col-sm-2">Geslacht</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="both" value="both" <?php if($gender == "both") echo( "checked"); ?> >
                                Beide
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="male" value="male" <?php if($gender == "male") echo( "checked"); ?>>
                                Jongeman
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="female" value="female" <?php if($gender == "female") echo( "checked"); ?> >
                                Jongedame
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="requestedage" class="col-sm-2 form-control-label">Leeftijd</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="agetype" id="agetype">
                            <option value="min" <?= $agetype == 'min' ? ' selected="selected"' : '';?>>Minimaal</option>
                            <option value="max" <?= $agetype == 'max' ? ' selected="selected"' : '';?>>Maximaal</option>
                            <option value="exact" <?= $agetype == 'exact' ? ' selected="selected"' : '';?>>Precies</option>
                        </select>
                        <input class="form-control" type="text" id="requestedage" placeholder="Leeftijd" value="<?php echo $requestedage;?>" name="requestedage">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="contrib" class="col-sm-2 form-control-label">Bijdrage</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="contrib" id="contrib">
                            <option value="all" <?= $contrib == 'all' ? ' selected="selected"' : '';?>>Alles</option>
                            <option value="iv" <?= $contrib == 'iv' ? ' selected="selected"' : '';?>>Interieur verzorging</option>
                            <option value="bar" <?= $contrib == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                            <option value="keuken" <?= $contrib == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                            <option value="act" <?= $contrib == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                            <option value="afb" <?= $contrib == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            <option value="opb" <?= $contrib == 'opb' ? ' selected="selected"' : '';?>>Opbouw</option>
                        </select>
                        <div class="radio">
                            <label>
                                <input type="radio" name="contribnr" id="contrib0" value="contrib0" <?php if($contribnr == "contrib0") echo( "checked"); ?>>
                                Eerste keus
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="contribnr" id="contrib1" value="contrib1" <?php if($contribnr == "contrib1") echo( "checked"); ?> >
                                Tweede keus
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="visits" class="col-sm-2 form-control-label">Aantal Bezoeken</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="visitstype" id="visitstype">
                            <option value="min" <?= $visitstype == 'min' ? ' selected="selected"' : '';?>>Minimaal</option>
                            <option value="max" <?= $visitstype == 'max' ? ' selected="selected"' : '';?>>Maximaal</option>
                            <option value="exact" <?= $visitstype == 'exact' ? ' selected="selected"' : '';?>>Precies</option>
                        </select>
                        <input class="form-control" type="text" id="visits" placeholder="Bezoeken" value="<?php echo $visits;?>" name="visits">
                    </div>
                </div>
                <!-- TODO only add on raffle, signups -->
                <div class="form-group row">
                    <label for="contrib" class="col-sm-2 form-control-label">Ronde</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="round" value="all" <?php if($round == -1) echo( "checked"); ?>>
                                Alles
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="round" value="first" <?php if($round == 0) echo( "checked"); ?>>
                                Eerste Ronde
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="round" value="second" <?php if($round == 1) echo( "checked"); ?> >
                                Tweede Ronde
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="round" value="third" <?php if($round == 2) echo( "checked"); ?> >
                                Derde Ronde
                            </label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-primary" type="submit">Filteren</button>
            </form>
            <div><?=$entries ?> Resultaten</div>
        </div>
    </div>
</div>
