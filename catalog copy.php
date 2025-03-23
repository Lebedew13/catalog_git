<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'connectdb.php';
include 'functions/order.php';

if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
} 

if (!isset($_SESSION['showid'])) {
    exit;
}

$showid = $_SESSION['showid'];
$query = "SELECT DISTINCT breed FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $showid);
$stmt->execute();
$result = $stmt->get_result();
$breeds = $result->fetch_all(MYSQLI_ASSOC);

$queryOwners = "SELECT DISTINCT owner FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($queryOwners);
$stmt->bind_param("i", $showid);
$stmt->execute();
$resultOwners = $stmt->get_result();
$owners = $resultOwners->fetch_all(MYSQLI_ASSOC);

$queryClasses = "SELECT DISTINCT class_breed FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($queryClasses);
$stmt->bind_param("i", $showid);
$stmt->execute();
$resultClasses = $stmt->get_result();
$classes = $resultClasses->fetch_all(MYSQLI_ASSOC);

$queryTypes = "SELECT DISTINCT type_breed FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($queryTypes);
$stmt->bind_param("i", $showid);
$stmt->execute();
$resultTypes = $stmt->get_result();
$types = $resultTypes->fetch_all(MYSQLI_ASSOC);

$queryGenders = "SELECT DISTINCT gender FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($queryGenders);
$stmt->bind_param("i", $showid);
$stmt->execute();
$resultGenders = $stmt->get_result();
$genders = $resultGenders->fetch_all(MYSQLI_ASSOC);
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
    <script src="./js/index.js" type="text/javascript"></script>
</head>
<body>
<header class="header">
    <div class="container">
        <div class="header__cont">        
            <div class="header__text">
                <h1 class="header__text-h1">INTERNATIONAL DOG BREEDERS CLUB</h1>
                <p class="header__text-descr">Интернациональный клуб заводчиков собак</p>
            </div>
        </div>
    </div>
</header>
<main class="main">
    <div class="container">
    <div class="catalog flex">                
                <p>
                    <a href="php/referi.php?showid=<?php echo $_SESSION['showid'] ?>">Судьи</a>
                    <a href="exit.php">Выйти</a>
                </p>
                <h3 class="main__title">Каталог</h3>     
                <div class="catalog__showName flex">
                    <p>
                        <?php
                        $sql = "SELECT * FROM show_idbc WHERE id = '{$_SESSION['showid']}'";
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
                        $sql = "SELECT * FROM experts WHERE show_id = '{$_SESSION['showid']}'";
                        $show = $conn->query($sql);
                            foreach($show as $row){
                                echo "<p>{$row['lastname']}\n{$row['firstname']}</p>";
                            }                           
                        ?>
                </div>
            </div>
        <div class="main__order flex">
            <h4>Фильтр</h4>
            <form method="GET">
                <input type="hidden" name="showid" value="<?php echo $showid; ?>">
                <select name="breed">
                    <option value="all">Все породы</option>
                    <?php foreach ($breeds as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['breed']) ?>"> <?php echo htmlspecialchars($row['breed']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <select name="class">
                    <option value="all">Все классы</option>
                    <?php foreach ($classes as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['class_breed']) ?>"> <?php echo htmlspecialchars($row['class_breed']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <select name="type">
                    <option value="all">Все типы</option>
                    <?php foreach ($types as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['type_breed']) ?>"> <?php echo htmlspecialchars($row['type_breed']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <select name="gender">
                    <option value="all">Все</option>
                    <?php foreach ($genders as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['gender']) ?>"> <?php echo htmlspecialchars($row['gender']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <select name="owner">
                    <option value="all">Все владельцы</option>
                    <?php foreach ($owners as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['owner']) ?>"> <?php echo htmlspecialchars($row['owner']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Применить</button>
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


<?php
session_start();
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'connectdb.php';
include 'functions/order.php';

if (isset($_GET['showid'])) {
    $_SESSION['showid'] = $_GET['showid'];
} 

if (!isset($_SESSION['showid'])) {
    exit;
}

$showid = $_SESSION['showid'];
$query = "SELECT DISTINCT breed FROM `idbc_dog` WHERE `show_idbc` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $showid);
$stmt->execute();
$result = $stmt->get_result();
$breeds = $result->fetch_all(MYSQLI_ASSOC);
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
    <script src="./js/index.js" type="text/javascript"></script>
</head>
<body>
<header class="header">
    <div class="container">
        <div class="header__cont">        
            <div class="header__text">
                <h1 class="header__text-h1">INTERNATIONAL DOG BREEDERS CLUB</h1>
                <p class="header__text-descr">Интернациональный клуб заводчиков собак</p>
            </div>
        </div>
    </div>
</header>
<main class="main">
    <div class="container">
    
        <div class="main__dog flex">
            <div class="main__order flex">
                <h4>Выберите породу</h4>
                <a href='?showid=<?php echo $showid ?>&breed=all'>Показать все</a>
                <?php foreach ($breeds as $row): ?>
                    <a href='?showid=<?php echo $showid ?>&breed=<?php echo urlencode($row['breed']) ?>'><?php echo htmlspecialchars($row['breed']) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="main__list-dog">
                <?php
                if (isset($_GET['breed'])) {
                    $selected_breed = $_GET['breed'];
                    echo "<h3>" . ($selected_breed === 'all' ? "Все породы" : htmlspecialchars($selected_breed)) . "</h3>";
                    $query = ($selected_breed === 'all') ? "SELECT * FROM `idbc_dog` WHERE `show_idbc` = ?" : "SELECT * FROM `idbc_dog` WHERE `breed` = ? AND `show_idbc` = ?";
                    $stmt = $conn->prepare($query);
                    if ($selected_breed === 'all') {
                        $stmt->bind_param("i", $showid);
                    } else {
                        $stmt->bind_param("si", $selected_breed, $showid);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $dogsByClassTypeGender = [];
                    while ($dog = $result->fetch_assoc()) {
                        $dogsByClassTypeGender[$dog['class_breed']][$dog['type_breed']][strtolower($dog['gender'])][] = $dog;
                    }
                    foreach ($dogsByClassTypeGender as $class => $types) {
                        echo "<h3 class='catalog_class'>$class</h3>";
                        foreach ($types as $type => $genders) {
                            if ($type != 'none') echo "<h4 class='catalog_type'>$type</h4>";
                            foreach ($genders as $gender => $dogs) {
                                $genderTitle = ($gender === 'сука') ? 'Суки' : 'Кобели';
                                echo "<h5 class='catalog__gender'>$genderTitle</h5>";
                                foreach ($dogs as $dog) {
                                    echo "<div class='catalog__blockDog flex'>
                                        <div class='catalog__blockDog-text flex'>
                                            <p id='number__pp'>{$dog['id']}</p>
                                            <p>{$dog['name_dog']}</p>
                                        </div>            
                                        <p>({$dog['color']}, {$dog['breeder']}, {$dog['owner']})</p>
                                    </div>";
                                    
                                }
                            }
                        }
                    }
                    if ($result->num_rows === 0) echo "<p>Собаки не найдены.</p>";
                }
                ?>
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

сделай тут, при выборе породы в main ордер , нужен фильтрацию динамическую по всем параметрам 