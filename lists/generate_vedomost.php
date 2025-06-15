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
$titleStyle = ['size' => 18, 'align' => 'center'];
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
foreach ($result as $dog) {


    // Добавляем одну строку
    $table->addRow();
    $table->addCell(11000)->addText($dog['breed']);
    $section->addTextBreak();
    

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
