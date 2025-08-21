<?php
include './config.php';

// التأكد من وجود معرف المنتج
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("معرف المنتج غير موجود.");
}

$product_id = intval($_GET['id']);

// جلب بيانات المنتج للتأكد وحذف الصورة
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("المنتج غير موجود.");
}

$product = $result->fetch_assoc();
$stmt->close();

// حذف المنتج من قاعدة البيانات
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    // حذف الصورة من السيرفر إذا كانت موجودة
    if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])) {
        unlink("../uploads/" . $product['image']);
    }

    echo "<script>alert('تم حذف المنتج بنجاح'); window.location='admin_products.php';</script>";
} else {
    echo "خطأ في الحذف: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<?php include './admin_footer.php';?> 
