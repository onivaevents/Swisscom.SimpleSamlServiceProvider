# Swisscom.SimpleSamlServiceProvider
[Neos Flow](https://flow.neos.io/) SAML authentication package containing a SAML service provider based on [SimpleSAMLphp](https://simplesamlphp.org).

## Installation

Cf: https://simplesamlphp.org/docs/2.4/simplesamlphp-install.html
 
Install this package via composer. It will add [simplesamlphp/simplesamlphp](https://github.com/simplesamlphp/simplesamlphp) as dependency.

Think as the simplesamlphp installation as an application inside your Flow application. Therefore, have a look at [Installing SimpleSAMLphp in alternative locations](https://simplesamlphp.org/docs/2.4/simplesamlphp-install.html#appendix-installing-simplesamlphp-in-alternative-locations)

### Apache

1. Create symlink `Web/simplesamlphp -> ../Packages/Libraries/simplesamlphp/simplesamlphp/public`
2. Patch the Apache .htaccess configuration to not rewrite simplesamlphp and set the `SIMPLESAMLPHP_CONFIG_DIR` environment var. There is apatch for you: [htaccess.patch](Resources/Private/Scripts/htaccess.patch)


### Nginx

For Nginx you we don't need a symlink. 

1. Use the configuration from here: [Configuring Nginx](https://simplesamlphp.org/docs/2.4/simplesamlphp-install.html#configuring-nginx)
2. Adapt the alias to the absolut path of your installation.

As a starting point for the coinfiguration, copy the example structure to the `SIMPLESAMLPHP_CONFIG_DIR` under `Configuration/SimpleSamlPhp/`


## Sample setup

As a sample and for test purposes, the serverless SAML identity provider [Samling](https://fujifish.github.io/samling/samling.html) 
can be configured most basically as follows:
    
    mkdir Configuration/SimpleSamlPhp/metadata
    cp Packages/Libraries/simplesamlphp/simplesamlphp/metadata/saml20-idp-remote.php.dist Configuration/SimpleSamlPhp/metadata/saml20-idp-remote.php
    
Add the following metadata config to `Configuration/SimpleSamlPhp/metadata/saml20-idp-remote.php`:

    $metadata['https://fujifish.github.io/samling/samling.html'] = array(
        /* Configuration options for the first IdP. */
        'SingleSignOnService' => [
            [
                'Location' => 'https://fujifish.github.io/samling/samling.html',
                'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            ],
        ],
        'certificate' => 'samling.pub'
    );

The certificate is copied from `https://fujifish.github.io/samling/samling.html` to the cert folder (see `certdir` in config.php). 

## Integration

Have a look into the package's `Configuration/Settings.yaml` and configure the entry points if needed.

The following setting has to match the authentication source configured in the SimpleSAMLphp `authsources.php` config file:

    Swisscom:
      SimpleSamlServiceProvider:
        authSource: 'default-sp'
