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

        <!-- Feedback Messages -->
        <?php
        if(isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="close" onclick="this.parentElement.style.display=\'none\';">&times;</button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        
        if(isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="close" onclick="this.parentElement.style.display=\'none\';">&times;</button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Card Section -->
        <div class="card-container">
            <?php
            include 'db_connect.php';
            // Fetch members from database
            $sql = "SELECT * FROM members";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine the image to display
                    $imagePath = !empty($row["image_path"]) ? $row["image_path"] : 'default image.jpg';
                    
                    echo "<div class='card'>";
                    echo "<img src='" . $imagePath . "' alt='Member'>";
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

    <script>
    // Get the modal
    var modal = document.getElementById("profileModal");

    // Get all buttons that should open the modal
    var btns = document.getElementsByClassName("borrow-btn");

    // When the user clicks on a button, open the modal with member data
    for (var i = 0; i < btns.length; i++) {
        btns[i].onclick = function() {
            var memberId = this.getAttribute("data-id");
            var memberName = this.getAttribute("data-name");
            var memberProgram = this.getAttribute("data-program");
            var memberPosition = this.getAttribute("data-position");
            var memberBirthdate = this.getAttribute("data-birthdate");
            var memberAddress = this.getAttribute("data-address");
            
            // Set the values in the modal
            document.getElementById("memberName").textContent = memberName;
            document.getElementById("memberProgram").textContent = memberProgram;
            document.getElementById("memberPosition").textContent = memberPosition;
            document.getElementById("memberBirthdate").textContent = memberBirthdate || "Not available";
            document.getElementById("memberAddress").textContent = memberAddress || "Not available";
            
            modal.style.display = "flex"; // Use flex to center the modal
        }
    }

    // Close button functionality
    document.getElementById("closeProfileBtn").onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal content, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>