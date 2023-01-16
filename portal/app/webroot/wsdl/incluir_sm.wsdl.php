<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<wsdl:definitions xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:soap11="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:wsp="http://www.w3.org/ns/ws-policy" xmlns:wsp200409="http://schemas.xmlsoap.org/ws/2004/09/policy" xmlns:wsp200607="http://www.w3.org/2006/07/ws-policy"
	xmlns:ns0="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm">
	<wsdl:types xmlns:xsd="http://www.w3.org/2001/XMLSchema">
		<xsd:schema>
			<xsd:import schemaLocation="incluir_sm.request.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm"/>
			<xsd:import schemaLocation="incluir_sm.response.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm"/>
		</xsd:schema>
	</wsdl:types>
	<wsdl:message name="IncluirSmRequestMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm" name="request" element="xsns:viagem"/>
	</wsdl:message>
	<wsdl:message name="IncluirSmResponseMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm" name="response" element="xsns:viagem_result"/>
	</wsdl:message>
	<wsdl:portType name="IncluirSmPort">
		<wsdl:operation name="incluirSm">
			<wsdl:input name="incluirSmRequest" message="ns0:IncluirSmRequestMessage"/>
			<wsdl:output name="incluirSmResponse" message="ns0:IncluirSmResponseMessage"/>
		</wsdl:operation>
	</wsdl:portType>
	<wsdl:binding name="IncluirSmSOAP11Binding" type="ns0:IncluirSmPort">
		<soap11:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
		<wsdl:operation name="incluirSm">	
			<soap11:operation style="document" />
			<wsdl:input name="incluirSmRequest">
				<soap11:body parts="request" use="literal"/>
			</wsdl:input>
			<wsdl:output name="incluirSmResponse">
				<soap11:body parts="response" use="literal"/>
			</wsdl:output>
		</wsdl:operation>
	</wsdl:binding>
	<wsdl:service name="IncluirSmService">
    	<wsdl:port name="IncluirSm" binding="ns0:IncluirSmSOAP11Binding">
			<soap11:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/incluir_sm_soap"/>
		</wsdl:port>
	</wsdl:service>
</wsdl:definitions>