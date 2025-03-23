<?php
include 'connectdb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Нет данных"]);
    exit;
}

foreach ($data as $index => $breedItem) {
    $breed = $conn->real_escape_string($breedItem['breed']);
    $position = (int) $breedItem['position'];

    $sql = "UPDATE catalog_dog SET position = '{$position}' WHERE breed = '{$breed}'";
    $conn->query($sql);
}

echo json_encode(["status" => "success"]);
?>
