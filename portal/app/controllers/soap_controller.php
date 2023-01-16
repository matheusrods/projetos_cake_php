<?php
class SoapController extends AppController {
	
	public $name = 'Soap';
	public $helpers = array();
	var $uses = array();
	var $components = array('SmSoap','TeleconsultSoap');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'incluir_sm_soap',
				'buonny_soap',
				'buonny2_soap',
				'PlanWebService_soap',
				'consulta_profissional_soap',
				'incluir_ficha_soap'
			)
		);
	}
	
	function incluir_sm_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/incluir_sm.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('SmSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
		
	}

	function buonny_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/buonny.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('SmSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();	    
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
		
	}

	function buonny2_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/buonny2.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('SmSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
		
	}

	function planwebservice_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/PlanWebService.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('SmSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
		
	}	

	function consulta_profissional_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/consulta_profissional.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('TeleconsultSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
	}	

	function incluir_ficha_soap() {
		ini_set("soap.wsdl_cache_enabled","0");
		$server = new SoapServer("http://{$_SERVER['HTTP_HOST']}/portal/wsdl/incluir_ficha.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
		$server->setClass('TeleconsultSoapComponent');
		$server->handle();
		$soapXml = ob_get_contents();
	    ob_end_clean();
	    $soapXml = substr($soapXml, strpos($soapXml, "<?"));
	    echo $soapXml;
		exit;
	}

}