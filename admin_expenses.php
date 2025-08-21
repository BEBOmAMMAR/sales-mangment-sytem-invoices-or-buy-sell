<?php
include './config.php'; // ملف الاتصال بقاعدة البيانات

// فلتر حسب التاريخ (اختياري)
$where = "";
if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $from = $conn->real_escape_string($_GET['from']);
    $to = $conn->real_escape_string($_GET['to']);
    $where = "WHERE date BETWEEN '$from' AND '$to'";
}

// جلب المصروفات
$sql = "SELECT * FROM expenses $where ORDER BY date DESC";
$expenses = $conn->query($sql);

if (!$expenses) {
    die("خطأ في الاستعلام: " . $conn->error);
}

// حساب مجموع المصروفات
$total_sql = "SELECT SUM(amount) as total_expenses FROM expenses $where";
$total_result = $conn->query($total_sql);
$total_row = $total_result ? $total_result->fetch_assoc() : ['total_expenses' => 0];
$total_expenses = $total_row['total_expenses'] ?? 0;

?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة المصروفات</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>إدارة المصروفات</h2>

<!-- فلتر -->
<form method="get">
    من: <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>">
    إلى: <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>">
    <button type="submit">تصفية</button>
</form>

<!-- المجموع -->
<h3>إجمالي المصروفات: <?= number_format($total_expenses, 2) ?> ريال</h3>

<!-- جدول المصروفات -->
<table border="1" width="100%">
    <tr>
        <th>م</th>
        <th>الوصف</th>
        <th>المبلغ</th>
        <th>التاريخ</th>
        <th>إجراءات</th>
    </tr>

    <?php if ($expenses->num_rows > 0): ?>
        <?php while ($row = $expenses->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td><?= $row['date'] ?></td>
                <td>
                    <a href="edit_expense.php?id=<?= $row['id'] ?>">تعديل</a> |
                    <a href="delete_expense.php?id=<?= $row['id'] ?>" onclick="return confirm('تأكيد الحذف؟')">حذف</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">لا توجد مصروفات</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
