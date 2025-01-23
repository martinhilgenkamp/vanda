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
        <th class="ui-corner-tl">Naam</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Actief</th>
        <th>Planbaar</th>
        <th class="ui-corner-tr">Verwijder</th>
    </tr>
    <?php
        foreach ($resUsers as $user) {
            // Define Role for the resource
            if ($user->level == 1) {
                $role = 'Beheerder';
            } elseif ($user->level == 2) {
                $role = 'Machine';
            } else {
                $role = 'Medewerker';
            }

            $active = $user->active == 1 ? 'Actief' : 'Inactief';
            $isresource = $user->isresource == 1 ? 'Ja' : 'Nee';

            $html = "<tr class='data-table-row user-row clickable-row' data-user-id='". $user->id ."'>";
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
    <tr>
        <th class="ui-corner-bottom" colspan="6">
            &nbsp;
        </th>
    </tr>
</table>
