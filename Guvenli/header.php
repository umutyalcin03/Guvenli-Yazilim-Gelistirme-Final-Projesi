
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Sitesi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            color: #333;
            padding: 10px 0;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
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
            border-color: #000;
        }

    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php" style="color: #fff;">Haber Sitesi</a></h1>
            </div>
            <nav>
                    <a href="admin_panel.php">Yönetim Paneli</a>
                    <a href="profile.php">Profil</a>
                    <a href="add_news.php">Haber Ekle</a>
                    <a href="logout.php">Çıkış Yap</a>
            </nav>
        </div>
    </header>
