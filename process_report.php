// ... existing code ...

$status = $_POST['status'];

// When inserting into the history table, include the status
$sql = "INSERT INTO history (type, borrowed_by, date, category, item_name, quantity, sn, `condition`, status, remarks) 
        VALUES ('REPORT', ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissss", $borrowed_by, $category, $item_name, $quantity, $sn, $condition, $status, $remarks);

// ... existing code ...