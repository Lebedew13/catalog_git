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

if (!$showid || !$ringid) {
    die('Не передан showid или ringid');
}

// Данные выставки
$show = $conn->query("SELECT * FROM show_idbc WHERE id = '{$showid}'");
$show_arr = $show->fetch_assoc();

$ring = $conn->query("SELECT * FROM ring WHERE id = '{$ringid}'");
$ring_arr = $ring->fetch_assoc();
// Эксперты
$experts = $conn->query("SELECT * FROM experts WHERE show_id = '{$showid}'");
$experts_arr = $experts->fetch_assoc();
// Собаки
$result = $conn->query("SELECT * FROM `catalog_dog_show_{$showid}_ring_{$ringid}`");
if (!$result) {
    die("Ошибка запроса: " . $conn->error);
}



$breeds = [];

// Группируем по породам
while ($row = $result->fetch_assoc()) {
    $breed = $row['breed'];
    if (!isset($breeds[$breed])) {
        $breeds[$breed] = [];
    }
    $breeds[$breed][] = $row;
    // var_dump($row);
    $class = $row['class_breed'];
}

// Создаем Word документ
$phpWord = new PhpWord();

// Стиль заголовков
$titleStyle = ['size' => 18, 'align' => 'center'];
$subTitleStyle = ['bold' => true, 'size' => 14];
$expertStyle = ['size' => 12];
$textStyle = ['size' => 12, 'align' => 'center'];
$breedStyle = ['underline' => 'single', 'bold' => true, 'size' => 14];
$nameStyle = ['bold' => true, 'size' => 12];
$infoStyle = ['size' => 12];
$underTextstyle = ['size' => 7, 'align' => 'center'];
function sortClassesCustom($a, $b) {
    $order = [
        'Бэби' => 1,
        'Щенки' => 2, 'Щенок' => 2, 
        'Щенки мини' => 3, 'Щенок мини' => 3, 
        'Щенки макси' => 4, 'Щенок макси' => 4, 
        'Щенки экзот' => 5, 'Щенок экзот' => 5, 
        'Юниоры' => 6, 'Юниор' => 6,
        'Юниоры мини' => 7, 'Юниор мини' => 7,
        'Юниоры макси' => 8, 'Юниор макси' => 8,
        'Юниоры экзот' => 9, 'Юниор экзот' => 9,
        'Взрослые' => 10, 'Взрослый' => 10,
        'Зрелые' => 11, 'Зрелые (2+)' => 11, '2+' => 11,
    ];

    // Функция определяет приоритет по первым словам
    $getWeight = function ($class) use ($order) {
        foreach ($order as $prefix => $weight) {
            if (mb_stripos($class, $prefix) === 0) {
                return $weight;
            }
        }
        return 999; // если не найдено — в конец
    };

    return $getWeight($a) <=> $getWeight($b);
}

function cmToTwip($cm) {
    return (int)($cm * 567);
}
    $section = $phpWord->addSection([
        'marginTop' => cmToTwip(0.7),
    'marginLeft' => cmToTwip(0.5),
    'marginRight' => cmToTwip(0.5),
    'marginBottom' => cmToTwip(1.5)
    ]);
 $table = $section->addTable([
    'borderSize' => 8,     // ширина рамки (8 = 1 пиксель примерно)
    'borderColor' => '000000', // цвет рамки - черный
    'cellMargin' => 30,    // внутренние отступы ячейки (80 = 0.5pt примерно)   
  ]);
  $table->addRow(700, ['cantSplit' => true]);
// Добавляем одну ячейку, которая занимает всю строку
    $cell = $table->addCell(11000);
    // Шапка
    $cell->addText(htmlspecialchars($show_arr['name_show']) .' ' .htmlspecialchars($show_arr['city_show']), $titleStyle, ['align' => 'center']);
    $cell->addText('Ринг:  '.htmlspecialchars($ring_arr['name_ring'] . ' - '.htmlspecialchars($experts_arr['lastname']). ' '.htmlspecialchars($experts_arr['firstname']) ), $titleStyle,  ['align' => 'center']);
// Для каждой собаки

// Теперь проходим по породам
foreach ($breeds as $breedName => $dogs) {
    // Определяем уникальные классы
    $classes = array_unique(array_column($dogs, 'class_breed'));
    usort($classes, 'sortClassesCustom');
    foreach ($classes as $class) {
        // Фильтруем собак по классу
        $dogsInClass = array_filter($dogs, function ($dog) use ($class) {
            return $dog['class_breed'] === $class;
        });

        // Группируем по полу: сначала суки, потом кобели
        $genders = ['Сука', 'Кобель'];

        foreach ($genders as $gender) {
            // Фильтруем по полу
            $dogsByGender = array_filter($dogsInClass, function ($dog) use ($gender) {
                return mb_strtolower(trim($dog['gender'])) === mb_strtolower($gender);
            });

            if (count($dogsByGender) === 0) {
                continue; // пропустить, если нет собак этого пола
            }

            // Создаём таблицу для каждого блока: порода → класс → пол
            $table = $section->addTable([
                'borderSize' => 0,
                'cellMargin' => 30,
            ]);

            // Порода
            $table->addRow();
            $table->addCell(11000)->addText(mb_strtoupper($breedName), ['bold' => true, 'size' => 14]);

            // Класс
            $table->addRow();
            $table->addCell(11000)->addText(mb_strtoupper($class), ['bold' => true, 'size' => 14]);

            // Пол
            $table->addRow();
            $table->addCell(11000)->addText(mb_strtoupper($gender), ['bold' => true, 'size' => 12]);

            // Места — не более 3
            $i = 1;
            foreach ($dogsByGender as $dog) {
                if ($i > 3) break;
                $table->addRow();
                $table->addCell(11000)->addText((string)$i, ['bold' => true, 'size' => 12]);
                $i++;
            }

            // // Отступ после блока пола
            // $section->addTextBreak(1);
        }
    }
}



// Очищаем буфер перед отправкой
if (ob_get_length()) ob_end_clean();

// Отправка документа
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
header('Content-Disposition: attachment; filename="vedomost.docx"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Сохраняем и отправляем файл
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
exit;
