<?php
include './config.php';
include './admin_header.php';


// إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $description = $_POST['description'] ?? '';

    // رفع الصورة
    $image_path = 'uploads/no-image.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $filename = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $quantity, $description, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: products.php");
    exit;
}

// حذف منتج
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // حذف صورة المنتج
    $res = $conn->query("SELECT image FROM products WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if ($row['image'] && file_exists($row['image']) && $row['image'] !== 'uploads/no-image.png') {
            unlink($row['image']);
        }
    }

    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit;
}

// جلب كل المنتجات
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");

?>

<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>إدارة المنتجات</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h2 class="mb-4">📦 قائمة المنتجات</h2>

  <!-- جدول عرض المنتجات -->
  <table class="table table-bordered text-center align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>اسم المنتج</th>
        <th>السعر (ر.س)</th>
        <th>الكمية</th>
        <th>الوصف</th>
        <th>الصورة</th>
        <th>إجراءات</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($products && $products->num_rows > 0): ?>
        <?php $i=1; while($row = $products->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= intval($row['quantity']) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($row['description'],0,50,'...')) ?></td>
            <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="صورة المنتج" style="width:70px; height:50px; object-fit:cover;"></td>
            <td>
              <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">تعديل</a>
              <a href="products.php?delete=<?= $row['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف المنتج؟');" class="btn btn-sm btn-danger">حذف</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">لا توجد منتجات حتى الآن</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- نموذج إضافة منتج جديد -->
  <h3 class="mt-5">➕ إضافة منتج جديد</h3>
  <form method="POST" enctype="multipart/form-data" class="mt-3">
    <div class="row g-3">
      <div class="col-md-4">
        <label for="name" class="form-label">اسم المنتج</label>
        <input type="text" name="name" id="name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label for="price" class="form-label">السعر (ر.س)</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label for="quantity" class="form-label">الكمية</label>
        <input type="number" name="quantity" id="quantity" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="image" class="form-label">صورة المنتج</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
      </div>
    </div>
    <div class="mt-3">
      <label for="description" class="form-label">الوصف</label>
      <textarea name="description" id="description" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" name="add_product" class="btn btn-success mt-3">إضافة المنتج</button>
  </form>
</div>
</body>
</html>
