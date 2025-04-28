<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="instruments_style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Additional styles for profile modal */
        .profile-info {
            margin-bottom: 15px;
        }
        .profile-info label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .profile-info p {
            margin: 0;
            padding: 10px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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

         <!-- Action Buttons -->
         <div class="action-buttons">
            <button class="add-button" id="addButton">
                <i class="fas fa-plus"></i> Add
            </button>
            <button class="delete-button" id="deleteButton">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch members from database
            $sql = "SELECT * FROM members";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<img src='2x2 pic formal.jpg' alt='Member'>"; // Placeholder image
                    echo "<h3>" . $row["members_name"] . "</h3>";
                
                    echo "<p>Program: " . $row["program"] . "</p>";
                    echo "<p>Position: " . $row["position"] . "</p>";
                    echo "<button class='borrow-btn' data-id='" . $row["member_id"] . "' 
                          data-name='" . $row["members_name"] . "' 
                          data-program='" . $row["program"] . "' 
                          data-position='" . $row["position"] . "' 
                          data-birthdate='" . (isset($row["birthdate"]) ? $row["birthdate"] : "") . "' 
                          data-address='" . (isset($row["address"]) ? $row["address"] : "") . "'>View Profile</button>";
                    echo "</div>";
                }
            } else {
                echo "<p>No members found</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <h2>Member Profile</h2>
            <div class="profile-info">
                <label for="memberName">Name:</label>
                <p id="memberName"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberProgram">Program:</label>
                <p id="memberProgram"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberPosition">Position:</label>
                <p id="memberPosition"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberBirthdate">Birthdate:</label>
                <p id="memberBirthdate"></p>
            </div>
            
            <div class="profile-info">
                <label for="memberAddress">Address:</label>
                <p id="memberAddress"></p>
            </div>
            
            <button class="submit-btn" id="closeProfileBtn">Close</button>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Member</h2>
            <form action="save_member.php" method="POST">
                <div class="form-group">
                    <label for="addName">Name:</label>
                    <input type="text" id="addName" name="members_name" required>
                </div>
                
                <div class="form-group">
                    <label for="addProgram">Program:</label>
                    <input type="text" id="addProgram" name="program" required>
                </div>
                
                <div class="form-group">
                    <label for="addPosition">Position:</label>
                    <input type="text" id="addPosition" name="position" required>
                </div>
                
                <div class="form-group">
                    <label for="addBirthdate">Birthdate:</label>
                    <input type="date" id="addBirthdate" name="birthdate">
                </div>
                
                <div class="form-group">
                    <label for="addAddress">Address:</label>
                    <input type="text" id="addAddress" name="address">
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Member Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Delete Member</h2>
            <form action="delete_member.php" method="POST">
                <div class="form-group">
                    <label for="deleteMember">Select Member:</label>
                    <select id="deleteMember" name="member_id" class="form-select" required>
                        <option value="">-- Select a member --</option>
                        <?php
                        include 'db_connect.php';
                        $sql = "SELECT member_id, members_name FROM members ORDER BY members_name";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["member_id"] . "'>" . $row["members_name"] . "</option>";
                            }
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                
                <div class="submit-container">
                    <button type="submit" class="submit-btn" style="background-color: #ff4444; color: white;">Delete Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Profile Modal
    var profileModal = document.getElementById("profileModal");
    var profileBtns = document.getElementsByClassName("borrow-btn");
    
    for (var i = 0; i < profileBtns.length; i++) {
        profileBtns[i].onclick = function() {
            var memberId = this.getAttribute("data-id");
            var memberName = this.getAttribute("data-name");
            var memberProgram = this.getAttribute("data-program");
            var memberPosition = this.getAttribute("data-position");
            var memberBirthdate = this.getAttribute("data-birthdate");
            var memberAddress = this.getAttribute("data-address");
            
            document.getElementById("memberName").textContent = memberName;
            document.getElementById("memberProgram").textContent = memberProgram;
            document.getElementById("memberPosition").textContent = memberPosition;
            document.getElementById("memberBirthdate").textContent = memberBirthdate || "Not available";
            document.getElementById("memberAddress").textContent = memberAddress || "Not available";
            
            profileModal.style.display = "flex";
        }
    }
    
    document.getElementById("closeProfileBtn").onclick = function() {
        profileModal.style.display = "none";
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
        if (event.target == profileModal) {
            profileModal.style.display = "none";
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