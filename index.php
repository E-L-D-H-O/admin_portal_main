<?php
require_once('dbinit.php');

// Variables to hold form data and error messages
$SmartphoneID = $SmartphoneName = $Description = $QuantityAvailable = $Price = $LaunchDate = "";
$errors = [];
$success_message = "";
$update_mode = false;

// Handle form submissions for Insert and Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST["SmartphoneName"])) {
        $errors['SmartphoneName'] = "Smartphone Name is required.";
    } else {
        $SmartphoneName = htmlspecialchars(trim($_POST["SmartphoneName"]));
    }

    if (empty($_POST["Description"])) {
        $errors['Description'] = "Description is required.";
    } else {
        $Description = htmlspecialchars(trim($_POST["Description"]));
    }

    if (empty($_POST["QuantityAvailable"]) || !is_numeric($_POST["QuantityAvailable"])) {
        $errors['QuantityAvailable'] = "Quantity must be a valid number.";
    } else {
        $QuantityAvailable = (int)$_POST["QuantityAvailable"];
    }

    if (empty($_POST["Price"]) || !is_numeric($_POST["Price"])) {
        $errors['Price'] = "Price must be a valid decimal number.";
    } else {
        $Price = (float)$_POST["Price"];
    }

    if (empty($_POST["LaunchDate"])) {
        $errors['LaunchDate'] = "Launch Date is required.";
    } else {
        $LaunchDate = $_POST["LaunchDate"];
    }

    // If no errors, proceed with insert or update
    if (empty($errors)) {
        if (isset($_POST['SmartphoneID']) && !empty($_POST['SmartphoneID'])) {
            // Update existing record
            $stmt = $conn->prepare("UPDATE smartphones SET SmartphoneName=?, Description=?, QuantityAvailable=?, Price=?, LaunchDate=? WHERE SmartphoneID=?");
            $stmt->bind_param("ssidsi", $SmartphoneName, $Description, $QuantityAvailable, $Price, $LaunchDate, $_POST['SmartphoneID']);
            if ($stmt->execute()) {
                $success_message = urlencode("Smartphone record updated successfully!");
                header("Location: index.php?success_message=$success_message");
                exit();
            } else {
                $errors[] = "Error updating record: " . $stmt->error;
            }
        } else {
            // Insert a new record
            $stmt = $conn->prepare("INSERT INTO smartphones (SmartphoneName, Description, QuantityAvailable, Price, LaunchDate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssids", $SmartphoneName, $Description, $QuantityAvailable, $Price, $LaunchDate);
            if ($stmt->execute()) {
                $success_message = urlencode("New smartphone added successfully!");
                header("Location: index.php?success_message=$success_message");
                exit();
            } else {
                $errors[] = "Error adding smartphone: " . $stmt->error;
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $SmartphoneID = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM smartphones WHERE SmartphoneID = ?");
    $stmt->bind_param("i", $SmartphoneID);
    if ($stmt->execute()) {
        $success_message = urlencode("Smartphone record deleted successfully!");
        header("Location: index.php?success_message=$success_message");
        exit();
    } else {
        $errors[] = "Error deleting record: " . $stmt->error;
    }
}

// Handle Edit (Populate form with existing data)
if (isset($_GET['edit'])) {
    $SmartphoneID = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM smartphones WHERE SmartphoneID = ?");
    $stmt->bind_param("i", $SmartphoneID);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    $row = $result_edit->fetch_assoc();
    if ($row) {
        $SmartphoneID = $row['SmartphoneID'];
        $SmartphoneName = $row['SmartphoneName'];
        $Description = $row['Description'];
        $QuantityAvailable = $row['QuantityAvailable'];
        $Price = $row['Price'];
        $LaunchDate = $row['LaunchDate'];
        $update_mode = true;
    }
}

// Fetch all smartphones to display in the table
$sql = "SELECT * FROM smartphones";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smartphones Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="text-center">ADMIN PANEL</h1>
        <hr>
        <H2>MANAGE SMARTPHONES</H2>

        <!-- Add/Edit Form -->
        <form action="" method="POST">
            <input type="hidden" name="SmartphoneID" value="<?php echo isset($SmartphoneID) ? $SmartphoneID : ''; ?>">

            <div class="mb-3">
                <label for="SmartphoneName" class="form-label">Smartphone Name</label>
                <input type="text" class="form-control" id="SmartphoneName" name="SmartphoneName" value="<?php echo $SmartphoneName; ?>">
                <?php if (isset($errors['SmartphoneName'])): ?>
                    <div class="text-danger"><?php echo $errors['SmartphoneName']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="Description" class="form-label">Description</label>
                <textarea class="form-control" id="Description" name="Description" rows="3"><?php echo $Description; ?></textarea>
                <?php if (isset($errors['Description'])): ?>
                    <div class="text-danger"><?php echo $errors['Description']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="QuantityAvailable" class="form-label">Quantity Available</label>
                <input type="number" class="form-control" id="QuantityAvailable" name="QuantityAvailable" value="<?php echo $QuantityAvailable; ?>">
                <?php if (isset($errors['QuantityAvailable'])): ?>
                    <div class="text-danger"><?php echo $errors['QuantityAvailable']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="Price" class="form-label">Price</label>
                <input type="number" class="form-control" id="Price" name="Price" step="0.01" value="<?php echo $Price; ?>">
                <?php if (isset($errors['Price'])): ?>
                    <div class="text-danger"><?php echo $errors['Price']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="LaunchDate" class="form-label">Launch Date</label>
                <input type="date" class="form-control" id="LaunchDate" name="LaunchDate" value="<?php echo $LaunchDate; ?>">
                <?php if (isset($errors['LaunchDate'])): ?>
                    <div class="text-danger"><?php echo $errors['LaunchDate']; ?></div>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-dark">
                    <?php echo $update_mode ? 'Update Smartphone' : 'Insert Smartphone'; ?>
                </button>
            </div>
        </form>

        <!-- Success and Error Messages -->
        <?php if (isset($_GET['success_message'])): ?>
            <div class="alert alert-success" id="successMessage">
                <?php echo htmlspecialchars($_GET['success_message']); ?>
            </div>
            <script>
                // Hide the success message after 3 seconds
                setTimeout(function() {
                    document.getElementById('successMessage').style.display = 'none';
                    window.history.replaceState(null, null, window.location.pathname);
                }, 2000);
            </script>
        <?php endif; ?>

        <hr>

        <!-- Display Table of Smartphones -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Launch Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['SmartphoneID']; ?></td>
                        <td><?php echo $row['SmartphoneName']; ?></td>
                        <td><?php echo $row['Description']; ?></td>
                        <td><?php echo $row['QuantityAvailable']; ?></td>
                        <td><?php echo $row['Price']; ?></td>
                        <td><?php echo $row['LaunchDate']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['SmartphoneID']; ?>" class="btn btn-secondary btn-sm">Update</a>
                            <a href="?delete=<?php echo $row['SmartphoneID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
