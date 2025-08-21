<?php
include 'config.php';

// التأكد من وجود رقم الطلب
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("طلب غير صالح");
}

$order_id = intval($_GET['id']);

// تغيير حالة الدفع
if (isset($_GET['toggle_payment'])) {
    $conn->query("UPDATE orders SET payment_status = IF(payment_status='مدفوع','غير مدفوع','مدفوع') WHERE id=$order_id");
    header("Location: admin_order_details.php?id=$order_id");
    exit;
}

// حذف منتج من الطلب
if (isset($_GET['delete_item']) && is_numeric($_GET['delete_item'])) {
    $item_id = intval($_GET['delete_item']);
    $conn->query("DELETE FROM order_items WHERE id=$item_id AND order_id=$order_id");
    header("Location: admin_order_details.php?id=$order_id");
    exit;
}

// إلغاء الطلب بالكامل
if (isset($_GET['cancel_order'])) {
    $conn->query("DELETE FROM order_items WHERE order_id=$order_id");
    $conn->query("DELETE FROM orders WHERE id=$order_id");
    header("Location: admin_orders.php");
    exit;
}

// تحديث الكميات
if (isset($_POST['update_qty']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $item_id => $new_qty) {
        $item_id = intval($item_id);
        $new_qty = max(1, intval($new_qty)); // لا تقل الكمية عن 1
        $stmt = $conn->prepare("UPDATE order_items SET quantity=? WHERE id=? AND order_id=?");
        $stmt = $conn->prepare("UPDATE order_items SET qty = ? WHERE id = ? AND order_id = ?");
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("iii", $new_qty, $item_id, $order_id);

        $stmt->bind_param("iii", $new_qty, $item_id, $order_id);
        $stmt->execute();
    }
    // تحديث الإجمالي
    $conn->query("UPDATE orders 
        SET total = (SELECT SUM(quantity * price) FROM order_items WHERE order_id=$order_id) 
        WHERE id=$order_id");
    header("Location: admin_order_details.php?id=$order_id");
    exit;
}

// جلب تفاصيل الطلب
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
if (!$order) {
    die("الطلب غير موجود");
}

$items = $conn->query("SELECT oi.*, p.name 
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id=$order_id");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الطلب #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h3>تفاصيل الطلب #<?= $order_id ?></h3>
<p><strong>اسم العميل:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
<p><strong>الهاتف:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
<p><strong>الحالة:</strong> <?= $order['payment_status'] ?></p>

<a href="?id=<?= $order_id ?>&toggle_payment=1" class="btn btn-warning btn-sm">تغيير حالة الدفع</a>
<a href="?id=<?= $order_id ?>&cancel_order=1" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من إلغاء الطلب؟')">إلغاء الطلب</a>

<hr>

<form method="post">
<table class="table table-bordered">
    <thead>
        <tr>
            <th>المنتج</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>الإجمالي</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $items->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td>
                <input type="number" name="qty[<?= $row['id'] ?>]" value="<?= $row['qty'] ?>" min="1" class="form-control" style="width:70px">
            </td>
            <td><?= number_format($row['price'] * $row['qty'], 2) ?></td>
            <td>
                <a href="?id=<?= $order_id ?>&delete_item=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('حذف هذا المنتج من الطلب؟')">حذف</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<button type="submit" name="update_qty" class="btn btn-primary">تحديث الكميات</button>
</form>

<hr>
<h4>إجمالي الطلب: <?= number_format($order['total'], 2) ?> ر.س</h4>

</body>
</html>
