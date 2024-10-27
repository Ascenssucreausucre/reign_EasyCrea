<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;

class AuthMiddleware {
    public static function verifierAdmin() {
        if (!isset($_SESSION['admin'])) {
            HTTP::redirect('/');
        }
    }
}