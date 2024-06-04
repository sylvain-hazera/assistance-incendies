<?php
   try{
      $pdo=new PDO("mysql:host=db5014786741.hosting-data.io;dbname=dbs12286113","dbu1192794","!Denkor1504@-D@nsl3srid3@uX");
   }
   catch(PDOException $e){
      echo $e->getMessage();
   }
?>