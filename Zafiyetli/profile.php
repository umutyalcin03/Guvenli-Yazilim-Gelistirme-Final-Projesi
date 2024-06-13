<?php
include 'config.php';
include 'header.php';

session_start();

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcının bilgilerini veritabanından al
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.username, u.email, u.profile_photo, u.role_id, r.role_name 
        FROM users u 
        INNER JOIN roles r ON u.role_id = r.id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $email, $profile_photo, $role_id, $role_name);
$stmt->fetch();
$stmt->close();

// Kullanıcının eklediği haberleri veritabanından al
$sql_news = "SELECT id, title, content, created_at FROM news WHERE user_id = ? ORDER BY created_at DESC";
$stmt_news = $conn->prepare($sql_news);
$stmt_news->bind_param("i", $user_id);
$stmt_news->execute();
$result_news = $stmt_news->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 10px;
        }

        h1, h2 {
            margin-top: 0;
        }

        .profile-info, .user-news {
            margin-bottom: 20px;
        }

        .profile-info p {
            margin: 5px 0;
        }

        .profile-info hr {
            margin: 20px 0;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            display: block;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #333;
        }

        .news-item {
            background: #fff;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .news-item h3 {
            margin: 0;
        }

        .news-item p {
            color: #333;
        }

        .news-item a {
            display: inline-block;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
            border: 1px solid #333;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }

        .news-item a:hover {
            background: #333;
            color: #fff;
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
    <div class="container">
        <h1>Profil</h1>
        <div class="profile-info">
            <?php if ($profile_photo): ?>
                <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profil Fotoğrafı" class="profile-photo">
            <?php endif; ?>
            <p><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>E-posta:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Rol:</strong> 
                <?php 
                    if ($role_id == 1) {
                        echo "Admin";
                    } elseif ($role_id == 2) {
                        echo "Yazar";
                    } elseif ($role_id == 3) {
                        echo "Okur";
                    } else {
                        echo "Bilinmeyen Rol";
                    }
                ?>
            </p>
            <hr>
            <h2>Profil Fotoğrafı Yükle</h2>
            <form action="upload_profile_photo.php" method="post" enctype="multipart/form-data">
                <input type="file" name="profile_photo" accept="image/*">
                <button type="submit">Yükle</button>
            </form>
        </div>

        <div class="user-news">
            <h2>Eklediğim Haberler</h2>
            <?php
            if ($result_news->num_rows > 0) {
                while($row_news = $result_news->fetch_assoc()) {
                    echo "<div class='news-item'>";
                    echo "<h3>" . htmlspecialchars($row_news["title"]) . "</h3>";
                    echo "<p>" . htmlspecialchars(substr($row_news["content"], 0, 200)) . "...</p>";
                    echo "<a href='news.php?id=" . $row_news["id"] . "'>Devamını Oku</a>";
                    // Sadece yazar rolü (role_id = 2) olan kullanıcılar için silme butonunu ekle
                    if ($role_name == 'Yazar') {
                        echo " | <a href='delete_news.php?id=" . $row_news["id"] . "'>Haberi Sil</a>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>Henüz haber eklemediniz.</p>";
            }
            $stmt_news->close();
            ?>
        </div>
        <a href="index.php" class="back-link">Geri Dön</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
