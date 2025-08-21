<?php
include './config.php';
include './admin_header.php';


$id = intval($_GET['id'] ?? 0);
$message = '';

// جلب بيانات التصنيف
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo '<div class="alert alert-danger m-4">❌ هذا التصنيف غير موجود.</div>';
    
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['category_name'] ?? '');

    if ($new_name === '') {
        $message = '<div class="alert alert-danger">⚠️ الرجاء إدخال اسم التصنيف.</div>';
    } else {
        // تحقق تكرار الاسم على تصنيفات غير هذا
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $stmt->bind_param("si", $new_name, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = '<div class="alert alert-warning">⚠️ اسم التصنيف موجود مسبقًا.</div>';
        } else {
            // تحديث الاسم
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $new_name, $id);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">✅ تم تحديث التصنيف بنجاح.</div>';
                // تحديث المتغير عشان يظهر الاسم الجديد في الحقل
                $category['name'] = $new_name;
            } else {
                $message = '<div class="alert alert-danger">❌ خطأ أثناء تحديث التصنيف.</div>';
            }
        }
    }
}
?>
<?php

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // تحقق إذا في منتجات مرتبطة
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM products WHERE category_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['cnt'] > 0) {
        echo '<div class="alert alert-danger mx-4">';
        echo "❌ ما تقدرش تحذف التصنيف ده، لأنه مرتبط بـ {$row['cnt']} منتج. لازم تحذف أو تغير تصنيف المنتجات دي أولًا.";
        echo '</div>';
    } else {
        // الحذف لو مافيش منتجات مرتبطة
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            echo '<div class="alert alert-success mx-4">✅ تم حذف التصنيف بنجاح.</div>';
        } else {
            echo '<div class="alert alert-danger mx-4">❌ خطأ أثناء حذف التصنيف.</div>';
        }
    }
}?>


<div class="container p-4" style="max-width: 500px;">
    <h3>✏ تعديل تصنيف</h3>
    <?= $message ?>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label for="category_name" class="form-label">اسم التصنيف</label>
            <input type="text" id="category_name" name="category_name" class="form-control" required
                   value="<?= htmlspecialchars($category['name']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="admin_categories.php" class="btn btn-secondary">عودة للقائمة</a>
    </form>
</div>

<?php include './admin_footer.php';  ?>
