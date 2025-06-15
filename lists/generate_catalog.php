<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// подключение к БД
include '../connectdb.php';
require_once '../vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$showid = $_SESSION['showid'] ?? null;
$ringid = $_SESSION['ringid'] ?? null;

$sql = "SELECT `name_show`, `date_show` FROM show_idbc WHERE id = '{$showid}'";
$show = $conn->query($sql);
$sql1 = "SELECT * FROM experts WHERE show_id = '{$showid}'";
$expets = $conn->query($sql1);





if (!$showid || !$ringid) {
    die('Не передан showid или ringid');
}

// получаем массив собак
$query = "
  SELECT * 
  FROM `catalog_dog_show_{$showid}_ring_{$ringid}`
  ORDER BY breed ASC, class_breed ASC, gender ASC, nameDog ASC
";
$result = $conn->query($query);
if (!$result) {
    die("Ошибка запроса: " . $conn->error);
}
$dogs = $result->fetch_all(MYSQLI_ASSOC);

// разделение на группы
$adultClasses = ['Взрослые', 'Взрослый', 'Зрелые', 'Зрелые (2+)'];

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

// создаём Word документ
$phpWord = new PhpWord();
$section = $phpWord->addSection();

$textStyle = [
    'name' => 'Calibri',
    'size' => 16,
    'align' => 'center',
    'width' => '100%',
    'bold' => true
];
// Стиль заголовков
$headingBreedStyle = ['bold' => true, 'size' => 16, 'underline' => 'single'];
$headingClassStyle = ['bold' => false, 'size' => 14];
$headingSexStyle   = ['bold' => true, 'size' => 12];
$dogTextStyle      = ['size' => 12];

$number = 1;
$currentBreed = '';
$currentClass = '';
$currentSex = '';

$section->addText('КАТАЛОГ', $textStyle, ['size' => 16, 'align' => 'center', 'bold' => true]);

foreach($show as $row){
    $showname = $row['name_show'];
    $section->addText($showname, $textStyle, ['align' => 'center', 'size' => 16]);
}


$section->addText('Эксперты', $textStyle, ['align' => 'center']);

foreach($expets as $row){
    $experts_fistname = $row['firstname'];
    $experts_lastname = $row['lastname']; 
    $section->addText($experts_fistname.' '.$experts_lastname, $textStyle, ['align' => 'center']);
}
function formatName($name) {
    $parts = explode(' ', $name);
    $formatted = $parts[0]; // Фамилия полностью
    if (isset($parts[1])) {
        $formatted .= ' ' . mb_substr($parts[1], 0, 1) . '.'; // Первая буква имени
    }
    if (isset($parts[2])) {
        $formatted .= ' ' . mb_substr($parts[2], 0, 1) . '.'; // Первая буква отчества
    }
    return $formatted;
}

// Функция для добавления списка собак
function addDogsToSection($section, $dogs, &$number, &$currentBreed, &$currentClass, &$currentSex, $styles) {
    foreach ($dogs as $dog) {
        $breed = trim($dog['breed']);
        $class = trim($dog['class_breed']);
        $sex = trim($dog['gender']);
        $name = trim($dog['nameDog']);
        $color = trim($dog['color']);
        $breeder = trim($dog['breeder']);
        $owner = trim($dog['owner']);


     
        

        // Порода
        if ($breed !== $currentBreed) {
            $section->addText(htmlspecialchars($breed), $styles['breed'], ['align' => 'center']);
            $currentBreed = $breed;
            $currentClass = '';
            $currentSex = '';
        }

        // Класс
        if ($class !== $currentClass) {
            $section->addText(htmlspecialchars($class), $styles['class'],  ['align' => 'center']);
            $currentClass = $class;
            $currentSex = '';
        }

        // Пол
        if ($sex !== $currentSex) {
            $section->addText(mb_strtoupper(htmlspecialchars($sex)), $styles['sex']);
            $currentSex = $sex;
        }

        // Собака
        $dogInfo = "{$number}. " . htmlspecialchars($name) .
                   " (" . htmlspecialchars($color) . 
                   ", зав. " . htmlspecialchars(formatName($breeder)) .
                   ", вл. " . htmlspecialchars(formatName($owner)) . ")";
        $section->addText($dogInfo, $styles['dog']);

        $number++;
    }
}

// Сначала младшие
addDogsToSection($section, $juniorDogs, $number, $currentBreed, $currentClass, $currentSex, [
    'breed' => $headingBreedStyle,
    'class' => $headingClassStyle,
    'sex'   => $headingSexStyle,
    'dog'   => $dogTextStyle,
]);

// Потом взрослые
addDogsToSection($section, $adultDogs, $number, $currentBreed, $currentClass, $currentSex, [
    'breed' => $headingBreedStyle,
    'class' => $headingClassStyle,
    'sex'   => $headingSexStyle,
    'dog'   => $dogTextStyle,
]);

// очищаем вывод
if (ob_get_length()) {
    ob_end_clean();
}


// заголовки
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="catalog.docx"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// сохраняем прямо в выходной поток
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
exit;
