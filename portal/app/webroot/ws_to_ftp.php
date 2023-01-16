<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('soap.wsdl_cache_ttl', 0);
ini_set('soap.wsdl_cache_enabled', 0);

define('APPLICATION_PATH', getcwd());
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}
if (!defined('APP')) {
	define('APP', ROOT.DS.APP_DIR.DS);
}
//echo ROOT;

class WSDLToFTP {

	var $retorno;
	/*
	* @var String ___FOR_ZEND_minOccurs=1 ___FOR_ZEND_maxOccurs=1
	*/	
	var $cliente = '';
	var $diretorioRetorno = '';

	public function __construct($cliente) {
		$this->cliente = $cliente;
	}

	private function arrayToObject($array) {
	    $object = new stdClass();
	    foreach ($array as $key => $value) {
	        if (is_array($value)) {
	            $value = $this->arrayToObject($value);
	        }
	        $object->$key = $value;
	    }
	    return $object;
	}

	private function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(array('WSDLToFTP','objectToArray'), $d);
        }
        else {
            return $d;
        }
    }
    
    private function convertToXML($obj, &$xml) {

    	if (is_object($obj)) {
    		$arr = $this->objectToArray($obj);
    	} else $arr = $obj;

		foreach($arr as $key => $value) {
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml->addChild("$key");
	                $this->convertToXML($value, $subnode);
	            }
	            else{
	                $subnode = $xml->addChild("item$key");
	                $this->convertToXML($value, $subnode);
	            }
	        }
	        else {
	            $xml->addChild("$key",htmlspecialchars("$value"));
	        }
	    }
		
    }

	/**
    * receber_evento
     * @return object
    */ 
    public function receber_evento($param) {
    	$result = Array();

    	if ($this->cliente=='teste') {
			$path = APP.DS.'tmp'.DS.'tests'.DS;
			$this->diretorioRetorno = $path.'retorno';
    	} else {
    		if ($this->cliente=='tam') {
    			$path = DS.'home'.DS.'tam'.DS.'tam'.DS;
    			$this->diretorioRetorno = $path.'retorno';
    		} else {
				$path = DS.'home'.DS.$this->cliente.DS.$this->cliente.DS;
				$this->diretorioRetorno = $path.'retorno';
				if (!is_dir($this->diretorioRetorno)) {
					$path = DS.'home'.DS.$this->cliente.DS;
					$this->diretorioRetorno = $path.'retorno';
				}    			
    		}
    	}

    	if (!is_dir($this->diretorioRetorno)) mkdir($this->diretorioRetorno);
		$tipo_evento = $param->EVENTO->evento;

		$nome_arquivo = $tipo_evento."_".date("YmdHis").".xml";

		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><".$tipo_evento."></".$tipo_evento.">");
		$this->convertToXML($param,$xml);
    	$result['cliente'] = $this->cliente;
    	try {
			if ($xml->asXML($this->diretorioRetorno.DS.$nome_arquivo)) {
		    	$result['sucesso'] = SoapComponent::SUCESSO;
		    	$result['arquivo'] = $this->diretorioRetorno.DS.$nome_arquivo;
			} else {
				$result['sucesso'] = SoapComponent::PROBLEMA_INTEGRACAO;
				$result['mensagem_erro'] = 'Erro ao gerar XML do Evento';
		    	$result['arquivo'] = $this->diretorioRetorno.DS.$nome_arquivo;
				$result['dados_evento'] = $this->objectToArray($param);
			}
		} catch(Exception $e) {
			$result['sucesso'] = SoapComponent::PROBLEMA_INTEGRACAO;
			$result['mensagem_erro'] = 'Erro ao gerar XML do Evento: '.$e->getMessage();
	    	$result['arquivo'] = $this->diretorioRetorno.DS.$nome_arquivo;
			$result['dados_evento'] = $this->objectToArray($param);
		}
    	$ret = Array(
    		'evento_result' => $result
    	);
    	$ret = $this->arrayToObject($ret);
    	return $ret;
	}

}

function __autoload($className) {
    require_once str_replace('_', '/', $className) . '.php';
	
}
require_once(APP.DS."controllers".DS."components".DS."soap.php");



set_include_path(get_include_path() . PATH_SEPARATOR . realpath('../'));

ini_set('soap.wsdl_cache_ttl', 0);
ini_set('soap.wsdl_cache_enabled', false);


$urlWebService = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
if (isset($_GET['cliente'])) {
	$urlWebService .= '?cliente='.$_GET['cliente'];
}
//$urlWebService = str_replace('/app/webroot', '', $urlWebService);
if (isset($_GET['wsdl']) || isset($_GET['WSDL'])) {	
    $server = new Zend_Soap_AutoDiscover(
                    new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex(),
                    $urlWebService
    );
    $server->setClass('WSDLToFTP');
} else {
	//echo $urlWebService;
	if (strpos($urlWebService,'?')) {
	    $arrURL = explode('?',$urlWebService);
    	$urlWebService = $arrURL[0];
    }

    $server = new Zend_Soap_Server($urlWebService . '?wsdl');
    $server->setClass('WSDLToFTP');
    $server->setEncoding('UTF-8');
    $server->setClassmap(array_combine(get_declared_classes(), get_declared_classes()));
    if (isset($_GET['cliente'])) {
 	    $server->setObject(new WSDLToFTP($_GET['cliente']));
    } else {
	    $server->setObject(new WSDLToFTP());
    }
}

$server->handle();


?>
