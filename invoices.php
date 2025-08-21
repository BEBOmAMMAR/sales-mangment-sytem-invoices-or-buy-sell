<?php
include './config.php';
include './admin_header.php';


// إجمالي المبيعات
$sales_result = $conn->query("SELECT SUM(total) AS total_sales FROM orders WHERE payment_status='مدفوع'");
$sales_row = $sales_result->fetch_assoc();
$total_sales = $sales_row['total_sales'] ?? 0;

// إجمالي المصروفات
$expense_result = $conn->query("SELECT SUM(expense_amount) AS total_expenses FROM expenses");
$expense_row = $expense_result->fetch_assoc();
$total_expenses = $expense_row['total_expenses'] ?? 0;

// صافي الربح
$net_profit = $total_sales - $total_expenses;
?>

<h2>📊 تقرير المبيعات والمصروفات</h2>
<table border="1" width="50%">
    <tr>
        <th>إجمالي المبيعات</th>
        <td><?= number_format($total_sales, 2) ?> ريال</td>
    </tr>
    <tr>
        <th>إجمالي المصروفات</th>
        <td><?= number_format($total_expenses, 2) ?> ريال</td>
    </tr>
    <tr>
        <th>صافي الربح</th>
        <td><?= number_format($net_profit, 2) ?> ريال</td>
    </tr>
</table>
