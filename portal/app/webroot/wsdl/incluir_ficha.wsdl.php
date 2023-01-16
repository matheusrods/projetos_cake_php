<?php
  header("Content-type: text/xml");
  echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:tns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_ficha" xmlns:xsd="http://www.w3.org/2001/XMLSchema" name="incluir_ficha" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_ficha">
    <types>
        <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/incluir_ficha" version="1.0" attributeFormDefault="unqualified" elementFormDefault="qualified">
            <xs:element name="Exception" type="tns:Exception" />
            <xs:element name="consultaFabricantes" type="tns:consultaFabricantes" />
            <xs:element name="consultaFabricantesResponse" type="tns:consultaFabricantesResponse" />
            <xs:element name="consultaModelos" type="tns:consultaModelos" />
            <xs:element name="ficha" type="tns:ficha" />
            <xs:element name="fichaResponse" type="tns:fichaResponse" />           
            <xs:complexType name='consultaFabricantes'>
                <xs:sequence>
                    <xs:element name='descricao' minOccurs='0' type='xs:string' />
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='consultaModelos'>
                <xs:sequence>
                    <xs:element name='fabricante' type='xs:string' />
                    <xs:element name='descricao' minOccurs='0' type='xs:string' />
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='consultaFabricantesResponse'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='fabricante' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='ficha'>
                <xs:sequence>
                    <xs:element name='autenticacao' type='tns:autenticacao'/>
                    <xs:element name='cnpj_cliente' type='tns:cnpj'/>
                    <xs:element name='produto' type='tns:produto'/>
                    <xs:element name='cnpj_embarcador' type='tns:cnpj'/>
                    <xs:element name='cnpj_transportador' type='tns:cnpj'/>
                    <xs:element name='observacao' type='xs:string'/>
                    <xs:element name='carga_tipo' type='xs:integer'/>
                    <xs:element name='carga_valor' type='tns:valor_moeda'/>
                    <xs:element name='pais_origem' type='tns:pais'/>
                    <xs:element name='uf_origem' type='tns:uf'/>
                    <xs:element name='cidade_origem' type='tns:cidade'/>
                    <xs:element name='pais_destino' type='tns:pais'/>
                    <xs:element name='uf_destino' type='tns:uf'/>
                    <xs:element name='cidade_destino' type='tns:cidade'/>
                    <xs:element name='profissional' type='tns:profissional'/>
                    <xs:element name='veiculos'>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element maxOccurs='3' name='veiculo' type='tns:veiculo'/>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>

                    <xs:element name='complementares' type='tns:complementares'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='profissional'>
                <xs:sequence>
                    <xs:element name='categoria' type='xs:integer'/>
                    <xs:element name='documento' type='tns:cpf'/>
                    <xs:element name='nome' type='xs:string'/>
                    <xs:element name='nome_pai' minOccurs='0' type='xs:string'/>
                    <xs:element name='nome_mae' type='xs:string'/>
                    <xs:element name='rg' type='tns:rg'/>
                    <xs:element name='uf_rg' type='tns:uf'/>
                    <xs:element name='data_emissao' type='xs:date'/>
                    <xs:element name='data_nascimento' type='xs:date'/>                     
                    <xs:element name='cidade_naturalidade' type='tns:cidade'/>
                    <xs:element name='uf_naturalidade' type='tns:uf'/>
                    <xs:element name='pais_naturalidade' type='tns:pais'/>
                    <xs:element name='cnh' minOccurs='0' type='tns:cnh_dados'/>
                    <xs:element name='endereco' type='tns:endereco_dados'/>
                    <xs:element name='contatos' minOccurs='0'>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element maxOccurs='unbounded' name='contato' type='tns:contato_dados'/>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>

                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='veiculo'>
                <xs:sequence>
                    <xs:element name='placa' type='tns:placa'/>
                    <xs:element name='chassi' minOccurs='0' type='tns:string_20'/>
                    <xs:element name='renavam' minOccurs='0' type='tns:string_20'/>
                    <xs:element name='pais_emplacamento' minOccurs='0' type='tns:pais'/>
                    <xs:element name='uf_emplacamento' minOccurs='0' type='tns:uf'/>
                    <xs:element name='cidade_emplacamento' minOccurs='0' type='tns:cidade'/>
                    <xs:element name='veiculo_cor' minOccurs='0' type='tns:string_20'/>
                    <xs:element name='ano_fabricacao' minOccurs='0' type='tns:ano'/>
                    <xs:element name='modelo' minOccurs='0' type='tns:string_50'/>
                    <xs:element name='ano_modelo' minOccurs='0' type='tns:ano'/>
                    <xs:element name='fabricante' minOccurs='0' type='tns:string_100'/>
                    <xs:element name='tecnologia' minOccurs='0' type='tns:string_20'/>
                    <xs:element name='proprietario' minOccurs='0' type='tns:proprietario'/>
                </xs:sequence>
            </xs:complexType>            
            <xs:complexType name='proprietario'>
                <xs:sequence>
                    <xs:element name='documento' type='tns:cpf'/>
                    <xs:element name='razao_social' type='tns:string_30'/>
                    <xs:element name='rg_ie' minOccurs='0' type='tns:integer_12'/>
                    <xs:element name='rntrc' minOccurs='0' type='tns:integer_10'/>
                    <xs:element name='endereco' minOccurs='0' type='tns:endereco_dados'/>
                    <xs:element name='contatos' minOccurs='0'>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element maxOccurs='unbounded' name='contato' type='tns:contato_dados2'/>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>                    
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='complementares'>
                <xs:sequence>
                    <xs:element name='foi_vitima_de_roubo' type='tns:sim_nao'/>
                    <xs:element name='quantidade_vezes_roubado' minOccurs='0' type='xs:integer'/>
                    <xs:element name='ja_sofreu_acidente' type='tns:sim_nao'/>
                    <xs:element name='quantidade_acidentes' minOccurs='0' type='xs:integer'/>
                    <xs:element name='transportou_empresa' type='tns:tipo_tempo_transportou_empresa'/>
                    <xs:element name='quantidade_tempo_transportou_para_a_empresa' minOccurs='0' type='xs:integer'/>
                    <xs:element name='possui_sistema_rastreamento' type='tns:sim_nao'/>
                    <xs:element name='nome_sistema_rastreamento' minOccurs='0' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>  
            <xs:complexType name='autenticacao'>
                <xs:sequence>
                    <xs:element name='token' type='tns:token'/>
                </xs:sequence>
            </xs:complexType>            
            <xs:complexType name='cnh_dados'>
                <xs:sequence>
                    <xs:element name='cnh' type='tns:string_20'/>
                    <xs:element name='categoria' type='tns:string_2'/>
                    <xs:element name='vencimento' type='xs:date'/>
                    <xs:element name='uf_emissao' type='tns:uf'/>
                    <xs:element name='data_primeira_cnh' minOccurs='0' type='xs:date'/>
                    <xs:element name='codigo_seguranca' minOccurs='0' type='tns:integer_20'/>
                    <xs:element name='data_inicio_mopp' minOccurs='0' type='xs:date'/>
                </xs:sequence>
            </xs:complexType>            
            <xs:complexType name='endereco_dados'>
                <xs:sequence>
                    <xs:element name='uf' type='tns:uf'/>
                    <xs:element name='cidade' type='tns:cidade'/>
                    <xs:element name='cep' type='tns:cep'/>
                    <xs:element name='bairro' type='tns:string_30'/>
                    <xs:element name='logradouro' type='tns:string_100'/>
                    <xs:element name='numero' type='tns:string_10'/>
                    <xs:element name='complemento' minOccurs='0' type='tns:string_10'/>
                </xs:sequence>
            </xs:complexType> 
            <xs:complexType name='contato_dados'>
                <xs:sequence>
                    <xs:element name='nome' type='tns:string_50'/>
                    <xs:element name='tipo_contato' type='tns:tipo_contato'/>
                    <xs:element name='tipo_retorno' type='tns:tipo_retorno'/>
                    <xs:element name='descricao' type='tns:string_100'/>
                </xs:sequence>
            </xs:complexType>      
            <xs:complexType name='contato_dados2'>
                <xs:sequence>
                    <xs:element name='nome' type='tns:string_50'/>
                    <xs:element name='tipo_contato' type='tns:tipo_contato'/>
                    <xs:element name='tipo_retorno' type='tns:tipo_retorno2'/>
                    <xs:element name='descricao' type='tns:string_100'/>
                </xs:sequence>
            </xs:complexType>      

            <xs:simpleType name="tipo_contato">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="1"/>
                    <xs:enumeration value="1"/>
                    <xs:enumeration value="2"/>
                    <xs:enumeration value="7"/>
                    <!-- 1 - RESIDENCIAL -->
                    <!-- 2 - COMERCIAL -->
                    <!-- 7 - REFERENCIA -->
                </xs:restriction>
            </xs:simpleType>      
            <xs:simpleType name="tipo_retorno">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="1"/>
                    <xs:enumeration value="1"/>
                    <xs:enumeration value="5"/>
                    <!-- 1 - TELEFONE -->
                    <!-- 5 - CELULAR MOTORISTA -->
                </xs:restriction>
            </xs:simpleType>   
            <xs:simpleType name="tipo_retorno2">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="1"/>
                    <xs:enumeration value="1"/>
                    <xs:enumeration value="2"/>
                    <xs:enumeration value="4"/>
                    <xs:enumeration value="6"/>
                    <!-- 1 - TELEFONE -->
                    <!-- 2 - E-MAIL -->
                    <!-- 4 - 0800 -->
                    <!-- 6 - RADIO -->
                </xs:restriction>
            </xs:simpleType>               
            <xs:simpleType name="tipo_tempo_transportou_empresa">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="2"/>
                    <xs:enumeration value="5"/>
                    <xs:enumeration value="6"/>
                    <xs:enumeration value="7"/>
                    <xs:enumeration value="40"/>
                    <!-- 5 - Anos -->
                    <!-- 6 - Meses -->
                    <!-- 7 - Vezes -->
                    <!-- 40 - Nunca Transportou -->
                </xs:restriction>
            </xs:simpleType>               
            <xs:simpleType name="sim_nao">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="1"/>
                    <xs:enumeration value="S"/>
                    <xs:enumeration value="N"/>
                </xs:restriction>
            </xs:simpleType>               
            <xs:simpleType name="cnpj">
                <xs:restriction base="xs:string">
                    <xs:minLength value="14"/>
                    <xs:maxLength value="14"/>
                </xs:restriction>
            </xs:simpleType>            
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
            <xs:simpleType name="cpf">
                <xs:restriction base="xs:string">
                    <xs:minLength value="11"/>
                    <xs:maxLength value="11"/>
                </xs:restriction>
            </xs:simpleType>     
            <xs:simpleType name="rg">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="8"/>
                </xs:restriction>
            </xs:simpleType>               
            <xs:simpleType name="placa">
                <xs:restriction base="xs:string">
                    <xs:minLength value="8"/>
                    <xs:maxLength value="8"/>
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
            <xs:simpleType name="cep">
                <xs:restriction base="xs:string">
                    <xs:pattern value="[0-9]{8}"/>
                </xs:restriction>
            </xs:simpleType>    
            <xs:simpleType name="ano">
                <xs:restriction base="xs:integer">
                    <xs:totalDigits value="4"/>
                </xs:restriction>
            </xs:simpleType>             
            <xs:simpleType name="string_100">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="100"/>
                </xs:restriction>
            </xs:simpleType> 
            <xs:simpleType name="string_50">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="50"/>
                </xs:restriction>
            </xs:simpleType>  
            <xs:simpleType name="string_30">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="30"/>
                </xs:restriction>
            </xs:simpleType>   
            <xs:simpleType name="string_20">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="20"/>
                </xs:restriction>
            </xs:simpleType>                             
            <xs:simpleType name="string_10">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="10"/>
                </xs:restriction>
            </xs:simpleType>               
            <xs:simpleType name="string_2">
                <xs:restriction base="xs:string">
                    <xs:minLength value="1"/>
                    <xs:maxLength value="2"/>
                </xs:restriction>
            </xs:simpleType>  
            <xs:simpleType name="string_fix_20">
                <xs:restriction base="xs:string">
                    <xs:minLength value="20"/>
                    <xs:maxLength value="20"/>
                </xs:restriction>
            </xs:simpleType>             
            <xs:simpleType name="integer_20">
                <xs:restriction base="xs:integer">
                    <xs:totalDigits value="20"/>
                </xs:restriction>
            </xs:simpleType>              
            <xs:simpleType name="integer_12">
                <xs:restriction base="xs:integer">
                    <xs:totalDigits value="12"/>
                </xs:restriction>
            </xs:simpleType>    
            <xs:simpleType name="integer_10">
                <xs:restriction base="xs:integer">
                    <xs:totalDigits value="10"/>
                </xs:restriction>
            </xs:simpleType>    
            <xs:simpleType name="valor_moeda">
                <xs:restriction base="xs:decimal">
                    <xs:totalDigits value="10"/>
                    <xs:fractionDigits value="2"/>
                </xs:restriction>
            </xs:simpleType>
            <xs:complexType name='fichaResponse'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='retorno' type='tns:retorno'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='retorno'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='sucesso' type='xs:string'/>
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
    <message name="incluir_ficha_fichaResponse">
        <part element="tns:fichaResponse" name="fichaResponse" />
    </message>
    <message name="incluir_ficha_ficha">
        <part element="tns:ficha" name="ficha" />
    </message>
    <message name="consultar_fabricantes_consultaResponse">
        <part name="consultaFabricantesResponse" />
    </message>
    <message name="consultar_fabricantes_consulta">
        <part element="tns:consultaFabricantes" name="consultaFabricantes" />
    </message>
    <message name="consultar_modelos_consultaResponse">
        <part name="consultaModelosResponse" />
    </message>
    <message name="consultar_modelos_consulta">
        <part element="tns:consultaModelos" name="consultaModelos" />
    </message>
  
    <portType name="incluir_ficha">
        <operation name="IncluirFicha" parameterOrder="IncluirFicha">
            <input message="tns:incluir_ficha_ficha" />
            <output message="tns:incluir_ficha_fichaResponse" />
            <fault message="tns:Exception" name="Exception" />
        </operation>
    </portType>
    <portType name="consultar_fabricantes">
        <operation name="ConsultarFabricantes" parameterOrder="ConsultarFabricantes">
            <input message="tns:consultar_fabricantes_consulta" />
            <output message="tns:consultar_fabricantes_consultaResponse" />
            <fault message="tns:Exception" name="Exception" />
        </operation>
    </portType>    
    <portType name="consultar_modelos">
        <operation name="ConsultarModelos" parameterOrder="ConsultarModelos">
            <input message="tns:consultar_modelos_consulta" />
            <output message="tns:consultar_modelos_consultaResponse" />
            <fault message="tns:Exception" name="Exception" />
        </operation>
    </portType>    
    <binding name="incluir_fichaBinding" type="tns:incluir_ficha">
        <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
        <operation name="IncluirFicha">
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
    <binding name="consultar_fabricantesBinding" type="tns:consultar_fabricantes">
        <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
        <operation name="ConsultarFabricantes">
            <soap:operation style="document" />
            <input name="ConsultarFabricantesRequest">
                <soap:body use="literal" />
            </input>
            <output name="ConsultarFabricantesResponse">
                <soap:body use="literal" />
            </output>
            <fault name="Exception">
                <soap:fault name="Exception" use="literal" />
            </fault>
        </operation>
    </binding>
    <binding name="consultar_modelosBinding" type="tns:consultar_modelos">
        <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
        <operation name="ConsultarModelos">
            <soap:operation style="document" />
            <input name="ConsultarModelosRequest">
                <soap:body use="literal" />
            </input>
            <output name="ConsultarModelosResponse">
                <soap:body use="literal" />
            </output>
            <fault name="Exception">
                <soap:fault name="Exception" use="literal" />
            </fault>
        </operation>
    </binding>
    <service name="incluir_ficha_service">
        <port binding="tns:incluir_fichaBinding" name="incluir_ficha_service">
            <soap:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/incluir_ficha_soap" />
        </port>
        <port binding="tns:consultar_fabricantesBinding" name="consultar_fabricantes_service">
            <soap:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/incluir_ficha_soap" />
        </port>
        <port binding="tns:consultar_modelosBinding" name="consultar_modelos_service">
            <soap:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/incluir_ficha_soap" />
        </port>
    </service>
</definitions>