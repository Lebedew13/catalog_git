<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDBC Show | Регистрация</title>
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/media.css">
</head>
<!-- <script src="./js/main.js" type="text/javascript"></script> -->
<script src="./js/index.js" type="text/javascript">
</script>
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
    <main>
    <div class="container">
        <div class="login__form">
        <form class="main__form flex" action="php/login.php" method="post">
            <h1>Войдите, чтобы продолжить</h1>
            <input class="main__form-input" type="text" name="login" placeholder="Логин">
            <input class="main__form-input" type="password" name="password" placeholder="Пароль">
            <input class="main__form-input" type="submit" value="Войти">
        </form>
        </div>
    </div>
    </main>
    <footer class="footer">
        <div class="container">

                    <div class="footer__descr flex">
                    <h3 class="footer__descr-h3">ANO "IDBC" - АНО "ИКЗС"</h3>
                    <h4 class="footer__descr-h3">Desing by Lebedev</h4>
                    </div>
                    
        </div>
    </footer>
</body>
</html>