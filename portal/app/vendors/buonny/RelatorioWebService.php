<?php
require_once 'SOAP/Client.php';

class RelatorioWebService
{
    /**
     * @var SoapClient
     */
    private $_soap;
    
    public function __construct() 
    {

    	if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
            $this->_soap = new SOAP_Client('http://jasperprd.ithealth.corp:8080/jasperserver/services/repository', false, false,
                array(
                    'user' => 'jasperadmin',
                    'pass' => 'jasperadmin',
                )
            );
        } else {
           $this->_soap = new SOAP_Client('http://jasperdev.ithealth.corp:8080/jasperserver/services/repository', false, false,
                array(
                    'user' => 'jasperadmin', 
                    'pass' => 'jasperadmin',
                )
            );
        }
        $this->_soap->setOpt('timeout', -1);
        ini_set('default_socket_timeout', -1);
        set_time_limit(0);
    }
    
    /**
     * Executa o relatório e retorna uma string com o relatório em binário
     * @return string Relatório em formato binário
     */
    public function executarRelatorio($nomeRelatorio, array $parametros = null, $formatoSaida = 'PDF',
        $lingua = 'pt_BR')
    {
        
        if (!is_null($parametros)) {
            foreach ($parametros as $parametro => $valor) {
                if (!isset($parametros_xml)) {
                    $parametros_xml = '';
                } else {
                    $parametros_xml .= "\n";
                }
                $parametros_xml .= "<parameter name=\"$parametro\"><![CDATA[$valor]]></parameter>";
            }
        }
         
        
        $formatoSaida = preg_replace('/\W/', '', $formatoSaida);
        $formatoSaida = ($formatoSaida ? $formatoSaida : 'PDF');

        $lingua = preg_replace('/\W/', '', $lingua);
        $lingua = ($lingua ? $lingua : 'PDF');
	
    	$xml = "<request operationName=\"runReport\" locale=\"$lingua\">";
        $xml .= "<argument name=\"USE_DIME_ATTACHMENTS\"><![CDATA[1]]></argument>";
        $xml .= "<argument name=\"RUN_OUTPUT_FORMAT\"><![CDATA[$formatoSaida]]></argument>";
        $xml .= "<resourceDescriptor name=\"\" wsType=\"\" uriString=\"$nomeRelatorio\" isNew=\"false\">";
        $xml .= "<label><![CDATA[null]]></label>";
        $xml .= "<parameter name=\"REPORT_LOCALE\"><![CDATA[$lingua]]></parameter>";
        $xml .= $parametros_xml;
        $xml .="</resourceDescriptor>";
		$xml .="</request>";
		
		

        $retorno = $this->_soap->__call('runReport', $xml);
        //debug($retorno);die();
        $attachments = $this->_soap->_soap_transport->attachments['cid:report'];
        //debug($retorno);die();
        if (is_soap_fault($retorno)) {
            $errorMessage = $retorno->getFault()->faultstring;
         	throw new Exception($errorMessage);
		}
		
       //debug ($attachments);
       return $attachments;
        /*
        $retorno_xml = simplexml_load_string($retorno, null, LIBXML_NOERROR);

        if ($retorno_xml) {
            if ((int)$retorno_xml->returnCode === 0) {
                return $this->_soap->_soap_transport->attachments['cid:report'];
            } else {
                throw new Exception($retorno_xml->returnMessage);
            }
        } else {
            throw new Exception($retorno);
        }*/
    }
    
    function getResourceDescriptors($operationResult)
	{
		$domDocument = new DOMDocument();
	 	$domDocument->loadXML($operationResult);

	 	$folders = array();
	 	$count = 0;

	 	foreach( $domDocument->childNodes AS $ChildNode )
	   	{
	       		if ( $ChildNode->nodeName != '#text' )
	       		{

	           		if ($ChildNode->nodeName == "operationResult")
	           		{
	           			foreach( $ChildNode->childNodes AS $ChildChildNode )
	   				{

	   					if ( $ChildChildNode->nodeName == 'resourceDescriptor' )
	       					{
	       						$resourceDescriptor = $this->readResourceDescriptor($ChildChildNode);
	   						$folders[ $count ] = $resourceDescriptor;
	           					$count++;
	           				}
	           			}
	           		}

	       		}
	   	}

	   	return $folders;
	}
	
	function readResourceDescriptor($node)
	{
		$resourceDescriptor = array();

		$resourceDescriptor['name'] = $node->getAttributeNode("name")->value;
	        $resourceDescriptor['uri'] =  $node->getAttributeNode("uriString")->value;
	        $resourceDescriptor['type'] = $node->getAttributeNode("wsType")->value;

		$resourceProperties = array();
		$subResources = array();
		$parameters = array();

		// Read subelements...
		foreach( $node->childNodes AS $ChildNode )
	   	{
	   		if ( $ChildNode->nodeName == 'label' )
			{
				$resourceDescriptor['label'] = 	$ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'description' )
			{
				$resourceDescriptor['description'] = 	$ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'creationDate' )
			{
				$resourceDescriptor['creationDate'] = 	$ChildNode->nodeValue / 1000;
			}
			else if ( $ChildNode->nodeName == 'resourceProperty' )
			{
				//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
				// read properties...
				$resourceProperty = $this->addReadResourceProperty($ChildNode );
				$resourceProperties[ $resourceProperty["name"] ] = $resourceProperty;
			}
			else if ( $ChildNode->nodeName == 'resourceDescriptor' )
			{
				array_push( $subResources, readResourceDescriptor($ChildNode));
			}
			else if ( $ChildNode->nodeName == 'parameter' )
			{
				$parameters[ $ChildNode->getAttributeNode("name")->value ] =  $ChildNode->nodeValue;
			}
		}

		$resourceDescriptor['properties'] = $resourceProperties;
		$resourceDescriptor['resources'] = $subResources;
		$resourceDescriptor['parameters'] = $parameters;


		return $resourceDescriptor;
	}
	
	function addReadResourceProperty($node)
	{
		$resourceProperty = array();

		$resourceProperty['name'] = $node->getAttributeNode("name")->value;

		$resourceProperties = array();

		// Read subelements...
		foreach( $node->childNodes AS $ChildNode )
	   	{
	   		if ( $ChildNode->nodeName == 'value' )
			{
				$resourceProperty['value'] = $ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'resourceProperty' )
			{
				//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
				// read properties...
				array_push( $resourceProperties, addReadResourceProperty($ChildNode ) );
			}
		}

		$resourceProperty['properties'] = $resourceProperties;

		return $resourceProperty;
	}
}
