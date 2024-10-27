<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [


    // afficher le formulaire d'ajout d'un nouvel avatar
    ['GET', '/carte/ajouter', 'carte@create'],
    // enregistrer les données soumises d'un nouvel avatar
    ['POST', '/carte/ajouter', 'carte@create'],

    // afficher le formulaire d'édition un avatar existant
    ['GET', '/avatars/éditer/{id}', 'avatar@edit'],

    // enregistrer les modifications sur un avatar existant
    ['POST', '/avatars/éditer/{id}', 'avatar@edit'],

    // effacer un avatar
    ['GET', '/avatars/effacer/{id:\d+}', 'avatar@delete'],

    // afficher les étudiants
    ['GET', '/carte', 'carte@index'],
    ['GET', '/', 'Deck@afficherDecks'],

    // afficher les parcours
    ['GET', '/deck', 'Deck@afficherDecks'],

    ['GET', '/createur/inscription', 'Createur@afficherFormulaire'],
    ['POST', '/createur/inscription', 'Createur@inscription'],

    ['GET', '/createur/connexion', 'Createur@afficherFormulaireConnexion'],
    ['POST', '/createur/connexion', 'Createur@connexion'],
    ['GET', '/createur/deconnexion', 'Createur@deconnexion'],

    ['GET', '/admin/inscription', 'Admin@afficherFormulaire'],
    ['POST', '/admin/inscription', 'Admin@inscription'],

    ['GET', '/admin/connexion', 'Admin@afficherFormulaireConnexion'],
    ['POST', '/admin/connexion', 'Admin@connexion'],
    ['GET', '/admin/deconnexion', 'Admin@deconnexion'],

    ['GET', '/deck/create', 'Deck@afficherForm'],
    ['POST', '/deck/create', 'Deck@create'],

    ['GET', '/deck/{idDeck:\d+}/ajouter-carte', 'Deck@ajouterCarte'],
    ['POST', '/deck/{idDeck:\d+}/ajouter-carte', 'Deck@ajouterCarte'],

    ['GET', '/createur/profil', 'Createur@profil'],


];
