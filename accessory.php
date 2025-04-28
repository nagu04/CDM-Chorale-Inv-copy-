<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="instruments_style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
        </a>
        <a href="accessory.php" class="icon-btn">
            <i class="fas fa-gem"></i>
        </a>
        <a href="clothing.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
        </a>
        <a href="members.php" class="icon-btn">
            <i class="fas fa-user"></i>
        </a>
        <a href="report.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
        </a>
        <a href="history.php" class="icon-btn">
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

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch instruments from database
            $sql = "SELECT * FROM accessories";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<img src='keyboard.jpg' alt='Instrument'>"; // Placeholder image
                    echo "<h3>" . $row["deco_name"] . "</h3>";
                    echo "<p>Condition: " . $row["condition"] . "</p>";
                    echo "<p>Quantity: " . $row["quantity"] . "</p>";
                    echo "<button class='borrow-btn' data-name='" . $row["deco_name"] . "'>Borrow</button>";
                    echo "</div>";
                }
            } else {
                echo "<p>No instruments found</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="action-buttons">
            <button class="add-button" id="addButton">
                <i class="fas fa-plus"></i> Add
            </button>
            <button class="delete-button" id="deleteButton">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    <!-- Modal -->
    <div id="borrowModal" class="modal">
        <div class="modal-content">
            <h2>Borrow item</h2>
            <form action="save_history.php" method="POST">
                <input type="hidden" name="type" value="BORROW">
                <div class="form-group">
                    <label for="borrowedBy">Borrowed by:</label>
                    <input type="text" id="borrowedBy" name="borrowedBy" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="Instruments">Instruments</option>
                        <option value="Accessories" selected>Accessories</option>
                        <option value="Clothing">Clothing</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="itemName">Item name:</label>
                    <input type="text" id="itemName" name="itemName" required>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>
                
                <div class="form-group">
                    <label for="sn">SN:</label>
                    <input type="text" id="sn" name="sn" required>
                </div>
              
                
                <div class="form-group">
                    <label for="remarks">Remarks:</label>
                    <textarea id="remarks" name="remarks" rows="4"></textarea>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Accessory Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Accessory</h2>
            <form action="save_accessory.php" method="POST">
                <div class="form-group">
                    <label for="addName">Name:</label>
                    <input type="text" id="addName" name="deco_name" required>
                </div>
                
                <div class="form-group">
                    <label for="addCondition">Condition:</label>
                    <input type="text" id="addCondition" name="condition" required>
                </div>
                
                <div class="form-group">
                    <label for="addQuantity">Quantity:</label>
                    <input type="number" id="addQuantity" name="quantity" required min="1">
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Accessory Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Delete Accessory</h2>
            <form action="delete_accessory.php" method="POST">
                <div class="form-group">
                    <label for="deleteAccessory">Select Accessory:</label>
                    <select id="deleteAccessory" name="deco_id" class="form-select" required>
                        <option value="">-- Select an accessory --</option>
                        <?php
                        include 'db_connect.php';
                        $sql = "SELECT deco_id, deco_name FROM accessories ORDER BY deco_name";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["deco_id"] . "'>" . $row["deco_name"] . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn" style="background-color: #ff4444; color: white;">Delete Accessory</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Borrow Modal
    var borrowModal = document.getElementById("borrowModal");
    var borrowBtns = document.getElementsByClassName("borrow-btn");
    
    for (var i = 0; i < borrowBtns.length; i++) {
        borrowBtns[i].onclick = function() {
            borrowModal.style.display = "flex";
            // Set the item name in the input field
            document.getElementById("itemName").value = this.getAttribute("data-name");
            // Set category to Instruments by default
            document.getElementById("category").value = "Accessory";
        
        }
    }
    
    // Add Modal
    var addModal = document.getElementById("addModal");
    var addButton = document.getElementById("addButton");
    
    addButton.onclick = function() {
        addModal.style.display = "flex";
    }
    
    // Delete Modal
    var deleteModal = document.getElementById("deleteModal");
    var deleteButton = document.getElementById("deleteButton");
    
    deleteButton.onclick = function() {
        deleteModal.style.display = "flex";
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == borrowModal) {
            borrowModal.style.display = "none";
        }
        if (event.target == addModal) {
            addModal.style.display = "none";
        }
        if (event.target == deleteModal) {
            deleteModal.style.display = "none";
        }
    }
    </script>
</body>
</html>

<?php

?>