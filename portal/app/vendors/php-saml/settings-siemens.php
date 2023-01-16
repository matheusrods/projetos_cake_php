<?php
 
$settings = array (
    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true,

    // Enable debug mode (to print errors)
    'debug' => true,

    // Set a BaseURL to be used instead of try to guess
    // the BaseURL of the view that process the SAML Message.
    // Ex. http://sp.example.com/
    //     http://example.com/sp/
    'baseurl' => null,

    // Service Provider Data that we are deploying
    'sp' => array (
        // Identifier of the SP entity  (must be a URI)
        'entityId' => 'https://portal.rhhealth.com.br/portal/',
        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array (
            // URL Location where the <Response> from the IdP will be returned
            'url' => 'https://portal.rhhealth.com.br/portal/azuread/return/71758',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-POST binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ),
        // If you need to specify requested attributes, set a
        // attributeConsumingService. nameFormat, attributeValue and
        // friendlyName can be omitted. Otherwise remove this section.
        // "attributeConsumingService"=> array(
        //         "serviceName" => "SP test",
        //         "serviceDescription" => "Test Service",
        //         "requestedAttributes" => array(
        //             array(
        //                 "name" => "",
        //                 "isRequired" => false,
        //                 "nameFormat" => "",
        //                 "friendlyName" => "",
        //                 "attributeValue" => ""
        //             )
        //         )
        // ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        'singleLogoutService' => array (
            // URL Location where the <Response> from the IdP will be returned
            'url' => 'http://portal.localhost/portal/usuarios/logout',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => '',
        'privateKey' => '',

        /*
         * Key rollover
         * If you plan to update the SP x509cert and privateKey
         * you can define here the new x509cert and it will be 
         * published on the SP metadata so Identity Providers can
         * read them and get ready for rollover.
         */
        // 'x509certNew' => '',
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array (
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => 'https://sts.windows.net/254ba93e-1f6f-48f3-90e6-e2766664b477/',
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array (
            // URL Target of the IdP where the SP will send the Authentication Request Message
            'url' => 'https://login.microsoftonline.com/254ba93e-1f6f-48f3-90e6-e2766664b477/saml2',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array (
            // URL Location of the IdP where the SP will send the SLO Request
            'url' => 'https://login.microsoftonline.com/254ba93e-1f6f-48f3-90e6-e2766664b477/saml2',
            // URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
            // if not set, url for the SLO Request will be used
            'responseUrl' => '',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => 'MIIC8DCCAdigAwIBAgIQPa3pWDXNSZhK8PR8LGxEQzANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQDEylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMjA4MTkxMDExMjdaFw0yNTA4MTkxMDExMjZaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQgU1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyYktGKfWZqnouLBFi68b7HAFPoMZz+Z1qJn1K4uVpnaa/Y0bEdWTk3tPYlBMgVqsd8r80Vd9EIn1+wOvEkNAkh4aBA+bUqjjixjQY1HuzcOPw4sIfbVbmDx9d2MdH2IhkYEDQf5xEwb9S2JoOJpCBNJicBdRFqFEEO3Kec5O6dMU6NREi7lBe+T9DYKgWhWZX1Bx3OvYo2yhTnqkYaMaBPwJsgg5Zfp6+2GMEE0Y3mp6GdjlTS8S4XNs+Kr7bOTI8QVPG3Q2Y9ikAnAeqpd4iCaLziZV4Q8hZWzYSAzxp33NKC/8avYX4m7tfv4lbsm37ryXfS5HVcpHqwibGye5+QIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQCxGW/S2EUQ3Rpb131XLeTRhSJepHX9fb9pvV6Z0HC9dpnSslEJ7HltqqONQxBIoOioCmixh9XVJ4xOUw5ZfgZR86hnocAaqIaojMFFvYcA+RN7U59ou2jk1dWglzgz7MyoHKVrspNYCgn1QZm94ZRLFzlwNsYGCZpaTdVeozxoJCmjwToo7IR0gcXte107v9Hzxj/MpdnEokVnfj3ntDCkndLPbe+Nxbu0lHduPSX0KMlN4/gUT041swWa8iw7Q6+IN367rwvYK7p7toXNmovHWywrihKkqe6Tho2Y3wFjbL2gmjRWTB1/beto806cVAEJkMDrekn0DUZQPotjrHET',
        /*
         *  Instead of use the whole x509cert you can use a fingerprint in
         *  order to validate the SAMLResponse, but we don't recommend to use
         *  that method on production since is exploitable by a collision
         *  attack.
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
         *   or add for example the -sha256 , -sha384 or -sha512 parameter)
         *
         *  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
         *  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
         *  'sha1' is the default value.
         */
        // 'certFingerprint' => '',
        // 'certFingerprintAlgorithm' => 'sha1',

        /* In some scenarios the IdP uses different certificates for
         * signing/encryption, or is under key rollover phase and more 
         * than one certificate is published on IdP metadata.
         * In order to handle that the toolkit offers that parameter.
         * (when used, 'x509cert' and 'certFingerprint' values are
         * ignored).
         */
        // 'x509certMulti' => array(
        //      'signing' => array(
        //          0 => '<cert1-string>',
        //      ),
        //      'encryption' => array(
        //          0 => '<cert2-string>',
        //      )
        // ),
    ),
);
