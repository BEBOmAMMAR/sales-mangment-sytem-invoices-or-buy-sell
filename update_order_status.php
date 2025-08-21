<?php
// update_order_status.php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$field = trim($_POST['field'] ?? '');
$value = trim($_POST['value'] ?? '');

// تحقق الحقول المسموح بها
$allowed = ['payment_status', 'order_status'];
if ($id <= 0 || !in_array($field, $allowed)) {
    echo json_encode(['status'=>'error','message'=>'بيانات غير صالحة']);
    exit;
}

// تحديث آمن (نستخدم prepared statement لكن الحقل ندرجه بحذر)
if ($field === 'payment_status') {
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $id);
} else { // order_status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $value, $id);
}

if (!$stmt) {
    echo json_encode(['status'=>'error','message'=>'خطأ في التحضير: '.$conn->error]);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'تم التحديث']);
} else {
    echo json_encode(['status'=>'error','message'=>'فشل في التحديث: '.$stmt->error]);
}

?>
<?php include './admin_footer.php';?> 
