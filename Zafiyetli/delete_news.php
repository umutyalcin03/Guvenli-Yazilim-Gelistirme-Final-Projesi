<?php
include 'config.php';
session_start();
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
    
    if ($role_name !== 'yazar') {
        echo "<script>alert('Bu işlemi gerçekleştirme yetkiniz yok!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    // Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
    header("Location: login.php");
    exit();
}

// Yazar rolüne sahip olmayan kullanıcıları engelle
// if ($_SESSION['role_id'] != 2) {
//     echo "<script>alert('Bu işlemi gerçekleştirme yetkiniz yok!');</script>";
//     echo "<script>window.location.href = 'profile.php';</script>";
//     exit();
// }

// Haber ID'si alınır
if (isset($_GET['id'])) {
    $news_id = $_GET['id'];

    // Haber veritabanında mevcut mu kontrol et
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id FROM news WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $news_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Haber mevcutsa sil
        $sql_delete = "DELETE FROM news WHERE id = ? AND user_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $news_id, $user_id);

        if ($stmt_delete->execute()) {
            echo "<script>alert('Haber başarıyla silindi.');</script>";
        } else {
            echo "<script>alert('Haber silinirken bir hata oluştu.');</script>";
        }
        $stmt_delete->close();
    } else {
        echo "<script>alert('Bu haberi silme yetkiniz yok veya haber mevcut değil.');</script>";
    }
} else {
    echo "<script>alert('Geçersiz istek.');</script>";
}

echo "<script>window.location.href = 'profile.php';</script>";
exit();
?>
