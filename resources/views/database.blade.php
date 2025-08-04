<?php

$server = "localhost";
$user = "root";
$pass = "";
$name = "voguevault";
$connection = "";


try{
$connection = mysqli_connect($server,$user,$pass,$name);
}

catch(mysqli_sql_exception){
    echo "Couldn't connect!";
}

if($connection){
    //echo "You're connected!";
}


?>