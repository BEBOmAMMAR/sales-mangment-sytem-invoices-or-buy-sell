<?php
include './config.php';
include './admin_header.php';


$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_name = trim($_POST['category_name'] ?? '');
    if ($cat_name === '') {
        $message = '<div class="alert alert-danger">⚠️ الرجاء إدخال اسم التصنيف.</div>';
    } else {
        // تحقق لو التصنيف موجود مسبقاً
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = '<div class="alert alert-warning">⚠️ هذا التصنيف موجود بالفعل.</div>';
        } else {
            // إضافة التصنيف
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $cat_name);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">✅ تم إضافة التصنيف بنجاح.</div>';
            } else {
                $message = '<div class="alert alert-danger">❌ حدث خطأ أثناء الإضافة.</div>';
            }
        }
    }
}
?>

<div class="container p-4">
    <h3>➕ إضافة تصنيف جديد</h3>
    <?= $message ?>
    <form method="post" class="mt-3" style="max-width: 400px;">
        <div class="mb-3">
            <label for="category_name" class="form-label">اسم التصنيف</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
        </div>
        <button type="submit" class="btn btn-primary">إضافة</button>
        <a href="admin_categories.php" class="btn btn-secondary">عودة للقائمة</a>
    </form>
</div>
<?php include './admin_footer.php'; ?>
