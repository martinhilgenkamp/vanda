<nav class="main-nav">
  <div class="logo"><img src="images/logo.jpg" alt="Logo"></div>
  <button id="menu-toggle" aria-label="Toon menu">&#9776;</button>

  <ul class="menu">
    <li><a href="index.php">Binnenkomst</a></li>

    <li class="has-submenu">
      <a href="index.php?page=task">Werkbonnen</a>
      <ul class="submenu">
        <li><a href="index.php?page=workorder/editworkorder">Nieuwe Werkbon</a></li>
        <li><a href="index.php?page=workorder/showworkorders">Werkbonnen Overzicht</a></li>
        <li><a href="index.php?page=workorder/timeline">Werkbonnen Planning</a></li>
        <li><a href="index.php?page=task&view=51">Taken Nummer 51</a></li>
        <li><a href="index.php?page=task&view=63">Taken Nummer 63</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="index.php?page=productie">Productie</a>
      <ul class="submenu">
        <li><a href="index.php?page=productie">Registreer Product</a></li>
        <li><a href="index.php?page=stansen">Registreer Stans</a></li>
        <li><a href="index.php?page=machines">Registreren Machines</a></li>
        <li><a href="index.php?page=machinetable">Overzicht Machines</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="#">Registratie Overzichten</a>
      <ul class="submenu">
        <li><a href="index.php?page=summary&period=day">Dag overzicht</a></li>
        <li><a href="index.php?page=summary&period=week">Week Overzicht</a></li>
        <li><a href="index.php?page=summary&period=month">Maand Overzicht</a></li>
        <li><a href="index.php?page=summary&period=custom">Aangepast</a></li>
        <li><a href="index.php?page=summary">Registraties</a></li>
        <li><a href="index.php?page=machinetable">Overzicht Machines</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="#">Productie Overzichten</a>
      <ul class="submenu">
        <li><a href="index.php?page=stock-summary&period=day">Dag overzicht</a></li>
        <li><a href="index.php?page=stock-summary&period=week">Week Overzicht</a></li>
        <li><a href="index.php?page=stock-summary&period=month">Maand Overzicht</a></li>
        <li><a href="index.php?page=stock-summary&period=custom">Aangepast</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="index.php?page=voorraad">Voorraad</a>
      <ul class="submenu">
        <li><a href="index.php?page=voorraad">Voorraad</a></li>
        <li><a href="index.php?page=ship">Leveren</a></li>
        <li><a href="index.php?page=zendingen">Zendingen</a></li>
      </ul>
    </li>

    <li class="has-submenu">
      <a href="#">Rollen</a>
      <ul class="submenu">
        <li><a href="index.php?page=rollen">Inboeken</a></li>
        <li><a href="index.php?page=rolltable">Rollen overzicht</a></li>
        <li><a href="index.php?page=roll-shipments">Zending overzicht</a></li>
      </ul>
    </li>
    <li class="has-submenu">
      <a href="#">WMS</a>
    	<ul class="submenu">
    		<a href="index.php?page=inventory/edit"><li>Inboeken</li></a>
            <a href="index.php?page=inventory/inventory"><li>Overziht</li></a>	
		</ul>
	</li>
    <li class="has-submenu">
      <a href="#"><img src="images/menu.png" class="burgermenu" alt="Menu" /></a>
      <ul class="submenu">
        <li><a href="index.php?page=user/userdetail&task=edit&id=<?php echo $user->id; ?>"><?php echo ucfirst($user->username); ?></a></li>
        <li><a href="index.php?page=verzondenmail">Verstuurde Transportmail</a></li>
        <li><a href="index.php?page=tasksummary">Taak Overzicht</a></li>
        <?php if ($user->level == 1): ?>
          <li><a href="index.php?page=options">Opties</a></li>
          <li><a href="index.php?page=user/users">Medewerkers</a></li>
        <?php endif; ?>
        <li><a href="index.php?page=changePassword">Wachtwoord veranderen</a></li>
        <li><a href="index.php?page=logout">Uitloggen</a></li>
      </ul>
    </li>
  </ul>
</nav>
<script>
  // Toggle the menu on mobile
  document.getElementById("menu-toggle").addEventListener("click", function () {
    document.querySelector(".main-nav .menu").classList.toggle("active");
  });

  // Expand/collapse submenu items on mobile
  document.querySelectorAll(".main-nav .has-submenu > a").forEach(link => {
    link.addEventListener("click", function (e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        this.parentElement.classList.toggle("open");
      }
    });
  });
</script>
