<?php
// history.php
session_start();
include 'db_connect.php';

// Fetch borrowed items
$borrowed_sql = "SELECT history_id, type, borrowed_by, date, category, item_name, quantity, sn, status, remarks, created_at FROM history WHERE type = 'BORROW' ORDER BY created_at DESC";
$borrowed_result = $conn->query($borrowed_sql);

// Fetch reported items
$reported_sql = "SELECT history_id, type, borrowed_by, date, category, item_name, quantity, sn, status, remarks, created_at FROM history WHERE type = 'REPORT' ORDER BY created_at DESC";
$reported_result = $conn->query($reported_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="instruments_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .table-container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #444;
        }
        th {
            background-color: rgba(44, 36, 116, 0.9);
            color: white;
            font-weight: bold;
        }
        td {
            background-color: rgba(5, 5, 5, 0.7);
        }
        tr:hover td {
            background-color: rgba(44, 36, 116, 0.7);
        }
        .section-title {
            color: white;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments_nonadmin.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
        </a>
        <a href="accessory_nonadmin.php" class="icon-btn">
            <i class="fas fa-gem"></i>
        </a>
        <a href="clothing_nonadmin.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
        </a>
        <a href="members_nonadmin.php" class="icon-btn">
            <i class="fas fa-user"></i>
        </a>
        <a href="report_nonadmin.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
        </a>
        <a href="history_nonadmin.php" class="icon-btn">
            <i class="fas fa-clock"></i>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <h2>CDM Chorale Inventory System</h2>
            <a href="index.php" class="logout">Log Out</a>
        </div>

        <!-- History Tables -->
        <div class="table-container">
            <!-- Borrowed Items Table -->
            <h2 class="section-title">Borrowed Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Borrowed By</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>SN</th>
                    
                        <th>Remarks</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($borrowed_result->num_rows > 0) {
                        while($row = $borrowed_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['borrowed_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <button class='edit-btn' onclick='openEditModal(" . $row['history_id'] . ", " . json_encode($row) . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='remove-btn' onclick='confirmDelete(" . $row['history_id'] . ")'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align: center;'>No borrowed items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Reported Items Table -->
            <h2 class="section-title">Reported Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Reported By</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>SN</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($reported_result->num_rows > 0) {
                        while($row = $reported_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['borrowed_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                    <button class='edit-btn' onclick='openEditModal(" . $row['history_id'] . ", " . json_encode($row) . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='remove-btn' onclick='confirmDelete(" . $row['history_id'] . ")'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align: center;'>No reported items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>EDIT ITEM</h2>
            <form id="editForm" action="update_history.php" method="POST">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="type" id="editType">
                <div class="form-group">
                    <label for="editBorrowedBy">Borrowed/Reported by:</label>
                    <input type="text" id="editBorrowedBy" name="borrowedBy" required>
                </div>
                
                <div class="form-group">
                    <label for="editDate">Date:</label>
                    <input type="date" id="editDate" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="editCategory">Category:</label>
                    <input type="text" id="editCategory" name="category" required>
                </div>
                
                <div class="form-group">
                    <label for="editItemName">Item name:</label>
                    <input type="text" id="editItemName" name="itemName" required>
                </div>
                
                <div class="form-group">
                    <label for="editQuantity">Quantity:</label>
                    <input type="number" id="editQuantity" name="quantity" required>
                </div>
                
                <div class="form-group">
                    <label for="editSn">SN:</label>
                    <input type="text" id="editSn" name="sn">
                </div>
                
                <div class="form-group">
                    <label for="editStatus">Status:</label>
                    <input type="text" id="editStatus" name="status" required>
                </div>
                
                <div class="form-group">
                    <label for="editRemarks">Remarks:</label>
                    <textarea id="editRemarks" name="remarks" rows="4"></textarea>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(id, data) {
        document.getElementById('editId').value = id;
        document.getElementById('editType').value = data.type;
        document.getElementById('editBorrowedBy').value = data.borrowed_by;
        document.getElementById('editDate').value = data.date;
        document.getElementById('editCategory').value = data.category;
        document.getElementById('editItemName').value = data.item_name;
        document.getElementById('editQuantity').value = data.quantity;
        document.getElementById('editSn').value = data.sn;
        document.getElementById('editStatus').value = data.status;
        document.getElementById('editRemarks').value = data.remarks;
        
        document.getElementById('editModal').style.display = 'flex';
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = 'delete_history.php?id=' + id;
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    </script>
</body>
</html>
<?php
$conn->close();
?>
