<?php
require_once('Database.php');
$conn = mysqli_connect('localhost', 'root', '', 'weatherforecast');


$lat = $_POST['lat'];
$lon = $_POST['lon'];
$email = $_POST['email'];
$userdatabase= new Database("localhost", "root", "", "weatherforecast");

if (isset($email)) {
    $userdatabase->addregion($email, $lat, $lon);
    $conn->close();
}
?>