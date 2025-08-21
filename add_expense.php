<?php
include './config.php';
include './admin_header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['expense_name'];
    $amount = $_POST['expense_amount'];
    $date = $_POST['expense_date'];
    $note = $_POST['expense_note'];

    $stmt = $conn->prepare("INSERT INTO expenses (expense_name, expense_amount, expense_date, expense_note) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $amount, $date, $note);
    $stmt->execute();
    header("Location: expenses.php");
}
?>

<h2> إضافة مصروف جديد</h2>
    <form action="" method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
 <div class="mb-3">
            <label class="form-label">   اسم المصروف </label>
            <input type="text" name="expense_name" class="form-control" required>
        </div>

 <div class="mb-3">
            <label class="form-label"> المبلغ </label>
            <input type="text" name="expense_amount" class="form-control" required>
        </div>
 <div class="mb-3">
            <label class="form-label"> التاريخ </label>
            <input type="text" name="expense_date" class="form-control" required>
        </div>
   <div class="mb-3">
            <label class="form-label"> الملاحظات  </label>
              <textarea name="expense_note"rows="4" cols="100"></textarea>

              </div>

        <button type="submit" name="submit" class="btn btn-success">💾 حفظ </button>


</form>
<?php include './admin_footer.php';?> 
