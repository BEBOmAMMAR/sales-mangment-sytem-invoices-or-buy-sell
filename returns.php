<?php
include './config.php';
include './admin_header.php';


session_start();

// جلب المرتجعات
$returns = $conn->query("SELECT * FROM returns ORDER BY status ASC, created_at DESC");

// إضافة مرتجع جديد
if (isset($_POST['add_return'])) {
    $product_name = trim($_POST['product_name']);
    $customer_name = trim($_POST['customer_name']);
    $reason = trim($_POST['reason']);

    if (!empty($product_name) && !empty($customer_name)) {
        $stmt = $conn->prepare("INSERT INTO returns (product_name, customer_name, reason, status, created_at) VALUES (?, ?, ?, 'قيد المراجعة', NOW())");
        $stmt->bind_param("sss", $product_name, $customer_name, $reason);
        $stmt->execute();
        header("Location: returns.php");
        exit;
    }
}

// تغيير الحالة
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $conn->query("UPDATE returns SET status = 
        CASE 
            WHEN status='قيد المراجعة' THEN 'مقبول'
            WHEN status='مقبول' THEN 'مرفوض'
            ELSE 'قيد المراجعة'
        END 
        WHERE id=$id");
    header("Location: returns.php");
    exit;
}

// حذف المرتجع
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM returns WHERE id=$id");
    header("Location: returns.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>المرتجعات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h3>🔄 قائمة المرتجعات</h3>

<form method="post" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="product_name" class="form-control" placeholder="اسم المنتج" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="customer_name" class="form-control" placeholder="اسم العميل" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="reason" class="form-control" placeholder="سبب المرتجع">
    </div>
    <div class="col-md-2">
        <button type="submit" name="add_return" class="btn btn-primary w-100">➕ إضافة</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>اسم المنتج</th>
            <th>اسم العميل</th>
            <th>السبب</th>
            <th>الحالة</th>
            <th>تاريخ الإضافة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $returns->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td>
                <span class="badge 
                    <?= $row['status'] == 'مقبول' ? 'bg-success' : ($row['status'] == 'مرفوض' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= $row['status'] ?>
                </span>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a href="?toggle_status=<?= $row['id'] ?>" class="btn btn-warning btn-sm">تغيير الحالة</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('حذف هذا المرتجع؟')" class="btn btn-danger btn-sm">حذف</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>



<?php include './admin_footer.php';?> 

</body>
</html>
