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

if (isset($_GET['ringid'])) {
    $_SESSION['ringid'] = $_GET['ringid'];
} 

$showid = $_SESSION['showid'];
$ringid = $_SESSION['ringid'];

$sql = "SELECT * FROM `ring` WHERE showid = '{$showid}'";
$res = $conn->query($sql);
foreach($res as $ringList){
    if($ringList['id'] == $ringid){
    $ringName = $ringList['name_ring'];
    }
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
<script src="../js/index.js" type="text/javascript"></script>
<script src="../js/editdog.js" type="text/javascript"></script>
<script src="../js/getListDog.js" type="text/javascript"></script>
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
                <h3 class="main__title">Формируем список собак для <?php echo $ringName?></h3>
                <p>
                    Внести изменения можно как здесь так и в основном каталоге!
                </p>
                <ul class="listDog flex list-reset">

                
                <?php
            $sql = "SELECT * FROM catalog_dog_show_{$showid}_ring_{$ringid}";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $dogs_in_ring = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

               foreach($dogs_in_ring as $dogs){
                    $breed = $dogs['breed'];
                    $class = $dogs['class_breed'];
                    $type = $dogs['type_breed'];
                    $namedog = $dogs['nameDog'];
                    $gender = $dogs['gender'];
                    $color = $dogs['color'];
                    $owner = $dogs['owner'];
                    $breeder = $dogs['breeder'];
                    $id = $dogs['id'];

                    echo "<li class='listDog-item flex'>
                    <div class='dog__info flex'>
                       <p>Кличка: $namedog, класс: $class, тип: $type</p>
                    </div>
                    <div class='button__block flex'>
                        <button class='formDogBlock' data-id='modal-$id'></button>
                        <input class='deleteDog' id='dog-row-$id' data-id='$id' data-showid='$showid' data-ringid='$ringid' type='submit' name='action' value=''>
                    </div>


                   
                    </li>
            
                <!-- Модальное окно -->
                <div id='modal-$id' class='modal'>
                    <div class='modal-content'>
                        <span class='close' data-id='modal-$id'>&times;</span>
                        <form action='editdogAction.php' method='POST' class='form__item'>
                            <input type='hidden' name='showid' value='$showid'>
                            <input type='hidden' name='ringid' value='$ringid'>
                            <input type='hidden' name='id' value='$id'>       
                            <input type='hidden' name='action' id='action'>
                            <label for='breed'>Порода</label>
                            <input class='main__form-input' type='text' id='breed' name='breed' value='".htmlspecialchars ($breed)."'>
                            <label for='type_breed'>Тип</label>
                            <input class='main__form-input' type='text' id='type_breed' name='type_breed' value='".htmlspecialchars ($type)."'>
                            <label for='class_breed'>Класс</label>
                            <input class='main__form-input' type='text' id='class_breed' name='class_breed' value='".htmlspecialchars ($class)."'>
            
                            <label for='name_dog'>Кличка</label>
                            <input class='main__form-input' type='text' id='name_dog' name='name_dog' value='".htmlspecialchars ($namedog)."'>
            
                            <label for='gender'>Пол</label>
                            <select class='main__form-input' id='gender' name='gender'>
                                <option value='Сука' " . (htmlspecialchars ($gender) == 'Сука' ? 'selected' : '') . ">Сука</option>
                                <option value='Кобель' " . (htmlspecialchars ($gender) == 'Кобель' ? 'selected' : '') . ">Кобель</option>
                            </select>
            
                            <label for='color'>Цвет</label>
                            <input class='main__form-input' type='text' id='color' name='color' value='".htmlspecialchars ($color)."'>
            
                            <label for='owner'>Владелец</label>
                            <input class='main__form-input' type='text' id='owner' name='owner' value='".htmlspecialchars ($owner)."'>
            
                            <label for='breeder'>Заводчик</label>
                            <input class='main__form-input' type='text' id='breeder' name='breeder' value='".htmlspecialchars ($breeder)."'>

                            <div class='button_block'>  
                                <input class='main__form-input' data-action='Изменить' type='submit' name='action' value='Изменить'>
                                <input class='main__form-input' data-action='Удалить' type='submit' name='action' value='Удалить'>
                            </div>
                        </form>   
                    </div>
                </div>";
               }

                ?>
            </div>
            <div class="button__start">
                <a class="main__form-input-a" href="../catalog/catalog.php?showid=<?php echo $showid?>&ringid=<?php echo $ringid?>">Начать ринг</a>
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
