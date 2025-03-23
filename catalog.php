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
$showid = $_SESSION['showid'];

$sql = "SELECT `name_show`, `date_show` FROM show_idbc WHERE id = '{$showid}'";
$show = $conn->query($sql);
$sql1 = "SELECT * FROM experts WHERE show_id = '{$showid}'";
$expets = $conn->query($sql1);

$query = "SELECT breed, MIN(id) as id FROM idbc_dog WHERE show_idbc = '{$showid}' GROUP BY breed ORDER BY position ASC";


$breed = $conn->query($query);

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let sortable = new Sortable(document.getElementById("sortable"), {
        animation: 150,
        onEnd: function () {
            saveOrder();
        }
    });

    function saveOrder() {
        let items = document.querySelectorAll(".breed-item");
        let order = [];

        items.forEach((item, index) => {
            order.push({
                breed: item.textContent.trim(), // Теперь передаем не id, а саму породу
                position: index + 1
            });
        });

        console.log("Сохраняем порядок:", order); // Проверка перед отправкой

        fetch("save_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(order)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Ответ сервера:", data);
            if (data.status === "success") {
                location.reload();
            } else {
                alert("Ошибка сохранения: " + data.message);
            }
        })
        .catch(error => console.error("Ошибка сохранения:", error));
    }
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
         
            <div class="catalog flex">                
                <p>
                    <a href="php/referi.php?showid=<?php echo htmlspecialchars($showid) ?>">Судьи</a>
                    <a href="editdog.php?showid=<?php echo htmlspecialchars($showid) ?>">Редактировать список собак</a>
                    <a href="exit.php">Выйти</a>
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
               
                <div class="main__dog flex">
                <div class="breed__order flex">
                <h2>Порядок пород</h2>
                <ul id="sortable" class="order__list list-reset flex">
                    <?php foreach ($breed as $dog): ?>
                        <li class="breed-item" data-id="<?= htmlspecialchars($dog['id']); ?>">
                            <?= htmlspecialchars($dog['breed']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                    <button id="saveOrder" class="main__form-button">Сохранить порядок</button>
                </div>
                <div class="dog__block flex" id="dog-list">
                        <h3>Список собак по породам</h3>
                        <?php 
                        $sql = "SELECT * FROM `idbc_dog` WHERE `show_idbc` = '{$showid}' ORDER BY position, breed, class_breed, type_breed, gender";

                        $result = $conn->query($sql);

                        $dogs_by_breed = [];

                        while ($dog = $result->fetch_assoc()) {
                            $breed = trim($dog['breed']);
                            $class = trim($dog['class_breed']);
                            $type = trim($dog['type_breed']);
                            $gender = trim($dog['gender']); // 'male' или 'female'

                            if (!isset($dogs_by_breed[$breed])) {
                                $dogs_by_breed[$breed] = [];
                            }

                            if (!isset($dogs_by_breed[$breed][$class])) {
                                $dogs_by_breed[$breed][$class] = [];
                            }

                            if (!isset($dogs_by_breed[$breed][$class][$gender])) {
                                $dogs_by_breed[$breed][$class][$gender] = [];
                            }

                            if ($breed == 'Американский булли' && !empty($type)) {
                                if (!isset($dogs_by_breed[$breed][$class][$type])) {
                                    $dogs_by_breed[$breed][$class][$type] = [];
                                }
                                if (!isset($dogs_by_breed[$breed][$class][$type][$gender])) {
                                    $dogs_by_breed[$breed][$class][$type][$gender] = [];
                                }
                                $dogs_by_breed[$breed][$class][$type][$gender][] = $dog;
                            } else {
                                $dogs_by_breed[$breed][$class][$gender][] = $dog;
                            }
                        }
                        $counter = 1;
                        foreach ($dogs_by_breed as $breed => $classes) {
                            echo "<div class='breed__section'>";
                            echo "<h2>Порода: " . htmlspecialchars($breed) . "</h2>";

                            foreach ($classes as $class => $types_or_genders) {
                                echo "<h3>Класс: " . htmlspecialchars($class) . "</h3>";

                                if ($breed == 'Американский булли') {
                                    foreach ($types_or_genders as $type => $genders) {
                                        echo "<h4>Тип: " . htmlspecialchars($type) . "</h4>";
                                        foreach ($genders as $gender => $dogs) {
                                    
                                            echo "<h5>Пол: " . ($gender === 'Сука' ? 'Сука' : 'Кабель') . "</h5>";
                                            foreach ($dogs as $dog) {
                                                echo "<div class='dog-item'>";
                                                echo "<p>".htmlspecialchars($counter)."</p>";
                                                echo "<p><strong>Кличка:</strong> " . htmlspecialchars($dog['name_dog']) . "</p>";
                                                echo "<p><strong>Окрас:</strong> " . htmlspecialchars($dog['color']) . "</p>";
                                                echo "<p><strong>Владелец:</strong> " . htmlspecialchars($dog['owner']) . "</p>";
                                                echo "<p><strong>Заводчик:</strong> " . htmlspecialchars($dog['breeder']) . "</p>";
                                                
                                                echo "</div>";
                                                $counter++;
                                            }
                                        }
                                    }
                                    
                                } else {
                                    foreach ($types_or_genders as $gender => $dogs) {
                                        echo "<h4>Пол: " . ($gender == 'Кобель' ? 'Кобель' : 'Сука') . "</h4>";
                                        foreach ($dogs as $dog) {
                                            echo "<div class='dog-item'>";
                                            echo "<p>".htmlspecialchars($counter)."</p>";
                                            echo "<p><strong>Кличка:</strong> " . htmlspecialchars($dog['name_dog']) . "</p>";
                                            echo "<p><strong>Окрас:</strong> " . htmlspecialchars($dog['color']) . "</p>";
                                            echo "<p><strong>Владелец:</strong> " . htmlspecialchars($dog['owner']) . "</p>";
                                            echo "<p><strong>Заводчик:</strong> " . htmlspecialchars($dog['breeder']) . "</p>";
                                            echo "</div>";
                                            $counter++;
                                        }


                                    }
                                    
                                }
                         
                            
                            }
                          
                            echo "</div>"; 
                         
                        }


                        ?>
                    </div>

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