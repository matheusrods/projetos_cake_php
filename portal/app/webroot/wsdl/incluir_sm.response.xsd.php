<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="viagem_result">
    <xs:complexType>
        <xs:sequence>
            <xs:element type="xs:string" name="codigo_sm"/>
            <xs:element type="xs:string" name="erro"/>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>
