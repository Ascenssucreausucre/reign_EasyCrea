<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [

    ['GET', '/carte/ajouter', 'carte@create'],
    ['POST', '/carte/ajouter', 'carte@create'],
    ['GET', '/carte', 'carte@index'],

    ['GET', '/', 'Deck@afficherDecks'],
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
