<?php
// Проверяем, активна ли сессия, если нет, запускаем её
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../connectdb.php';

// Получаем идентификаторы выставки и ринга
if (!isset($_GET['showid']) || !isset($_GET['ringid'])) {
    die("Ошибка: отсутствует идентификатор показа или ринга.");
}

$showid = (int) $_GET['showid'];
$ringid = (int) $_GET['ringid'];

// Формируем имя таблицы для данного ринга
$tableName = "catalog_dog_show_{$showid}_ring_{$ringid}";

// Создаём таблицу, если её нет
$createTableSQL = "CREATE TABLE IF NOT EXISTS `$tableName` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number_pp INT(11),
    breed VARCHAR(255),
    class_breed VARCHAR(255),
    type_breed VARCHAR(255),
    color VARCHAR(255),
    gender VARCHAR(255),
    nameDog VARCHAR(255),
    breeder VARCHAR(255),
    owner VARCHAR(255),
    position INT(11)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($createTableSQL);

// Получаем список собак для выставки
$stmt = $conn->prepare("SELECT * FROM `idbc_dog` WHERE show_idbc = ?");
$stmt->bind_param("i", $showid);
$stmt->execute();
$dogList = $stmt->get_result();

while ($dog = $dogList->fetch_assoc()) {
    $name = $dog['name_dog'];

    // Проверяем, есть ли собака в текущем ринге
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `$tableName` WHERE nameDog = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        // Добавляем собаку в таблицу ринга
        $stmt = $conn->prepare("INSERT INTO `$tableName` (breed, class_breed, type_breed, color, gender, nameDog, breeder, owner) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $dog['breed'], $dog['class_breed'], $dog['type_breed'], $dog['color'], $dog['gender'], $dog['name_dog'], $dog['breeder'], $dog['owner']);
        $stmt->execute();
        $stmt->close();
    }
}

// Перенаправляем на страницу редактирования каталога
header("Location: editcatalog.php?showid=$showid&ringid=$ringid");
exit();
?>