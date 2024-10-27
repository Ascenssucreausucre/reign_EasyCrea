<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Carte;

class CarteController extends Controller
{
    public function index()
    {
        // récupérer les informations sur les cartes
        $cartes = Carte::getInstance()->findAll();
        // dans les vues TWIG, on peut utiliser la variable cartes
        $this->display('carte/index.html.twig', compact('carte'));
    }
}
