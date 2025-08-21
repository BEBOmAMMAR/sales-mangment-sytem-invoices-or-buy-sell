<?php
include './config.php';
include './admin_header.php';


// عند إرسال الفورم
if (isset($_POST['submit'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $quantity    = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);

    // رفع الصورة
    $imagePath = "uploads/no-image.png";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        // تأكد من وجود مجلد الرفع
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    // إدخال البيانات في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, category_id, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("ssdiis", $name, $description, $price, $quantity, $category_id, $imagePath);
        if ($stmt->execute()) {
            $success = "✅ تم إضافة المنتج بنجاح";
        } else {
            $error = "❌ فشل في إضافة المنتج: " . $stmt->error;
        }
    } else {
        $error = "خطأ في قاعدة البيانات: " . $conn->error;
    }
}

// جلب التصنيفات لعرضها في القائمة
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>إضافة منتج</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h3>➕ إضافة منتج جديد</h3>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">اسم المنتج</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="row">
            <div class="mb-3 col-md-4">
                <label class="form-label">السعر</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">الكمية</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">التصنيف</label>
                <select name="category_id" class="form-control" required>
                    <option value="">اختر التصنيف</option>
                    <?php while ($cat = $cats->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">صورة المنتج</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" name="submit" class="btn btn-success">💾 حفظ المنتج</button>
    </form>
</div>
<?php 
include './admin_footer.php';?>
</body>
</html>
