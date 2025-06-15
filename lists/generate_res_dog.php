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
$phpWord->setDefaultFontName('Century Schoolbook');
// Стиль заголовков
$titleStyle = ['bold' => true, 'size' => 12, 'align' => 'center','Times New Roman'];
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
     foreach($experts as $exp){
    $section = $phpWord->addSection([
        'marginTop' => cmToTwip(8),
    'marginLeft' => cmToTwip(5),
    'marginRight' => cmToTwip(5),
    'marginBottom' => cmToTwip(5)
    ]);

    // Шапка

/// Подготовим значения
$dateFormatted = date('d.m.Y', strtotime($show_arr['date_show']));
$expertInitial = mb_substr($exp['firstname'], 0, 1, 'UTF-8');
$expertSignature = htmlspecialchars($exp['lastname']) . ' ' . $expertInitial . '.';

// Стиль таблицы
$tableStyle = [
    'borderSize' => 0,
    'borderColor' => 'FFFFFF', // можно опустить, но это точно "невидимо"
    'cellMargin' => 30
];
$phpWord->addTableStyle('InfoTable', $tableStyle);

// Стиль ячеек
$cellStyle = ['valign' => 'center'];

// Стиль левой колонки (название поля)
$leftFont = ['bold' => true, 'size' => 11];

// Стиль правой колонки (значение)
$rightFont = ['underline' => 'single', 'bold' => true, 'valign' => 'right', 'size' => 12];
$rightParagraph = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
$centerParagraph = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
$leftParagraph = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];

// Город + дата
$section->addText(htmlspecialchars(mb_strtoupper($show_arr['city_show'])) . '                 ' . $dateFormatted, ['bold' => true, 'size' => 12], $centerParagraph);
$section->addTextBreak();
// Добавляем таблицу
$table = $section->addTable('InfoTable');


// Строки таблицы
$table->addRow();
$table->addCell(5000,$cellStyle)->addText('ПОРОДА', $leftFont, $leftParagraph);
$table->addCell(7000, $cellStyle)->addText(htmlspecialchars(mb_strtoupper($dog['breed'])), $rightFont, $rightParagraph);

$table->addRow();
$table->addCell(5000, $cellStyle)->addText('КЛИЧКА', $leftFont, $leftParagraph);
$table->addCell(7000, $cellStyle)->addText(htmlspecialchars(mb_strtoupper($dog['nameDog'])), $rightFont, $rightParagraph);

$table->addRow();
$table->addCell(5000, $cellStyle)->addText('ПОЛ', $leftFont, $leftParagraph);
$table->addCell(7000, $cellStyle)->addText(htmlspecialchars(mb_strtoupper($dog['gender'])), $rightFont, $rightParagraph);

$table->addRow();
$table->addCell(5000, $cellStyle)->addText('НОМЕР УЧАСТНИКА', $leftFont, $leftParagraph);
$table->addCell(7000, $cellStyle)->addText(mb_strtoupper($dog['id']), $rightFont, $rightParagraph);

$table->addRow();
$table->addCell(5000, $cellStyle)->addText('КЛАСС', $leftFont, $leftParagraph);
$table->addCell(7000, $cellStyle)->addText(htmlspecialchars(mb_strtoupper($dog['class_breed'])), $rightFont, $rightParagraph);

// Результаты (текстовая линия)
$section->addTextBreak(1);
$section->addText('РЕЗУЛЬТАТЫ  _________________________________', $leftFont, $leftParagraph);
$section->addTextBreak(1);

// Подпись эксперта
$section->addText('ПОДПИСЬ ЭКСПЕРТА  _' . $expertSignature . '____________________', $leftFont, $leftParagraph);

    }
}



// Очищаем буфер перед отправкой
if (ob_get_length()) ob_end_clean();

// Отправка документа
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=utf-8');
header('Content-Disposition: attachment; filename="diplom.docx"');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Сохраняем и отправляем файл
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
exit;
