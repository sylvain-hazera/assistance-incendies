<?php
   try{
      $pdo=new PDO("mysql:host=localhost;dbname=www","xxx","yyy");
   }
   catch(PDOException $e){
      echo $e->getMessage();
   }
?>