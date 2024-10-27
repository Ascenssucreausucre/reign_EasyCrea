<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Admin;
use App\Controller\AuthMiddleware;

class AdminController extends Controller
{
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function afficherFormulaire()
    {
        AuthMiddleware::verifierAdmin();
        // Afficher la vue de formulaire
        return $this->display('admin/inscription.html.twig');
    }

    /**
     * Gestion du formulaire d'inscription
     */
    public function inscription()
{
    AuthMiddleware::verifierAdmin();
    // Vérifier si c'est une requête POST
    if ($this->isPostMethod()) {
        $email = $_POST['ad_mail_admin'] ?? null;
        $motDePasse = $_POST['mdp_admin'] ?? null;

        try {
            // Validation des champs
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("L'adresse email est invalide.");
            }

            if (strlen($motDePasse) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères.");
            }

            // Vérifier si l'email existe déjà
            if (Admin::getInstance()->emailExistant($email)) {
                throw new \Exception("Cet email est déjà utilisé.");
            }

            // Inscription du créateur
            $adminId = Admin::getInstance()->inscrire([
                'ad_mail_admin' => htmlspecialchars($email),
                'mdp_admin' => password_hash($motDePasse, PASSWORD_BCRYPT) // Hachage du mot de passe
            ]);

            if ($adminId) {
                // Succès de l'inscription, redirection ou affichage d'un message de succès
                return $this->display('admin/success.twig', ['message' => 'Inscription réussie !']);
            } else {
                throw new \Exception("Erreur lors de l'inscription.");
            }

        } catch (\Exception $e) {
            // Gestion des erreurs, affichage du formulaire avec message d'erreur
            return $this->display('admin/inscription.html.twig', ['error' => $e->getMessage()]);
        }
    }

    // Si ce n'est pas une requête POST, afficher le formulaire
    return $this->afficherFormulaire();
}
// Afficher le formulaire de connexion
public function afficherFormulaireConnexion()
{
    return $this->display('admin/connexion.html.twig');
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

            // Vérifier si l'utilisateur existe
            $createur = Admin::getInstance()->getByEmail($email);
            if (!$createur) {
                throw new \Exception("Identifiants invalides.");
            }

            // Vérifier le mot de passe (en supposant que tu utilises password_hash et password_verify)
            if (!password_verify($motDePasse, $createur['mdp_admin'])) {
                throw new \Exception("Identifiants invalides.");
            }
            // Connexion réussie
            // Ici, tu peux établir une session utilisateur ou tout autre traitement nécessaire
            $_SESSION['admin'] = $createur['id_administrateur']; // Exemples de stockage de données utilisateur dans la session
            return HTTP::redirect('/');
            

        } catch (\Exception $e) {
            // Gestion des erreurs
            // return $this->display('/createur/connexion.html.twig', ['message' => $e->getMessage(), 'success' => false]);
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    // Si ce n'est pas une requête POST, afficher le formulaire
    return $this->afficherFormulaireConnexion();
}

public function deconnexion()
{
    // Détruire la session ou supprimer les données utilisateur
    session_destroy();
    return $this->display('/carte', ['message' => 'Déconnexion réussie !', 'success' => true]);
}
}
