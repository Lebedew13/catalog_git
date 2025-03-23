<?php
    include './connectdb.php';


    $query = "SELECT DISTINCT  `breed` FROM `idbc_dog`";
    $breed = $conn->query($query);

?>