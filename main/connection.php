<?php

//PDO : USED FOR CONNECTION WITH DATABASE 3 ARGS : DSN , USER, PASS 
function connexpdo($base)
{
    $dsn =  "mysql:host=localhost;dbname=$base;charset=utf8";
    $user = "root";
    $pass = "admin123";
    try
    {
        $idcom = new PDO($dsn, $user, $pass);
        return $idcom;
    }
    catch (PDOException $except)
    {
        echo "Échec de la connexion : ", $except->getMessage();
        return FALSE;
    }
}
?>