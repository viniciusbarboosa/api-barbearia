<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie','storage/*'], // Rotas que terão CORS habilitado
    'allowed_methods' => ['*'], // Métodos HTTP permitidos
    'allowed_origins' => ['http://localhost:5173', 'https://vcommits.online'], // Origens permitidas
    'allowed_origins_patterns' => [], // Padrões de origens permitidas
    'allowed_headers' => ['*'], // Headers permitidos
    'exposed_headers' => [], // Headers expostos
    'max_age' => 0, // Tempo de cache do CORS
    'supports_credentials' => true, // Habilitar suporte a credenciais
];
