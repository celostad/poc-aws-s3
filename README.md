
# INSTALL
composer install

# CRIAR ARQUIVO DE CONFIGURAÇÃO 'config.php'

    <?php
    define('ACCESS_KEY' , 'SUA CHAVE DE ACESSO AQUI!');
    define('SECRET_KEY' , 'SUA CHAVE SECRETA AQUI!');    
    
    $region = 'REGIÃO AWS'; // EX: 'us-east-1'
    $bucket = 'SEU BUCKET AQUI';
    $prefix = '';
    $path = ''; // Can be empty '' or myfiles/
    
    // AWS access info
    if (!defined('awsAccessKey')) define('awsAccessKey', ACCESS_KEY);
    if (!defined('awsSecretKey')) define('awsSecretKey', SECRET_KEY);
    
    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => $region,
        'credentials' => array(
            'key' => ACCESS_KEY,
            'secret' => SECRET_KEY
        )
    ]);




