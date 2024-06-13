<?php
include 'config.php';
session_start();

$message = '';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kullanıcı giriş yapmışsa, rol kimliğini al
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
    // Rol kimliği 1 (admin) değilse, erişim engeli
    if ($role_name !== 'admin') {
        echo "<script>alert('Bu sayfaya erişim izniniz yok!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    // Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
    header("Location: login.php");
    exit();
}

// Yönetim paneli işlemleri
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'add_news' && isset($_POST['add_news'])) {
        // Haber ekleme işlemi
        $title = $_POST['title'];
        $content = $_POST['content'];
        $user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO news (title, content, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $user_id);
        if ($stmt->execute()) {
            $message = "Haber başarıyla eklendi.";
        } else {
            $message = "Haber eklenirken bir hata oluştu.";
        }
        $stmt->close();
    } elseif ($action == 'delete_news' && isset($_POST['delete_news'])) {
        // Haber silme işlemi
        $news_id = $_POST['news_id'];

        $sql = "DELETE FROM news WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $news_id);
        if ($stmt->execute()) {
            $message = "Haber başarıyla silindi.";
        } else {
            $message = "Haber silinirken bir hata oluştu.";
        }
        $stmt->close();
    } elseif ($action == 'update_news' && isset($_POST['update_news'])) {
        // Haber güncelleme işlemi
        $news_id = $_POST['news_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        $sql = "UPDATE news SET title = ?, content = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $news_id);
        if ($stmt->execute()) {
            $message = "Haber başarıyla güncellendi.";
        } else {
            $message = "Haber güncellenirken bir hata oluştu.";
        }
        $stmt->close();
    } elseif ($action == 'delete_user' && isset($_POST['delete_user'])) {
        // Kullanıcı silme işlemi
        $user_id = $_POST['user_id'];

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "Kullanıcı başarıyla silindi.";
        } else {
            $message = "Kullanıcı silinirken bir hata oluştu.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli</title>
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
        .admin-panel {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 500px;
            text-align: center;
        }
        .admin-panel h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .admin-panel input, .admin-panel textarea, .admin-panel select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .admin-panel button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .admin-panel button:hover {
            background-color: #555;
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
    
    <div class="admin-panel">
        <h2>Yönetim Paneli</h2>
        <?php if ($message != ''): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <!-- Haber Ekleme Formu -->
        <form action="admin_panel.php" method="post">
            <input type="hidden" name="action" value="add_news">
            <h3>Haber Ekle</h3>
            <input type="text" name="title" placeholder="Başlık" required>
            <textarea name="content" placeholder="İçerik" required></textarea>
            <button type="submit" name="add_news">Haber Ekle</button>
        </form>

        <!-- Haber Silme Formu -->
        <form action="admin_panel.php" method="post">
            <input type="hidden" name="action" value="delete_news">
            <h3>Haber Sil</h3>
            <select name="news_id" required>
                <option value="" disabled selected>Haber Seç
                <option value="" disabled selected>Haber Seçiniz</option>
                <?php
                $sql = "SELECT id, title FROM news";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                    }
                }
                ?>
            </select>
            <button type="submit" name="delete_news">Haber Sil</button>
        </form>

        <!-- Haber Güncelleme Formu -->
        <form action="admin_panel.php" method="post">
            <input type="hidden" name="action" value="update_news">
            <h3>Haber Güncelle</h3>
            <select name="news_id" required>
                <option value="" disabled selected>Haber Seçiniz</option>
                <?php
                $sql = "SELECT id, title FROM news";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
                    }
                }
                ?>
            </select>
            <input type="text" name="title" placeholder="Yeni Başlık" required>
            <textarea name="content" placeholder="Yeni İçerik" required></textarea>
            <button type="submit" name="update_news">Haberi Güncelle</button>
        </form>

        <!-- Kullanıcı Silme Formu -->
        <form action="admin_panel.php" method="post">
            <input type="hidden" name="action" value="delete_user">
            <h3>Kullanıcı Sil</h3>
            <select name="user_id" required>
                <option value="" disabled selected>Kullanıcı Seçiniz</option>
                <?php
                $sql = "SELECT id, username FROM users WHERE role_id != 1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                    }
                }
                ?>
            </select>
            <button type="submit" name="delete_user">Kullanıcı Sil</button>
        </form>
        <a href="index.php" class="back-link">Geri Dön</a>
    </div>
</body>
</html>
