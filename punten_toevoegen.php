<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'database.php';
require_once 'Module.php';
require_once 'Persoon.php';
require_once 'Punten.php';

// Test database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch modules and persons for dropdowns
$modules = Module::getAllModules($conn);
$personen = Persoon::getAllPersonen($conn);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $moduleID = (int)$_POST['moduleID'];
    $persoonID = (int)$_POST['persoonID'];
    $punt = (int)$_POST['punt'];

    $punten = new Punten($moduleID, $persoonID, $punt);
    if ($punten->saveToDatabase()) {
        $message = "Grade successfully added!";
    } else {
        $message = "Error: Grade already exists for this person and module.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punten toevoegen</title>
</head>
<body>
    <h1>Punten toevoegen</h1>

    <!-- Form for adding grades -->
    <form action="punten_toevoegen.php" method="post">
        <label for="persoonID">Select Person:</label>
        <select name="persoonID" id="persoonID" required>
            <?php foreach ($personen as $persoon): ?>
                <option value="<?= $persoon->getId() ?>">
                    <?= htmlspecialchars($persoon->getVoornaam() . ' ' . $persoon->getFamilienaam()) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="moduleID">Select Module:</label>
        <select name="moduleID" id="moduleID" required>
            <?php foreach ($modules as $module): ?>
                <option value="<?= $module->getId() ?>">
                    <?= htmlspecialchars($module->getNaam()) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="punt">Grade (Punt):</label>
        <input type="number" name="punt" id="punt" min="0" max="100" required>

        <button type="submit">Add Grade</button>
    </form>

    <!-- Display message if set -->
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Navigation Buttons -->
    <div style="margin-top: 20px;">
        <button onclick="window.location.href='modules_lijst.php';">Go to Modules List</button>
        <button onclick="window.location.href='personen_lijst.php';">Go to Persons List</button>
    </div>
</body>
</html>