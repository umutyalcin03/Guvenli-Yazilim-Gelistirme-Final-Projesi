<?php
include 'config.php';
session_start();

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
  
    // Rol kimliğini veritabanından al
    $sql = "SELECT r.role_name 
            FROM users u 
            INNER JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($role_name);
    $stmt->fetch();
    $stmt->close();
    
    if ($role_name !== 'yazar' and $role_name !== 'admin') {
        echo "<script>alert('Bu işlemi gerçekleştirme yetkiniz yok!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    // Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO news (title, content, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Haber başarıyla eklendi.'); window.location.href = 'index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Haber eklenirken bir hata oluştu.'); window.location.href = 'add_news.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Ekle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .news-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 300px;
            text-align: center;
        }
        .news-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .news-container input, .news-container textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .news-container button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .news-container button:hover {
            background-color: #333;
        }
        .news-container p {
            margin-top: 15px;
            color: #333;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="news-container">
        <h2>Haber Ekle</h2>
        <form action="add_news.php" method="post">
            <input type="text" name="title" placeholder="Haber Başlığı" required>
            <textarea name="content" placeholder="Haber İçeriği" rows="5" required></textarea>
            <button type="submit">Ekle</button>
        </form>
        <a href="index.php" class="back-link">Geri Dön</a>
    </div>
</body>
</html>
