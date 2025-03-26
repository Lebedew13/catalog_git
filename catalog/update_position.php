<?php
include '../connectdb.php'; // Подключение к БД

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dog_id'], $_POST['position'], $_POST['showid'], $_POST['ringid'], $_POST['breed'])) {
    $dog_id = intval($_POST['dog_id']);
    $position = intval($_POST['position']);
    $showid = intval($_POST['showid']);
    $ringid = intval($_POST['ringid']);
    $breed = $_POST['breed'];

    $sql_update = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} SET position = $position WHERE id = $dog_id";
    
    if ($conn->query($sql_update)) {
        echo "Позиция успешно обновлена!<br>";

        // 2. Получаем всех собак, кроме обновленной
        $sql_select = "SELECT id, breed FROM catalog_dog_show_{$showid}_ring_{$ringid} WHERE id != $dog_id ORDER BY breed, position ";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $dogs_by_breed = [];

            // 3. Группируем собак по породам
            while ($dog = $result->fetch_assoc()) {
                $dogs_by_breed[$dog['breed']][] = $dog['id'];
            }

            // 4. Обновляем позиции для каждой группы пород
            foreach ($dogs_by_breed as $breed_name => $dog_ids) {
                $new_position = $position + 1; // Начинаем с позиции обновленной собаки

                foreach ($dog_ids as $id) {
                    $sql_update_position = "UPDATE catalog_dog_show_{$showid}_ring_{$ringid} SET position = $new_position WHERE id = $id";
                    $conn->query($sql_update_position);
                    $new_position++; // Увеличиваем позицию для следующей собаки
                }
            }

            echo "Все позиции успешно обновлены!";
        } else {
            echo "Других собак в списке нет.";
        }
    } else {
        echo "Ошибка обновления позиции: " . $conn->error;
    }
}
?>
