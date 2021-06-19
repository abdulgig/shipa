<?php
//error_reporting(0);

/*$servername = "10.200.1.20";
$username = "svy";
$password = "svy9278";
$dbname = "dialer";*/

/*
$servername = "10.200.1.4";
$username = "dev";
$password = "shipa2215";
$dbname = "dialer";
*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dialer";


//$conn = mysqli_connect($servername, $username, $password, $dbname);
try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=$dbname", $username, $password);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
