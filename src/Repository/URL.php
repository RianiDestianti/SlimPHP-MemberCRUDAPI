<?php

namespace App\Repository;

class URL {

    public function frontendUrl()
    {
        return $_ENV['FRONTEND_URL'];
    }

    public function adminURL()
    {
        return $_ENV['ADMIN_URL'];
    }

}