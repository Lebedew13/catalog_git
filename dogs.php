<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'connectdb.php';
//include 'functions/selects.php';
include 'functions/order.php';



if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
} 
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
<!-- <script src="./js/main.js" type="text/javascript"></script> -->
<script src="js/index.js" type="text/javascript">
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
         
<?php
// Проверка на наличие showid в сессии
// Проверяем, существует ли showid в сессии
if (isset($_SESSION['showid'])) {
    $showid = $_SESSION['showid'];
} else {
    // Если переменная не установлена, перенаправляем на главную или страницу ошибки
    //header("Location: selectShow.php");  // Например, перенаправление на главную
    exit;
}

?>
            <div class="catalog flex">                
                <p>
                <?php
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        $previousPage = $_SERVER['HTTP_REFERER'];
                        echo "<a href='{$previousPage}'>Назад</a>";
                    } else {
                        echo "<a href='index.php'>На главную</a>";  // Если нет информации о предыдущей странице
                    }
                    ?>
                    <a href="php/referi.php?showid=<?php echo $_SESSION['showid'] ?>">Судьи</a>
                    <a href="exit.php">Выйти</a>
                    
                </p>
                <h3 class="main__title">Каталог</h3>     
                <div class="catalog__showName flex">
                    <p>
                        <?php
                        $sql = "SELECT * FROM `show_idbc` WHERE `id` = '{$_SESSION['showid']}'";
                        $show = $conn->query($sql);
                            foreach($show as $row){
                          
                            }
                            echo $row['name_show'];
                        ?>
                    </p>    
                    <p>
                        <?php echo date("d.m.Y", strtotime($row['date_show']))?>
                    </p>
                </div>
                <div class="catalog__experts flex">
                    <?php
                        $sql = "SELECT * FROM `experts` WHERE `show_id` = '{$_SESSION['showid']}'";
                        $show = $conn->query($sql);
                            foreach($show as $row){
                                echo "<p>{$row['lastname']}\n{$row['firstname']}</p>";
                            }                           
                        ?>
                </div>
            </div>
    
            <?php
                        // Проверяем, существует ли HTTP_REFERER (предыдущая страница)

         
// Подключение к базе данных
include 'connectdb.php';

// Проверяем, передан ли параметр id
if (isset($_GET['id'])) {
    $dogId = $_GET['id'];

    // Запрос для получения данных о собаке по ID
    $query = "SELECT * FROM `idbc_dog` WHERE `id` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $dogId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Если собака найдена, выводим её информацию
    if ($result->num_rows > 0) {
        $dog = $result->fetch_assoc();
        echo "<div class='aboute__dog flex'>";
        echo "<h2>Кличка: " . htmlspecialchars($dog['name_dog'], ENT_QUOTES, 'UTF-8') . "</h2>";
        echo "<div class='aboute__dog-text flex'>";
        echo "<p>Порода: " . htmlspecialchars($dog['breed'], ENT_QUOTES, 'UTF-8') . "</p>";
        if($dog['type_breed'] != 'none'){
        echo "<p>Тип: " . htmlspecialchars($dog['type_breed'], ENT_QUOTES, 'UTF-8') . "</p>";
        }
        echo "<p>Возраст: " . htmlspecialchars($dog['birthday'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p>Класс: " . htmlspecialchars($dog['class_breed'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p>Пол: " . htmlspecialchars($dog['gender'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p>Цвет: " . htmlspecialchars($dog['color'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p>Владелец: " . htmlspecialchars($dog['owner'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "</div>";
        echo "<div>";
        echo "<textarea class='text__voice'  x-webkit-speech></textarea>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p>Собака не найдена.</p>";
    }
} else {
    echo "<p>Не указан ID собаки.</p>";
}


?>
<div>
    <label for="voiceInput">Голосовой ввод:</label>
    <input id="voiceInput" class="text__voice" type="text" placeholder="Говорите сюда..." />
    <button id="startButton">Начать запись</button>
</div>






            
             
             
             
             <?php
                // $dogsByBreedClassTypeGender = [];
                // foreach ($result as $dog) {
                //     $breed = $dog['breed'];
                //     $class = $dog['class_breed'];
                //     $type = $dog['type_breed']; // Тип породы
                //     $gender = strtolower($dog['gender']); // Пол в нижнем регистре
                    
                //     // Группируем по порода → класс → тип → пол
                //     $dogsByBreedClassTypeGender[$breed][$class][$type][$gender][] = $dog;
                // }
                
                // $counter = 1; // Общий счётчик
                
                // foreach ($dogsByBreedClassTypeGender as $breed => $classes) {
                //     echo '<div class="catalog__dogList flex">';
                //     echo "<h2 class=\"catalog__breed\">$breed</h2>";
                
                //     // Перебираем классы для каждой породы
                //     foreach ($classes as $class => $types) {
                //         echo "<h3 class=\"catalog_class\">$class</h3>";
                
                //         // Перебираем типы для каждого класса
                //         foreach ($types as $type => $genders) {
                //             if($type != 'none'){
                //             echo "<h4 class=\"catalog_type\">$type</h4>";
                //             }
                //             // Перебираем полы для каждого типа
                //             foreach ($genders as $gender => $dogs) {
                //                 // Название для пола
                //                 $genderTitle = ($gender === 'сука') ? 'Суки' : 'Кобели';
                //                 echo "<h5 class=\"catalog__gender\">$genderTitle</h5>";
                
                //                 // Выводим данные для каждой собаки
                //                 foreach ($dogs as $dog) {
                //                     echo '<div class="catalog__blockDog flex">
                //                             <div class="catalog__blockDog-text flex">
                //                                 <p id="number__pp">'.$counter.'</p>
                //                                 <p>'.$dog['name_dog'].'</p>
                //                             </div>            
                //                             <p>
                //                                 ( '.$dog['color'].', '.$dog['breeder'].', '.$dog['owner'].' )
                //                             </p>
                //                         </div>';
                //                     $counter++; // Увеличиваем общий счётчик
                //                 }
                //             }
                //         }
                //     }
                //     echo '</div>'; // Закрываем блок породы
                // }

                ?>

            
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