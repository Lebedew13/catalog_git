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

$query = "SELECT DISTINCT breed FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $showid); // 'i' — это тип для целого числа
$stmt->execute();
$result = $stmt->get_result();
?>
            <div class="catalog flex">                
                <p>
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

             
            <div class="main__dog flex">
            <div class="main__order flex">
                <h4>Выберите породу</h4>
                <?php
                echo "<a href='?showid={$_SESSION['showid']}&breed=all'>Показать все</a>";
                    // Перебираем породы
                    foreach($result as $row){
                        // Формируем ссылку с параметрами showid и breed
                        echo "<a href='?showid={$_SESSION['showid']}&breed=" . urlencode($row['breed']) . "'>" . htmlspecialchars($row['breed'], ENT_QUOTES, 'UTF-8') . "</a>";
                    }    
                    // Ссылка "Показать все" — сбрасываем breed в URL, чтобы отображать все породы
                    
                ?>
            </div>

    <div class="main__list-dog">
        
    <?php
    
    if (isset($_GET['breed'])) {
        $selected_breed = $_GET['breed'];
        if($selected_breed === 'all'){
            echo "<h3>Все породы</h3>";
        }else{
            echo "<h3>".$selected_breed."</h3>";
        }
        echo "<ul class='list__dog list-reset flex'>";
        
        // Если выбрана опция "Показать все"
        if ($selected_breed === 'all') {
            // Запрос всех собак
            $query = "SELECT * FROM `idbc_dog` WHERE `show_idbc` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $_SESSION['showid']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='list__dog-item flex'>";
                    echo "<p>Кличка: " . htmlspecialchars($row['name_dog'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<p>Порода: " . htmlspecialchars($row['breed'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<a href='dogs.php?showid={$_SESSION['showid']}&id={$row['id']}'>Подробнее</a>";
                    echo "</li>";
                }
            } else {
                echo "<p>Собаки не найдены.</p>";
            }
        } else {
            // Фильтруем собак по породе
            $query = "SELECT * FROM `idbc_dog` WHERE `breed` = ? AND `show_idbc` = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $selected_breed, $_SESSION['showid']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='list__dog-item flex'>";
                    echo "<p>Кличка: " . htmlspecialchars($row['name_dog'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "</li>";
                }
            } else {
                echo "<p>Собаки данной породы не найдены.</p>";
            }
        }
    } else {
        // Если breed не передан, выводим всех собак
        $query = "SELECT * FROM `idbc_dog` WHERE `show_idbc` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['showid']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li class='list__dog-item'>";
                echo "<p>Имя: " . htmlspecialchars($row['name_dog'], ENT_QUOTES, 'UTF-8') . "</p>";
                echo "<p>Порода: " . htmlspecialchars($row['breed'], ENT_QUOTES, 'UTF-8') . "</p>";
                echo "</li>";
            }
        } else {
            echo "<p>Собаки не найдены.</p>";
        }
    }
?>
        </ul>
    </div>
    </div>
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