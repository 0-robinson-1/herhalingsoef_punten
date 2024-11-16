<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cursusphp";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch modules for dropdown
$modulesResult = $conn->query("SELECT id, naam FROM modules");
if (!$modulesResult) {
    die("Error fetching modules: " . $conn->error);
}

// Check if a module has been selected
$selectedModuleID = isset($_GET['moduleID']) ? (int)$_GET['moduleID'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules Lijst</title>
</head>
<body>
    <h1>Modules Lijst</h1>

    <form method="GET" action="">
        <label for="moduleID">Select Module:</label>
        <select name="moduleID" id="moduleID" required>
            <?php
            while ($row = $modulesResult->fetch_assoc()) {
                $selected = $selectedModuleID == $row['id'] ? "selected" : "";
                echo "<option value='" . htmlspecialchars($row['id']) . "' $selected>" . htmlspecialchars($row['naam']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Show Results</button>
    </form>

    <?php
    // If a module is selected, display the points
    if ($selectedModuleID) {
        $sql = "SELECT personen.voornaam, personen.familienaam, punten.punt
                FROM punten
                JOIN personen ON punten.persoonID = personen.id
                WHERE punten.moduleID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $selectedModuleID);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Results for Selected Module:</h2>";
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Name</th>
                        <th>Grade (Punt)</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['voornaam'] . " " . $row['familienaam']) . "</td>
                        <td>" . htmlspecialchars($row['punt']) . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No grades found for this module.</p>";
        }
        $stmt->close();
    }

    $conn->close();
    ?>

    <!-- Back to Punten Toevoegen Button -->
    <div style="margin-top: 20px;">
        <button onclick="window.location.href='punten_toevoegen.php';">Back to Punten Toevoegen</button>
    </div>
</body>
</html>