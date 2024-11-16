<?php

declare(strict_types=1);

class Persoon
{
    private int $id;
    private string $familienaam;
    private string $voornaam;
    private DateTime $geboortedatum;

    public function __construct(int $id, string $familienaam, string $voornaam, DateTime $geboortedatum)
    {
        $this->id = $id;
        $this->familienaam = $familienaam;
        $this->voornaam = $voornaam;
        $this->geboortedatum = $geboortedatum;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFamilienaam(): string
    {
        return $this->familienaam;
    }

    public function getVoornaam(): string
    {
        return $this->voornaam;
    }

    // Static method to fetch all persons from the database
    public static function getAllPersonen(mysqli $conn): array
    {
        $personen = [];
        $query = "SELECT id, familienaam, voornaam, geboortedatum FROM personen";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $personen[] = new Persoon((int)$row['id'], $row['familienaam'], $row['voornaam'], new DateTime($row['geboortedatum']));
            }
        }
        return $personen;
    }
}