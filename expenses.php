<?php
include './config.php';
include './admin_header.php';

$result = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC");
?>

<h2>فواتير المصروفات</h2>
<a href="add_expense.php" class="btn btn-primary">➕ إضافة مصروف</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
    <tr>
        <th>رقم</th>
        <th>اسم المصروف</th>
        <th>المبلغ</th>
        <th>التاريخ</th>
        <th>ملاحظات</th>
        <th>إجراءات</th>
    </tr>
        </thead>
        <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['expense_name'] ?></td>
            <td><?= $row['expense_amount'] ?> ريال</td>
            <td><?= $row['expense_date'] ?></td>
            <td><?= $row['expense_note'] ?></td>
            <td>
                <a href="edit_expense.php?id=<?= $row['id'] ?> "class="btn btn-sm btn-warning">✏ تعديل</a>
                <a href="delete_expense.php?id=<?= $row['id'] ?>"class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑 حذف</a>
            </td>
        </tr>
    <?php } ?>
</table>
<?php include './admin_footer.php';?> 
