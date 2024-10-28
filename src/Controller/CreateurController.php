<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Createur;
use App\Model\Carte;
use App\Model\Admin;

class CreateurController extends Controller
{
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function afficherFormulaire()
    {
        // Afficher la vue de formulaire
        return $this->display('createur/inscription.html.twig');
    }

    /**
     * Gestion du formulaire d'inscription
     */
    public function inscription()
    {
    // Vérifier si c'est une requête POST
    if ($this->isPostMethod()) {
        $nom = $_POST['nom_createur'] ?? null;
        $email = $_POST['ad_mail_createur'] ?? null;
        $motDePasse = $_POST['mdp_createur'] ?? null;
        $genre = $_POST['genre'] ?? null;
        $ddn = $_POST['ddn'] ?? null;

        try {
            // Validation des champs
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("L'adresse email est invalide.");
            }

            if (strlen($motDePasse) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères.");
            }

            // Vérifier si l'email existe déjà
            if (Createur::getInstance()->emailExistant($email)) {
                throw new \Exception("Cet email est déjà utilisé.");
            }

            // Inscription du créateur
            $createurId = Createur::getInstance()->inscrire([
                'nom_createur' => htmlspecialchars($nom),
                'ad_mail_createur' => htmlspecialchars($email),
                'mdp_createur' => password_hash($motDePasse, PASSWORD_BCRYPT), // Hachage du mot de passe
                'genre' => htmlspecialchars($genre),
                'ddn' => htmlspecialchars($ddn)
            ]);

            if ($createurId) {
                // Succès de l'inscription, redirection ou affichage d'un message de succès
                return $this->display('createur/success.twig', ['message' => 'Inscription réussie !']);
            } else {
                throw new \Exception("Erreur lors de l'inscription.");
            }

        } catch (\Exception $e) {
            // Gestion des erreurs, affichage du formulaire avec message d'erreur
            return $this->display('createur/inscription.html.twig', ['error' => $e->getMessage()]);
        }
    }

    // Si ce n'est pas une requête POST, afficher le formulaire
    return $this->afficherFormulaire();
    }
    // Afficher le formulaire de connexion
    public function afficherFormulaireConnexion()
    {
        return $this->display('createur/connexion.html.twig');
    }

    // Gérer la connexion
    public function connexion()
{
    // Vérifier si c'est une requête POST
    if ($this->isPostMethod()) {
        $email = $_POST['email'] ?? null;
        $motDePasse = $_POST['passwd'] ?? null;

        try {
            // Validation des champs
            if (empty($email) || empty($motDePasse)) {
                throw new \Exception("Tous les champs sont requis.");
            }

            // Vérifier si l'utilisateur est un créateur
            $createur = Createur::getInstance()->getByEmail($email);
            if ($createur) {
                // Vérifier le mot de passe pour le créateur
                if (!password_verify($motDePasse, $createur['mdp_createur'])) {
                    throw new \Exception("Identifiants invalides.");
                }

                // Connexion réussie pour le créateur
                $_SESSION['user'] = [
                    'id_createur' => $createur['id_createur'],
                    'nom_createur' => $createur['nom_createur']
                ];
                return HTTP::redirect('/');
            }

            // Si aucun créateur n'est trouvé, vérifier s'il s'agit d'un administrateur
            $admin = Admin::getInstance()->getByEmail($email);
            if ($admin) {
                // Vérifier le mot de passe pour l'administrateur
                if (!password_verify($motDePasse, $admin['mdp_admin'])) {
                    throw new \Exception("Identifiants invalides.");
                }

                // Connexion réussie pour l'administrateur
                $_SESSION['admin'] = $admin['id_administrateur']; // Remarque : session basée sur votre code fourni
                return HTTP::redirect('/');
            }

            // Si aucun utilisateur n'est trouvé, lancer une exception
            throw new \Exception("Identifiants invalides.");

        } catch (\Exception $e) {
            // Gestion des erreurs
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    // Si ce n'est pas une requête POST, afficher le formulaire de connexion
    return $this->afficherFormulaireConnexion();
}



    public function deconnexion()
    {
        // Détruire la session ou supprimer les données utilisateur
        session_destroy();
        return HTTP::redirect('/');
    }

    public function profil() {
        // Récupérer l'ID du créateur connecté
        $idCreateur = $_SESSION['user']['id_createur'];

        // Récupérer les cartes créées par l'utilisateur
        $cartes = Carte::getInstance()->getCartesParCreateur($idCreateur);

        // Afficher la vue du profil
        return $this->display('/createur/profil.html.twig', [
            'cartes' => $cartes,
            'username' => $_SESSION['user']['nom_createur']
        ]);
    }

}
