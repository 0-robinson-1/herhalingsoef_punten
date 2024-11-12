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

// If the form is submitted, handle data insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['moduleID'], $_POST['persoonID'], $_POST['punt'])) {
        $moduleID = $_POST['moduleID'];
        $persoonID = $_POST['persoonID'];
        $punt = $_POST['punt'];

        $sql = "SELECT * FROM punten WHERE moduleID = ? AND persoonID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $moduleID, $persoonID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<p style='color: red;'>Error: This module-person combination already has a point.</p>";
        } else {
            $sql = "INSERT INTO punten (moduleID, persoonID, punt) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $moduleID, $persoonID, $punt);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>Point successfully added.</p>";
            } else {
                echo "<p style='color: red;'>Error inserting point: " . $stmt->error . "</p>";
            }
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error: Form data not received properly.</p>";
    }
}

// Fetch modules and personen for the dropdowns
$modulesResult = $conn->query("SELECT id, naam FROM modules");
if (!$modulesResult) {
    die("Error fetching modules: " . $conn->error);
}

$personenResult = $conn->query("SELECT id, familienaam, voornaam FROM personen");
if (!$personenResult) {
    die("Error fetching personen: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punten Invoeren</title>
</head>
<body>
    <h1>Voer punten in</h1>

    <form action="" method="POST">
        <label for="moduleID">Module:</label>
        <select name="moduleID" id="moduleID" required>
            <?php
            if ($modulesResult->num_rows > 0) {
                while ($row = $modulesResult->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['naam']) . "</option>";
                }
            } else {
                echo "<option value=''>Geen modules beschikbaar</option>";
            }
            ?>
        </select>

        <label for="persoonID">Persoon:</label>
        <select name="persoonID" id="persoonID" required>
            <?php
            if ($personenResult->num_rows > 0) {
                while ($row = $personenResult->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['voornaam'] . " " . $row['familienaam']) . "</option>";
                }
            } else {
                echo "<option value=''>Geen personen beschikbaar</option>";
            }
            ?>
        </select>

        <label for="punt">Punt:</label>
        <input type="number" name="punt" id="punt" min="0" max="100" required>

        <button type="submit">Invoeren</button>
    </form>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>