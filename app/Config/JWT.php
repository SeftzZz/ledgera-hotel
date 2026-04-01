<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    public string $secret = '';
    public string $algo   = 'HS256';

    public int $accessTokenTTL  = 86400; // 24 jam
    public int $refreshTokenTTL = 604800; // 7 hari

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
    }
}