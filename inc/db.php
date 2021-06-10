<?php
//error_reporting(0);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dialer";

try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=$dbname", $username, $password);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
