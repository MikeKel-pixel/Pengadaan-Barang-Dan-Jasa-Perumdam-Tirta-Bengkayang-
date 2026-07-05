<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=perumdam_db', 'root', '');
    echo "✅ Koneksi database BERHASIL!";
} catch (PDOException $e) {
    echo "❌ Koneksi database GAGAL: " . $e->getMessage();
}
?>
