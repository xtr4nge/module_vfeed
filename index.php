<? 
/*
    Copyright (C) 2013-2014 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWifi</title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../../../style.css" />

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include "../menu.php"; ?>

<br>

<?

include "../../config/config.php";
include "_info_.php";
include "../../login_check.php";
include "../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_POST["service"], "msg.php", $regex_extra);
    regex_standard($_POST["vfeed_cve"], "msg.php", $regex_extra);
    regex_standard($_POST["vfeed_type"], "msg.php", $regex_extra);
    regex_standard($_POST["vfeed_search"], "msg.php", $regex_extra);
}

$action = $_GET["action"];
$service = $_POST["service"];

?>

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;version <?=$mod_version?><br>
    <? 
    if (file_exists( $bin_vfeed )) { 
        echo "&nbsp;&nbsp; $mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp; $mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>

    <?
    $exec = exec("cd includes/vFeed/; ./vfeedcli.py get_latest |grep -iEe '^0 total added new'");
    if ($exec != "") { 
        echo "&nbsp;&nbsp; $mod_alias <font style='color:lime'>updated</font><br>";
    } else {
        echo "&nbsp;&nbsp; $mod_alias <a href='includes/module_action.php?install=update_$mod_name' style='color:red'>update</a><br>";
    } 
    ?>
    
</div>

<br>

<style>
    .div_91 {
        color: red;
        padding-left: 30px;
    }
    
    .div_93 {
        c-olor: yellow;
        padding-left: 20px;
    }
    
    .div_94 {
        c-olor: blue;
        p-adding-left: 10px;
    }
    
</style>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>


<div id="body" style="display:none;">

    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">About</a></li>
        </ul>
        
        <!-- OUTPUT -->

        <div id="result-1" class="history">
            
            <?

            $vfeed_cve = $_POST["vfeed_cve"];
            $vfeed_type = $_POST["vfeed_type"];
            $vfeed_search = $_POST["vfeed_search"];
            
            if ($vfeed_cve == "") {
                $vfeed_cve = "";
                //$vfeed_cve = "CVE-2014-0160";
            }
            
            if ($vfeed_type == "") {
                $vfeed_type = "";
                //$vfeed_type = "exploitation";
            }
            
            if ($vfeed_search == "") {
                $vfeed_search = "";
                //$vfeed_search = "get_msf";
            }
            
            //$bin_cd = "cd";
            //$bin_python = "/usr/bin/python";
            
            function clean($line) {
                
                if ( strpos($line, "[91m") !== false) {
                    $line = str_replace("[91m", "<div class='div_91'>", $line);                    
                } else if ( strpos($line, "[93m") !== false) {
                    $line = str_replace("[93m", "<div class='div_93'>", $line);                    
                } else if ( strpos($line, "[94m") !== false) {
                    $line = str_replace("[94m", "<div class='div_94'>", $line);                    
                } else {
                    $line = "<div>" . $line;          
                }
                
                if ( strpos($line, "[0m") !== false) {
                    $line = str_replace("[0m", "", $line);                    
                }
                
                $line = $line . "</div>";
                
                $line = preg_replace( '/[^[:print:]]/', '',$line);
                
                $line = str_replace("-------", "&nbsp;", $line);
                
                return trim($line);
            }
            
            function show($output) {
                for ($i=0; $i < count($output); $i++) {
                    $line = clean($output[$i]);
                    //if ($line != "xxx")
                    echo clean($output[$i]) . "";
                }
            }
            
            //print_r($output);
            
            ?>
            <form action="index.php" method="POST" autocomplete="off">
                search:
                <input class="input" name="vfeed_cve" value="<?=$vfeed_cve?>">
                
                <select id="first-choice" class="input" name="vfeed_type">
                    <option selected value="base">-</option>
                    <option value="information">Information</option>
                    <option value="references">References</option>
                    <option value="risk">Risk</option>
                    <option value="patchs">Patchs</option>
                    <option value="assessment">Assessment</option>
                    <option value="defense">Defense</option>
                    <option value="exploitation">Exploitation</option>
                </select>
                
                <select id="second-choice" class="input" name="vfeed_search">
                    <option>-</option>
                </select>
                
                <input class="input" type="submit" value="submit"> <font style="color: grey">(example: CVE-2014-0160)</font>
                    
            </form>
            
            <? if ($vfeed_search != "") { ?>
            <br>
            
	    <div class="rounded-top" align="center"> <?=strtoupper($vfeed_cve)?> </div>
	    <div class="rounded-bottom general" style="width: auto; font-family: courier; font-size: 11px;">
                
                <?
                
                unset($output);
                $exec = "$bin_cd includes/vFeed/; $bin_python vfeedcli.py search $vfeed_cve";
                exec($exec, $output);
                show($output);
                
                ?>
                
	    </div>
            
            <div class="rounded-top" align="center"> <?=strtoupper($vfeed_search)?> </div>
	    <div class="rounded-bottom general" style="width: auto; font-family: courier; font-size: 11px;">
                
                <?
                
                unset($output);
                $exec = "$bin_cd includes/vFeed/; $bin_python vfeedcli.py $vfeed_search $vfeed_cve";
                //echo "DEBUG: " . $exec . "<br>";
                exec($exec, $output);
                show($output);
                
                ?>
                
	    </div>
            <? } ?>
        </div>
	
	<!-- ABOUT -->

        <div id="result-2" class="history">
	    <? include "includes/about.php"; ?>
	</div>
	
	<!-- END ABOUT -->
        
    </div>

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <script>

    $('#loading').hide();

    </script>

    <?
    if ($_GET["tab"] == 1) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 0 });";
        echo "</script>";
    } else if ($_GET["tab"] == 2) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 1 });";
        echo "</script>";
    } else if ($_GET["tab"] == 3) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 3 });";
        echo "</script>";
    } else if ($_GET["tab"] == 4) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 4 });";
        echo "</script>";
    } 
    ?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

<script>
    
    $("#first-choice").change(function() {

	var $dropdown = $(this);

	$.getJSON("includes/data.json", function(data) {
	
		var key = $dropdown.val();
		var vals = [];
							
		switch(key) {
			case 'information':
				vals = data.information.split(",");
				break;
                        case 'references':
				vals = data.references.split(",");
				break;
			case 'risk':
				vals = data.risk.split(",");
				break;
                        case 'patchs':
				vals = data.patchs.split(",");
				break;
                        case 'assessment':
				vals = data.assessment.split(",");
				break;
                        case 'defense':
				vals = data.defense.split(",");
				break;
                        case 'exploitation':
				vals = data.exploitation.split(",");
				break;
			case 'base':
				vals = ['-'];
		}
		
		var $secondChoice = $("#second-choice");
		$secondChoice.empty();
		$.each(vals, function(index, value) {
                    if (value == '<?=$vfeed_search?>') {
                        $secondChoice.append("<option value='" + value + "' selected>" + value + "</option>");
                    } else {
			$secondChoice.append("<option value='" + value + "'>" + value + "</option>");
                    }
		});

	});
    });
    
</script>

<script>

    $(document).ready(function() {
        //$("#first-choice option[value='-']").attr('selected', 'selected');
        $("#first-choice option[value='<?=$vfeed_type?>']").attr('selected', 'selected');
    });
    
    $("#first-choice").change(function () { 
        var str = ""; 
        str = $(this).find(":selected").text(); 

        $(".out").text(str);
        //$("#second-choice option[value='<?=$vfeed_search?>']").attr('selected', 'selected');
    }).trigger('change');
    
</script>

</body>
</html>
