<?php
include '../connectdb.php'; // Подключение к базе данных
if (!empty($_GET['name_dog']) && !empty($_GET['showid']) && !empty($_GET['ringid'])) {
    $nameDog = trim($_GET['name_dog']);
    $showid = intval($_GET['showid']);
    $ringid = intval($_GET['ringid']);

    $sql_s = "SELECT * FROM catalog_dog_show_{$showid}_ring_{$ringid} WHERE nameDog LIKE '%$nameDog%'";
    $res = $conn->query($sql_s);

    if ($res->num_rows > 0) {
        echo "<p>Найдено: " . $res->num_rows . " собак(и)</p>";
        while ($dog = $res->fetch_assoc()) {
            echo "<div class='dog-item flex'>";
            echo "<p><strong>Кличка:</strong> " . htmlspecialchars($dog['nameDog']) . "</p>";
            echo "<p><strong>Порода:</strong> " . htmlspecialchars($dog['breed']) . "</p>";
            echo "<p><strong>Позиция:</strong> " . htmlspecialchars($dog['position']) . "</p>";

            echo "<form class='position-form'>";
            echo "<input type='hidden' name='dog_id' value='" . $dog['id'] . "'>";
            echo "<input type='hidden' name='showid' value='" . $showid . "'>";
            echo "<input type='hidden' name='ringid' value='" . $ringid . "'>";
            echo "<input type='hidden' name='breed' value='" . $dog['breed'] . "'>";
            echo "<label for='position'>Новая позиция:</label>";
            echo "<input type='number' name='position' value='" . $dog['position'] . "' required>";
            echo "<input type='submit' value='Обновить'>";
            echo "</form>";

            echo "</div>";
        }
    } else {
        echo "<p>Собака не найдена.</p>";
    }
}
?>