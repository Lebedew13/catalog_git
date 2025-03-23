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
$showid = $_SESSION['showid'];

$sql = "SELECT `name_show`, `date_show` FROM show_idbc WHERE id = '{$showid}'";
$show = $conn->query($sql);
$sql1 = "SELECT * FROM experts WHERE show_id = '{$showid}'";
$expets = $conn->query($sql1);

$query = "SELECT * FROM idbc_dog WHERE show_idbc = '{$showid}'";


$dog_query = $conn->query($query);


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
<script src="./js/index.js" type="text/javascript"></script>


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
                    <a href="catalog.php">На главную</a>
                    <a href="php/referi.php?showid=<?php echo htmlspecialchars($showid) ?>">Судьи</a>
                    <a href="php/editdog.php?showid=<?php echo htmlspecialchars($showid) ?>">Редактировать список собак</a>
                    <a href="exit.php">Выйти</a>
                </p>
                <h3 class="main__title">Редактирование списка собак на выставку: </h3>   
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
                   
                </div>
                <div class="insert_dog">
                   
                <ul class="editor__list flex">
              
                       <li class="editor__list-item flex">
                       
                       <form action="editdogAction.php" method="post" class="form__item">
                       <h3>
                        Добавить новую собаку
                    </h3>
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
                   <div class="button_block">  
                       <input class="main__form-input" type="submit" name="edit" value="Добавить">
                   </div>
                   </form>
                       </li>
                   </ul>
                </div>
                <div class="editor__block ">
                
                    <ul class="editor__list flex">
                   
                    <?php foreach ($dog_query as $dogs):  ?>
                        <li class="editor__list-item flex">
                        <form action="editdogAction.php" method="post" class="form__item">
                    <input type="text" hidden name="id" value="<?php echo $dogs['id']?>">       
                    <label for="breed">Порода</label>
                    <input class="main__form-input" type="text" id="breed" name="breed" value="<?php echo $dogs['breed']?>">
                    <label for="type_breed">Тип</label>
                    <input  class="main__form-input" type="text" id="type_breed" name="type_breed" value="<?php echo $dogs['type_breed']?>">
                    <label for="class_breed">Класс</label>
                    <input  class="main__form-input" type="text" id="class_breed" name="class_breed" value="<?php echo $dogs['class_breed']?>">
                    <label for="name_dog">Кличка</label>
                    <input  class="main__form-input" type="text" id="name_dog" name="name_dog" value="<?php echo $dogs['name_dog']?>">
                    <label for="gender">Пол</label>
                    <!-- <input  class="main__form-input" type="text" id="gender" name="gender" value="<?php echo $dogs['gender']?>"> -->
                    <select class="main__form-input" id="gender" name="gender">
                        <option value="Сука" <?php echo ($dogs['gender'] == 'Сука') ? 'selected' : ''; ?>>Сука</option>
                        <option value="Кобель" <?php echo ($dogs['gender'] == 'Кобель') ? 'selected' : ''; ?>>Кобель</option>
                    </select>
                    <label for="color">Цвет</label>
                    <input  class="main__form-input" type="text" id="color" name="color" value="<?php echo $dogs['color']?>">
                    <label for="owner">Заводчик</label>
                    <input  class="main__form-input" type="text" id="owner" name="owner" value="<?php echo $dogs['owner']?>">
                    <label for="breeder">Владелец</label>
                    <input  class="main__form-input" type="text" id="breeder" name="breeder" value="<?php echo $dogs['breeder']?>">
                    <div class="button_block">  
                        <input class="main__form-input" type="submit" name="edit" value="Изменить">
                        <input class="main__form-input"  type="submit"  name="edit" value="Удалить">
                    </div>
                    </form>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    
                    
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
