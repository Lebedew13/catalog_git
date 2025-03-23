<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include '../connectdb.php';
//include 'functions/selects.php';
include '../functions/order.php';



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

$query = "SELECT breed, MIN(id) as id FROM idbc_dog WHERE show_idbc = '{$showid}' GROUP BY breed ORDER BY position ASC";


$breed = $conn->query($query);

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
<!-- <script src="./js/main.js" type="text/javascript"></script> -->
<script src="../js/index.js" type="text/javascript">
</script>
<script src="../js/editdog.js" type="text/javascript"></script>
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
<script>
    document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.deleteDog').forEach(button => {
        button.addEventListener('click', function(event) {
            const dogId = button.getAttribute('data-id');
            let showid = button.getAttribute('data-showid');
            let ringid = button.getAttribute('data-ringid');


            console.log("Данные отправляются: ", {
                    dogid: dogId,
                    ringid: ringid,
                    showid: showid,
                    action: 'Удалить'
                });
            if (confirm('Вы уверены, что хотите удалить эту собаку?')) {
                // Отправляем запрос на удаление
                fetch('deleteDog.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: dogId ,
                        ringid: ringid,
                        showid: showid
                    }),  // Передаем данные как JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const dogRow = document.getElementById('dog-row-' + dogId);
                        if (dogRow) {
                            alert('Собака удалена');
                            location.reload();
                            //dogRow.remove();
                        } else {
                            alert('Элемент не найден на странице');
                        }
                    } else {
                        alert('Ошибка удаления: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Ошибка при удалении: ' + error);
                    console.log(error);
                });

            }
        });
    });
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
                        $sql = "SELECT * FROM catalog_dog_show_{$showid}_ring_{$ringid} ORDER BY position, breed, class_breed, type_breed, gender";

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
                                                echo "<div class='dog-item flex'>";
                                                echo "<div class='dog__text flex'>
                                                        <p>".htmlspecialchars($counter)."</p>
                                                        <p><strong>Кличка:</strong> " . htmlspecialchars($dog['nameDog']) . "</p>
                                                    </div>";
                                                echo "<div class='dog__info flex'>
                                                <p><strong>Окрас:</strong> " . htmlspecialchars($dog['color']) . "</p>
                                                <p><strong>Владелец:</strong> " . htmlspecialchars($dog['owner']) . "</p>
                                               <p><strong>Заводчик:</strong> " . htmlspecialchars($dog['breeder']) . "</p>
                                                </div>";
                                          
                                                
                                                echo "</div>";
                                                $counter++;
                                            }
                                        }
                                    }
                                    
                                } else {
                                    foreach ($types_or_genders as $gender => $dogs) {
                                        echo "<h4>Пол: " . ($gender == 'Кобель' ? 'Кобель' : 'Сука') . "</h4>";
                                        foreach ($dogs as $dog) {
                                            $id = $dog['id'];
                                            echo "<div class='dog-item flex'>";
                                            echo "<div class='dog__text flex'>
                                                    <p><strong>".htmlspecialchars($counter)."</strong></p>
                                                    <p><strong>Кличка:</strong> " . htmlspecialchars($dog['nameDog']) . "</p>
                                                </div>";
                                            echo "<div class='dog__info flex'>
                                            <p><strong>Окрас:</strong> " . htmlspecialchars($dog['color']) . "</p>
                                            <p><strong>Владелец:</strong> " . htmlspecialchars($dog['owner']) . "</p>
                                           <p><strong>Заводчик:</strong> " . htmlspecialchars($dog['breeder']) . "</p>
                                            </div>";
                                            echo "<div class='button__block flex'>
                                                <button class='formDogBlock' data-id='modal-$id'></button>
                                                <input class='deleteDog' id='dog-row-$id' data-id='$id' data-showid='$showid' data-ringid='$ringid' type='submit' name='action' value=''>
                                            </div>";
                                            
                                            echo "</div>";
                                            echo "<!-- Модальное окно -->
                                            <div id='modal-$id' class='modal'>
                                                <div class='modal-content'>
                                                    <span class='close' data-id='modal-$id'>&times;</span>
                                                    <form action='editdogAction.php' method='POST' class='form__item'>
                                                        <input type='hidden' name='showid' value='$showid'>
                                                        <input type='hidden' name='ringid' value='$ringid'>
                                                        <input type='hidden' name='id' value='$id'>       
                                                        <input type='hidden' name='action' id='action'>
                                                        <label for='breed'>Порода</label>
                                                        <input class='main__form-input' type='text' id='breed' name='breed' value='".htmlspecialchars ($dog['breed'])."'>
                                                        <label for='type_breed'>Тип</label>
                                                        <input class='main__form-input' type='text' id='type_breed' name='type_breed' value='".htmlspecialchars ($dog['type_breed'])."'>
                                                        <label for='class_breed'>Класс</label>
                                                        <input class='main__form-input' type='text' id='class_breed' name='class_breed' value='".htmlspecialchars ($dog['class_breed'])."'>
                                        
                                                        <label for='name_dog'>Кличка</label>
                                                        <input class='main__form-input' type='text' id='name_dog' name='name_dog' value='".htmlspecialchars ($dog['nameDog'])."'>
                                        
                                                        <label for='gender'>Пол</label>
                                                        <select class='main__form-input' id='gender' name='gender'>
                                                            <option value='Сука' " . (htmlspecialchars ($gender) == 'Сука' ? 'selected' : '') . ">Сука</option>
                                                            <option value='Кобель' " . (htmlspecialchars ($gender) == 'Кобель' ? 'selected' : '') . ">Кобель</option>
                                                        </select>
                                        
                                                        <label for='color'>Цвет</label>
                                                        <input class='main__form-input' type='text' id='color' name='color' value='".htmlspecialchars ($dog['color'])."'>
                                        
                                                        <label for='owner'>Владелец</label>
                                                        <input class='main__form-input' type='text' id='owner' name='owner' value='".htmlspecialchars ($dog['owner'])."'>
                                        
                                                        <label for='breeder'>Заводчик</label>
                                                        <input class='main__form-input' type='text' id='breeder' name='breeder' value='".htmlspecialchars ($dog['breeder'])."'>

                                                        <div class='button_block'>  
                                                            <input class='main__form-input' data-action='Изменить' type='submit' name='action' value='Изменить'>
                                                            <input class='main__form-input' data-action='Удалить' type='submit' name='action' value='Удалить'>
                                                        </div>
                                                    </form>   
                                                </div>
                                            </div>";
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