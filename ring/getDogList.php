<?php
// Подключаемся к базе данных
include '../connectdb.php';

header('Content-Type: application/json');

// Получаем данные из JSON-запроса
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['showid']) || !isset($data['ringid'])) {
    echo json_encode(['success' => false, 'message' => 'Отсутствуют параметры']);
    exit;
}

$showid = (int) $data['showid'];
$ringid = (int) $data['ringid'];

// Формируем имя таблицы для данного ринга
$tableName = "catalog_dog_show_{$showid}_ring_{$ringid}";

// Проверяем, существует ли таблица (на случай, если кто-то удалил вручную)
$checkTableSQL = "SHOW TABLES LIKE '$tableName'";
$result = $conn->query($checkTableSQL);

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Таблица ринга не найдена']);
    exit;
}

// Подготавливаем ответ
$response = ['success' => false, 'message' => 'Произошла ошибка'];

try {
    // Получаем всех собак из таблицы ринга
    $sql = "SELECT * FROM `$tableName`";
    $stmt = $conn->prepare($sql);

    if (!$stmt->execute()) {
        throw new Exception("Ошибка запроса списка: " . $stmt->error);
    }

    $dogs_in_ring = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Формируем успешный ответ
    $response['success'] = true;
    $response['dogs'] = $dogs_in_ring;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
