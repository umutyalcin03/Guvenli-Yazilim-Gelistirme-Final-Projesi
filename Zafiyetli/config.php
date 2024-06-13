<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";  // Veritabanı sunucusu
$username = "root";         // Veritabanı kullanıcı adı
$password = "1907";             // Veritabanı şifresi
$dbname = "news_site";         // Veritabanı adı

// Veritabanı bağlantısı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

?>
