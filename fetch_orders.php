<?php
// fetch_orders.php
header('Content-Type: application/json; charset=utf-8');
include 'config.php'; // $conn (mysqli)

// استقبال فلتر الطلب
$start_date = trim($_POST['start_date'] ?? '');
$end_date   = trim($_POST['end_date'] ?? '');
$payment_status = trim($_POST['payment_status'] ?? '');
$order_status   = trim($_POST['order_status'] ?? '');

// بناء شرط SQL ديناميكي وآمن
$where = " WHERE 1=1 ";
$params = [];
$types = "";

// تواريخ
if ($start_date !== '') {
    $where .= " AND DATE(created_at) >= ?";
    $params[] = $start_date;
    $types .= "s";
}
if ($end_date !== '') {
    $where .= " AND DATE(created_at) <= ?";
    $params[] = $end_date;
    $types .= "s";
}
// حالة الدفع
if ($payment_status !== '') {
    $where .= " AND payment_status = ?";
    $params[] = $payment_status;
    $types .= "s";
}
// حالة الطلب
if ($order_status !== '') {
    $where .= " AND order_status = ?";
    $params[] = $order_status;
    $types .= "s";
}

$sql = "SELECT id, customer_name, customer_phone, customer_email, total, payment_status, order_status, created_at
        FROM orders
        $where
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['status'=>'error','message'=>'خطأ في الاستعلام: '.$conn->error]);
    exit;
}
if ($params) {
    // ربط الوسائط ديناميكياً
    $bind_names[] = $types;
    for ($i=0;$i<count($params);$i++) {
        $bind_names[] = &$params[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}

$stmt->execute();
$res = $stmt->get_result();

$orders_count = 0;
$total_sales = 0.0;
$rows_html = '';
$rows_html .= "<div class='table-responsive'><table class='table table-bordered table-striped'>";
$rows_html .= "<thead class='table-dark'><tr>
    <th>رقم</th>
    <th>العميل</th>
    <th>الهاتف</th>
    <th>الإيميل</th>
    <th>الإجمالي</th>
    <th>حالة الدفع</th>
    <th>حالة الطلب</th>
    <th>التاريخ</th>
    <th>إجراءات</th>
</tr></thead><tbody>";

while ($row = $res->fetch_assoc()) {
    $orders_count++;
    $total_sales += floatval($row['total']);

    // خيارات الحالة (ممكن تعدل النصوص حسب جدولك)
    $payment_select = "<select class='form-select payment-select' data-id='{$row['id']}'>
        <option value='مدفوع' ".($row['payment_status']=='مدفوع'?'selected':'').">مدفوع</option>
        <option value='غير مدفوع' ".($row['payment_status']=='غير مدفوع'?'selected':'').">غير مدفوع</option>
    </select>";

    $order_select = "<select class='form-select order-select' data-id='{$row['id']}'>
        <option value='جاري التحضير' ".($row['order_status']=='جاري التحضير'?'selected':'').">جاري التحضير</option>
        <option value='تم الشحن' ".($row['order_status']=='تم الشحن'?'selected':'').">تم الشحن</option>
        <option value='تم التسليم' ".($row['order_status']=='تم التسليم'?'selected':'').">تم التسليم</option>
        <option value='ملغي' ".($row['order_status']=='ملغي'?'selected':'').">ملغي</option>
    </select>";

    $rows_html .= "<tr>
        <td>{$row['id']}</td>
        <td>".htmlspecialchars($row['customer_name'])."</td>
        <td>".htmlspecialchars($row['customer_phone'])."</td>
        <td>".htmlspecialchars($row['customer_email'])."</td>
        <td>".number_format($row['total'],2)." ر.س</td>
        <td style='min-width:130px;'>$payment_select</td>
        <td style='min-width:160px;'>$order_select</td>
        <td>{$row['created_at']}</td>
        <td>
            <a href='admin_order_details.php?id={$row['id']}' class='btn btn-sm btn-info'>عرض</a>
            <a href='delete_order.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"حذف الطلب؟\")'>حذف</a>
        </td>
    </tr>";
}

$rows_html .= "</tbody></table></div>";

// إرجاع JSON
echo json_encode([
    'status' => 'ok',
    'orders_count' => $orders_count,
    'total_sales'  => number_format($total_sales, 2),
    'table' => $rows_html
]);
