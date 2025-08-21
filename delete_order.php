<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_orders.php");
    exit;
}

$id = intval($_GET['id']);

// حذف العناصر المرتبطة أولاً
$stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// حذف الطلب
$stmt2 = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();

header("Location: admin_orders.php");
exit;
?>
