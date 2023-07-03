<?php
    require_once('Database.php');

    $lat=$_POST['lat'];
    $lon=$_POST['lon'];
    $email=$_POST['email'];
    $userdatabase= new Database("localhost", "root", "", "weatherforecast");
    $result=$userdatabase->deleteregion($email, $lat, $lon);
    if($result){
        echo "Region removed successfully";
    }else{
        echo "Error removing region";
    }
?>
