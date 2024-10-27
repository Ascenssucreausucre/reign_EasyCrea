<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOStatement;

class CarteDeck extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'carte_deck';

    public function creer(array $datas): ?int
    {
        // Insérer l'association carte-deck dans la table `carte_deck`
        return $this->create($datas);
    }

    public function compterCartesParDeck($idDeck) {
        $sql = "SELECT COUNT(*) as nombre_cartes FROM carte_deck WHERE id_deck = :id_deck";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_deck' => $idDeck]);
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nombre_cartes'] ?? 0;
    }
}
