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

// Fetch persons for dropdown
$personsResult = $conn->query("SELECT id, voornaam, familienaam FROM personen");
if (!$personsResult) {
    die("Error fetching persons: " . $conn->error);
}

// Check if a person has been selected
$selectedPersonID = isset($_GET['persoonID']) ? (int)$_GET['persoonID'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personen Lijst</title>
</head>
<body>
    <h1>Personen Lijst</h1>

    <form method="GET" action="">
        <label for="persoonID">Select Person:</label>
        <select name="persoonID" id="persoonID" required>
            <?php
            while ($row = $personsResult->fetch_assoc()) {
                $selected = $selectedPersonID == $row['id'] ? "selected" : "";
                echo "<option value='" . htmlspecialchars($row['id']) . "' $selected>" . htmlspecialchars($row['voornaam'] . " " . $row['familienaam']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Show Results</button>
    </form>

    <?php
    // If a person is selected, display the points
    if ($selectedPersonID) {
        $sql = "SELECT modules.naam AS module, punten.punt
                FROM punten
                JOIN modules ON punten.moduleID = modules.id
                WHERE punten.persoonID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $selectedPersonID);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Results for Selected Person:</h2>";
        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Module</th>
                        <th>Grade (Punt)</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['module']) . "</td>
                        <td>" . htmlspecialchars($row['punt']) . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No grades found for this person.</p>";
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