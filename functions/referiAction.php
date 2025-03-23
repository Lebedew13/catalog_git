<?php

include '../connectdb.php';


if(isset($_POST['edit'])){
    $sql = "DELETE FROM `experts` WHERE `id` = '{$_POST['id']}'";
    $result = $conn->query($sql);
    header('Location: ../php/referi.php');
}

if(isset($_POST['add'])){

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $showid = $_POST['showid'];

    $sql = "INSERT INTO `experts`(`firstname`, `lastname`, `show_id`) VALUES ('$firstname', '$lastname', '$showid')";
    $result = $conn->query($sql);
    header('Location: ../php/referi.php');
}

?>