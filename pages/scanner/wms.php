<?php
require_once("../../inc/class/class.option.php");
require_once("../../inc/class/class.user.php");
$om = new OptionManager();
$um = new UserManager();

$options = $om->getAllOptions()[0];
$user_loggedin = $um->checkLogin("");


//Check if the user is signed in
//If user is nog signed in relay to login page
if(is_string($user_loggedin) || $user_loggedin === false) {
   header('Location: ../loginscanner.php?page=/scanner/wms.php');
   exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <!-- Define viewport for handheld scanners !-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
        <title>Vanda Carpets - Process Management</title>

        <!-- Iinclude Stylesheet !-->
         <link rel="stylesheet" href="css/style.css">
          <link rel="stylesheet" href="css/wms.css">

        <!-- Adding required scripts !-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="inc/script/wms.js"></script>
    </head>

    <body>
        <div id="result"></div>
        <div class="switch">
            <div id="checkin" class="button button_selected_green"><span>inboeken</span></div>
            <div id="checkout" class="button"><span>Uitscannen</span></div>
        </div>
        <div id="shiplist">
            <!-- inventory in form !-->
            <form class="inventoryForm" id="inventory_in" name="inventory_in" method="post">	
                <ul class="mobilelist" id="inventory_in_list">
                    <li><label for="barcode_in">Barcode: </label><input type="text" id="barcode_in" name="barcode_in" placeholder="Barcode"/></li>
                    <li><label for="location_in">Locatie: </label><input type="text" id="location_in" name="location_in" placeholder="Locatie" disabled/></li>
                    <li><label for="quality_in">Kwaliteit: </label><input type="text" id="quality_in" name="quality_in"  placeholder="Kwaliteit" disabled/></li>
                    <li><label for="length_in">Lengte: </label><input type="text" id="length_in" name="length_in"  placeholder="Lengte" disabled/></li>
                    <li><label for="width_in">Breedte: </label><input type="text" id="width_in" name="width_in"  placeholder="Breedte" value="4" disabled/></li>
                    <li><button type="submit_in">Submit</button><span> </span><button type="reset">Herstel</button></li>
                </ul>
            </form>

            <!-- inventory out form !-->
            <form class="inventoryForm" id="inventory_out" name="inventory_out" method="post" style="display: none;">	
                <ul class="mobilelist" id="inventory_out_list">
                    <li><label for="barcode_out">Barcode: </label><input type="text" id="barcode_out" name="barcode_out" placeholder="Barcode"/></li>
                    <li><label for="location_out">Locatie: </label><input type="text" id="location_out" name="location_out" placeholder="location" disabled/></li>
                    <li><button type="submit_out">Submit</button><span> </span><button type="reset">Herstel</button></li>
                </ul>
            </form>
            <div id="results">
                <table>
                </table>
            </div>
        </div>
    </body>
</html>
