<?php
include './config.php';
include './admin_header.php';

$statusFilter = $_GET['status'] ?? 'all'; // all, paid, unpaid

// بناء شرط الفلترة
$whereClause = "";
if ($statusFilter === 'paid') {
    $whereClause = "WHERE payment_status = 'مدفوع'";
} elseif ($statusFilter === 'unpaid') {
    $whereClause = "WHERE payment_status = 'غير مدفوع'";
}

// إحصائيات
$statsSql = "SELECT 
    COUNT(*) AS total_orders,
    SUM(total) AS total_sales
    FROM orders
    $whereClause";
$statsResult = $conn->query($statsSql);
$stats = $statsResult->fetch_assoc();

// جلب الطلبات
$sql = "SELECT * FROM orders $whereClause ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container p-4">
    <h3>📦 إدارة الطلبات</h3>

    <form method="get" class="mb-3 d-flex gap-3 align-items-center">
        <label>فلترة حسب حالة الدفع:</label>
        <select name="status" class="form-select" style="width: 150px;" onchange="this.form.submit()">
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>الكل</option>
            <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>مدفوع</option>
            <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>غير مدفوع</option>
        </select>
    </form>

    <div class="mb-4">
        <strong>عدد الطلبات:</strong> <?= $stats['total_orders'] ?? 0 ?> |
        <strong>إجمالي المبيعات:</strong> <?= number_format($stats['total_sales'] ?? 0, 2) ?> ر.س
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم العميل</th>
                <th>رقم الهاتف</th>
                <th>طريقة الدفع</th>
                <th>حالة الدفع</th>
                <th>المجموع</th>
                <th>تاريخ الطلب</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): 
                $i = 1;
                while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    <td>
                        <?php if ($order['payment_status'] === 'مدفوع'): ?>
                            <span class="badge bg-success">مدفوع</span>
                        <?php else: ?>
                            <span class="badge bg-danger">غير مدفوع</span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($order['total'], 2) ?> ر.س</td>
                    <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <a href="admin_order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">التفاصيل</a>
                        <a href="toggle_payment_status.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-warning"
                            onclick="return confirm('هل تريد تغيير حالة الدفع؟');">
                            تبديل الدفع
                        </a>
                        <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('هل أنت متأكد من حذف الطلب؟');">
                            حذف
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="8" class="text-center">لا توجد طلبات لعرضها</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include './admin_footer.php'; ?>
