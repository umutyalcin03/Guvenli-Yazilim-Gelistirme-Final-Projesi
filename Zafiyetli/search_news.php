<?php
include 'config.php';

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

if ($result->num_rows > 0) {
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
} else {
    echo "<p>Aramanızla eşleşen haber bulunamadı.</p>";
}
$conn->close();
?>
