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
        <div class="switch">
            <div id="checkin" class="button button_selected"><span>inboeken</span></div>
            <div id="checkout" class="button"><span>Uitscannen</span></div>
        </div>

        <div>
            <!-- inventory in form !-->
            <form id="inventort_in" name="inventory_in" method="post">	
                <ul class="mobilelist" id="inventory_in">
                    <li><label for="barcode">Barcode: </label><input type="text" id="barcode" name="barcode" placeholder="Barcode"/></li>
                    <li><label for="locatie">Locatie: </label><input type="hidden" id="locatie" name="locatie" placeholder="locatie"/></li>
                    <li><label for="kwaliteit">Kwaliteit: </label><input type="text" id="kwaliteit" name="kwaliteit"  placeholder="kwaliteit" /></li>
                    <li><button type="submit">Submit</button><span> </span><button type="reset">Herstel</button></li>
                </ul>
            </form>

             <!-- inventory out form !-->
            <form id="inventort_out" name="inventort_out" method="post" style="display: none;">	
                <ul class="mobilelist" id="inventort_out">
                    <li><label for="barcode">Barcode: </label><input type="text" id="barcode" name="barcode" placeholder="Barcode"/></li>
                    <li><label for="kwaliteit">Kwaliteit: </label><input type="text" id="kwaliteit" name="kwaliteit"  placeholder="kwaliteit" /></li>
                    <li><button type="submit">Submit</button><span> </span><button type="reset">Herstel</button></li>
                </ul>
            </form>
        </div>
    </body>
</html>
