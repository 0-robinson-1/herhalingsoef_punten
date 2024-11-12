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

// Fetch personen for dropdown
$personenResult = $conn->query("SELECT id, voornaam, familienaam FROM personen");
if (!$personenResult) {
    die("Error fetching personen: " . $conn->error);
}

// Check if a person has been selected
$selectedPersoonID = isset($_GET['persoonID']) ? $_GET['persoonID'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punten per Persoon</title>
</head>
<body>
    <h1>Punten per Persoon</h1>

    <form method="GET" action="">
        <label for="persoonID">Kies een persoon:</label>
        <select name="persoonID" id="persoonID" required>
            <?php
            while ($row = $personenResult->fetch_assoc()) {
                $selected = $selectedPersoonID == $row['id'] ? "selected" : "";
                echo "<option value='" . htmlspecialchars($row['id']) . "' $selected>" . htmlspecialchars($row['voornaam'] . " " . $row['familienaam']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Bekijk punten</button>
    </form>

    <?php
    // If a person is selected, display the points
    if ($selectedPersoonID) {
        $sql = "SELECT modules.naam, punten.punt
                FROM punten
                JOIN modules ON punten.moduleID = modules.id
                WHERE punten.persoonID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $selectedPersoonID);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<h2>Resultaten voor gekozen persoon:</h2>";
        if ($result->num_rows > 0) {
            echo "<table border='1'><tr><th>Module</th><th>Punt</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['naam']) . "</td><td>" . htmlspecialchars($row['punt']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Geen punten gevonden voor deze persoon.</p>";
        }
        $stmt->close();
    }

    $conn->close();
    ?>
</body>
</html>