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

$query = "SELECT breed, type_breed, MIN(id)  as id FROM `catalog_dog_show_{$showid}_ring_{$ringid}` GROUP BY breed, type_breed ORDER BY position, sub_position ASC";

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
<script src="../js/searchDog.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let sortable = new Sortable(document.getElementById("sortable-list"), {
        animation: 150,
        onEnd: function () {
            saveOrder();
        }
    });
    let sortableSub = new Sortable(document.getElementById("sub-breeds"), {
        animation: 150,
        onEnd: function () {
            saveOrder();
        }
    });

    function saveOrder() {
        let items = document.querySelectorAll(".breed-item");
        let subitems = document.querySelectorAll(".sub-breed");
        let order = [];
        //const urlParams = new URLSearchParams(window.location.search);
        const showid = document.getElementById('showid').value;
        const ringid = document.getElementById('ringid').value;

        items.forEach((item, index) => {
            let breed = item.textContent.trim(); // Убираем лишние пробелы и переносы строк
            breed = breed.replace(/\n/g, '').replace(/\s{2,}/g, ' '); // Убираем символы новой строки и лишние пробелы
            
            // Очищаем от типов породы
            breed = breed.replace(/(Классик|Стандарт|XL|Покет|Экзот|Микро)/g, '').trim(); // Убираем типы породы (можно добавить другие типы по мере необходимости)

            order.push({
                breed: breed, // Теперь передаем очищенную породу
                position: index + 1
            });
        });

        subitems.forEach((item, index) => {
            let typeBreed = item.textContent.trim();
            typeBreed = typeBreed.replace(/\n/g, '').replace(/\s{2,}/g, ' '); // Очищаем подтипы
            order.push({
                type_breed: typeBreed, // Теперь передаем очищенный подтип
                sub_position: index + 1.1 // Пример использования дробной позиции
            });
        });

        const dataToSend = {
            order: order,
            showid: showid,
            ringid: ringid
        };

        console.log("Сохраняем порядок:", dataToSend);

        fetch("save_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataToSend)
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Открытие модального окна
        let button = document.getElementById('modal__search');
            button.addEventListener('click', function () {
            document.getElementById('modal-search').style.display = 'flex';
        });
    

    // Закрытие модального окна
    document.querySelectorAll('.close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
                document.getElementById('modal-search').style.display = 'none';
        });
    });

    // Закрытие окна при клике вне формы
    window.addEventListener('click', function (event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

   
});
document.addEventListener("DOMContentLoaded", function () {
    const searchForm = document.getElementById("search-form");
    const searchResults = document.getElementById("search-results");

    // Обработчик поиска
    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const nameDog = document.getElementById("search-input").value.trim();
        const showid = document.getElementById("showid").value;
        const ringid = document.getElementById("ringid").value;

        if (nameDog === "") return;

        fetch("searchDog.php?name_dog=" + encodeURIComponent(nameDog) + "&showid=" + showid + "&ringid=" + ringid)
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data; // Обновляем результаты
            })
            .catch(error => console.error("Ошибка поиска:", error));
    });

    // Обработчик изменения позиции
    searchResults.addEventListener("submit", function (event) {
        if (event.target.classList.contains("position-form")) {
            event.preventDefault();
            const formData = new FormData(event.target);

            fetch("update_position.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Выводим сообщение об успехе
                document.getElementById("modal-search").style.display = "none";
                location.reload();
            })
            .catch(error => console.error("Ошибка обновления позиции:", error));
        }
    });

    // Закрытие модального окна
    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("modal-search").style.display = "none";
    });

    document.getElementById("modal__search").addEventListener("click", function () {
        document.getElementById("modal-search").style.display = "flex";
    });
});
</script> 
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Открытие модального окна
        let button = document.getElementById('modal__add');
            button.addEventListener('click', function () {
            document.getElementById('modal-add').style.display = 'flex';
        });
    

    // Закрытие модального окна
    document.querySelectorAll('.close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
                document.getElementById('modal-add').style.display = 'none';
        });
    });

    // Закрытие окна при клике вне формы
    window.addEventListener('click', function (event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

});

