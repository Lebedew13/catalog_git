<?php
// save_order.php
include '../connectdb.php';

// Получаем данные из JSON
$data = json_decode(file_get_contents('php://input'), true);

// Проверяем наличие данных
if (!$data || !isset($data['order'])) {
    echo json_encode(['status' => 'error', 'message' => 'Нет данных']);
    exit;
}

// Переменные для получения showid и ringid
$showid = isset($data['showid']) ? (int)$data['showid'] : null;
$ringid = isset($data['ringid']) ? (int)$data['ringid'] : null;

if ($showid === null || $ringid === null) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные параметры showid или ringid']);
    exit;
}

// Обработка пород
foreach ($data['order'] as $index => $item) {
    if (isset($item['breed'])) {
        // Обрабатываем основную породу
        $breed = $conn->real_escape_string($item['breed']);
        $position = (int)$item['position'];

        $sql = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} SET position = '{$position}' WHERE breed = '{$breed}'";
        if ($conn->query($sql) === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка SQL: ' . $conn->error]);
            exit;
        }
    }

    if (isset($item['type_breed'])) {
        // Обрабатываем подтип породы
        $subBreed = $conn->real_escape_string($item['type_breed']);
        $subPosition = (float)$item['sub_position']; // Для подтипов мы используем дробные значения

        // Предположим, что подтипы связаны с породами через родительский ключ
        $sql = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} SET sub_position = '{$subPosition}' WHERE type_breed = '{$subBreed}'";
        if ($conn->query($sql) === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка SQL: ' . $conn->error]);
            exit;
        }
    }
}

echo json_encode(['status' => 'success']);
