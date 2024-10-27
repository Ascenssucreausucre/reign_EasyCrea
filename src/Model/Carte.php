<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use PDOStatement;

class Carte extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'Carte';

    public function getCarteAleatoireDuDeck(int $idDeck): ?array {
        // Requête pour obtenir une carte aléatoire du deck
        $query = "SELECT carte.*
              FROM carte_deck
              JOIN carte ON carte_deck.id_carte = carte.id_carte
              WHERE carte_deck.id_deck = :idDeck
              ORDER BY RAND()
              LIMIT 1";

    // Préparer et exécuter la requête
    $stmt = $this->db->prepare($query);
    $stmt->execute(['idDeck' => $idDeck]);

    // Retourner le résultat
    $carte = $stmt->fetch();

    if ($carte) {
        // Si la carte est trouvée, décoder les champs JSON
        if (isset($carte['valeurs_choix1'])) {
            $carte['valeurs_choix1'] = json_decode($carte['valeurs_choix1'], true);
        }

        if (isset($carte['valeurs_choix2'])) {
            $carte['valeurs_choix2'] = json_decode($carte['valeurs_choix2'], true);
        }
    }

    return $carte;
    }

    public function creer(array $datas): ?int{
        return $this->create($datas);
    }
    
    public function associerCarteAuDeck($idCarte, $idDeck) {
        $sql = "INSERT INTO carte_deck (id_carte, id_deck) VALUES (:id_carte, :id_deck)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_carte' => $idCarte,
            ':id_deck' => $idDeck
        ]);
    }

    public function carteExistantePourDeck($idDeck, $idCreateur) {
        $sql = "SELECT COUNT(*) FROM carte
                INNER JOIN carte_deck ON carte.id_carte = carte_deck.id_carte
                WHERE carte_deck.id_deck = :id_deck AND carte.id_createur = :id_createur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_deck' => $idDeck,
            ':id_createur' => $idCreateur
        ]);
        
        return $stmt->fetchColumn() > 0; // Retourne vrai si la carte existe
    }
    
    public function getCartesParCreateur($idCreateur) {
        $sql = "SELECT * FROM carte WHERE id_createur = :id_createur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_createur' => $idCreateur]);
    
        // Récupérer toutes les cartes
        $cartes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Parcourir les cartes et décoder les champs JSON si nécessaire
        foreach ($cartes as &$carte) {
            if (isset($carte['valeurs_choix1'])) {
                $carte['valeurs_choix1'] = json_decode($carte['valeurs_choix1'], true);
            }
    
            if (isset($carte['valeurs_choix2'])) {
                $carte['valeurs_choix2'] = json_decode($carte['valeurs_choix2'], true);
            }
        }
    
        return $cartes; // Retourne toutes les cartes avec les valeurs JSON décodées
    }
    public function compterCartesPourDeck(int $idDeck): int {
        $sql = "SELECT COUNT(*) FROM carte_deck WHERE id_deck = :id_deck";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_deck' => $idDeck]);
        
        // Retourner le nombre de cartes
        return (int) $stmt->fetchColumn();
    }
    
    
}
