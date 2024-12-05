<?php
require_once('inc/class/class.user.php');
$um = new UserManager;


if (!$user->level) {
    header('Location: index.php?page=403');
}

if (isset($_GET['id'])) {
    $recUser = $um->getUserById($_GET['id']);
    $action = "Gebruiker Bewerken";
} else {
    $action = "Gebruiker Toevoegen";
}
?>

<script type="text/javascript" src="inc/script/user/users.js"></script>
<h1><?php echo $action; ?></h1>
<form name='userform' id='userform' method='post' action=''>
    <input type="hidden" id="task" name="task" value="<?= $_GET['task'] ?>">
    <input type="hidden" id="editid" name="editid" value="<?php echo isset($recUser->id) ? htmlspecialchars($recUser->id, ENT_QUOTES, 'UTF-8') : ''; ?>"/>
    <div id="userform-edit" class="userform-edit">
        <table id="userList">
            <tr>
                <td>Gebruikersnaam</td>
                <td><input id="editusername" name="editusername" type="text" value="<?= !empty($recUser) ? $recUser->username : '' ?>" required/></td>
            </tr>
            <tr>
                <td>Wachtwoord</td>
                <td><input id="editpassword" name="editpassword" type="text" value="" <?= $_GET['task'] == "add" ? 'required' : '' ?> /></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><input id="editemail" name="editemail" type="text" value="<?= !empty($recUser) ? $recUser->email : '' ?>" /></td>
            </tr>
            <tr>
                <td>Rol</td>
                <td>
                    <select id="editlevel" name="editlevel">
                        <option value="0" <?= !empty($recUser) && $recUser->level == 0 ? 'selected' : '' ?>>Medewerker</option>
                        <option value="2" <?= !empty($recUser) && $recUser->level == 2 ? 'selected' : '' ?>>Machine</option>
                        <option value="1" <?= !empty($recUser) && $recUser->level == 1 ? 'selected' : '' ?>>Beheerder</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Actief</td>
                <td>
                    <select id="editactive" name="editactive">
                        <option value="1" <?= !empty($recUser) && $recUser->active == 1 ? 'selected' : '' ?>>Actief</option>
                        <option value="0" <?= !empty($recUser) && $recUser->active == 0 ? 'selected' : '' ?>>Inactief</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Planbaar</td>
                <td>
                    <select id="isresource" name="isresource">
                        <option value="1" <?= !empty($recUser) && $recUser->isresource == 1 ? 'selected' : '' ?>>Planbaar</option>
                        <option value="0" <?= !empty($recUser) && $recUser->isresource == 0 ? 'selected' : '' ?>>Niet Planbaar</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="button" id="SaveButton" name="opslaan" value="Opslaan" />
        <input type="button" id="CancelButton" value="Terug" />
    </div>
</form>