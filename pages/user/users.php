<?php

if (!$user->level) {
    header('Location: index.php?page=403');
}

require_once('inc/class/class.user.php');

$um = new UserManager;

$resUsers = $um->listUsers();
?>

<input type="button" id="AddUserButton" value="Toevoegen">

<table class="data-table" id="userTable">
    <tr>
        <th>Naam</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Actief</th>
        <th>Planbaar</th>
        <th>Verwijder</th>
    </tr>
    <?php
        foreach ($resUsers as $user) {
            $role = $user->level == 1 ? 'Administrator' : 'Medewerker';
            $active = $user->active == 1 ? 'Actief' : 'Inactief';
            $isresource = $user->isresource == 1 ? 'Ja' : 'Nee';

            $html = "<tr class='data-table-row user-row' data-user-id='". $user->id ."'>";
            $html .= "<td>". $user->username ."</td>";
            $html .= "<td>". $user->email ."</td>";
            $html .= "<td>". $role ."</td>";
            $html .= "<td>". $active ."</td>";
            $html .= "<td>". $isresource ."</td>";
            $html .= "<td><img class='delete' src='images/delete.png'/></td>";
            $html .= "<tr>";

            echo $html;
        }

    ?>
</table>
