<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_orders.php");
    exit;
}

$id = intval($_GET['id']);

// جلب الحالة الحالية
$stmt = $conn->prepare("SELECT payment_status FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: admin_orders.php");
    exit;
}

$order = $res->fetch_assoc();
$new_status = ($order['payment_status'] === 'مدفوع') ? 'غير مدفوع' : 'مدفوع';

// تحديث الحالة
$stmt2 = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
$stmt2->bind_param("si", $new_status, $id);
$stmt2->execute();

header("Location: admin_orders.php");
exit;
?>
