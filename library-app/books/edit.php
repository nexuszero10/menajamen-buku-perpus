<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$conn = $database->getConnection();

// Ambil ID buku dari parameter URL
$book_id = $_GET['id'] ?? null;
if (!$book_id) {
    die("ID buku tidak valid.");
}

// Ambil data buku
$query = "SELECT * FROM books WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $book_id);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die("Buku tidak ditemukan.");
}

// Ambil kategori yang tersedia
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil kategori buku saat ini
$query = "SELECT category_id FROM book_categories WHERE book_id = :book_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':book_id', $book_id);
$stmt->execute();
$current_category = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];
    $genre = $_POST['genre'];
    $category_id = $_POST['category'];

    // Update buku
    $query = "UPDATE books SET title = :title, author = :author, published_year = :published_year, genre = :genre WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':id', $book_id);
    $stmt->execute();

    // Update kategori buku
    $query = "DELETE FROM book_categories WHERE book_id = :book_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    $query = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();

    echo "<div class='alert alert-success'>Buku berhasil diperbarui!</div>";
}
?>

<h2 class="mb-4">Edit Buku</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" name="title" class="form-control" value="<?= $book['title']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Pengarang</label>
        <input type="text" name="author" class="form-control" value="<?= $book['author']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Tahun Terbit</label>
        <input type="number" name="published_year" class="form-control" value="<?= $book['published_year']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Genre</label>
        <input type="text" name="genre" class="form-control" value="<?= $book['genre']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>" <?= ($category['id'] == $current_category) ? 'selected' : ''; ?>>
                    <?= $category['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?php include '../includes/footer.php'; ?>