<?php

session_start();

if($_SESSION){
    include 'selectShow.php';
}else{
    include 'login.php';
}

?>