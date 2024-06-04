<?php
$host = $_SERVER['HTTP_HOST'];
//Environnement de PROD
if (strstr($host, "assista-crise.fr")){
    $db = new PDO(
        'mysql:host=db5014786741.hosting-data.io;dbname=dbs12286113;charset=utf8',
        'dbu1192794',
        '!Denkor1504@-D@nsl3srid3@uX',
        array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)
    );
}
