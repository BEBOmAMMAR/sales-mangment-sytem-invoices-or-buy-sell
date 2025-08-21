<?php
//
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css">

</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">💊 لوحة التحكم</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">تسجيل الخروج</a>
    </div>
</nav>

<div class="d-flex">
    <!-- سايدبار -->
    <div class="bg-light border-end p-3" style="width: 250px; min-height: 100vh;">
        <h6 class="text-muted">القائمة</h6>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="add_product.php" class="nav-link">➕ إضافة منتج</a></li>
            <li class="nav-item"><a href="admin_products.php" class="nav-link">🛒 إدارة المنتجات</a></li>
            <li class="nav-item"><a href="admin_add_category.php" class="nav-link">📂 إضافة قسم</a></li>
            <li class="nav-item"><a href="admin_categories.php" class="nav-link">📋 إدارة الأقسام</a></li>
            <li class="nav-item"><a href="admin_orders.php" class="nav-link">الطلبات </a></li>
            <li class="nav-item"><a href="admin_invoices.php" class="nav-link">الفواتير  </a></li>
            <li class="nav-item"><a href="add_expense.php" class="nav-link">  اضافه فاتورة </a></li>
            <li class="nav-item"><a href="expenses.php" class="nav-link">  المصروفات  </a></li>

            
            <li class="nav-item"><a href="missing_medicines.php" class="nav-link">⚠️ الأدوية الناقصة</a></li>
            <li class="nav-item"><a href="returns.php" class="nav-link">🔄 المرتجع</a></li>
            <li class="nav-item"><a href="all_reporter.php" class="nav-link">🔄  التقارير </a></li>

        </ul>
    </div>

    <!-- محتوى الصفحة -->
    <div class="p-4 flex-grow-1">
