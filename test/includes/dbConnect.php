<?php
$host = $_SERVER['HTTP_HOST'];
//Environnement de PROD
if (strstr($host, "assista-crise.fr")){
    $db = new PDO(
        'mysql:host=;;charset=utf8',
        '',
        '',
        array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)
    );
}
