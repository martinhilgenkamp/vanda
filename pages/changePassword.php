<?php
require_once('inc/class/class.user.php');

$um = new UserManager();
$succeededChange = false;
$errors = [];
$usernameIndex = 'username';

if (!isset($_SESSION[$usernameIndex]) || strlen($_SESSION[$usernameIndex]) ==0) {
    die('Unable to change a password without a logged in user');
}
$username = $_SESSION[$usernameIndex];

function GetItemFromPost($id) {
    if (isset($_POST[$id]))
        return $_POST[$id];

    return null;
}

function checkPassword($pwd, &$errors) {
    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors[] = "Wachtwoord te kort!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }     

    return ($errors == $errors_init);
}

$password1 = GetItemFromPost("password1");
$password2 = GetItemFromPost("password2");
$currentPassword = GetItemFromPost("currentpassword");

if ($password1 != null) {
    checkPassword($password1, $errors);
}

if ($password1 != $password2) {
    $errors[] = "Wachtwoorden komen niet overeen!";
}

if (strlen($password1) != 0 && count($errors) == 0) {
    $succeededChange = $um->changePassword($username, $password1, $currentPassword, $errors);
}

## Displaying html
if (!$succeededChange): ?>

<div  class="register-form">
    <form action="index.php?page=changePassword" method="POST">
        <p><label>Huidige wachtwoord: </label>
        <input id="currentpassword" type="password" name="currentpassword" placeholder="wachtwoord..." /></p>

        <p><label>Nieuwe wachtwoord: </label>
        <?php
            if (count($errors) > 0) {
                echo('<ul class="password_message">');
                foreach ($errors as $error) {
                    echo("<li>".$error."</li>");
                }
                echo('</ul>');
            }
        ?>
        <input id="password1" type="password" name="password1" placeholder="wachtwoord..." /></p>

        <p><label>Wachtwoord herhalen: </label>
        <input id="password2" type="password" name="password2" placeholder="wachtwoord..." /></p>
        
        <input class="btn register" type="submit" name="submit" value="Opslaan" />
    </form>
</div>
<?php endif; 

if ($succeededChange) {
    echo ('<div  class="register-form"><p>Uw wachtwoord is gewijzigd!</p></div>');
}

?>