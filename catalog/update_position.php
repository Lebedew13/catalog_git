<?php
include '../connectdb.php'; // Подключение к БД

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dog_id'], $_POST['position'], $_POST['showid'], $_POST['ringid'], $_POST['breed'])) {
    $dog_id = intval($_POST['dog_id']);
    $position = intval($_POST['position']);
    $showid = intval($_POST['showid']);
    $ringid = intval($_POST['ringid']);
    $breed = $_POST['breed'];
    $sub_pos = isset($_POST['sub_position']) ? intval($_POST['sub_position']) : null;

    // 1. Получаем породу и подтип обновляемой собаки
    $sql_get_breed_and_type = "SELECT breed, type_breed FROM catalog_dog_show_{$showid}_ring_{$ringid} WHERE id = ?";
    $stmt = $conn->prepare($sql_get_breed_and_type);
    $stmt->bind_param('i', $dog_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $dog_data = $result->fetch_assoc();
        $dog_breed = $dog_data['breed'];
        $dog_type = $dog_data['type_breed'];

        // 2. Обновляем позицию для всех собак этой породы
        $sql_update_breed_position = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} 
                                      SET position = ? 
                                      WHERE breed = ?";
        $stmt_update = $conn->prepare($sql_update_breed_position);
        $stmt_update->bind_param('is', $position, $dog_breed);
        if ($stmt_update->execute()) {
            echo "Позиция для породы '$dog_breed' успешно обновлена!<br>";

            // 3. Если порода "Американский Булли", обновляем подтип
            if ($dog_breed === 'Американский Булли' && $sub_pos !== null) {
                // Обновляем подпозицию для всех собак этого подтипа
                $sql_upd_type = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} 
                                 SET sub_position = ? 
                                 WHERE type_breed = ?";
                $stmt_upd_type = $conn->prepare($sql_upd_type);
                $stmt_upd_type->bind_param('is', $sub_pos, $dog_type);
                $stmt_upd_type->execute();

                // Перераспределяем подпозиции для всех подтипов этой породы
                $sql_select_subtypes = "SELECT id, breed, type_breed, sub_position FROM catalog_dog_show_{$showid}_ring_{$ringid} 
                                        WHERE breed = 'Американский Булли' AND type_breed = ? ORDER BY sub_position";
                $stmt_subtypes = $conn->prepare($sql_select_subtypes);
                $stmt_subtypes->bind_param('s', $dog_type);
                $stmt_subtypes->execute();
                $result_subtypes = $stmt_subtypes->get_result();

                $new_sub_position = $sub_pos;
                while ($subtype = $result_subtypes->fetch_assoc()) {
                    // Обновляем подпозицию для каждого подтипа, если она отличается от текущей
                    if ($subtype['sub_position'] !== $new_sub_position) {
                        $sql_update_subtype_position = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} 
                                                        SET sub_position = ? 
                                                        WHERE id = ?";
                        $stmt_update_subtype = $conn->prepare($sql_update_subtype_position);
                        $stmt_update_subtype->bind_param('ii', $new_sub_position, $subtype['id']);
                        $stmt_update_subtype->execute();
                    }
                    $new_sub_position++; // Увеличиваем подпозицию для следующего подтипа
                }
            }

            // 4. Перераспределяем позиции для всех собак, начиная с собаки, чью позицию мы изменили
            $sql_select = "SELECT id, breed, position FROM catalog_dog_show_{$showid}_ring_{$ringid} ORDER BY position";
            $result = $conn->query($sql_select);

            if ($result->num_rows > 0) {
                $new_position = $position;
                $update_position_flag = false;

                while ($dog = $result->fetch_assoc()) {
                    // Сначала обновляем позицию для собак этой породы
                    if ($dog['breed'] === $dog_breed) {
                        $sql_update_position = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} 
                                                 SET position = ? 
                                                 WHERE id = ?";
                        $stmt_update_pos = $conn->prepare($sql_update_position);
                        $stmt_update_pos->bind_param('ii', $new_position, $dog['id']);
                        $stmt_update_pos->execute();
                    } else {
                        // Когда мы доходим до другой породы, увеличиваем позицию на 1
                        if (!$update_position_flag) {
                            $new_position++; // Увеличиваем позицию только один раз
                            $update_position_flag = true;
                        }
                        // Обновляем остальные собаки
                        $sql_update_position = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} 
                                                 SET position = ? 
                                                 WHERE id = ?";
                        $stmt_update_pos = $conn->prepare($sql_update_position);
                        $stmt_update_pos->bind_param('ii', $new_position, $dog['id']);
                        $stmt_update_pos->execute();
                        $new_position++; // Увеличиваем позицию на 1 для каждой следующей собаки
                    }
                }
            }

            echo "Позиции для всех собак успешно обновлены!";
        } else {
            echo "Ошибка при обновлении позиции для породы '$dog_breed'.<br>";
        }
    } else {
        echo "Собака не найдена!<br>";
    }
}
?>
