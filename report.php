<?php
session_start();
?>

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

        <!-- Report Form -->
        <div style="display: flex; justify-content: center; align-items: start; padding-top: 20px;">
            <div class="modal-content" style="margin: 0;">
                <h2 style="text-align: center; text-transform: uppercase;">Report item</h2>
                <form action="save_history.php" method="POST">
                    <input type="hidden" name="type" value="REPORT">
                    <div class="form-group">
                        <label for="borrowedBy">Reported by:</label>
                        <input type="text" id="borrowedBy" name="borrowedBy" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" class="form-select" onchange="loadItems()">
                        <option value="Instruments">Instruments</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Clothing">Clothing</option>
                    </select>
                </div>
                
                    
                    <div class="form-group">
                        <label for="itemName">Item name:</label>
                        <select id="itemName" name="itemName" class="form-select" required>
                            <option value="">-- Select an item --</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <select id="quantity" name="quantity" class="form-select" required>
                            <option value="">-- Select quantity --</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sn">SN:</label>
                        <input type="text" id="sn" name="sn">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <input type="text" id="status" name="status" required>
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
    </div>
</body>
</html>
<script>
    // Function to load items based on selected category
    function loadItems() {
        const category = document.getElementById('category').value;
        const itemSelect = document.getElementById('itemName');
        
        // Clear existing options
        itemSelect.innerHTML = '<option value="">-- Select an item --</option>';
        // Clear quantity options too
        document.getElementById('quantity').innerHTML = '<option value="">-- Select quantity --</option>';
        
        // Fetch items for the selected category
        fetch('get_items.php?category=' + category)
            .then(response => response.json())
            .then(data => {
                // Store the data for later use when selecting quantities
                window.itemsData = data;
                
                // Add new options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    option.dataset.quantity = item.quantity;
                    itemSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading items:', error));
    }
    
    // Function to update quantity dropdown based on selected item
    function updateQuantityDropdown() {
        const itemSelect = document.getElementById('itemName');
        const quantitySelect = document.getElementById('quantity');
        const selectedItem = itemSelect.options[itemSelect.selectedIndex];
        
        // Clear existing options
        quantitySelect.innerHTML = '<option value="">-- Select quantity --</option>';
        
        if (selectedItem && selectedItem.value) {
            // Find the selected item's data
            const itemData = window.itemsData.find(item => item.name === selectedItem.value);
            
            if (itemData) {
                const maxQuantity = parseInt(itemData.quantity);
                // Create options from 1 to maxQuantity
                for (let i = 1; i <= maxQuantity; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    quantitySelect.appendChild(option);
                }
            }
        }
    }
    
    // Add event listener for item change
    document.getElementById('itemName').addEventListener('change', updateQuantityDropdown);
    
    // Load items on page load
    document.addEventListener('DOMContentLoaded', loadItems);
</script>