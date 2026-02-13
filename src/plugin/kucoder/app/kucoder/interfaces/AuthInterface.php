<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\interfaces;


interface AuthInterface
{
    public function login();

    public function logout();
}