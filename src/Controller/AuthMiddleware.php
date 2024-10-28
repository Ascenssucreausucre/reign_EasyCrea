<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;

class AuthMiddleware {
    public static function verifierAdmin() {
        
    var_dump($_SESSION);
        if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
            HTTP::redirect('/');
        }
    }
}