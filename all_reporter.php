<?php
include './config.php';
include './admin_header.php';


// فلتر التاريخ (افتراضي الشهر الحالي)
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// إجمالي المبيعات
$stmt_sales = $conn->prepare("SELECT SUM(total) AS total_sales FROM orders WHERE payment_status='مدفوع' AND DATE(created_at) BETWEEN ? AND ?");
$stmt_sales->bind_param("ss", $start_date, $end_date);
$stmt_sales->execute();
$sales_row = $stmt_sales->get_result()->fetch_assoc();
$total_sales = $sales_row['total_sales'] ?? 0;

// إجمالي المصروفات
$stmt_exp = $conn->prepare("SELECT SUM(expense_amount) AS total_expenses FROM expenses WHERE DATE(expense_date) BETWEEN ? AND ?");
$stmt_exp->bind_param("ss", $start_date, $end_date);
$stmt_exp->execute();
$expense_row = $stmt_exp->get_result()->fetch_assoc();
$total_expenses = $expense_row['total_expenses'] ?? 0;

// صافي الربح
$net_profit = $total_sales - $total_expenses;

// تفاصيل المبيعات
$stmt_sales_details = $conn->prepare("SELECT id, customer_name, total, created_at FROM orders WHERE payment_status='مدفوع' AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC");
$stmt_sales_details->bind_param("ss", $start_date, $end_date);
$stmt_sales_details->execute();
$sales_details = $stmt_sales_details->get_result();

// تفاصيل المصروفات
$stmt_exp_details = $conn->prepare("SELECT id, expense_name, expense_amount, expense_date FROM expenses WHERE DATE(expense_date) BETWEEN ? AND ? ORDER BY expense_date DESC");
$stmt_exp_details->bind_param("ss", $start_date, $end_date);
$stmt_exp_details->execute();
$exp_details = $stmt_exp_details->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>📊 تقرير المبيعات والمصروفات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4" id="reportArea">
    <h1 class="text-center mb-4">📊 تقرير المبيعات والمصروفات</h1>

    <!-- فلتر التاريخ -->
    <form method="get" class="row g-3 mb-4 bg-white p-3 rounded shadow-sm">
        <div class="col-md-5">
            <label class="form-label">من تاريخ</label>
            <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control">
        </div>
        <div class="col-md-5">
            <label class="form-label">إلى تاريخ</label>
            <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control">
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">عرض</button>
        </div>
    </form>

    <!-- الإحصائيات -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">💰 إجمالي المبيعات</h5>
                    <h2><?= number_format($total_sales, 2) ?> ريال</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">💸 إجمالي المصروفات</h5>
                    <h2><?= number_format($total_expenses, 2) ?> ريال</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body text-center">
                    <h5 class="card-title">📈 صافي الربح</h5>
                    <h2><?= number_format($net_profit, 2) ?> ريال</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المبيعات -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            تفاصيل المبيعات
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-success">
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>اسم العميل</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sales_details->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= number_format($row['total'], 2) ?> ريال</td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- جدول المصروفات -->
    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white">
            تفاصيل المصروفات
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-danger">
                    <tr>
                        <th>رقم المصروف</th>
                        <th>اسم المصروف</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $exp_details->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['expense_name']) ?></td>
                        <td><?= number_format($row['expense_amount'], 2) ?> ريال</td>
                        <td><?= $row['expense_date'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- زر الطباعة -->
<div class="container text-center mb-4">
    <button onclick="printReport()" class="btn btn-dark btn-lg">🖨 طباعة التقرير</button>
</div>

<script>
function printReport() {
    var printContents = document.getElementById('reportArea').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>
<?php include './admin_footer.php'; ?> 

</body>
</html>