document.addEventListener("DOMContentLoaded", function() {
 // Обработчик изменения позиции
    const addForm = document.getElementById("add-form");

    // Обработчик поиска
    addForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const showid = document.getElementById("showid").value;
        const ringid = document.getElementById("ringid").value;
        const breed = document.getElementById("breed").value;
        const type_breed = document.getElementById("type_breed").value;
        const class_breed = document.getElementById("class_breed").value;
        const name_dog = document.getElementById("name_dog").value;
        const gender = document.getElementById("gender").value;
        const color = document.getElementById("color").value;
        const owner = document.getElementById("owner").value;
        const breeder = document.getElementById("breeder").value;

        const dataToSend = {
            showid: showid,
            ringid: ringid,
            breed: breed,
            type_breed: type_breed,
            class_breed: class_breed,
            name_dog: name_dog,
            gender: gender,
            color, color,
            owner: owner,
            breeder: breeder
        };

        console.log("Добавляем собаку:", dataToSend);

        fetch("addDog.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Ответ сервера:", data);
            if (data.status === "success") {
                alert('Собака успешно добавлена!');
                location.reload();
            } else {
                alert("Ошибка сохранения: " + data.message);
            }
        })
        .catch(error => console.error("Ошибка сохранения:", error));
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
                <div class="breed__order ">
                    <div class="fixed__order flex">
                    <h2>Порядок пород</h2>
                <?php
                $breedsList = []; // Для группировки пород и подтипов

                // Группируем данные по породам и их подтипам
                            // Группируем данные по породам и их подтипам
                foreach ($breed as $dog) {
                    $breedName = trim($dog['breed']);      // Название породы
                    $typeName = trim($dog['type_breed']);  // Подтип породы

                    // Если порода еще не добавлена, создаем для неё массив
                    if (!isset($breedsList[$breedName])) {
                        $breedsList[$breedName] = [];
                    }

                    // Если подтип не "none" и не пустой, добавляем его в массив для этой породы
                    if ($typeName !== "none" && !empty($typeName)) {
                        if (!in_array($typeName, $breedsList[$breedName])) {
                            $breedsList[$breedName][] = $typeName;
                        }
                    }
                }
                ?>

                <ul id="sortable-list" class="order__list list-reset flex">
                    <input type="hidden" value="<?php echo $showid ?>" id="showid">
                    <input type="hidden" value="<?php echo $ringid ?>" id="ringid">
                    <?php foreach ($breedsList as $breed => $types): ?>
                        <li class="breed-item" data-breed="<?= htmlspecialchars($breed); ?>">
                            
                            <a class="breed__item-name" href="#<?php echo $breed ?>"><?= htmlspecialchars($breed); ?></a>
                            <?php if (!empty($types)): // Если есть подтипы ?>
                                <ul id="sub-breeds" class="sub-breeds list-reset flex">
                                    <?php foreach ($types as $type): ?>
                                        <li class="sub-breed" data-sub-breed="<?= htmlspecialchars($type); ?>">
                                            <a class="subbreed__item-name" href="#<?php echo $type ?>"><?= htmlspecialchars($type); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <button id="saveOrder" class="main__form-button">Сохранить порядок</button>
                <button id="modal__search" class="main__form-button search__dog">Найти собаку</button>
                <div class="modal" id='modal-search'>
                    <div class="modal-content">
                        <span class='close'>&times;</span>
                        <div class="search__block flex">
                            <form id="search-form" class="main__form flex">
                                <p>Введите кличку собаки для поиска</p>
                                <input type="hidden" id="showid" value="<?= $showid ?>">
                                <input type="hidden" id="ringid" value="<?= $ringid ?>">
                                <input class="main__form-input" type="search" id="search-input" name="name_dog">
                                <input class="main__form-input" type="submit" value="Найти собаку">
                            </form>

                            <div id="search-results" class="dog__info">
                                <p>Здесь отобразятся результаты поиска...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button id="modal__add" class="main__form-button add__dog">Добавить собаку</button>
                <div class="modal" id='modal-add'>
                    <div class="modal-content">
                        <span class='close'>&times;</span>
                        <div class="add__block flex">
                            <form id="add-form" class="main__form flex">
                                <p>Добавить собаку:</p>
                                <input type="hidden" id="showid" value="<?= $showid ?>">
                                <input type="hidden" id="ringid" value="<?= $ringid ?>">
                                <label for="breed">Порода</label>
                                <input class="main__form-input" type="text" id="breed" name="breed">
                                <label for="type_breed">Тип</label>
                                <input  class="main__form-input" type="text" id="type_breed" name="type_breed" >
                                <label for="class_breed">Класс</label>
                                <input  class="main__form-input" type="text" id="class_breed" name="class_breed">
                                <label for="name_dog">Кличка</label>
                                <input  class="main__form-input" type="text" id="name_dog" name="name_dog" >
                                <label for="gender">Пол</label>
                                <select  class="main__form-input" id="gender" name="gender" >
                                    <option value="Сука" >Сука</option>
                                    <option value="Кобель" >Кобель</option>
                                </select>
                                <label for="color">Цвет</label>
                                <input  class="main__form-input" type="text" id="color" name="color">
                                <label for="owner">Заводчик</label>
                                <input  class="main__form-input" type="text" id="owner" name="owner">
                                <label for="breeder">Владелец</label>
                                <input  class="main__form-input" type="text" id="breeder" name="breeder">
                                <input class="main__form-input" type="submit" value="Добавить собаку">
                            </form>
                        </div>
                    </div>
                </div>
                                 
                </div>
                
        </div>
            <div class="dog__block flex" id="dog-list">
            <h3>Список собак по породам</h3>
            <?php 
            $sql = "SELECT * FROM catalog_dog_show_{$showid}_ring_{$ringid} ORDER BY position, sub_position, breed,
                CASE  class_breed
                WHEN 'Бэби' THEN 1
                WHEN 'Щенки мини' THEN 2
                WHEN 'Щенки макси' THEN 3
                WHEN 'Щенки' THEN 4
                WHEN 'Юниор мини' THEN 5
                WHEN 'Юниор макси' THEN 6
                WHEN 'Юниор' THEN 7
                WHEN 'Взрослые' THEN 8
                WHEN 'Зрелые' THEN 9
                ELSE 999
                END, type_breed, gender";

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
            
                if (!isset($dogs_by_breed[$breed][$type])) {
                    $dogs_by_breed[$breed][$type] = [];
                }
            
                if (!isset($dogs_by_breed[$breed][$type][$class])) {
                    $dogs_by_breed[$breed][$type][$class] = [];
                }
            
                if (!isset($dogs_by_breed[$breed][$type][$class][$gender])) {
                    $dogs_by_breed[$breed][$type][$class][$gender] = [];
                }
            
                // Вставляем собаку в соответствующий массив
                $dogs_by_breed[$breed][$type][$class][$gender][] = $dog;
            }
            
            $counter = 1;
            foreach ($dogs_by_breed as $breed => $types) {
                echo "<div class='breed__section'>";
                echo "<h2 id='$breed'>Порода: " . htmlspecialchars($breed) . "</h2>";
                
                foreach ($types as $type => $classes) {
                    if($type != 'none'){
                    echo "<h3 id='$type'>Тип: " . htmlspecialchars($type) . "</h3>";
                    }
                    foreach ($classes as $class => $genders) {
                        echo "<h4>Класс: " . htmlspecialchars($class) . "</h4>";
                        
                        foreach ($genders as $gender => $dogs) {
                            echo "<h5>Пол: " . ($gender == 'Сука' ? 'Сука' : 'Кобель') . "</h5>"; // Печатаем пол
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

                                            <div class='button__block flex'>  
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