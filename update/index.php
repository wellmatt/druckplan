<?php

error_reporting(-1);
ini_set('display_errors', 1);

require_once('php-github-updater.class.php');
$user = 'schealex';
$repository = 'contilas';
$localVersion = 'v3.2.001-alpha';

$updater = new PhpGithubUpdater($user, $repository);
if( !$updater->isUpToDate($localVersion) ) {
    $updater->installLatestVersion(
        '/var/www/vhosts/mein-druckplan.de/contilas2.mein-druckplan.de/php-github-updater/app',
        '/var/www/vhosts/mein-druckplan.de/contilas2.mein-druckplan.de/php-github-updater/temp'
    );
    echo "update successful!";
} else {
	echo "up to date!";
}
?>