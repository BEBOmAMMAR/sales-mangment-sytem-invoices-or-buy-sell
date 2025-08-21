<?php
include './config.php';
include './admin_header.php';

// حذف تصنيف
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success mx-4">✅ تم حذف التصنيف بنجاح.</div>';
    } else {
        echo '<div class="alert alert-danger mx-4">❌ خطأ أثناء حذف التصنيف.</div>';
    }
}

// جلب التصنيفات
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📂 إدارة التصنيفات</h3>
        <a href="admin_add_category.php" class="btn btn-primary">➕ إضافة تصنيف جديد</a>
    </div>

    <table class="table table-bordered table-striped" id="categoriesTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>اسم التصنيف</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($cat = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <a href="admin_edit_category.php?id=<?= $cat['id'] ?>" class="btn btn-warning btn-sm">✏ تعديل</a>
                            <a href="admin_categories.php?delete_id=<?= $cat['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ هذا قد يؤثر على المنتجات المرتبطة!');">
                               🗑 حذف
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">🚫 لا توجد تصنيفات مضافة بعد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#categoriesTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
        },
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [5, 10, 20, 50]
    });
});
</script>

<?php include './admin_footer.php'; ?>
