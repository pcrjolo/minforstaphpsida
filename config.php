<?php
$host = '127.0.0.1';
$db   = 'bibliotek';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    /* ASSOC = Associative array. Inte numerisk. Man kan söka på kolumn namnet inne i curly bracket istället för 0, 1 osv som i en JavaScript array.*/
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options); /* skapar ny databaskontakt */

?>