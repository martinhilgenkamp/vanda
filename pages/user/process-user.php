<?php

require_once('../../inc/class/class.user.php');

$um = new UserManager;

foreach($_POST as $key => $val) {
    $post[$key] = $val;
    $_SESSION[$key] = $val;
}

$post = (object)$post;

if(!$post->task) {
    $post->task = '';
}

switch ($post->task) {
    case 'add':
        $data = array(
            "username" => $post->username,
            "password" => password_hash($post->password, PASSWORD_DEFAULT),
            "email" => $post->email,
            "level" => $post->level,
            "active" => $post->active,
            "isresource" => $post->isresource
        );

        echo $um->addUser($data);
        break;
    case 'edit':
        $data = array(
            "username" => $post->username,
            "email" => $post->email,
            "level" => $post->level,
            "active" => $post->active,
            "isresource" => $post->isresource
        );

        if (!empty($post->password)) {
            $data["password"] = password_hash($post->password, PASSWORD_DEFAULT);
        }

        echo $um->editUser($data, $post->id);        
        break;
    case 'delete':
        $um->deleteUser($post->id);
        break;
    
}

?>