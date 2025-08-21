<?php
include './config.php';

// جلب بيانات المنتج للتعديل
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("معرف المنتج غير موجود.");
}

$product_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("المنتج غير موجود.");
}

$product = $result->fetch_assoc();
$stmt->close();

// جلب التصنيفات لعرضها في القائمة
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// تحديث بيانات المنتج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);

    // إذا تم رفع صورة جديدة
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = "../uploads/" . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            // حذف الصورة القديمة إذا كانت موجودة
            if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])) {
                unlink("../uploads/" . $product['image']);
            }
            $image_sql = ", image = ?";
        } else {
            die("فشل رفع الصورة.");
        }
    } else {
        $image_sql = "";
    }

    // بناء الاستعلام
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?" . $image_sql . " WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($image_sql) {
        $stmt->bind_param("ssdssi", $name, $description, $price, $category_id, $image_name, $product_id);
    } else {
        $stmt->bind_param("ssdii", $name, $description, $price, $category_id, $product_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('تم تحديث المنتج بنجاح'); window.location='admin_products.php';</script>";
    } else {
        echo "خطأ في التحديث: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل المنتج</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<div class="container">
    <h2 class="mb-4">تعديل المنتج</h2>

    <form method="post" enctype="multipart/form-data" class="border p-3 rounded">
        <div class="mb-3">
            <label class="form-label">اسم المنتج</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">السعر</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">التصنيف</label>
            <select name="category_id" class="form-select" required>
                <option value="">اختر التصنيف</option>
                <?php while ($cat = $cats->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">الصورة الحالية</label><br>
            <?php if (!empty($product['image'])): ?>
                <img src="../uploads/<?= $product['image'] ?>" alt="" width="150" class="border p-1 mb-2">
            <?php else: ?>
                <p>لا توجد صورة</p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">تغيير الصورة</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="admin_products.php" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

</body>
</html>
