<?php
include 'connectdb.php';

$showid = intval($_GET['showid']);
$sql = "SELECT * FROM `catalog_dog` WHERE `showid` = '{$showid}' ORDER BY position ASC";
$result = $conn->query($sql);

$output = "";
foreach ($result as $dogs) {
    $output .= "<p>{$dogs['name_dog']} - {$dogs['breed']}</p>";
}

echo $output;
?>
