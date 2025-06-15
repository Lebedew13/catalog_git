<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../connectdb.php';




if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
} 

if (isset($_GET['ringid'])) {
    $_SESSION['ringid'] = $_GET['ringid'];
} 

$showid = $_SESSION['showid'];
$ringid = $_SESSION['ringid'];

$sql = "SELECT `name_show`, `date_show` FROM show_idbc WHERE id = '{$showid}'";
$show = $conn->query($sql);
$sql1 = "SELECT * FROM experts WHERE show_id = '{$showid}'";
$expets = $conn->query($sql1);

$query = "SELECT * FROM `catalog_dog_show_{$showid}_ring_{$ringid}` GROUP BY breed, class_breed, type_breed ORDER BY breed ASC";

$dogs = $conn->query($query);

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
                <p>
                    <a href="../php/referi.php?showid=<?php echo htmlspecialchars($showid) ?>">Судьи</a>
                    <a href="editdog.php?showid=<?php echo htmlspecialchars($showid) ?>">Редактировать список собак</a>
                    <a href="../exit.php">Выйти</a>
                </p>
                <h3 class="main__title">Каталог</h3>   
                <div class="catalog__showName flex">
                    <p>
                    
                        <?php
                        
                            foreach($show as $row){
                                echo "<p>{$row['name_show']}</p>" ;
                            }
                           
                        ?>
                    </p>    
                    <p>
                        <?php echo date("d.m.Y", strtotime($row['date_show']))?>
                    </p>
                    <div class="catalog__experts flex">
                    <?php
                            foreach($expets as $row){
                                echo "<p>{$row['lastname']}\n{$row['firstname']}</p>";
                            }                           
                        ?>
                    </div>
                </div>               
                <div class="main__catalogDog">
                <?php
               
// Классы для младшей и взрослой группы
$adultClasses = ['Взрослые', 'Взрослый', 'Зрелые', 'Зрелые (2+)', ''];

$juniorDogs = [];
$adultDogs = [];

foreach ($dogs as $dog) {
    $class = trim($dog['class_breed']);
    if (in_array($class, $adultClasses, true)) {
        $adultDogs[] = $dog;
    } else {
        $juniorDogs[] = $dog;
    }
}

$number = 1; // Счётчик сквозной
$currentBreed = '';
$currentClass = '';
$currentSex = '';

// Функция для вывода списка собак
function displayDogs($dogs, &$number, &$currentBreed, &$currentClass, &$currentSex) {
    foreach ($dogs as $dog) {
        $breed = trim($dog['breed']);
        $class = trim($dog['class_breed']);
        $sex = trim($dog['gender']);
        $name = trim($dog['nameDog']);
        $color = trim($dog['color']);
        $breeder = trim($dog['breeder']);
        $owner = trim($dog['owner']);

        // Если изменилась порода — выводим заголовок породы
        if ($breed !== $currentBreed) {
            echo "<h2>" . htmlspecialchars($breed) . "</h2>";
            $currentBreed = $breed;
            $currentClass = ''; // Сброс класса при смене породы
            $currentSex = '';   // Сброс пола при смене породы
        }

        // Если изменился класс — выводим заголовок класса
        if ($class !== $currentClass) {
            echo "<h3>" . htmlspecialchars($class) . "</h3>";
            $currentClass = $class;
            $currentSex = ''; // Сброс пола при смене класса
        }

        // Если изменился пол — выводим заголовок пола
        if ($sex !== $currentSex) {
            echo "<h4>" . htmlspecialchars(mb_strtoupper($sex)) . "</h4>";
            $currentSex = $sex;
        }

        // Выводим собаку
        echo "<p><strong>{$number}.</strong> " . htmlspecialchars($name) .
            " (" . htmlspecialchars($color) . ", зав. " . htmlspecialchars($breeder) .
            ", вл. " . htmlspecialchars($owner) . ")</p>";

        $number++;
    }
}

// Вывод сначала младших
displayDogs($juniorDogs, $number, $currentBreed, $currentClass, $currentSex);

// Потом взрослых
displayDogs($adultDogs, $number, $currentBreed, $currentClass, $currentSex);
?>
<div class="main__form">
<form action="generate_catalog.php" method="post">
    <button class="main__form-input" type="submit">Сформировать каталог и скачать Word</button>
</form>

<form action="generate_field.php" method="post">
    <button type="submit">Сформировать  описные листы скачать Word</button>
</form>
<form action="generate_res_dog.php" method="post">
    <button type="submit">Сформировать  дипломы и скачать Word</button>
</form>
</div>

<!-- <form action="generate_vedomost.php" method="post">
    <button type="submit">Сформировать  ведомость победителей скачать Word</button>
</form> -->
                </div>
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