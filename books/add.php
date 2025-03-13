<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM categories ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];
    $genre = $_POST['genre'];
    $category_id = $_POST['category'];

    $query = "INSERT INTO books (title, author, published_year, genre) VALUES (:title, :author, :published_year, :genre)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':genre', $genre);
    $stmt->execute();
    $book_id = $conn->lastInsertId();

    $query = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();

    $successMessage = "✅ Buku <strong>{$title}</strong> berhasil ditambahkan!";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="bi bi-book"></i> Tambah Buku</h3>
                </div>
                <div class="card-body bg-light">
                    
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $successMessage; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-book-fill"></i> Judul Buku</label>
                            <input type="text" name="title" class="form-control" required placeholder="Masukkan judul buku">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-fill"></i> Pengarang</label>
                            <input type="text" name="author" class="form-control" required placeholder="Masukkan nama pengarang">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-calendar"></i> Tahun Terbit</label>
                            <input type="number" name="published_year" class="form-control" required placeholder="Masukkan tahun terbit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-bookmark-fill"></i> Genre</label>
                            <input type="text" name="genre" class="form-control" required placeholder="Masukkan genre buku">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-tags-fill"></i> Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="">📂 Pilih Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id']; ?>"> <?= $category['name']; ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save"></i> Simpan Buku
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Buku
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<?php include '../includes/footer.php'; ?>
