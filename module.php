<?php

declare(strict_types=1);

class Module
{
    private int $id;
    private string $naam;
    private float $prijs;

    public function __construct(int $id, string $naam, float $prijs)
    {
        $this->id = $id;
        $this->naam = $naam;
        $this->prijs = $prijs;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNaam(): string
    {
        return $this->naam;
    }

    public function getPrijs(): float
    {
        return $this->prijs;
    }

    public function __toString(): string
    {
        return "Module ID: {$this->id}, Naam: {$this->naam}, Prijs: {$this->prijs}";
    }

    // Static method to fetch all modules from the database
    public static function getAllModules(mysqli $conn): array
    {
        $modules = [];
        $query = "SELECT id, naam, prijs FROM modules";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $modules[] = new Module((int)$row['id'], $row['naam'], (float)$row['prijs']);
            }
        }
        return $modules;
    }

    public static function getModuleById(mysqli $conn, int $id): ?Module
    {
        $query = "SELECT id, naam, prijs FROM modules WHERE id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
    
        return $row ? new Module((int)$row['id'], $row['naam'], (float)$row['prijs']) : null;
    }
}
?>