<?php

include '../connectdb.php';
include 'header.php';
include '../functions/selectShow.php';


echo "<div class=\"header__links\">
    <a href='../catalog/catalog.php'>На главную</a>
</div>";



?>

<form  class="referi flex" action="../functions/referiAction.php" method="post">
    <div>
    <input type="text" name="firstname" placeholder="имя">
    <input type="text" name="lastname" placeholder="Фамилия">
    Выберите шоу 

    <select name="showid">
        <?php   
            foreach($show as $row){
                echo $row['id'];
              echo "<option value='{$row['id']}'>".$row['name_show']."</option>";
            }
        ?>
    </select>
    <input type='submit' name="add" value="Добавить">
    </div>
</form>
<?php

$sql = "SELECT * FROM `experts`";
$result2 = $conn->query($sql);

foreach($result2 as $exp){
    $name = $exp['firstname'];
    $lname = $exp['lastname'];
}





echo "<form  class=\"referi flex\" action=\"../functions/referiAction.php\" method='post'>";
$sql = "SELECT `experts`.*, `show_idbc`.`name_show`
    FROM `experts`, `show_idbc` WHERE `experts`.`show_id` = `show_idbc`.`id`";
    $result = $conn->query($sql);

    $num_row = mysqli_num_rows($result);
    if(!$num_row){
        echo "Добавьте судей для выставок";
    }else{
foreach ($result as $row){
    
            echo "<p>Судья: <input type='text' hidden name='id' value='{$row['id']}'>{$row['firstname']}\n{$row['lastname']}".PHP_EOL."<b>Ринг:\n{$row['name_show']}</b>".PHP_EOL."<input  type='submit' class='btn-reset' name='edit' value='Удалить'></p>";
    
        }
    }

echo "</form>";
echo "</div>";
include 'footer.php';
?>
