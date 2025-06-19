<?php
// Konfigurasi koneksi database MySQL
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bookingseminar"; // harus $dbname, bukan $database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Set error mode ke exception supaya mudah debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
