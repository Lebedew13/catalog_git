<?php
include '../connectdb.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Некорректные входные данные']);
    exit;
}

$response = ['success' => false, 'message' => 'Произошла ошибка'];

if (isset($data['name']) && isset($data['showid'])) {
    // Добавление ринга
    $ringName = $data['name'];
    $showid = (int) $data['showid'];

    try {
        $stmt = $conn->prepare("INSERT INTO `ring` (name_ring, showid) VALUES (?, ?)");
        $stmt->bind_param("si", $ringName, $showid);

        if (!$stmt->execute()) {
            throw new Exception("Ошибка добавления ринга: " . $stmt->error);
        }

        $response = [
            'success' => true,
            'ringid' => $stmt->insert_id,
            'ringName' => htmlspecialchars($ringName, ENT_QUOTES, 'UTF-8'),
            'showid' => $showid
        ];
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} elseif (isset($data['ringid'])) {
    // Удаление ринга
    $ringId = (int) $data['ringid'];

    try {
        // Принудительно включаем foreign_key_checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1;");
        
        // Удаляем сам ринг
        $stmt = $conn->prepare("DELETE FROM ring WHERE id = ?");
        $stmt->bind_param("i", $ringId);

        if (!$stmt->execute()) {
            throw new Exception("Ошибка удаления: " . $stmt->error);
        }

        $response = ['success' => true];
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Ошибка удаления ринга: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Отсутствуют параметры';
}

echo json_encode($response);
?>
