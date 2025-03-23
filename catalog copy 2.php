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
$filters = [
    'breed' => $_GET['breed'] ?? 'all',
    'class' => $_GET['class'] ?? 'all',
    'type' => $_GET['type'] ?? 'all',
    'gender' => $_GET['gender'] ?? 'all',
    'owner' => $_GET['owner'] ?? 'all'
];

$queryFilters = "SELECT DISTINCT breed, class_breed, type_breed, gender, owner FROM idbc_dog WHERE show_idbc = ?";
$stmt = $conn->prepare($queryFilters);
$stmt->bind_param("i", $showid);
$stmt->execute();
$resultFilters = $stmt->get_result();
$filterData = $resultFilters->fetch_all(MYSQLI_ASSOC);

$breeds = array_unique(array_column($filterData, 'breed'));
$classes = array_unique(array_column($filterData, 'class_breed'));
$types = array_unique(array_column($filterData, 'type_breed'));
$genders = array_unique(array_column($filterData, 'gender'));
$owners = array_unique(array_column($filterData, 'owner'));

$query = "SELECT * FROM idbc_dog WHERE show_idbc = ?";
$params = ["i", $showid];

foreach ($filters as $key => $value) {
    if ($value !== 'all') {
        $query .= " AND $key = ?";
        $params[0] .= "s";
        $params[] = $value;
    }
}

$stmt = $conn->prepare($query);
$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();
$dogs = $result->fetch_all(MYSQLI_ASSOC);
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
    <form method="GET">
        <input type="hidden" name="showid" value="<?php echo $showid; ?>">
        <?php foreach (["breed" => $breeds, "class" => $classes, "type" => $types, "gender" => $genders, "owner" => $owners] as $name => $options): ?>
            <select name="<?php echo $name; ?>">
                <option value="all">Все</option>
                <?php foreach ($options as $option): ?>
                    <option value="<?php echo htmlspecialchars($option); ?>" <?php echo ($filters[$name] === $option) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($option); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endforeach; ?>
        <button type="submit">Применить</button>
    </form>
    <?php if (empty($dogs)): ?>
        <p>Собаки не найдены.</p>
    <?php else: ?>
        <?php foreach ($dogs as $dog): ?>
            <div class='dog-info'>
                <p><strong><?php echo htmlspecialchars($dog['name_dog']); ?></strong> (<?php echo htmlspecialchars($dog['color']); ?>, <?php echo htmlspecialchars($dog['breeder']); ?>, <?php echo htmlspecialchars($dog['owner']); ?>)</p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
