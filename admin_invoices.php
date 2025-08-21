<?php
include './admin_header.php';
include './config.php'; // الاتصال بقاعدة البيانات

// فلترة البيانات
$where = "1";
$params = [];
if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $where .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params[] = $_GET['from'];
    $params[] = $_GET['to'];
}
if (!empty($_GET['payment_method'])) {
    $where .= " AND payment_method = ?";
    $params[] = $_GET['payment_method'];
}

// جلب الفواتير
$sql = "SELECT * FROM orders WHERE $where ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

// ربط الباراميترات ديناميكيًا
if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// حساب الإجمالي
$total_sales = 0;
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
    $total_sales += $row['total'];
}
?>

<div class="container-fluid">
    <h4 class="mb-4">📄 إدارة الفواتير</h4>

    <!-- فلتر -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>من تاريخ</label>
            <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label>إلى تاريخ</label>
            <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label>طريقة الدفع</label>
            <select name="payment_method" class="form-select">
                <option value="">الكل</option>
                <option value="كاش" <?= (($_GET['payment_method'] ?? '') == 'كاش') ? 'selected' : '' ?>>كاش</option>
                <option value="فيزا" <?= (($_GET['payment_method'] ?? '') == 'فيزا') ? 'selected' : '' ?>>فيزا</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">تصفية</button>
        </div>
    </form>

    <!-- إحصائيات -->
    <div class="alert alert-success">
        عدد الفواتير: <b><?= count($orders) ?></b> | إجمالي المبيعات: <b><?= number_format($total_sales, 2) ?> ج.م</b>
    </div>

    <!-- جدول الفواتير -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم العميل</th>
                <th>الهاتف</th>
                <th>الإجمالي</th>
                <th>طريقة الدفع</th>
                <th>تاريخ الإنشاء</th>
                <th>طباعة</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders): ?>
                <?php foreach ($orders as $invoice): ?>
                    <tr>
                        <td><?= $invoice['id'] ?></td>
                        <td><?= htmlspecialchars($invoice['customer_name']) ?></td>
                        <td><?= htmlspecialchars($invoice['customer_phone']) ?></td>
                        <td><?= number_format($invoice['total'], 2) ?> ج.م</td>
                        <td><?= htmlspecialchars($invoice['payment_method']) ?></td>
                        <td><?= $invoice['created_at'] ?></td>
                        <td>
                            <a href="print_invoice.php?id=<?= $invoice['id'] ?>" target="_blank" class="btn btn-sm btn-info">🖨 طباعة</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">لا توجد فواتير</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include './admin_footer.php'; ?>
