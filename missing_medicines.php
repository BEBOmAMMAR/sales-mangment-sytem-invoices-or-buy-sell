<?php
include './config.php';
include './admin_header.php';

session_start();

// جلب الأدوية الناقصة
$medicines = $conn->query("SELECT * FROM missing_medicines ORDER BY status ASC, name ASC");

// إضافة دواء جديد
if (isset($_POST['add_medicine'])) {
    $name = trim($_POST['name']);
    $note = trim($_POST['note']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO missing_medicines (name, note, status, created_at) VALUES (?, ?, 'ناقص', NOW())");
        $stmt->bind_param("ss", $name, $note);
        $stmt->execute();
        header("Location: missing_medicines.php");
        exit;
    }
}

// تحديث حالة الدواء
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $conn->query("UPDATE missing_medicines SET status = IF(status='ناقص', 'متوفر', 'ناقص') WHERE id=$id");
    header("Location: missing_medicines.php");
    exit;
}

// حذف دواء
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM missing_medicines WHERE id=$id");
    header("Location: missing_medicines.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الأدوية الناقصة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h3>📦 قائمة الأدوية الناقصة</h3>

<form method="post" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="اسم الدواء" required>
    </div>
    <div class="col-md-4">
        <input type="text" name="note" class="form-control" placeholder="ملاحظات">
    </div>
    <div class="col-md-2">
        <button type="submit" name="add_medicine" class="btn btn-primary w-100">➕ إضافة</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>اسم الدواء</th>
            <th>ملاحظات</th>
            <th>الحالة</th>
            <th>تاريخ الإضافة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $medicines->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td>
                <span class="badge <?= $row['status'] == 'ناقص' ? 'bg-danger' : 'bg-success' ?>">
                    <?= $row['status'] ?>
                </span>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a href="?toggle_status=<?= $row['id'] ?>" class="btn btn-warning btn-sm">تغيير الحالة</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('حذف هذا الدواء؟')" class="btn btn-danger btn-sm">حذف</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
