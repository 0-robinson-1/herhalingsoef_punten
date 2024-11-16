<?php

declare(strict_types=1);
require_once 'database.php';

class Punten
{
    private int $moduleID;
    private int $persoonID;
    private int $punt;

    public function __construct(int $moduleID, int $persoonID, int $punt)
    {
        $this->moduleID = $moduleID;
        $this->persoonID = $persoonID;
        $this->punt = $punt;
    }

    public function getModuleID(): int
    {
        return $this->moduleID;
    }

    public function getPersoonID(): int
    {
        return $this->persoonID;
    }

    public function getPunt(): int
    {
        return $this->punt;
    }

    public function saveToDatabase(): bool
    {
        global $conn;

        // Check if the record already exists
        $checkQuery = "SELECT * FROM punten WHERE moduleID = ? AND persoonID = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $this->moduleID, $this->persoonID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Error: A grade for this person and module has already been entered.";
            return false;
        }

        // If no existing record, insert the new grade
        $query = "INSERT INTO punten (moduleID, persoonID, punt) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $this->moduleID, $this->persoonID, $this->punt);

        return $stmt->execute();
    }

    public static function getGradesByModule(mysqli $conn, int $moduleID): array
{
    $query = "SELECT CONCAT(p.voornaam, ' ', p.familienaam) AS persoon, punten.punt 
              FROM personen p
              JOIN punten ON p.id = punten.persoonID
              WHERE punten.moduleID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $moduleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $grades;
}
}