<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="alvo_result">
    <xs:complexType>
        <xs:sequence>
        	<xs:element name="coordenada">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="latitude"/>
                        <xs:element type="xs:string" name="longitude"/>
                        <xs:element type="xs:string" name="data" />
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="terminal">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="numero"/>
                        <xs:element type="xs:string" name="versao"/>
                        <xs:element type="xs:string" name="tecnologia" />
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="local">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="logradouro"/>
                        <xs:element type="xs:string" name="cidade"/>
                        <xs:element type="xs:string" name="estado" />
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>
