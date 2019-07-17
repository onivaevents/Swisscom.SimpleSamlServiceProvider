<?php

include(dirname(__FILE__) . '/../../../Packages/Libraries/simplesamlphp/simplesamlphp/config-templates/config.php');

$config['metadatadir'] = dirname(__FILE__) . '/../metadata/';
$config['loggingdir'] = dirname(__FILE__) . '/../../../Data/Logs/';
$config['datadir'] = dirname(__FILE__) . '/../../../Data/';

// TODO: Overwrite config here