

<?php
header("Cache-Control: no-cache, must-revalidate");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'connectdb.php';
                   $sql = "SELECT * FROM `show_idbc`";

                   $result = $conn->query($sql);
                   echo "<h2>Добавленные шоу</h2>";
                   foreach($result as $row){

                 
                        echo "<table>
                        <form method='post' action='showaction.php?id=".$row['id']."'>
                        <tr>
                     
                        <td><input type='text' value='".$row['name_show']."' id='name_show1' name='name_show1'></td>
                        <td><input type='date' value='".$row['date_show']."' id='date_show1' name='date_show1'></td>
                        <td><input type='text' value='".$row['city_show']."' id='city_show1' name='city_show1'></td>
                        <td><input type='email' value='".$row['email']."' id='email_show1' name='email_show1'</td>
                        <td><input type='submit' name='update_show' value='Изменить'></td>
                        <td><input type='submit' name='delete_show' value='Удалить'></td>
                        </tr>
                        </form>
                        </table>
                      ";
                    }



?>
<h2>Добавить шоу</h2>
<form action="showaction.php" method="POST">
<input type="text" name="name_show" id="name_show" placeholder="Название шоу">
<input type="date" name="date_show" id="date_show" >
<input type="text" name="city_show" placeholder="город">
<input type="email" name="email_show" placeholder="Емаил организатора">
<input type="submit" value="Отправить" name='insert_into'>
</form>
</html>