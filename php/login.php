<?php

session_start();

if($_POST['login'] === 'admin_idbc' && $_POST['password'] === 'pass'){

    $_SESSION['admin'] = $_POST['login'];
    print_r($_SESSION);
   header('Location: ../index.php');
}else{
    header('Location: ../index.php');
}


?>