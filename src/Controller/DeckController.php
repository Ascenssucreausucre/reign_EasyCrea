<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Model\Deck;
use App\Model\Carte;
use App\Model\CarteDeck;
use App\Helper\HTTP;
use App\Controller\AuthMiddleware;

class DeckController extends Controller
{

    public function index()
    {
        // récupérer les informations sur les decks
        $decks = Deck::getInstance()->findAll();
        // dans les vues TWIG, on peut utiliser la variable decks
        $this->display('deck/deck.html.twig', compact('decks'));
    }

    public function afficherDecks() {
        $decks = Deck::getInstance()->findAll(); // Récupérer tous les decks
        $deckDetails = [];
    
        foreach ($decks as $deck) {
            // Compter le nombre de cartes pour chaque deck
            $nombreCartes = CarteDeck::getInstance()->compterCartesParDeck($deck['id_deck']);
            
            // Calculer la différence entre la date actuelle et la date de fin
            $dateFin = new \DateTime($deck['date_fin_deck']);
            $now = new \DateTime();
            $interval = $now->diff($dateFin);
            $estDateExpiree = ($now > $dateFin);
            
            // Déterminer si c'est dans moins de 24 heures et formater l'affichage
            if ($estDateExpiree) {
                $tempsRestant = "Deck expiré";
            } else {
                if ($interval->days > 0) {
                    $tempsRestant = $interval->days . " jour" . ($interval->days > 1 ? "s" : "");
                } elseif ($interval->h > 0) {
                    $tempsRestant = $interval->h . " heure" . ($interval->h > 1 ? "s" : "");
                    if ($interval->i > 0) {
                        $tempsRestant .= " et " . $interval->i . " minute" . ($interval->i > 1 ? "s" : "");
                    }
                } else {
                    $tempsRestant = $interval->i . " minute" . ($interval->i > 1 ? "s" : "");
                }
            }
    
            $deckDetails[] = [
                'deck' => $deck,
                'nombre_cartes' => $nombreCartes,
                'est_expire' => $estDateExpiree,
                'temps_restant' => $tempsRestant
            ];
        }
    
        return $this->display('deck/deck.html.twig', [
            'deckDetails' => $deckDetails
        ]);
    }
    

    public function create() {
        AuthMiddleware::verifierAdmin();
    
        if (isset($_SESSION['admin'])) {
            if ($this->isPostMethod()) {
                $titreDeck = $_POST['titre_deck'] ?? null;
                $dateDebut = $_POST['date_debut_deck'] ?? null;
                $dateFin = $_POST['date_fin_deck'] ?? null;
                $nbCartes = $_POST['nb_cartes'] ?? 1; // Le nombre de cartes est au moins 1 (celle qui sera ajoutée)
    
                // Champs pour la carte
                $texteCarte = $_POST['texte_carte'] ?? null;
    
                // Champs pour choix 1
                $choix1Text = $_POST['valeurs_choix1_text'] ?? null;
                $choix1Nb1 = $_POST['valeurs_choix1_nb1'] ?? null;
                $choix1Nb2 = $_POST['valeurs_choix1_nb2'] ?? null;
    
                // Champs pour choix 2
                $choix2Text = $_POST['valeurs_choix2_text'] ?? null;
                $choix2Nb1 = $_POST['valeurs_choix2_nb1'] ?? null;
                $choix2Nb2 = $_POST['valeurs_choix2_nb2'] ?? null;
    
                try {
                    // Validation des champs du deck
                    if (empty($titreDeck) || empty($dateDebut) || empty($dateFin) || empty($nbCartes)) {
                        throw new \Exception("Tous les champs pour le deck sont requis.");
                    }
    
                    // Validation des champs de la carte
                    if (empty($texteCarte) || empty($choix1Text) || empty($choix1Nb1) || empty($choix1Nb2) ||
                        empty($choix2Text) || empty($choix2Nb1) || empty($choix2Nb2)) {
                        throw new \Exception("Tous les champs pour la carte sont requis.");
                    }
    
                    // Créer les tableaux pour les choix
                    $choix1 = [
                        'text' => $choix1Text,
                        'number1' => intval($choix1Nb1),
                        'number2' => intval($choix1Nb2)
                    ];
    
                    $choix2 = [
                        'text' => $choix2Text,
                        'number1' => intval($choix2Nb1),
                        'number2' => intval($choix2Nb2)
                    ];
    
                    // Convertir les tableaux en JSON pour les stocker dans la base de données
                    $choix1Json = json_encode($choix1);
                    $choix2Json = json_encode($choix2);
    
                    // Insérer le deck dans la base de données avec l'ID de l'administrateur
                    $deckId = Deck::getInstance()->creer([
                        'titre_deck' => $titreDeck,
                        'date_debut_deck' => $dateDebut,
                        'date_fin_deck' => $dateFin,
                        'nb_cartes' => $nbCartes,
                        'id_administrateur' => $_SESSION['admin'] // ID admin de la session
                    ]);
    
                    if ($deckId) {
                        // Si le deck est créé, on insère la première carte
                        $carteId = Carte::getInstance()->creer([
                            'texte_carte' => $texteCarte,
                            'valeurs_choix1' => $choix1Json,
                            'valeurs_choix2' => $choix2Json,
                            'id_createur' => null, // Carte ajoutée par l'admin donc pas de créateur
                        ]);
    
                        // Associer la carte au deck dans la table carte_deck
                        if ($carteId) {
                            CarteDeck::getInstance()->creer([
                                'id_carte' => $carteId,
                                'id_deck' => $deckId
                            ]);
    
                            return HTTP::redirect('/');
                        } else {
                            throw new \Exception("Erreur lors de l'ajout de la carte.");
                        }
                    } else {
                        throw new \Exception("Erreur lors de la création du deck.");
                    }
    
                } catch (\Exception $e) {
                    return $this->display('/deck/create.html.twig', ['error' => $e->getMessage()]);
                }
            }
    
            // Si ce n'est pas une requête POST, rediriger vers la page de création du deck
            return HTTP::redirect('/');
        }
    }
    

