<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="posicoes">
    <xs:complexType>
        <xs:sequence>
            <xs:element name="posicao_em_viagem" maxOccurs="unbounded" minOccurs="0">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="idPosicao"/>
				    	<xs:element type="xs:string" name="dataHora"/>
				    	<xs:element type="xs:string" name="placa"/>
				    	<xs:element type="xs:string" name="idTerminal"/>
				    	<xs:element type="xs:double" name="latitude"/>
				    	<xs:element type="xs:double" name="longitude"/>
				    	<xs:element type="xs:string" name="cidade"/>
				    	<xs:element type="xs:string" name="estado"/>
				    	<xs:element type="xs:string" name="logradouro"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>