<?php

// $settings = array (
//     // If 'strict' is True, then the PHP Toolkit will reject unsigned
//     // or unencrypted messages if it expects them signed or encrypted
//     // Also will reject the messages if not strictly follow the SAML
//     // standard: Destination, NameId, Conditions ... are validated too.
//     'strict' => true,

//     // Enable debug mode (to print errors)
//     'debug' => true,

//     // Set a BaseURL to be used instead of try to guess
//     // the BaseURL of the view that process the SAML Message.
//     // Ex. http://sp.example.com/
//     //     http://example.com/sp/
//     'baseurl' => null,

//     // Service Provider Data that we are deploying
//     'sp' => array (
//         // Identifier of the SP entity  (must be a URI)
//         'entityId' => 'portal-ithelath',
//         // Specifies info about where and how the <AuthnResponse> message MUST be
//         // returned to the requester, in this case our SP.
//         'assertionConsumerService' => array (
//             // URL Location where the <Response> from the IdP will be returned
//             'url' => 'https://portal.localhost/portal/azuread/return/71758',
//             // SAML protocol binding to be used when returning the <Response>
//             // message.  Onelogin Toolkit supports for this endpoint the
//             // HTTP-POST binding only
//             'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
//         ),
//         // If you need to specify requested attributes, set a
//         // attributeConsumingService. nameFormat, attributeValue and
//         // friendlyName can be omitted. Otherwise remove this section.
//         // "attributeConsumingService"=> array(
//         //         "serviceName" => "SP test",
//         //         "serviceDescription" => "Test Service",
//         //         "requestedAttributes" => array(
//         //             array(
//         //                 "name" => "",
//         //                 "isRequired" => false,
//         //                 "nameFormat" => "",
//         //                 "friendlyName" => "",
//         //                 "attributeValue" => ""
//         //             )
//         //         )
//         // ),
//         // Specifies info about where and how the <Logout Response> message MUST be
//         // returned to the requester, in this case our SP.
//         'singleLogoutService' => array (
//             // URL Location where the <Response> from the IdP will be returned
//             'url' => 'https://portal.localhost/portal/logout',
//             // SAML protocol binding to be used when returning the <Response>
//             // message.  Onelogin Toolkit supports for this endpoint the
//             // HTTP-Redirect binding only
//             'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
//         ),
//         // Specifies constraints on the name identifier to be used to
//         // represent the requested subject.
//         // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
//         'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',

//         // Usually x509cert and privateKey of the SP are provided by files placed at
//         // the certs folder. But we can also provide them with the following parameters
//         'x509cert' => '',
//         'privateKey' => '',

//         /*
//          * Key rollover
//          * If you plan to update the SP x509cert and privateKey
//          * you can define here the new x509cert and it will be 
//          * published on the SP metadata so Identity Providers can
//          * read them and get ready for rollover.
//          */
//         // 'x509certNew' => '',
//     ),

//     // Identity Provider Data that we want connect with our SP
//     'idp' => array (
//         // Identifier of the IdP entity  (must be a URI)
//         'entityId' => 'https://sts.windows.net/d87506ea-36fa-43b0-bd0f-4631c99d847a/',
//         // SSO endpoint info of the IdP. (Authentication Request protocol)
//         'singleSignOnService' => array (
//             // URL Target of the IdP where the SP will send the Authentication Request Message
//             'url' => 'https://login.microsoftonline.com/d87506ea-36fa-43b0-bd0f-4631c99d847a/saml2',
//             // SAML protocol binding to be used when returning the <Response>
//             // message.  Onelogin Toolkit supports for this endpoint the
//             // HTTP-Redirect binding only
//             'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
//         ),
//         // SLO endpoint info of the IdP.
//         'singleLogoutService' => array (
//             // URL Location of the IdP where the SP will send the SLO Request
//             'url' => 'https://login.microsoftonline.com/d87506ea-36fa-43b0-bd0f-4631c99d847a/saml2',
//             // URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
//             // if not set, url for the SLO Request will be used
//             'responseUrl' => '',
//             // SAML protocol binding to be used when returning the <Response>
//             // message.  Onelogin Toolkit supports for this endpoint the
//             // HTTP-Redirect binding only
//             'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
//         ),
//         // Public x509 certificate of the IdP
//         'x509cert' => 'MIIC8DCCAdigAwIBAgIQVBSLoL+bl6tOFNxxWvDTmDANBgkqhkiG9w0BAQsFADA0MTIwMAYDVQQD
// EylNaWNyb3NvZnQgQXp1cmUgRmVkZXJhdGVkIFNTTyBDZXJ0aWZpY2F0ZTAeFw0yMjA5MTYxNTM5
// NDNaFw0yNTA5MTYxNTQyMzdaMDQxMjAwBgNVBAMTKU1pY3Jvc29mdCBBenVyZSBGZWRlcmF0ZWQg
// U1NPIENlcnRpZmljYXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsp7iIWhpwvoF
// tuaX413DnuYVX3fCFjb+6Ryk9y79wk5W82/ssbYbLwvHm77wRA1/vy1QRilKQD907KnRXzr9UShF
// eLDT236Aw8gbNkGvjHe23hzbhVcowGUx1P4eNVASajO4ecCniFm3Ge+7bCz7PWPTuT57fFf8CW2P
// pyoyT5+w2Ma/A87c5UTuEE5hjKyg37ayr5Eq6Y0bRsNyeVHvkv6GOqP0dOQ/r4+D9u23+C5O7uuP
// PbZl8U55WSPKLxIxamgsKQW62nu7/HMHaeMZNsYBsL+1eDbsakeB63iumzt6fTc59yiTjjsp3O+s
// 9QOJho/oPe4kJDg3BFCGAaM7UQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQBwP+4Sw7Di1d5tNEMd
// MdLRvuki+PNQWNV3MhgYMl9XXlp8VWFlnVZ0an8zBR+N347L+8eNITdPeBjzaZSp+Rpjmg97Jakh
// Y9SaIs7+KR8EYiLRYnIcSYG+zV/39q1MYAD5hLJ0T5/zUpqZNVOpyBMMIlxRcFAQnfuCBFPaQKVa
// 200pnU9pdk+PgmvclEa9APmNrBmI5oyZGG/YH2Sx6xeqGssIomFzGBWXhUiBH74iuOg7FkoxpoTX
// fXX428cY5ToQb6OWs/EJvK18XU4BviOSplHZXYcV2Mk3mlL8dKOjMNJ5UaSl/8p2ba7yc5lSl+i4
// 14H6DDb+FWiRMWu7uuOY',
//         /*
//          *  Instead of use the whole x509cert you can use a fingerprint in
//          *  order to validate the SAMLResponse, but we don't recommend to use
//          *  that method on production since is exploitable by a collision
//          *  attack.
//          *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
//          *   or add for example the -sha256 , -sha384 or -sha512 parameter)
//          *
//          *  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
//          *  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
//          *  'sha1' is the default value.
//          */
//         // 'certFingerprint' => '',
//         // 'certFingerprintAlgorithm' => 'sha1',

//         /* In some scenarios the IdP uses different certificates for
//          * signing/encryption, or is under key rollover phase and more 
//          * than one certificate is published on IdP metadata.
//          * In order to handle that the toolkit offers that parameter.
//          * (when used, 'x509cert' and 'certFingerprint' values are
//          * ignored).
//          */
//         // 'x509certMulti' => array(
//         //      'signing' => array(
//         //          0 => '<cert1-string>',
//         //      ),
//         //      'encryption' => array(
//         //          0 => '<cert2-string>',
//         //      )
//         // ),
//     ),
// );
