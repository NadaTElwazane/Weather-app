<?php
require_once('Database.php');
// start a session to get email
session_start();
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}
$userdatabase=new Database("localhost", "root", "", "weatherforecast");
$result=$userdatabase->searchregions($email);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lat = $row["lat"];
        $lon = $row["lon"];
    }
} else {
    echo "0 results";
}
$conn->close();


?>