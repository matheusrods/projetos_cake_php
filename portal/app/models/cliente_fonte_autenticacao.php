<?php

class ClienteFonteAutenticacao extends AppModel
{
    var $name = 'ClienteFonteAutenticacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'clientes_fontes_autenticacao';
    var $primaryKey = 'codigo';

    var $hasOne = array(
        'Cliente' => array(
            'className'    => 'Cliente',
            'conditions' => 'ClienteFonteAutenticacao.codigo_cliente = Cliente.codigo',
            'foreignKey' => false,
            'dependent' => false
        )
    );

    public $validate = array(
        'codigo_cliente' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o cliente',
                'required' => true
            ),
            'validaCodigoClienteMatrizGrupoEconomico' => array(
                'rule' => 'validaCodigoClienteMatrizGrupoEconomico',
                'message' => 'Cliente não se trata de matriz de grupo econômico'
            )
        ),
        'id_cliente' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o identificador do cliente',
                'required' => true
            )
        ),
        'id_entity' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o id da aplicação',
                'required' => true
            )
        ),
        'id_azure_ad' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o id do Azure AD',
                'required' => true
            )
        ),
        'url_login' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a URL de login',
            'required' => true
        ),
        'url_logout' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a URL de logout',
            'required' => true
        ),
        'url_reply' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a URL de resposta',
            'required' => true
        ),
        'certificado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o certificado',
            'required' => true
        )
    );

    public function getByCodigoCliente($codigo_cliente)
    {

        $options = array(
            'fields' => array(
                'ClienteFonteAutenticacao.codigo',
                'ClienteFonteAutenticacao.codigo_cliente',
                'ClienteFonteAutenticacao.id_entity',
                'ClienteFonteAutenticacao.id_azure_ad',
                'ClienteFonteAutenticacao.url_login',
                'ClienteFonteAutenticacao.url_logout',
                'ClienteFonteAutenticacao.url_reply',
                'ClienteFonteAutenticacao.certificado',
                'ClienteFonteAutenticacao.id_cliente',
                'ClienteFonteAutenticacao.cor_botao',
                'ClienteFonteAutenticacao.auto_redirect',
                'Cliente.codigo',
                'Cliente.nome_fantasia',
                'Cliente.razao_social',
                'Cliente.caminho_arquivo_logo',
            ),
            // 'joins' => array(
            //     array(
            //         'table' => 'cliente',
            //         'alias' => 'Cliente',
            //         'type' => 'INNER',
            //         'conditions' => array('Cliente.codigo = ClienteFonteAutenticacao.codigo_cliente'),
            //     )
            // ),
            'conditions' => array('ClienteFonteAutenticacao.codigo_cliente' => $codigo_cliente),
        );

        return $this->find('first', $options);
    }

    public function validaCodigoClienteMatrizGrupoEconomico()
    {
        $codigo_cliente = $this->data['ClienteFonteAutenticacao']['codigo_cliente'];

        $this->GrupoEconomico = &ClassRegistry::init('GrupoEconomico');

        $grupoEconomico = $this->GrupoEconomico->find('first', array(
            'conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente)
        ));

        if (!empty($grupoEconomico)) {

            return true;
        }

        return false;
    }

    public function initArr()
    {

        return array(
            'ClienteFonteAutenticacao' => array(
                'codigo' => null,
                'id_entity' => '',
                'id_azure_ad' => '',
                'url_login' => '',
                'url_logout' => '',
                'url_reply' => '',
                'certificado' => '',
                'codigo_cliente' => '',
                'id_cliente' => '',
                'cor_botao' => '',
                'auto_redirect' => '0'
            )
        );
    }

    public function getSamlSettingsByCodigoCliente($codigo_cliente)
    {

        $fonteAutenticacaoArr = $this->getByCodigoCliente($codigo_cliente);


        if (!empty($fonteAutenticacaoArr)) {
            return array(
                // If 'strict' is True, then the PHP Toolkit will reject unsigned
                // or unencrypted messages if it expects them signed or encrypted
                // Also will reject the messages if not strictly follow the SAML
                // standard: Destination, NameId, Conditions ... are validated too.
                'strict' => true,

                // Enable debug mode (to print errors)
                'debug' => Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO? false :true,

                // Set a BaseURL to be used instead of try to guess
                // the BaseURL of the view that process the SAML Message.
                // Ex. http://sp.example.com/
                //     http://example.com/sp/
                'baseurl' =>  str_replace('http://', 'https://',Ambiente::getUrl()),

                // Service Provider Data that we are deploying
                'sp' => array(
                    // Identifier of the SP entity  (must be a URI)
                    'entityId' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['id_entity'],
                    // Specifies info about where and how the <AuthnResponse> message MUST be
                    // returned to the requester, in this case our SP.
                    'assertionConsumerService' => array(
                        // URL Location where the <Response> from the IdP will be returned
                        'url' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['url_reply'],
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
                    'singleLogoutService' => array(
                        // URL Location where the <Response> from the IdP will be returned
                        'url' => 'https://portal.rhhealth.com.br/portal/logout',
                        // SAML protocol binding to be used when returning the <Response>
                        // message.  Onelogin Toolkit supports for this endpoint the
                        // HTTP-Redirect binding only
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',

                    ),

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
                'idp' => array(
                    // Identifier of the IdP entity  (must be a URI)
                    'entityId' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['id_azure_ad'],
                    // SSO endpoint info of the IdP. (Authentication Request protocol)
                    'singleSignOnService' => array(
                        // URL Target of the IdP where the SP will send the Authentication Request Message
                        'url' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['url_login'],
                        // SAML protocol binding to be used when returning the <Response>
                        // message.  Onelogin Toolkit supports for this endpoint the
                        // HTTP-Redirect binding only
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    ),
                    // SLO endpoint info of the IdP.
                    'singleLogoutService' => array(
                        // URL Location of the IdP where the SP will send the SLO Request
                        'url' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['url_logout'],
                        // URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
                        // if not set, url for the SLO Request will be used
                        'responseUrl' => '',
                        // SAML protocol binding to be used when returning the <Response>
                        // message.  Onelogin Toolkit supports for this endpoint the
                        // HTTP-Redirect binding only
                        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    ),
                    // Public x509 certificate of the IdP
                    'x509cert' => $fonteAutenticacaoArr['ClienteFonteAutenticacao']['certificado'],
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
        }
    }



    public function getByIdentificadorCliente($id_cliente)
    {

        $options = array(
            'fields' => array(
                'ClienteFonteAutenticacao.codigo',
                'ClienteFonteAutenticacao.codigo_cliente',
                'ClienteFonteAutenticacao.id_entity',
                'ClienteFonteAutenticacao.id_azure_ad',
                'ClienteFonteAutenticacao.url_login',
                'ClienteFonteAutenticacao.url_logout',
                'ClienteFonteAutenticacao.url_reply',
                'ClienteFonteAutenticacao.certificado',
                'ClienteFonteAutenticacao.id_cliente',
                'ClienteFonteAutenticacao.cor_botao',
                'ClienteFonteAutenticacao.auto_redirect',
                'Cliente.codigo',
                'Cliente.nome_fantasia',
                'Cliente.razao_social',
                'Cliente.caminho_arquivo_logo',
            ),
            'conditions' => array('ClienteFonteAutenticacao.id_cliente' => $id_cliente),
        );

        return $this->find('first', $options);
    }
}
