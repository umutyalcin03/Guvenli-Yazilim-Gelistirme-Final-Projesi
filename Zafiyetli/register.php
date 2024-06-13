<?php
include 'config.php';

// Mevcut admin kullanıcı sayısını al
$sql_admin_count = "SELECT COUNT(*) as admin_count FROM users WHERE role_id = 1";
$result_admin_count = $conn->query($sql_admin_count);
$row_admin_count = $result_admin_count->fetch_assoc();
$admin_count = $row_admin_count['admin_count'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role']; // Kullanıcının seçtiği rol id'si

    // Eğer seçilen rol Admin ise ve zaten bir Admin varsa, kayıt yapma
    if ($role_id == 1 && $admin_count > 0) {
        $error = "Zaten bir Admin kullanıcısı var.";
    } else {
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $email, $password, $role_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Kullanıcı adı veya e-posta zaten alınmış.";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Kayıt Sayfası</title>
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
        .register-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            width: 300px;
            text-align: center;
        }
        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .register-container input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-container select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #333;
        }
        .register-container p {
            margin-top: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Kayıt Ol</h2>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <select name="role" required>
                <option value="" disabled selected>Rol Seçiniz</option>
                <option value="1">Admin</option>
                <option value="2">Yazar</option>
                <option value="3">Okur</option>
            </select>
            <button type="submit">Kayıt Ol</button>
        </form>
        <?php
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
        <p>Zaten bir hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
    </div>
</body>
</html>
