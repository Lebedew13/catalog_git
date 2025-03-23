<?php
include './connectdb.php';

// Проверяем, существует ли showid в сессии
if (isset($_SESSION['showid'])) {
    $showid = $_SESSION['showid'];
} else {
    // Если переменная не установлена, перенаправляем на главную или страницу ошибки
    //header("Location: selectShow.php");  // Например, перенаправление на главную
    exit;
}

// Подготавливаем запрос с использованием параметра для предотвращения SQL инъекций
$query = "SELECT * FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $showid); // 'i' — это тип для целого числа
$stmt->execute();
$result = $stmt->get_result();

// Теперь можно использовать $result для вывода данных о собаках
?>
