<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$conn = $database->getConnection();

// Ambil data kategori untuk filter
$categoryQuery = "SELECT * FROM categories ORDER BY name ASC";
$categoryStmt = $conn->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT books.id, books.title, books.author, books.published_year, books.genre, categories.name as category 
          FROM books
          LEFT JOIN book_categories ON books.id = book_categories.book_id
          LEFT JOIN categories ON book_categories.category_id = categories.id
          ORDER BY books.id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4 bg-light rounded">
        <h2 class="mb-4 text-center text-primary">📚 Daftar Buku Perpustakaan 📚</h2>
        <div class="d-flex justify-content-between mb-3">
            <a href="add.php" class="btn btn-success btn-lg">➕ Tambah Buku</a>
            <select id="categoryFilter" class="form-select w-25 border-primary">
                <option value="">📂 Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['name']; ?>"> <?= $category['name']; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <table class="table table-hover table-striped table-bordered text-center bg-white" id="booksTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun Terbit</th>
                    <th>Genre</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id']; ?></td>
                        <td><strong><?= $book['title']; ?></strong></td>
                        <td><?= $book['author']; ?></td>
                        <td><?= $book['published_year']; ?></td>
                        <td><span class="badge bg-info text-dark"><?= $book['genre']; ?></span></td>
                        <td class="category"><span class="badge bg-warning text-dark"> <?= $book['category'] ?: '-'; ?> </span></td>
                        <td>
                            <a href="edit.php?id=<?= $book['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="delete.php?id=<?= $book['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">🗑️ Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#booksTable').DataTable();

        $('#categoryFilter').on('change', function() {
            var selectedCategory = $(this).val();
            table.column(5).search(selectedCategory).draw();
        });
    });
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<?php include '../includes/footer.php'; ?>