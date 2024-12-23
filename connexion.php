<?php
   try{
      $pdo=new PDO("mysql:host=;dbname=","","");
   }
   catch(PDOException $e){
      echo $e->getMessage();
   }
?>
