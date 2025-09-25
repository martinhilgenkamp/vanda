<?php
require_once("../../inc/class/class.option.php");
$om = new OptionManager();

$options = $om->getAllOptions()[0];
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
        <link rel="stylesheet" href="inc/css/style.css">

        <!-- Adding required scripts !-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="inc/script/wms.js"></script>
    </head>

    <body>
        <h1>Magazijnbeheer</h1>
        <div id="result"></div>
        <div class="switch">
            <div id="checkin" class="button button_selected"><span>inboeken</span></div>
            <div id="checkout" class="button"><span>Uitscannen</span></div>
        </div>

        <div>
            <!-- inventory in form !-->
            <form id="inventory_in" name="inventory_in" method="post">	
                <ul class="mobilelist" id="inventory_in_list">
                    <li><label for="barcode">Barcode: </label><input type="text" id="barcode" name="barcode" placeholder="Barcode"/></li>
                    <li><label for="location">Locatie: </label><input type="text" id="location" name="location" placeholder="location" disabled/></li>
                    <li><label for="kwaliteit">Kwaliteit: </label><input type="text" id="quality" name="quality"  placeholder="quality" disabled/></li>
                    <li><button type="submit">Submit</button><span> </span><button type="reset">Herstel</button></li>
                </ul>
            </form>

             <!-- inventory out form !-->

        </div>
    </body>
</html>
