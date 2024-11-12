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

// Fetch modules
$modulesResult = $conn->query("SELECT id, naam FROM modules");
if (!$modulesResult) {
    die("Error fetching modules: " . $conn->error);
}

// Fetch personen
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

    <form action="punten.php" method="POST">
        <label for="moduleID">Module:</label>
        <select name="moduleID" id="moduleID">
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
        <select name="persoonID" id="persoonID">
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