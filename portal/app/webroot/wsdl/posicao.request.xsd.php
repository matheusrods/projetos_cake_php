<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="alvo">
    <xs:complexType>
        <xs:sequence>
        	<xs:element name="autenticacao">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="token"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element type="xs:string" name="cnpj_cliente"/>
            <xs:element type="xs:string" name="tipo"/>
            <xs:element type="xs:string" name="valor"/>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>