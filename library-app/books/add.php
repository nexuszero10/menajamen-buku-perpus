<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$conn = $database->getConnection();

// Ambil kategori yang tersedia
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];
    $genre = $_POST['genre'];
    $category_id = $_POST['category'];

    // Insert buku baru
    $query = "INSERT INTO books (title, author, published_year, genre) VALUES (:title, :author, :published_year, :genre)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':genre', $genre);
    $stmt->execute();
    $book_id = $conn->lastInsertId();

    // Insert kategori ke book_categories
    $query = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();

    echo "<div class='alert alert-success'>Buku berhasil ditambahkan!</div>";
}
?>

<h2 class="mb-4">Tambah Buku</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label class="form-label">Judul Buku</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Pengarang</label>
        <input type="text" name="author" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Tahun Terbit</label>
        <input type="number" name="published_year" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Genre</label>
        <input type="text" name="genre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category" class="form-select" required>
            <option value="">Pilih Kategori</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>"> <?= $category['name']; ?> </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?php include '../includes/footer.php'; ?>
