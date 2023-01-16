<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" attributeFormDefault="unqualified" elementFormDefault="qualified">
	<xs:element type="xs:string" name="mensagem"/>
</xs:schema>