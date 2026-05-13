<?php
// constantes de configuração da aplicação
define( 'APP_NAME', 'NOME_DA_APLICAÇÃO' ); // Nome da aplicação
define( 'APP_ENV', 'development' ); // Mudat para 'production' em ambiente de produção
define( 'ALLOWED_ORIGINS', [ // Adicionar origens permitidas para CORS
    'http://localhost:8000',
    'http://localhost:8080', // Exemplo de frontend de origens adicionais permitidas
    'http://127.0.0.1:8000', 
    'http://127.0.0.1:8080'  // Exemplo de frontend de origens adicionais permitidas
]);

// constantes de configuração do banco de dados
define( 'DB_HOST', 'ENDEREÇO_DO_HOST'); // Em servidor local utilize '127.0.0.1'
define( 'DB_PORT', 'PORTA_DO_BANCO_MYSQL');
define( 'DB_NAME', 'NOME_DO_BANCO_CRIADO' );
define( 'DB_USER', 'USUARIO_DO_BANCO' );
define( 'DB_PASSWORD', 'SENHA_DO_BANCO' );