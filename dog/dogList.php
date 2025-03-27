<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../connectdb.php';


if(isset($_GET['id'])){
    $_SESSION['dogid'] = $_GET['id'];
} 
if(isset($_GET['showid'])){
    $_SESSION['showid'] = $_GET['showid'];
}
if(isset($_GET['ringid'])){
    $_SESSION['ringid'] = $_GET['ringid'];
}

$showid = $_SESSION['showid'];
$ringid = $_SESSION['ringid'];
$dogid = $_SESSION['dogid'];



$sql = "SELECT * FROM catalog_dog_show_{$showid}_ring_{$ringid} WHERE id = '$dogid'";
$res = $conn->query($sql);

foreach($res as $dog){
    $nameDog = $dog['nameDog'];
    $class = $dog['class_breed'];
    $type = $dog['type_breed'];
    $color = $dog['color'];
    $breed = $dog['breed'];
    $owner = $dog['owner'];
    $breeder = $dog['breeder'];
    $gender = $dog['gender'];
}

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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = "ru-RU"; // Русский язык
    recognition.continuous = false;
    recognition.interimResults = false;

    const textarea = document.getElementById("abouteDog");
    const button = document.getElementById("voiceInputBtn");

    button.addEventListener("click", function () {
        recognition.start();
        button.textContent = "🎤 Говорите...";
    });

    recognition.onresult = function (event) {
        const transcript = event.results[0][0].transcript;
        textarea.value += transcript + " "; // Добавляет текст в поле
    };

    recognition.onend = function () {
        button.textContent = "🎤 Говорите";
    };

    recognition.onerror = function (event) {
        console.error("Ошибка распознавания:", event.error);
        button.textContent = "🎤 Ошибка";
    };
});
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
    <main class="main">
        <div class="container">
        <div class="dogs__contain flex">
            <div class="dogs__links flex">       
                    <a href='javascript:history.back()'>Назад</a>
                    <a href="../exit.php">Выйти</a>
            </div>
            <div class="dogs__contain-text flex">
                <h3 class="dogs__title">
                    <?php echo $nameDog?>
                </h3>
                <div class="dogs__aboute flex">
                    <ul class="dogs__aboute-list list-reset flex"> 
                        <li class="dogs__aboute-item"><p>Пол: <?php echo $gender ?></p></li>
                        <li class="dogs__aboute-item"><p>Класс: <?php echo $class ?></p></li>
                        <?php
                        if($type !== 'none' ){
                            echo  "<li class='dogs__aboute-item'><p>тип: ".$type."</p></li>";
                        }
                        ?>
                       
                        <li class="dogs__aboute-item"><p>Окрас: <?php echo $color ?></p></li>
                        <li class="dogs__aboute-item"><p>Владелец: <?php echo $owner ?></p></li>
                        <li class="dogs__aboute-item"><p>Заводчик: <?php echo $breeder ?></p></li>
                    </ul>
                    <div class="dogs__aboute-text">
                        <form action="" method="post">
                            <label for="abouteDog">Описание собаки:</label>
                            <textarea name="abouteDog" id="abouteDog" class="main__form-input" rows="10"></textarea>
                            <button type="button" id="voiceInputBtn">🎤 Говорите</button>
                            <input type="submit" value="Сохранить описание" class="main__form-input">
                        </form>
                    </div>
                </div>
            </div>
         
<?php




?>
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