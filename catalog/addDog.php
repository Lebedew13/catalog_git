<?php
include '../connectdb.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['showid'], $data['ringid'], $data['breed'], $data['type_breed'], $data['class_breed'], $data['name_dog'], $data['gender'], $data['color'], $data['owner'], $data['breeder'])) {
    // Подключение к базе данных
    $showid = intval($data['showid']);
    $ringid = intval($data['ringid']);
    $breed = $data['breed'];
    $type_breed = $data['type_breed'];
    $class_breed = $data['class_breed'];
    $name_dog = $data['name_dog'];
    $gender = $data['gender'];
    $color = $data['color'];
    $owner = $data['owner'];
    $breeder = $data['breeder'];

    // Выполнение запроса на добавление собаки
    $sql = "INSERT INTO catalog_dog_show_{$showid}_ring_{$ringid} (breed, type_breed, class_breed, nameDog, gender, color, owner, breeder) VALUES ('$breed', '$type_breed', '$class_breed', '$name_dog', '$gender', '$color', '$owner', '$breeder')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}




?>