<?php
include './config.php';

if (!isset($_GET['id'])) {
    die("رقم الفاتورة غير موجود");
}

$order_id = intval($_GET['id']);

// جلب بيانات الفاتورة
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("الفاتورة غير موجودة");
}

// جلب تفاصيل المنتجات
$stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>فاتورة #<?= $order['id'] ?></title>
<style>
    body { font-family: Arial, sans-serif; direction: rtl; text-align: right; margin: 20px; }
    .invoice-box { width: 100%; border: 1px solid #ccc; padding: 20px; }
    h2 { margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid #ccc; }
    th, td { padding: 8px; }
    .total { font-weight: bold; }
    .text-center { text-align: center; }
</style>
</head>
<body onload="window.print()">

<div class="invoice-box">
    <h2>فاتورة مبيعات</h2>
    <p>رقم الفاتورة: <?= $order['id'] ?></p>
    <p>اسم العميل: <?= htmlspecialchars($order['customer_name']) ?></p>
    <p>الهاتف: <?= htmlspecialchars($order['customer_phone']) ?></p>
    <p>تاريخ الفاتورة: <?= $order['created_at'] ?></p>
    <p>طريقة الدفع: <?= $order['payment_method'] ?></p>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            while ($item = $items->fetch_assoc()):
                $item_total = $item['qty'] * $item['price'];
                $total += $item_total;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= number_format($item['price'], 2) ?> ج.م</td>
                <td><?= number_format($item_total, 2) ?> ج.م</td>
            </tr>
            <?php endwhile; ?>
            <tr class="total">
                <td colspan="3" class="text-center">الإجمالي الكلي</td>
                <td><?= number_format($total, 2) ?> ج.م</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:20px,text_align:center;">    <h1>شكراً لتعاملكم معنا 🌿  </h1> </p>
</div>

<!-- زر الرجوع -->
<div style="margin-top: 20px; text-align: center;">
    <a href="admin_orders.php" style="
        display: inline-block;
        padding: 10px 20px;
        background-color: #28a745;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    ">⬅ الرجوع للصفحة الرئيسية</a>
</div>

<?php include './admin_footer.php';?> 

</body>
</html>
