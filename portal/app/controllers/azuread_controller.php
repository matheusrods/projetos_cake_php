<?php
include_once(APP . 'vendors' . DS . 'php-saml' . DS . '_toolkit_loader.php');
class AzureadController extends AppController
{
    public $name = 'Azuread';
    public $uses = array('ClienteFonteAutenticacao');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('sso');
    }

    public function retorno($codigo_cliente=false)
    {
       //$this->redirect();
    }

    public function sso($codigo_cliente='')
    {
        $settings = new OneLogin_Saml2_Settings($this->ClienteFonteAutenticacao->getSamlSettingsByCodigoCliente($codigo_cliente));
        $authRequest = new OneLogin_Saml2_AuthnRequest($settings);
        $samlRequest = $authRequest->getRequest();

        $parameters = array('SAMLRequest' => $samlRequest);
        $parameters['RelayState'] = OneLogin_Saml2_Utils::getSelfURLNoQuery();

        $idpData = $settings->getIdPData();
        $ssoUrl = $idpData['singleSignOnService']['url'];
        $url = OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);

        header("Location: $url");
        exit;
    }
}
