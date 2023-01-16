<?php
// openssl pkcs12 -nodes -in webgoal.pfx -out webgoal.pem
define('CERTIFICATE_FILE_PATH', APP.'config/buonny.pem');

class SoapComponent {
    
		const SUCESSO = 1;
		const DESCARTADO_CLIENTE = 2;
		const PROBLEMA_INTEGRACAO = 3;

		var $name = 'Soap';
	
    public function request($url, $function, Envelope $envelope, $options = array(), $param = null) {
    	$param = (!empty($param) ? '&'.$param :'');
        $client = new MSSoapClient($url . '?WSDL'.$param, array_merge($this->options($url), $options));
        return $client->$function($envelope);
    }

	private function options($url) {
		return array(
   			 //'encoding'      => 'UTF-8',
   			 'location'      => $url,
			 //'soap_version'  => SOAP_1_1,
			 'local_cert'    => CERTIFICATE_FILE_PATH,
			 //'exceptions'    => true,
			 //'trace' => true,
			 'cache_wsdl' => false,

			// 'proxy_host'     => "172.16.1.50",
			// 'proxy_port'     => 3128,
			// 'proxy_login'    => "sistemas",
			// 'proxy_password' => 'D3s3nV873'
		);
	}
    
}

class Envelope {
	public function Envelope() {
		$this->EVENTO = new stdClass();
	}
}

class MSSoapClient extends SoapClient {

    function __doRequest($request, $location, $action, $version) {
        echo $request;
        return parent::__doRequest($request, $location, $action, $version);
    }

}