<?php
  header("Content-type: text/xml");
  echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:tns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/consulta_profissional" xmlns:xsd="http://www.w3.org/2001/XMLSchema" name="consulta_profissional" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/consulta_profissional">
    <types>
        <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/consulta_profissional" version="1.0">
            <xs:element name="Exception" type="tns:Exception" />
            <xs:element name="consulta" type="tns:consulta" />
            <xs:element name="consultaResponse" type="tns:consultaResponse" />
            <xs:complexType name='consulta'>
                <xs:sequence>
                    <xs:element name='cnpj_cliente' type='tns:cnpj'/>
                    <xs:element name='autenticacao' type='tns:autenticacao'/>
                    <xs:element name='produto' type='tns:produto'/>
                    <xs:element name='profissional' type='tns:profissional'/>
                    <xs:element name='veiculos'>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element maxOccurs='unbounded' name='placa' type='tns:placa'/>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>
                    <xs:element name='carga_tipo' type='tns:tipo_carga'/>
                    <xs:element name='carga_valor' type='tns:valor'/>
                    <xs:element name='pais_origem' type='tns:pais'/>
                    <xs:element name='uf_origem' type='tns:uf'/>
                    <xs:element name='cidade_origem' type='tns:cidade'/>
                    <xs:element name='pais_destino' type='tns:pais'/>
                    <xs:element name='uf_destino' type='tns:uf'/>
                    <xs:element name='cidade_destino' type='tns:cidade'/>
                </xs:sequence>
            </xs:complexType>
            <xs:simpleType name="cnpj">
                <xs:restriction base="xs:string">
                    <xs:minLength value="14"/>
                    <xs:maxLength value="14"/>
                </xs:restriction>
            </xs:simpleType>            
            <xs:complexType name='autenticacao'>
                <xs:sequence>
                    <xs:element name='token' type='tns:token'/>
                </xs:sequence>
            </xs:complexType>
            <xs:simpleType name="token">
                <xs:restriction base="xs:string">
                    <xs:minLength value="32"/>
                    <xs:maxLength value="32"/>
                </xs:restriction>
            </xs:simpleType>                  
            <xs:simpleType name="produto">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="22"/>
                </xs:restriction>
            </xs:simpleType>             
            <xs:simpleType name="tipo_carga">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="2"/>
                    <xs:enumeration value="1"/>
                    <xs:enumeration value="2"/>
                    <xs:enumeration value="3"/>
                    <xs:enumeration value="4"/>
                    <xs:enumeration value="5"/>
                    <xs:enumeration value="6"/>
                    <xs:enumeration value="7"/>
                    <xs:enumeration value="8"/>
                    <xs:enumeration value="9"/>
                    <xs:enumeration value="10"/>
                    <xs:enumeration value="11"/>
                    <xs:enumeration value="12"/>
                    <xs:enumeration value="13"/>
                    <xs:enumeration value="14"/>
                    <xs:enumeration value="15"/>
                    <xs:enumeration value="16"/>
                    <xs:enumeration value="17"/>
                    <xs:enumeration value="18"/>
                    <xs:enumeration value="19"/>
                    <xs:enumeration value="20"/>
                    <xs:enumeration value="21"/>
                    <xs:enumeration value="22"/>
                    <xs:enumeration value="23"/>
                    <xs:enumeration value="24"/>
                    <xs:enumeration value="25"/>
                    <xs:enumeration value="26"/>
                    <xs:enumeration value="27"/>
                    <xs:enumeration value="28"/>
                    <xs:enumeration value="29"/>
                    <xs:enumeration value="30"/>
                    <xs:enumeration value="31"/>
                    <xs:enumeration value="32"/>
                    <xs:enumeration value="33"/>
                    <xs:enumeration value="34"/>
                    <xs:enumeration value="35"/>
                    <xs:enumeration value="36"/>
                    <xs:enumeration value="37"/>
                    <xs:enumeration value="38"/>
                    <xs:enumeration value="39"/>
                    <xs:enumeration value="40"/>
                    <xs:enumeration value="41"/>
                    <xs:enumeration value="42"/>
                    <xs:enumeration value="43"/>
                    <xs:enumeration value="44"/>
                    <!-- 1 - TIPO DE CARGA 1 -->
                    <!-- 2 - TIPO DE CARGA 2 -->
                    <!-- 3 - DIVERSOS -->
                    <!-- 4 - ALUMINIO -->
                    <!-- 5 - BEBIDAS -->
                    <!-- 6 - ELETRO/ELETRONICOS -->
                    <!-- 7 - COBRE -->
                    <!-- 8 - ARROZ -->
                    <!-- 9 - SOJA -->
                    <!-- 10 - PAPEL -->
                    <!-- 11 - BOBINAS DE ACO -->
                    <!-- 12 - CIGARROS -->
                    <!-- 13 - MEDICAMENTOS -->
                    <!-- 14 - CAFE -->
                    <!-- 15 - PROD. ALIMENTICIOS -->
                    <!-- 16 - PROD. FRIGORIFICOS -->
                    <!-- 17 - PROD. QUIMICOS -->
                    <!-- 18 - ACUCAR -->
                    <!-- 19 - BOBINAS -->
                    <!-- 20 - ALGODAO EM PLUMA -->
                    <!-- 21 - LEITE -->
                    <!-- 22 - CHAPAS DE ACO -->
                    <!-- 23 - PRODUTOS SIDERURGICOS -->
                    <!-- 24 - OLEO DE SOJA -->
                    <!-- 25 - SEMENTES -->
                    <!-- 26 - POLIETILENO -->
                    <!-- 27 - VERGALHAO -->
                    <!-- 28 - MAQUINAS EM GERAL -->
                    <!-- 29 - BICABORNATO DE SODIO -->
                    <!-- 30 - TRIGO -->
                    <!-- 31 - PRODUTOS AGRICOLAS -->
                    <!-- 32 - ACO -->
                    <!-- 33 - FERRO -->
                    <!-- 34 - PRODUTOS DE HIGIENE E LIMPEZA -->
                    <!-- 35 - TECIDOS -->
                    <!-- 36 - VIDRO -->
                    <!-- 37 - CHAPAS DE MDF -->
                    <!-- 38 - LAMINADOS -->
                    <!-- 39 - CIMENTO -->
                    <!-- 40 - TINTAS -->
                    <!-- 41 - CARGA FRACIONADA -->
                    <!-- 42 - OUTROS -->
                    <!-- 43 - COURO -->
                    <!-- 44 - ALGODAO -->
                </xs:restriction>
            </xs:simpleType>                   
            <xs:complexType name='profissional'>
                <xs:sequence>
                    <xs:element name='documento' type='tns:cpf'/>
                    <xs:element name='carreteiro' type='xs:string' minOccurs='0' />
                </xs:sequence>
            </xs:complexType>
            <xs:simpleType name="cpf">
                <xs:restriction base="xs:string">
                    <xs:minLength value="11"/>
                    <xs:maxLength value="11"/>
                </xs:restriction>
            </xs:simpleType>     
            <xs:simpleType name="placa">
                <xs:restriction base="xs:string">
                    <xs:minLength value="8"/>
                    <xs:maxLength value="8"/>
                </xs:restriction>
            </xs:simpleType>   
            <xs:simpleType name="valor">
                <xs:restriction base="xs:decimal">
                    <xs:totalDigits value="10"/>
                    <xs:fractionDigits value="2"/>
                </xs:restriction>
            </xs:simpleType>
            <xs:simpleType name="pais">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="3"/>
                </xs:restriction>
            </xs:simpleType>   
            <xs:simpleType name="uf">
                <xs:restriction base="xs:string">
                    <xs:minLength value="2"/>
                    <xs:maxLength value="2"/>
                </xs:restriction>
            </xs:simpleType>   
            <xs:simpleType name="cidade">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="200"/>
                </xs:restriction>
            </xs:simpleType>   
            <xs:complexType name='consultaResponse'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='retorno' type='tns:retorno'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='retorno'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='consulta' type='xs:string'/>
                    <xs:element minOccurs='0' name='status' type='xs:string'/>
                    <xs:element minOccurs='0' name='mensagem' type='xs:string'/>
                    <xs:element minOccurs='0' name='validade' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='Exception'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='erro' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
        </xs:schema>
    </types>
    <message name="Exception">
        <part element="tns:Exception" name="Exception" />
    </message>
    <message name="consulta_profissional_consultaResponse">
        <part element="tns:consultaResponse" name="consultaResponse" />
    </message>
    <message name="consulta_profissional_consulta">
        <part element="tns:consulta" name="consulta" />
    </message>
  
    <portType name="consulta_profissional">
        <operation name="consultarProfissional" parameterOrder="consultarProfissional">
            <input message="tns:consulta_profissional_consulta" />
            <output message="tns:consulta_profissional_consultaResponse" />
            <fault message="tns:Exception" name="Exception" />
        </operation>
    </portType>
    <binding name="consulta_profissionalBinding" type="tns:consulta_profissional">
        <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
        <operation name="consultarProfissional">
            <soap:operation soapAction="" />
            <input>
                <soap:body use="literal" />
            </input>
            <output>
                <soap:body use="literal" />
            </output>
            <fault name="Exception">
                <soap:fault name="Exception" use="literal" />
            </fault>
        </operation>
    </binding>
    <service name="consulta_profissional_service">
        <port binding="tns:consulta_profissionalBinding" name="consulta_profissional_service">
            <soap:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/consulta_profissional_soap" />
        </port>
    </service>
</definitions>