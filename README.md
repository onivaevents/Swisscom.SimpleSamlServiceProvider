# Swisscom.SimpleSamlServiceProvider
[Neos Flow](https://flow.neos.io/) SAML authentication package containing a SAML service provider based on [SimpleSAMLphp](https://simplesamlphp.org).

## Installation

First, install this package via composer. It will add [simplesamlphp/simplesamlphp](https://github.com/simplesamlphp/simplesamlphp) as dependency.

Via post update and install scripts composer will also do the following to make simplesamlphp work within the Flow app:

1. Create symlink `Web/simplesamlphp -> ../Packages/Libraries/simplesamlphp/simplesamlphp/www`
2. Patch the Apache .htaccess configuration to not rewrite simplesamlphp and set the `SIMPLESAMLPHP_CONFIG_DIR` environment var.
3. Copy the example config structure to the `SIMPLESAMLPHP_CONFIG_DIR` under `Configuration/SimpleSamlPhp/`

    
## Sample setup

As a sample and for test purposes, the serverless SAML identity provider [Samling](https://capriza.github.io/samling/samling.html) 
can be configured most basically as follows:
    
    mkdir Configuration/SimpleSamlPhp/metadata
    cp Packages/Libraries/simplesamlphp/simplesamlphp/metadata-templates/saml20-idp-remote.php Configuration/SimpleSamlPhp/metadata/
    
Add the following metadata config to `Configuration/SimpleSamlPhp/metadata/saml20-idp-remote.php`:

    $metadata['https://capriza.github.io/samling/samling.html'] = array(
        /* Configuration options for the first IdP. */
        'SingleSignOnService' => 'https://capriza.github.io/samling/samling.html',
        'certificate' => 'samling.pub'
    );

The certificate is copied from `https://capriza.github.io/samling/samling.html` to the cert folder (see `certdir` in config.php). 

## Integration

Have a look into the package's `Configuration/Settings.yaml` and configure the entry points if needed.

The following setting has to match the authentication source configured in the SimpleSAMLphp `authsources.php` config file:

    Swisscom:
      SimpleSamlServiceProvider:
        authSource: 'default-sp'