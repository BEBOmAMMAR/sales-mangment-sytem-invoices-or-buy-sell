<?php
include './config.php';
include './admin_header.php';

// جلب المنتجات من قاعدة البيانات
$result = $conn->query("
    SELECT p.id, p.name, p.price, p.quantity, p.image, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>📦 إدارة المنتجات</h3>
        <a href="add_product.php" class="btn btn-primary">
            ➕ إضافة منتج جديد
        </a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>الصورة</th>
                <th>الاسم</th>
                <th>التصنيف</th>
                <th>السعر</th>
                <th>الكمية</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="60" height="60" style="object-fit:cover">
                            <?php else: ?>
                                <span class="text-muted">لا توجد صورة</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['category_name'] ?? 'غير محدد') ?></td>
                        <td><?= number_format($row['price'], 2) ?> ر.س</td>
                        <td><?= intval($row['quantity']) ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏ تعديل</a>
                            <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑 حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">🚫 لا توجد منتجات مضافة بعد</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include './admin_footer.php'; ?>
