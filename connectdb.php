<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


$conn = new mysqli("localhost", "root", "", "idbc");
if($conn->connect_error){
    die("no" . $conn->connect_error);
}


// try {
//     // Подключение к базе данных
//     $conn = new mysqli("localhost", "idbcdog_idbc", "root", "idbcdog_idbc");
//     $conn->set_charset("utf8mb4"); // Устанавливаем кодировку
// } catch (mysqli_sql_exception $e) {
//     // Обрабатываем ошибку подключения
//     die("Ошибка подключения: " . $e->getMessage());
// }
?>