<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_photo'])) {
    $user_id = $_SESSION['user_id'];
    $upload_dir = '';
    $file_name = basename($_FILES['profile_photo']['name']);
    $target_file = $upload_dir . uniqid() . '-' . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Dosya tipi kontrolü
    $check = getimagesize($_FILES['profile_photo']['tmp_name']);
    if ($check === false) {
        echo "Bu bir resim dosyası değil.";
        exit();
    }

    // Dosya boyutu kontrolü (örneğin, 5MB sınırı)
    if ($_FILES['profile_photo']['size'] > 5000000) {
        echo "Dosya çok büyük.";
        exit();
    }

    // Yalnızca belirli dosya türlerine izin ver
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Yalnızca JPG, JPEG, PNG & GIF dosyalarına izin verilir.";
        exit();
    }

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        // Eski profil fotoğrafını sil (isteğe bağlı)
        $sql = "SELECT profile_photo FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($old_profile_photo);
        $stmt->fetch();
        if ($old_profile_photo && file_exists($old_profile_photo)) {
            unlink($old_profile_photo);
        }
        $stmt->close();

        // Yeni profil fotoğrafını veritabanına kaydet
        $sql = "UPDATE users SET profile_photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        $stmt->close();

        // Yüklenen fotoğrafı göster
        echo "<img src='" . $target_file . "' alt='Profil Fotoğrafı'>";
        echo "<script>alert('Dosya başarıyla yüklendi.'); window.location.href = 'profile.php';</script>";
    } else {
        echo "Dosya yüklenirken bir hata oluştu.";
    }
} else {
    echo "Geçersiz istek.";
}
?>