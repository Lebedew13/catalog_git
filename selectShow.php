<?php
// Проверяем, активна ли сессия, и если нет, то запускаем её
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'connectdb.php';
include 'functions/selectShow.php';

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDBC Show | Регистрация</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">
</head>
<script src="./js/index.js" type="text/javascript"></script>

<body>
    <header class="header">
        <div class="container">
            <div class="header__cont">        
                <div class="header__text">
                    <h1 class="header__text-h1">
                        INTERNATIONAL DOG BREEDERS CLUB
                    </h1>
                    <p class="header__text-descr">
                        Интернациональный клуб заводчиков собак
                    </p>
                </div>
            </div>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="catalog flex">
                <h3 class="main__title">Выберите шоу</h3>
                 
                <?php
                    foreach ($show as $row){
                        // Выводим ссылки на выбор шоу
                        echo "<a href='ring/ring.php?showid={$row['id']}'>" . htmlspecialchars($row['name_show'], ENT_QUOTES, 'UTF-8') . "</a>";

                    }
                ?>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer__descr flex">
                <h3 class="footer__descr-h3">ANO "IDBC" - АНО "ИКЗС"</h3>
                <h4 class="footer__descr-h3">Design by Lebedev</h4>
            </div>
        </div>
    </footer>
</body>
</html>
