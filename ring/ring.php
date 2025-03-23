<?php
// Проверяем, активна ли сессия, и если нет, то запускаем её
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../connectdb.php';

if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
} 
$showid = $_SESSION['showid'];


$sql = "SELECT * FROM `ring` WHERE showid = '{$showid}'";
$res = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDBC Show | Регистрация</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/media.css">
</head>
<script src="../js/index.js" type="text/javascript"></script>
<script src="../js/editRing.js" type="text/javascript"></script>
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
                <h3 class="main__title">Выберите ринг</h3>
                <button id='addRing'>
                    Добавить ринг
                </button>
                <div class="modal" id='formAddRing'>
                    <form method="post" class="modal-content" action="editRing.php">
                        <div class="form-modal flex">
                        <span class='close_modal' id='modal-close'>&times;</span>
                            <p>
                                Введите название ринга для <?php
                                    $show = "SELECT name_show FROM `show_idbc` WHERE id = '{$showid}'";
                                    $showName = $conn->query($show);
                                    foreach($showName as $showName1){
                                        echo $showName1['name_show'];
                                    }
                                ?>
                            </p>
                        <input class="main__form-input" type="text" required>
                        <input type="hidden" value="<?php echo $showid?>">
                        <input class="main__form-input" type="submit" value="Добавить ринг">
                        </div>
                    </form>
                </div>
                <div>
                    <ul class='ring-list list-reset flex'>
                    <?php
                    foreach ($res as $row){
                        // Выводим ссылки на выбор шоу
                        echo "<li class='ring-item'>
                        <a href='selectRing.php?showid={$row['showid']}&ringid={$row['id']}'>" . htmlspecialchars($row['name_ring'], ENT_QUOTES, 'UTF-8') . "</a>
                        <button class='delete-ring' data-id='{$row['id']}'>Удалить</button></li>";
                    }
                ?>
                    </ul>
               
                </div>
                
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
