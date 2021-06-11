<?php
//error_reporting(0);

$servername = "40.123.209.252";
$username = "dev";
$password = "shipa2215";
$dbname = "dialer";

try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=$dbname", $username, $password);

} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
