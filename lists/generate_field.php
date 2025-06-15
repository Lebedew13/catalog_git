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

// Создаем Word документ
$phpWord = new PhpWord();

// Стиль заголовков
$titleStyle = ['bold' => true, 'size' => 16];
$subTitleStyle = ['bold' => true, 'size' => 14];
$expertStyle = ['size' => 12];
$textStyle = ['size' => 12, 'align' => 'center'];
$breedStyle = ['underline' => 'single', 'bold' => true, 'size' => 14];
$nameStyle = ['bold' => true, 'size' => 12];
$infoStyle = ['size' => 12];
$underTextstyle = ['size' => 7, 'align' => 'center'];
function cmToTwip($cm) {
    return (int)($cm * 567);
}
// Для каждой собаки
foreach ($result as $dog) {
    $section = $phpWord->addSection([
        'marginTop' => cmToTwip(1.25),
    'marginLeft' => cmToTwip(0.7),
    'marginRight' => cmToTwip(0.4),
    'marginBottom' => cmToTwip(0.2)
    ]);

  
    // Шапка
    if($dog['breed'] == 'Американский Булли'){
    $section->addText(htmlspecialchars($show_arr['city_show']). '                        '. htmlspecialchars($show_arr['date_show']), $titleStyle);
    $section->addTextBreak();
    $section->addText('Порода: ' . htmlspecialchars($dog['breed']), $titleStyle);
    $section->addText('№________, Пол      '. htmlspecialchars($dog['gender']).', Класс         '.htmlspecialchars($dog['class_breed']), $titleStyle);
    $section->addText('Оценка: отл▢ оч.хор.▢ хор.▢ 1▢ 2▢ 3▢ Титулы Булли: ЛКК▢ ЛСК▢ ЛКТ▢ ЛСТ▢', $textStyle);
    $section->addText('дисквал▢ без оценки▢                     без титула▢                                ПТ▢ ЛПП▢', $textStyle);
    $section->addText('ЛКК – Лучший кобель класса ЛСК – Лучшая сука класса ЛКТ – Лучший кобель типа ЛСТ – Лучшая сука типа  ПТ- Победитель типа ЛПП – Лучший представитель породы', $underTextstyle);
    $section->addText('Описание', $titleStyle);
    
   $table = $section->addTable([
    'borderSize' => 8,     // ширина рамки (8 = 1 пиксель примерно)
    'borderColor' => '000000', // цвет рамки - черный
    'cellMargin' => 80,    // внутренние отступы ячейки (80 = 0.5pt примерно)   
]);
    }else{
         $section->addText(htmlspecialchars($show_arr['city_show']). '                        '. htmlspecialchars($show_arr['date_show']), $titleStyle);
    $section->addTextBreak();
    $section->addText('Порода: ' . htmlspecialchars($dog['breed']), $titleStyle);
    $section->addText('№________, Пол      '. htmlspecialchars($dog['gender']).', Класс         '.htmlspecialchars($dog['class_breed']), $titleStyle);
    $section->addText('Оценка: отл▢ оч.хор.▢ хор.▢ 1▢ 2▢ 3▢ Титулы Все породы: ЛКК▢ ЛСК▢ ЛКТ▢ ЛСТ▢', $textStyle);
    $section->addText('дисквал▢ без оценки▢                     без титула▢                                ПТ▢ ЛПП▢', $textStyle);
    $section->addText('ЛКК – Лучший кобель класса ЛСК – Лучшая сука класса ЛКТ – Лучший кобель типа ЛСТ – Лучшая сука типа  ПТ- Победитель типа ЛПП – Лучший представитель породы', $underTextstyle);
    $section->addText('Описание', $titleStyle);
   $table = $section->addTable([
    'borderSize' => 8,     // ширина рамки (8 = 1 пиксель примерно)
    'borderColor' => '000000', // цвет рамки - черный
    'cellMargin' => 80,    // внутренние отступы ячейки (80 = 0.5pt примерно)   
]);
    }
// Добавляем одну строку
$table->addRow(10800, ['cantSplit' => true]);

// Добавляем одну ячейку, которая занимает всю строку
$cell = $table->addCell(11000);
$youngClasses = ['Щенки', 'Щенки мини', 'Щенки макси', 'Щенок мини', 'Щенок макси', 'Бэби', 'Юниор', 'Юниор мини',  'Юниор макси', 'Юниоры', 'Юниор экзот'];

if (in_array($dog['class_breed'], $youngClasses)) {
    $cell->addText('Семенники у коб.:                            Прикус:                                         Степень породности:');
    $cell->addText('');
    $cell->addText('Перспектива:'); 
    $cell->addText('не перспективный ____                    перспективный ____                      очень перспективный ____');
}else{
    $cell->addText('');
    $cell->addText('');
    $cell->addText('');
    $cell->addText('');
}
// Добавляем текст в ячейку
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText('');
$cell->addText(' Эксперт: '.htmlspecialchars($experts_arr['lastname']).'  '.htmlspecialchars($experts_arr['firstname']).'___________________________' , $titleStyle);

}


// Очищаем буфер перед отправкой
if (ob_get_length()) ob_end_clean();

// Отправка документа
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
header('Content-Disposition: attachment; filename="opisnoy_list_all.docx"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Сохраняем и отправляем файл
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
exit;
