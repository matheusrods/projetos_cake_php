<?php
	header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_sm" attributeFormDefault="unqualified" elementFormDefault="qualified">
<xs:element name="viagem">
    <xs:complexType>
        <xs:sequence>
        	<xs:element name="autenticacao">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="token"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element type="xs:string" name="sistema_origem"/>            
            <xs:element type="xs:string" name="cnpj_cliente"/>
            <xs:element type="xs:string" name="cnpj_embarcador"/>
            <xs:element type="xs:string" name="cnpj_transportador"/>
            <xs:element type="xs:string" name="cnpj_gerenciadora_de_risco"/>
            <xs:element type="xs:string" name="pedido_cliente" nillable="true"/>
            <xs:element type="xs:string" name="numero_liberacao"/>
            <xs:element type="xs:integer" name="tipo_de_transporte"/>
            <xs:element type="xs:string" name="observacao" nillable="true"/>
            <xs:element name="controle_temperatura" nillable="true">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:integer" name="de" nillable="true"/>
                        <xs:element type="xs:integer" name="ate" nillable="true"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="motorista">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="nome"/>
                        <xs:element type="xs:string" name="cpf"/>
                        <xs:element type="xs:string" name="telefone" nillable="true"/>
                        <xs:element type="xs:string" name="radio" nillable="true"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="veiculos">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="placa" maxOccurs="unbounded" minOccurs="1"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="origem" minOccurs="1">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element type="xs:string" name="codigo_externo" />
                        <xs:element type="xs:string" name="descricao"/>
                        <xs:element type="xs:string" name="logradouro"/>
                        <xs:element type="xs:integer" name="numero"/>
                        <xs:element type="xs:string" name="complemento"/>
                        <xs:element type="xs:string" name="bairro"/>
                        <xs:element type="xs:string" name="cep"/>
                        <xs:element type="xs:string" name="cidade"/>
                        <xs:element type="xs:string" name="estado"/>
                        <xs:element type="xs:decimal" name="latitude"/>
                        <xs:element type="xs:decimal" name="longitude"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element type="xs:boolean" name="monitorar_retorno" minOccurs="1"/>
            <xs:element type="xs:dateTime" name="data_previsao_inicio" minOccurs="1"/>
            <xs:element type="xs:dateTime" name="data_previsao_fim" minOccurs="1"/>
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
            <xs:element name="iscas">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="isca" minOccurs="0">
                            <xs:complexType>
                                <xs:sequence>
                                    <xs:element type="xs:string" name="tecnologia"/>
                                    <xs:element type="xs:string" name="numero_terminal"/>
                                </xs:sequence>
                            </xs:complexType>
                        </xs:element>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="escolta">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="empresa" minOccurs="0">
                            <xs:complexType>
                                <xs:sequence>
                                    <xs:element type="xs:string" name="cnpj_empresa"/>
                                    <xs:element type="xs:string" name="nome_empresa"/>
                                    <xs:element name="veiculos">
                                        <xs:complexType>
                                            <xs:sequence>
                                                <xs:element name="veiculo">
                                                    <xs:complexType>
                                                        <xs:sequence>
                                                            <xs:element type="xs:string" name="placa"/>
                                                            <xs:element type="xs:string" name="equipe"/>
                                                            <xs:element type="xs:string" name="telefone"/>
                                                            <xs:element type="xs:string" name="numero_terminal"/>
                                                            <xs:element type="xs:string" name="tecnologia"/>
                                                            <xs:element type="xs:string" name="versao"/>
                                                            <xs:element type="xs:string" name="armada"/>
                                                            <xs:element type="xs:string" name="velada"/>
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
            <xs:element type="xs:string" name="tipo_pgr" nillable="true"/>
        </xs:sequence>
    </xs:complexType>
</xs:element>
</xs:schema>
