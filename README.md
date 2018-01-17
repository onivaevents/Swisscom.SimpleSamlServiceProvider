# Swisscom.SimpleSamlServiceProvider
[Neos Flow](https://flow.neos.io/) SAML authentication package containing a SAML service provider based on [SimpleSAMLphp](https://simplesamlphp.org).

## Installation

First, install this package via composer. It will add [simplesamlphp/simplesamlphp](https://github.com/simplesamlphp/simplesamlphp) as dependency.

Next, to get SimpleSAMLphp configured some manual work is needed. It is currently not handled by this package. 

### Apache

Adjust your configuration file of the virtual host:

        SetEnv SIMPLESAMLPHP_CONFIG_DIR /var/neos-flow-project/Configuration/SimpleSamlPhp/config
        Alias /simplesaml /var/neos-flow-project/Packages/Libraries/simplesamlphp/simplesamlphp/www

Cf. https://simplesamlphp.org/docs/stable/simplesamlphp-install#section_6

Note: The `SIMPLESAMLPHP_CONFIG_DIR` path is simply a suggestion. It can also be stored elsewhere.


### Configuration

SimpleSAMLphp uses its own configuration file structure. Copy it to the file path set above.

    cd /var/neos-flow-project/
    mkdir Configuration/SimpleSamlPhp
    cp -r Packages/Libraries/simplesamlphp/simplesamlphp/config-templates/ Configuration/SimpleSamlPhp/config
    
Depending on your identity provider, you want to set the 'METADATA CONFIGURATION' in the `config.php` and the 'idp' in the `authsources.php`.
    
## Sample setup

As a sample and for test purposes, the serverless SAML identity provider [Samling](https://capriza.github.io/samling/samling.html) 
is configured most basically as follows:
    
    mkdir Configuration/SimpleSamlPhp/metadata
    cp Packages/Libraries/simplesamlphp/simplesamlphp/metadata-templates/saml20-idp-remote.php Configuration/SimpleSamlPhp/metadata/

Add the following metadata config to `Configuration/SimpleSamlPhp/metadata/saml20-idp-remote.php`:

    $metadata['samling'] = array(
        /* Configuration options for the first IdP. */
        'SingleSignOnService' => 'https://capriza.github.io/samling/samling.html',
        'certificate' => '../../../../samling.pub'
    );

The certificate is copied from `https://capriza.github.io/samling/samling.html`. 

## Integration

Have a look into the package's `Configuration/Settings.yaml` and configure the entry points if needed.

The following setting has to match the authentication source configured in the SimpleSAMLphp `authsources.php` config file:

    Swisscom:
      SimpleSamlServiceProvider:
        authSource: 'default-sp'