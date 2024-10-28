<?php

namespace App\Model;

use PDO;
use PDOStatement;

class Admin extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'administrateur';

    /**
     * Inscription d'un créateur
     *
     * @param array $datas Les données du créateur à insérer (nom, email, mot de passe, etc.)
     * @return int|null L'identifiant du créateur créé ou null en cas d'échec
     */
    public function inscrire(array $datas): ?int
    {
        // Utilise la méthode create() de la classe parent (Model) pour insérer les données
        return $this->create($datas);
    }
    
    public function connecter($email, $motDePasse)
    {
        // Préparez la requête pour trouver l'utilisateur par email
        $createur = $this->getByEmail($email);

    
        // Vérifiez si l'utilisateur existe et si le mot de passe correspond
        if ($createur && password_verify($motDePasse, $createur['mdp_admin'])) {
            return $createur; // Connexion réussie
        }
    
        return false; // Identifiants invalides
    }
    
    /**
     * Vérifie si un email existe déjà dans la base de données
     *
     * @param string $email
     * @return bool
     */
    public function emailExistant(string $email): bool
    {
        // Utilise findOneBy pour vérifier si un enregistrement existe avec cet email
        return $this->findOneBy(['ad_mail_admin' => $email]) !== null;
    }
    public function getByEmail(string $email)
{
    // Ici, tu devras effectuer une requête pour récupérer l'utilisateur par e-mail.
    $query = "SELECT * FROM administrateur WHERE ad_mail_admin = :email";
    $stmt = $this->db->prepare($query);
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}
