<?php
include '../config/database.php';
include '../includes/header.php';

$database = new Database();
$conn = $database->getConnection();

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

<div class="container mt-5">
    <div class="card shadow-lg p-4 bg-white rounded">
        <h2 class="mb-4 text-center text-primary"> Manajemen Buku Perpustakaan </h2>
        <div class="d-flex justify-content-between mb-3">
            <select id="categoryFilter" class="form-select w-25 border-primary">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['name']; ?>"> <?= $category['name']; ?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered text-center" id="booksTable">
                <thead class="table-warning text-white"> 
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
                <tbody class="table-light">
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= $book['id']; ?></td>
                            <td><strong><?= $book['title']; ?></strong></td>
                            <td><?= $book['author']; ?></td>
                            <td><?= $book['published_year']; ?></td>
                            <td>
                                <span class="badge 
                        <?php if ($book['genre'] == 'Fantasy') echo 'bg-primary';
                        elseif ($book['genre'] == 'Thriller') echo 'bg-danger';
                        elseif ($book['genre'] == 'Sejarah') echo 'bg-success';
                        else echo 'bg-info'; ?>">
                                    <?= $book['genre']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"> <?= $book['category'] ?: '-'; ?> </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $book['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="delete.php?id=<?= $book['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

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