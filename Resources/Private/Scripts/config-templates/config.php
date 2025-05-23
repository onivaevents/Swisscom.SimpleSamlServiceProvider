<?php

include(dirname(__FILE__) . '/../../../Packages/Libraries/simplesamlphp/simplesamlphp/config/config.php.dist');

$config['metadatadir'] = dirname(__FILE__) . '/../metadata/';
$config['loggingdir'] = dirname(__FILE__) . '/../../../Data/Logs/';
$config['cachedir'] = dirname(__FILE__) . '/../../../Data/Temporary/' . getenv('FLOW_CONTEXT') . '/Cache/SimpleSamlPhp/';
$config['datadir'] = dirname(__FILE__) . '/../../../Data/';

// TODO: Overwrite config here
