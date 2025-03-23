<?php
// Подключаем соединение с базой данных
include '../connectdb.php';

// Чтение входящих данных
$data = json_decode(file_get_contents('php://input'), true);

// Проверка, что данные были получены
if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Не удалось распарсить данные']);
    exit();
}

// Проверка, что получен id собаки
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Не передан id собаки']);
    exit();
}

// Получаем идентификаторы
$dogid = (int)$data['id'];
$showid = (int)$data['showid'];
$ringid = (int)$data['ringid'];

// Подготовка SQL-запроса
$tableName = "catalog_dog_show_{$showid}_ring_{$ringid}";
$sql = "SELECT * FROM `$tableName` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dogid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Если запись существует, выполняем удаление
    $delete_sql = "DELETE FROM `$tableName` WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $dogid);
    $stmt->execute();

    // Ответ на запрос
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Собака не найдена']);
}
