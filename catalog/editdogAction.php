<?php
include '../connectdb.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Логируем входящие данные для отладки
error_log('Received POST data: ' . print_r($_POST, true), 3, 'errors.log');

$response = ['success' => false, 'message' => 'Произошла ошибка'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем идентификаторы
    $showid = isset($_POST['showid']) ? (int)$_POST['showid'] : 0;
    $ringid = isset($_POST['ringid']) ? (int)$_POST['ringid'] : 0;
    $dogid = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Логируем полученные значения
    error_log('showid: ' . $showid, 3, 'errors.log');
    error_log('ringid: ' . $ringid, 3, 'errors.log');
    error_log('dogid: ' . $dogid, 3, 'errors.log');
    error_log('action: ' . $action, 3, 'errors.log');

    // Проверка обязательных данных
    if (!$dogid || !$action) {
        $response['message'] = 'Некорректные данные для выполнения действия.';
        echo json_encode($response);
        exit;
    }

    // Проверяем корректность входных данных
    if ($showid && $ringid && $dogid && in_array($action, ['Изменить', 'Удалить'])) {
        // Формируем имя таблицы для ринга
        $tableName = "catalog_dog_show_{$showid}_ring_{$ringid}";
        error_log('Table Name: ' . $tableName, 3, 'errors.log');

        // Проверяем существование таблицы
        $checkTableSQL = "SELECT * FROM `$tableName`";
        $result = $conn->query($checkTableSQL);

        if ($result === false) {
            $response['message'] = 'Таблица ринга не найдена';
            error_log('SQL Error: ' . $conn->error, 3, 'errors.log');  // Логируем ошибку SQL
            echo json_encode($response);
            exit;
        }

        // Действие "Изменить"
        if ($action == 'Изменить') {
            // Логируем все полученные данные
            $breed = htmlspecialchars($_POST['breed'], ENT_QUOTES, 'UTF-8');
            $type_breed = htmlspecialchars($_POST['type_breed'], ENT_QUOTES, 'UTF-8');
            $class_breed = htmlspecialchars($_POST['class_breed'], ENT_QUOTES, 'UTF-8');
            $name_dog = htmlspecialchars($_POST['name_dog'], ENT_QUOTES, 'UTF-8');
            $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');
            $color = htmlspecialchars($_POST['color'], ENT_QUOTES, 'UTF-8');
            $owner = htmlspecialchars($_POST['owner'], ENT_QUOTES, 'UTF-8');
            $breeder = htmlspecialchars($_POST['breeder'], ENT_QUOTES, 'UTF-8');

            error_log("Updated Data: breed: $breed, type_breed: $type_breed, class_breed: $class_breed, name_dog: $name_dog, gender: $gender, color: $color, owner: $owner, breeder: $breeder", 3, 'errors.log');

            $sql = "UPDATE `$tableName` 
                    SET breed = ?, type_breed = ?, class_breed = ?, nameDog = ?, gender = ?, color = ?, owner = ?, breeder = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                $response['message'] = 'Ошибка подготовки запроса';
                error_log('Prepare Error: ' . $conn->error, 3, 'errors.log');
            } else {
                $stmt->bind_param('ssssssssi', $breed, $type_breed, $class_breed, $name_dog, $gender, $color, $owner, $breeder, $dogid);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['dogid'] = $dogid;
                } else {
                    $response['message'] = 'Не удалось обновить данные собаки.';
                    error_log('SQL Error: ' . $stmt->error, 3, 'errors.log');
                }
            }
        }

        // Действие "Удалить"
        elseif ($action == 'Удалить') {
            $conn->begin_transaction();
            try {
                // Удаляем собаку из таблицы ринга
                $delete_sql = "DELETE FROM `$tableName` WHERE id = ?";
                $stmt = $conn->prepare($delete_sql);

                if (!$stmt) {
                    throw new Exception('Ошибка подготовки запроса для удаления');
                }

                $stmt->bind_param("i", $dogid);

                if ($stmt->execute()) {
                    $conn->commit();
                    $response['success'] = true;
                    $response['dogid'] = $dogid;
                } else {
                    $conn->rollback();
                    $response['message'] = 'Не удалось удалить собаку.';
                    error_log('SQL Error: ' . $stmt->error, 3, 'errors.log');
                }
            } catch (mysqli_sql_exception $e) {
                $conn->rollback();
                $response['message'] = 'Ошибка удаления: ' . $e->getMessage();
                error_log('SQL Error: ' . $e->getMessage(), 3, 'errors.log');
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
                error_log('Exception: ' . $e->getMessage(), 3, 'errors.log');
            }
        }
    } else {
        $response['message'] = 'Некорректные данные для выполнения действия.';
    }
}

// Отправляем JSON-ответ
echo json_encode($response);
?>
