<?php
include 'config.php';
session_start();

// Kullanıcı oturumu kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Arama terimini kontrol et
$searchTerm = "";
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Haberleri veritabanından çek
$sql = "SELECT news.id, news.title, news.content, news.created_at, users.username 
        FROM news 
        INNER JOIN users ON news.user_id = users.id
        WHERE news.title LIKE '%$searchTerm%' OR news.content LIKE '%$searchTerm%'
        ORDER BY news.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Sitesi</title>
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

        .news-item h3 {
            margin: 0;
        }

        .news-item p {
            color: #666;
        }

        .news-item a {
            display: inline-block;
            margin-top: 10px;
            color: #333;
            text-decoration: none;
            border: 1px solid #333;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .news-item a:hover {
            background: #333;
            color: #fff;
        }

        .author-info {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }

        /* Arama Formu Stili */
        .search-form {
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-form input[type="text"] {
            padding: 8px 200px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        .search-form button {
            padding: 8px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
            outline: none;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="color: white;">Haber Sitesi</a></h1>
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
            <h2>Güncel Haberler</h2>

            <!-- Haber Arama Formu -->
            <form class="search-form" action="index.php" method="GET">
                <input type="text" name="search" placeholder="Haber ara..." value="<?php echo $searchTerm; ?>">
                <button type="submit">Ara</button>
            </form>
            
            <!-- Haberler burada gösterilecek -->
            <div class="news-results">
                <?php
                // Arama yapılmamışsa veya arama sonuçları varsa bunları göster
                if (empty($searchTerm) && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='news-item'>";
                        echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                        echo "<p>" . htmlspecialchars(substr($row["content"], 0, 200)) . "...</p>";
                        echo "<div class='author-info'>";
                        echo "<p><strong>Yazar:</strong> " . htmlspecialchars($row["username"]) . "</p>";
                        echo "<p><strong>Yayınlanma Tarihi:</strong> " . htmlspecialchars($row["created_at"]) . "</p>";
                        echo "</div>";
                        echo "<a href='news.php?id=" . $row["id"] . "'>Devamını Oku</a>";
                        echo "</div>";
                    }
                }
                ?>
            </div>

        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('.search-form');
            const searchInput = searchForm.querySelector('input[name="search"]');
            const newsResults = document.querySelector('.news-results');

            searchForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const searchTerm = searchInput.value;

                const xhr = new XMLHttpRequest();
                xhr.open('GET', `search_news.php?search=${encodeURIComponent(searchTerm)}`, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        newsResults.innerHTML = xhr.responseText;
                    }
                };
                xhr.send();
            });
        });
    </script>
</body>
</html>
