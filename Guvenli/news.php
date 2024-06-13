<?php
include 'config.php';

session_start();

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Haber id'sini al
$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($news_id == 0) {
    // Geçersiz haber id'si, anasayfaya yönlendir
    header("Location: index.php");
    exit();
}

// Haber veritabanından çek
$sql = "SELECT title, content, created_at, user_id FROM news WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $content, $created_at, $user_id);
$stmt->fetch();

// Kullanıcı adı ve haberi ekleyen kullanıcıyı veritabanından al
$sql_user = "SELECT username FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->store_result();
$stmt_user->bind_result($username);
$stmt_user->fetch();

if ($stmt->num_rows == 0) {
    // Haber bulunamadıysa anasayfaya yönlendir
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            color: #fff;
            padding: 10px 0;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo h1 {
            margin: 0;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            padding: 5px 10px;
            border: 1px solid transparent;
            transition: 0.3s;
        }

        header nav a:hover {
            border-color: #fff;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        main {
            padding: 20px 0;
        }

        .news-item {
            background: #fff;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .news-item h2 {
            margin: 0;
        }

        .news-item p {
            color: #666;
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

        .author-info {
            margin-top: 20px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Haber Sitesi</h1>
            </div>
            <nav>
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="admin_panel.php">Yönetim Paneli</a>
                    <a href="profile.php">Profil</a>
                    <a href="add_news.php">Haber Ekle</a>
                    <a href="logout.php">Çıkış Yap</a>
                <?php else: ?>
                    <a href="login.php">Giriş Yap</a>
                    <a href="register.php">Kayıt Ol</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="news-item">
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content)); ?></p>
                <div class="author-info">
                    <p><strong>Yazar:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Yayınlanma Tarihi:</strong> <?php echo htmlspecialchars($created_at); ?></p>
                </div>
                <a href="index.php" class="back-link">Geri Dön</a>
            </div>
        </div>
    </main>
</body>
</html>