    public function afficherForm(){
        return $this->display('deck/create.html.twig');
    }

    public function ajouterCarte($idDeck) {
        error_log("Données POST: " . print_r($_POST, true));
        $idDeck = intval($idDeck); // Convertit l'ID du deck en entier
        $verif = Deck::getInstance()->find($idDeck);
    
        // Récupérer les informations du deck, y compris nb_cartes
        $nbCartesMax = $verif['nb_cartes'];
    
        // Vérifiez si le deck existe
        if (!$verif) {
            return HTTP::redirect('/');
        }
    
        $dateFin = new \DateTime($verif['date_fin_deck']);
        $now = new \DateTime();
        $estDateExpiree = ($now > $dateFin);
    
        // Compter le nombre de cartes existantes dans le deck
        $nombreCartesExistantes = Carte::getInstance()->compterCartesPourDeck($idDeck);
        
        // Vérifiez si la date est expirée ou si le nombre de cartes dépasse le maximum autorisé
        if ($estDateExpiree || $nombreCartesExistantes >= $nbCartesMax) {
            return HTTP::redirect('/');
        } else {
            // Récupérer une carte aléatoire du deck
            $carteAleatoire = Carte::getInstance()->getCarteAleatoireDuDeck($idDeck);
    
            if ($this->isPostMethod()) {
                // Récupérer la carte soumise par le créateur
                $nouvelleCarte = $_POST['texte_carte'] ?? null;
    
                // Récupérer les valeurs pour Choix 1
                $valeursChoix1Text = $_POST['valeurs_choix1_text'] ?? null;
                $valeursChoix1Nb1 = $_POST['valeurs_choix1_nb1'] ?? null;
                $valeursChoix1Nb2 = $_POST['valeurs_choix1_nb2'] ?? null;
    
                // Récupérer les valeurs pour Choix 2
                $valeursChoix2Text = $_POST['valeurs_choix2_text'] ?? null;
                $valeursChoix2Nb1 = $_POST['valeurs_choix2_nb1'] ?? null;
                $valeursChoix2Nb2 = $_POST['valeurs_choix2_nb2'] ?? null;
    
                try {
                    // Validation des champs
                    if (empty($nouvelleCarte) || empty($valeursChoix1Text) || empty($valeursChoix1Nb1) || empty($valeursChoix1Nb2) ||
                        empty($valeursChoix2Text) || empty($valeursChoix2Nb1) || empty($valeursChoix2Nb2)) {
                        throw new \Exception("Tous les champs sont requis.");
                    }
    
                    // Vérifier si le créateur a déjà ajouté une carte à ce deck
                    if (Carte::getInstance()->carteExistantePourDeck($idDeck, $_SESSION['user']['id_createur'])) {
                        throw new \Exception("Vous avez déjà ajouté une carte à ce deck.");
                    }
    
                    // Créer les tableaux de valeurs pour chaque choix
                    $valeursChoix1 = [
                        'text' => $valeursChoix1Text,
                        'number1' => intval($valeursChoix1Nb1),
                        'number2' => intval($valeursChoix1Nb2)
                    ];
    
                    $valeursChoix2 = [
                        'text' => $valeursChoix2Text,
                        'number1' => intval($valeursChoix2Nb1),
                        'number2' => intval($valeursChoix2Nb2)
                    ];
    
                    // Convertir les tableaux en JSON pour stockage dans la base de données
                    $valeursChoix1Json = json_encode($valeursChoix1);
                    $valeursChoix2Json = json_encode($valeursChoix2);
    
                    // Insérer la carte dans la table `carte`
                    $idCarte = Carte::getInstance()->creer([
                        'texte_carte' => $nouvelleCarte,
                        'valeurs_choix1' => $valeursChoix1Json,
                        'valeurs_choix2' => $valeursChoix2Json,
                        'id_createur' => $_SESSION['user']['id_createur'] // ID du créateur connecté
                    ]);
    
                    // Associer la carte au deck dans la table `carte_deck`
                    Carte::getInstance()->associerCarteAuDeck($idCarte, $idDeck);
    
                    return $this->display('/createur/success.twig', ['message' => 'Carte ajoutée avec succès !']);
    
                } catch (\Exception $e) {
                    return $this->display('/deck/ajouter-carte.html.twig', [
                        'error' => $e->getMessage(),
                        'carteAleatoire' => $carteAleatoire,
                        'idDeck' => $idDeck
                    ]);
                }
            }
        }
    
        // Si ce n'est pas une requête POST, on affiche le formulaire
        return $this->display('/deck/ajouter-carte.html.twig', [
            'carteAleatoire' => $carteAleatoire,
            'idDeck' => $idDeck
        ]);
    }
    
    

}