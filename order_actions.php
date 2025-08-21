<?php
// order_actions.php
include 'config.php'; // الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // تغيير حالة الطلب
    if ($action == 'update_order_status') {
        $order_id = intval($_POST['order_id']);
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        echo $stmt->execute() ? "success" : "error";
        exit;
    }

    // تغيير حالة الدفع
    if ($action == 'update_payment_status') {
        $order_id = intval($_POST['order_id']);
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        echo $stmt->execute() ? "success" : "error";
        exit;
    }

    // تحديث كمية منتج داخل الطلب
    if ($action == 'update_qty') {
        $order_id = intval($_POST['order_id']);
        $item_id = intval($_POST['item_id']);
        $new_qty = intval($_POST['qty']);

        $stmt = $conn->prepare("UPDATE order_items SET qty = ? WHERE id = ? AND order_id = ?");
        $stmt->bind_param("iii", $new_qty, $item_id, $order_id);
        echo $stmt->execute() ? "success" : "error";
        exit;
    }

    // حذف منتج من الطلب
    if ($action == 'delete_item') {
        $order_id = intval($_POST['order_id']);
        $item_id = intval($_POST['item_id']);

        $stmt = $conn->prepare("DELETE FROM order_items WHERE id = ? AND order_id = ?");
        $stmt->bind_param("ii", $item_id, $order_id);
        echo $stmt->execute() ? "success" : "error";
        exit;
    }

    // إلغاء الطلب بالكامل
    if ($action == 'cancel_order') {
        $order_id = intval($_POST['order_id']);

        $stmt1 = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt1->bind_param("i", $order_id);
        $stmt1->execute();

        $stmt2 = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt2->bind_param("i", $order_id);
        echo $stmt2->execute() ? "success" : "error";
        exit;
    }
}

echo "invalid"; ?> 
<?php include './admin_footer.php';?> 
