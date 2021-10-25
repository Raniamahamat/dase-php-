<title>BackUp Database MySQL </title>
<h2>backup de base de donnee mysql </h2>
<form>





    

    <?php
    // database credits
    $db_host   = "127.0.0.1";
    $db_user   = "";
    $db_pass   = "";


$connect = new PDO("mysql:host=localhost;dbname=", "root", "");

    
$connection = mysqli_connect($db_host, $db_user, $db_pass);
    if (!$connection) {
        echo "Error: Unable to connect to MySQL => ".mysqli_connect_errno();
        exit;
    }

    $system_db = ['information_schema','mysql','performance_schema','phpmyadmin','']; // system default database
    $connection = mysqli_connect('localhost', 'root', '');
    $result = mysqli_query($connection,"SHOW DATABASES");
    



    while ($row = mysqli_fetch_assoc($result)) {
        if (!in_array($row['Database'], $system_db)) { // hide System Database
            ?>

                <label>

                    <input type='checkbox' name='database[]' value=<?php print $row['Database'] ?> /><?php print $row['Database'] ?>
                     <from action="sql_bakp_tables.php"> 
                </label>                
                <br>
            <?php
        }
    }

    ?>

<input type="submit" name="Restauration" value="Restauration"/>

    <input type="submit" name="submit" value="BackUp" />

</form>

<input type="submit" name="Diff" value="Diff" />



<?php

if(isset($_GET['submit'])){//to run PHP script on submit

    // create your zip file
    $zipname = 'file.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);

    $all_tables = [];

    foreach ($_GET['database'] as $database) {
        print $database."</br>";

        mysqli_select_db($connection, $database) or die(mysqli_error($connection));

        $result = mysqli_query($connection, "show tables");
        while ($table = mysqli_fetch_array($result)) {
            array_push($all_tables, $table[0]);
        }

        for ($i=0; $i < count($all_tables); $i++) {

            $sql = "SELECT * FROM $all_tables[$i]";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                $increment = 0;
                $all_col_name = [];
                $all_col = [];
                $new_row = [];
                while ($row = $result->fetch_assoc()) {
                    array_push($new_row, $row);
                    foreach ($row as $col_name => $col_value) {
                        if ($increment >= 0 and $increment < count($row)) {
                            array_push($all_col_name, $col_name);
                        }
                        $increment++;
                    }
                }
        
                // create a temporary file
                $fd = fopen('php://temp/maxmemory:104857600', 'w');
                if (false === $fd) {
                    die('Failed to create temporary file');
                }
                // write the data to csv
                fputcsv($fd, $all_col_name);
                foreach ($new_row as $record) {
                    fputcsv($fd, $record);
                }
                var_dump($fd);
                // return to the start of the stream
                rewind($fd);
                // add the in-memory file to the archive, giving a name
                $zip->addFromString("$all_tables[$i].csv", stream_get_contents($fd));
                //close the file
                fclose($fd);
            } else {
                echo "0 results<br>";
            }
        }
        // close the archive
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        // header('Content-Length: ' . filesize($zipname));

        readfile($zipname);
        // remove the zip archive
        // you could also use the temp file method above for this.
        unlink($zipname);
        mysqli_close($connection);
    }
}




if(isset($_GET['Tables'])) {
        onFunc();
    }
    

?>




<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


<form action="sql_bakp_tables.php" method="POST">
 
 <input type="submit" name="Tables" value="Tables" onClick="send('/sql_bakp_tables.php')";>


</form>


<script>
$(document).ready(function(){
 $('#return').click(function(){
  var count = 0;
  $('.checkbox_table').each(function(){
   if($(this).is(':checked'))
   {
    count = count + 1;
   }
  });
  if(count > 0)
  {
   $('#export_form').return();
  }
  else
  {
   alert("Please Select Atleast one table for Backup");
   return false;
  }
 });
});
</script>

<?php
  $host = 'localhost';
  $dbname = 'enterprise';
  $username = 'root';
  $password = '';
    
  $dsn = "mysql:host=$host;dbname=$dbname"; 
  // récupérer tous les utilisateurs
  $sql = "SELECT * FROM gestion";
   
  try{
   $pdo = new PDO($dsn, $username, $password);
   $stmt = $pdo->query($sql);
   
   if($stmt === false){
    die("Erreur");
   }
   
  }catch (PDOException $e){
    echo $e->getMessage();
  }
?>



