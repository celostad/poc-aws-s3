
# INSTALL
composer install

# CRIAR ARQUIVO DE CONFIGURAÇÃO 'config.php'

    <?php
    // AWS access info
    if (!defined('ACCESS_KEY')) define('ACCESS_KEY' , 'SUA CHAVE DE ACESSO AQUI!');
    if (!defined('SECRET_KEY')) define('SECRET_KEY' , 'SUA CHAVE SECRETA AQUI!');


    $region = 'REGIÃO AWS'; // EX: 'us-east-1'
    $bucket = 'SEU BUCKET AQUI';
    $prefix = '';
    $path = ''; // Can be empty '' or myfiles/




