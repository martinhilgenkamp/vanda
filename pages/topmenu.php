<ul class="horizontal_menu">
	<a href="index.php" class="active"><li>Binnenkomst</li></a>
    <a href="index.php?page=task" ><li>Taken<ul class="submenu">
            <a href="index.php?page=task&view=51"><li>Nummer 51</li></a>
            <a href="index.php?page=task&view=63"><li>Nummer 63</li></a>
        </ul>
    </li></a>
    <a href="index.php?page=productie"><li>Productie
        <ul class="submenu">
          <a href="index.php?page=stansen"><li>Stansen</li></a>
          <a href="index.php?page=machines"><li>Registreren Machines</li></a>
          <a href="index.php?page=machinetable"><li>Overzicht Machines</li></a>
        </ul>
    </li></a>
   	<li>Overzichten
    	<ul class="submenu">
            <a href="index.php?page=summary&period=day"><li>Dag overzicht</li></a>
            <a href="index.php?page=summary&period=week"><li>Week Overzicht</li></a>
            <a href="index.php?page=summary&period=month"><li>Maand Overzicht</li></a>
            <a href="index.php?page=summary&period=custom"><li>Aangepast</li></a>
            <a href="index.php?page=summary"><li>Registraties</li></a>
            <a href="index.php?page=machinetable"><li>Overzicht Machines</li></a>
        </ul>
    </li>
    <a href="index.php?page=voorraad"><li>Voorraad
    	<ul class="submenu">
        	<a href="index.php?page=voorraad"><li>Voorraad</li></a>
            <a href="index.php?page=ship"><li>Leveren</li></a>
            <a href="index.php?page=zendingen"><li>Zendingen</li></a>
        </ul>
    </li></a>
    <li>Rollen
    	<ul class="submenu">
    		<a href="index.php?page=rollen"><li>Inboeken</li></a>	
    		<a href="index.php?page=rolltable"><li>Rollen overzicht</li></a>
    		<a href="index.php?page=roll-shipments"><li>Zending overzicht</li></a>
		</ul>
	</li>
    <li>Programma
    	<ul class="submenu">
        <a href="index.php?page=verzondenmail"><li>Verstuurde Tansportmail</li></a>
            <a href="index.php?page=tasksummary"><li>Taak Overzicht</li></a>
            <?php if($user->level){
                echo "<a href=\"index.php?page=options\"><li>Opties</li></a>";
            }?>
            <a href="index.php?page=logout"><li>Uitloggen</li></a>
        </ul>
	</li>
</ul>
