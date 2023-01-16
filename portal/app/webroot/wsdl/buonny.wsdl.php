<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<wsdl:definitions xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:soap11="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:wsp="http://www.w3.org/ns/ws-policy" xmlns:wsp200409="http://schemas.xmlsoap.org/ws/2004/09/policy" xmlns:wsp200607="http://www.w3.org/2006/07/ws-policy"
	xmlns:ns0="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny">
	
	<!-- schema -->
	<wsdl:types xmlns:xsd="http://www.w3.org/2001/XMLSchema">
		<xsd:schema attributeFormDefault="unqualified" elementFormDefault="qualified">
			<xsd:import schemaLocation="incluir_sm_b.request.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="incluir_sm_b.response.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="posicao.request.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="posicao.response.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="autenticador.request.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="posicao_em_viagem.response.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
			<xsd:import schemaLocation="ola_cliente.response.xsd" namespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny"/>
		</xsd:schema>
	</wsdl:types>

	<!-- message -->
	<wsdl:message name="IncluirSmRequestMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="requestViagem" element="xsns:viagem"/>
	</wsdl:message>
	<wsdl:message name="IncluirSmResponseMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="responseViagemResult" element="xsns:viagem_result"/>
	</wsdl:message>
	<wsdl:message name="PosicaoRequestMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="requestAlvo" element="xsns:alvo"/>
	</wsdl:message>
	<wsdl:message name="PosicaoResponseMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="responseAlvoResult" element="xsns:alvo_result"/>
	</wsdl:message>
	<wsdl:message name="AutenticadorRequestMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="requestAutenticadorResult" element="xsns:autenticador"/>
	</wsdl:message>
	<wsdl:message name="PosicoesResponseMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="responsePosicoesResult" element="xsns:posicoes"/>
	</wsdl:message>
	<wsdl:message name="OlaClienteResponseMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="resultOlaClienteResult" element="xsns:mensagem"/>
	</wsdl:message>
	<wsdl:message name="OlaClienteRequestMessage">
		<wsdl:part xmlns:xsns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" name="requestOlaClienteRequest" element="xsns:mensagem"/>
	</wsdl:message>

	<!-- port -->
	<wsdl:portType name="IncluirSmPort">
		<wsdl:operation name="incluirSm">
			<wsdl:input name="incluirSmRequest" message="ns0:IncluirSmRequestMessage"/>
			<wsdl:output name="incluirSmResponse" message="ns0:IncluirSmResponseMessage"/>
		</wsdl:operation>
	</wsdl:portType>
	<wsdl:portType name="PosicaoPort">
		<wsdl:operation name="posicao">
			<wsdl:input name="posicaoRequest" message="ns0:PosicaoRequestMessage"/>
			<wsdl:output name="posicaoResponse" message="ns0:PosicaoResponseMessage"/>
		</wsdl:operation>
	</wsdl:portType>
	<wsdl:portType name="PosicaoEmViagemPort">
		<wsdl:operation name="posicaoEmViagem">
			<wsdl:input name="posicaoEmViagemRequest" message="ns0:AutenticadorRequestMessage"/>
			<wsdl:output name="posicaoEmViagemResponse" message="ns0:PosicoesResponseMessage"/>
		</wsdl:operation>
	</wsdl:portType>
	<wsdl:portType name="OlaClientePort">
		<wsdl:operation name="olaCliente">
			<wsdl:input name="olaClienteRequest" message="ns0:OlaClienteRequestMessage"/>
			<wsdl:output name="olaClienteResponse" message="ns0:OlaClienteResponseMessage"/>
		</wsdl:operation>
	</wsdl:portType>

	<!-- binding -->
	<wsdl:binding name="IncluirSmSOAP11Binding" type="ns0:IncluirSmPort">
		<soap11:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
		<wsdl:operation name="incluirSm">	
			<soap11:operation style="document" />
			<wsdl:input name="incluirSmRequest">
				<soap11:body parts="requestViagem" use="literal"/>
			</wsdl:input>
			<wsdl:output name="incluirSmResponse">
				<soap11:body parts="responseViagemResult" use="literal"/>
			</wsdl:output>
		</wsdl:operation>
	</wsdl:binding>
	<wsdl:binding name="PosicaoSOAP11Binding" type="ns0:PosicaoPort">
		<soap11:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
		<wsdl:operation name="posicao">	
			<soap11:operation style="document" />
			<wsdl:input name="posicaoRequest">
				<soap11:body parts="requestAlvo" use="literal"/>
			</wsdl:input>
			<wsdl:output name="posicaoResponse">
				<soap11:body parts="responseAlvoResult" use="literal"/>
			</wsdl:output>
		</wsdl:operation>
	</wsdl:binding>
	<wsdl:binding name="PosicaoEmViagemSOAP11Binding" type="ns0:PosicaoEmViagemPort">
		<soap11:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
		<wsdl:operation name="posicaoEmViagem">	
			<soap11:operation style="document" />
			<wsdl:input name="posicaoEmViagemRequest">
				<soap11:body parts="requestAutenticadorResult" use="literal"/>
			</wsdl:input>
			<wsdl:output name="posicaoEmViagemResponse">
				<soap11:body parts="responsePosicoesResult" use="literal"/>
			</wsdl:output>
		</wsdl:operation>
	</wsdl:binding>
	<wsdl:binding name="OlaClienteSOAP11Binding" type="ns0:OlaClientePort">
		<soap11:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
		<wsdl:operation name="olaCliente">
			<soap11:operation style="document" />
			<wsdl:input name="olaClienteRequest">
				<soap11:body parts="requestOlaClienteRequest" use="literal"/>
			</wsdl:input>
			<wsdl:output name="olaClienteResponse">
				<soap11:body parts="resultOlaClienteResult" use="literal"/>
			</wsdl:output>
		</wsdl:operation>
	</wsdl:binding>

	<!-- service -->
	<wsdl:service name="BuonnyService">
    	<wsdl:port name="IncluirSm" binding="ns0:IncluirSmSOAP11Binding">
			<soap11:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/buonny_soap"/>
		</wsdl:port>
		<wsdl:port name="Posicao" binding="ns0:PosicaoSOAP11Binding">
			<soap11:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/buonny_soap"/>
		</wsdl:port>
		<wsdl:port name="PosicaoEmViagem" binding="ns0:PosicaoEmViagemSOAP11Binding">
			<soap11:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/buonny_soap"/>
		</wsdl:port>
		<wsdl:port name="OlaCliente" binding="ns0:OlaClienteSOAP11Binding">
			<soap11:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/buonny_soap"/>
		</wsdl:port>
	</wsdl:service>
</wsdl:definitions>