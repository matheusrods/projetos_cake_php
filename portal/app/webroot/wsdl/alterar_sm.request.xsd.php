<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/buonny" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="alterar_sm">
    <xs:complexType>
        <xs:sequence>
            <xs:element type="xs:string" name="codigo_sm"/>
        	<xs:element name="autenticacao">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="token"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element type="xs:string" name="sistema_origem"/>
            <xs:element type="xs:string" name="cnpj_cliente"/>
            <xs:element name="itinerario">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="alvo" maxOccurs="unbounded" minOccurs="1">
                            <xs:complexType>
                                <xs:sequence>
                                    <xs:element type="xs:string" name="codigo_externo"/>
                                    <xs:element type="xs:string" name="descricao"/>
                                    <xs:element type="xs:string" name="cep"/>
                                    <xs:element type="xs:string" name="logradouro"/>
                                    <xs:element type="xs:integer" name="numero"/>
                                    <xs:element type="xs:string" name="complemento"/>
                                    <xs:element type="xs:string" name="bairro"/>
                                    <xs:element type="xs:string" name="cidade"/>
                                    <xs:element type="xs:string" name="estado"/>
                                    <xs:element type="xs:decimal" name="latitude"/>
                                    <xs:element type="xs:decimal" name="longitude"/>
                                    <xs:element type="xs:string" name="bandeira"/>
                                    <xs:element type="xs:string" name="regiao"/>
                                    <xs:element type="xs:integer" name="tipo_parada"/>
                                    <xs:element type="xs:time" name="janela_inicio"/>
                                    <xs:element type="xs:time" name="janela_fim"/>
                                    <xs:element type="xs:dateTime" name="previsao_de_chegada"/>
                                    <xs:element name="dados_da_carga">
                                        <xs:complexType>
                                            <xs:sequence>
                                                <xs:element name="carga" maxOccurs="unbounded" minOccurs="1">
                                                    <xs:complexType>
                                                        <xs:sequence>
                                                            <xs:element type="xs:string" name="loadplan_chassi"/>
                                                            <xs:element type="xs:integer" name="nf"/>
                                                            <xs:element type="xs:string" name="serie_nf"/>
                                                            <xs:element type="xs:integer" name="tipo_produto"/>
                                                            <xs:element type="xs:decimal" name="valor_total_nf"/>
                                                            <xs:element type="xs:integer" name="volume"/>
                                                            <xs:element type="xs:decimal" name="peso"/>
                                                        </xs:sequence>
                                                    </xs:complexType>
                                                </xs:element>
                                            </xs:sequence>
                                        </xs:complexType>
                                    </xs:element>
                                </xs:sequence>
                            </xs:complexType>
                        </xs:element>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>
