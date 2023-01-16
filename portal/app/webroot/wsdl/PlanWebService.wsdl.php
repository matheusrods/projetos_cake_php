<?php
  header("Content-type: text/xml");
  echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:tns="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/PlanWebService" xmlns:xsd="http://www.w3.org/2001/XMLSchema" name="PlanService" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/PlanWebService">
    <types>
        <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/wsdl/PlanWebService" version="1.0">
            <xs:element name="Exception" type="tns:Exception" />
            <xs:element name="criarPlanoViagem" type="tns:criarPlanoViagem" />
            <xs:element name="criarPlanoViagemResponse" type="tns:criarPlanoViagemResponse" />
            <xs:element name="fmt_busca_plano_viagem_exception">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="standard" type="tns:ExchangeFaultData"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="mt_busca_plano_viagem_response" type="tns:dt_busca_plano_viagem_response"/>
            <xs:element name="mt_busca_plano_viagem_request" type="tns:dt_busca_plano_viagem_request"/>

            <xs:complexType name='criarPlanoViagem'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Plano' type='tns:planBean'/>
                    <xs:element minOccurs='0' name='Itinerario' type='tns:itineraryBean'/>
                    <xs:element minOccurs='0' name='Usuario' type='tns:userBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='planBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Motoristas' type='tns:driversBean'/>
                    <xs:element minOccurs='0' name='Cabecalho' type='tns:planHeaderBean'/>
                    <xs:element minOccurs='0' name='PlanoStatus' type='tns:planoStatusBean'/>
                    <xs:element minOccurs='0' name='PlanoValores' type='tns:planValueBean'/>
                    <xs:element minOccurs='0' name='Transportadora' type='tns:transportBean'/>
                    <xs:element minOccurs='0' name='Veiculos' type='tns:vehiclesBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='driversBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='PrimeiroMotorista' type='tns:driverBean'/>
                    <xs:element minOccurs='0' name='Remover' type='xs:string'/>
                    <xs:element minOccurs='0' name='SegundoMotorista' type='tns:driverBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='driverBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Documento' type='tns:documentoBean'/>
                    <xs:element minOccurs='0' name='Nome' type='xs:string'/>
                    <xs:element minOccurs='0' name='Tipo' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='documentoBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Numero' type='xs:string'/>
                    <xs:element minOccurs='0' name='Tipo' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='planHeaderBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='CodigoCliente' type='xs:string'/>
                    <xs:element minOccurs='0' name='NomeProcesso' type='xs:string'/>
                    <xs:element minOccurs='0' name='CodigoOperacao' type='xs:string'/>
                    <xs:element minOccurs='0' name='IdentificadorPlano' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='planoStatusBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='TipoPlano' type='xs:string'/>
                    <xs:element minOccurs='0' name='TipoRastreamento' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='planValueBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Observacao' type='xs:string'/>
                    <xs:element minOccurs='0' name='IdetificadorTemperatura' type='xs:string'/>
                    <xs:element minOccurs='0' name='ValorTotal' type='xs:string'/>
                    <xs:element minOccurs='0' name='PesoTotal' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='transportBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Entidade' type='tns:relationalBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='relationalBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Cidade' type='xs:string'/>
                    <xs:element minOccurs='0' name='CodigoExternoCliente' type='xs:string'/>
                    <xs:element minOccurs='0' name='Pais' type='xs:string'/>
                    <xs:element minOccurs='0' name='Documento' type='tns:documentoBean'/>
                    <xs:element minOccurs='0' name='NomeFantasia' type='xs:string'/>
                    <xs:element minOccurs='0' name='Ibge' type='xs:string'/>
                    <xs:element minOccurs='0' name='Latitude' type='xs:string'/>
                    <xs:element minOccurs='0' name='Logradouro' type='tns:logradouroBean'/>
                    <xs:element minOccurs='0' name='Longitude' type='xs:string'/>
                    <xs:element minOccurs='0' name='Nome' type='xs:string'/>
                    <xs:element minOccurs='0' name='Estado' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='logradouroBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Bairro' type='xs:string'/>
                    <xs:element minOccurs='0' name='Cep' type='xs:string'/>
                    <xs:element minOccurs='0' name='Complemento' type='xs:string'/>
                    <xs:element minOccurs='0' name='Endereco' type='xs:string'/>
                    <xs:element minOccurs='0' name='Numero' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='vehiclesBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='PrimeiroVeiculos' type='tns:vehicleBean'/>
                    <xs:element minOccurs='0' name='Remover' type='xs:string'/>
                    <xs:element maxOccurs='unbounded' minOccurs='0' name='Carretas' type='tns:vehicleBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='vehicleBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='CodigoDispositivo' type='xs:string'/>
                    <xs:element minOccurs='0' name='Placa' type='xs:string'/>
                    <xs:element minOccurs='0' name='NomeProvedor' type='xs:string'/>
                    <xs:element minOccurs='0' name='TipoVeiculo' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='itineraryBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Destinos' type='tns:destinationBean'/>
                    <xs:element minOccurs='0' name='Origem' type='tns:pointBean'/>
                    <xs:element minOccurs='0' name='Processo' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='destinationBean'>
                <xs:sequence>
                    <xs:element maxOccurs='unbounded' minOccurs='0' name='Destino' type='tns:pointBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='pointBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Carga' type='xs:string'/>
                    <xs:element minOccurs='0' name='Consignatario' type='tns:consiguinatarioBean'/>
                    <xs:element minOccurs='0' name='Descarga' type='xs:string'/>
                    <xs:element maxOccurs='unbounded' minOccurs='0' name='Documentos' type='tns:fiscalDocumentBean'/>
                    <xs:element minOccurs='0' name='Previsao' type='xs:string'/>
                    <xs:element minOccurs='0' name='Ordem' type='xs:int'/>
                    <xs:element minOccurs='0' name='Entidade' type='tns:relationalBean'/>
                    <xs:element maxOccurs='unbounded' minOccurs='0' name='Rotas' type='tns:rotaBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='consiguinatarioBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Documento' type='tns:documentoBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='fiscalDocumentBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Codigo' type='xs:string'/>
                    <xs:element minOccurs='0' name='Tipo' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='rotaBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Cnpj' type='xs:string'/>
                    <xs:element minOccurs='0' name='Previsao' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='userBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Nome' type='xs:string'/>
                    <xs:element minOccurs='0' name='Senha' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='criarPlanoViagemResponse'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='Resultado' type='tns:planReturnBean'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='planReturnBean'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='CodigoCliente' type='xs:string'/>
                    <xs:element minOccurs='0' name='Codigo' type='xs:string'/>
                    <xs:element minOccurs='0' name='Mensagem' type='xs:string'/>
                    <xs:element minOccurs='0' name='CodigoOperacao' type='xs:string'/>
                    <xs:element minOccurs='0' name='IdentificadorPlano' type='xs:string'/>
                    <xs:element minOccurs='0' name='NomeProcesso' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name='Exception'>
                <xs:sequence>
                    <xs:element minOccurs='0' name='message' type='xs:string'/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name="ExchangeFaultData">
                <xs:sequence>
                    <xs:element name="faultText" type="xs:string"/>
                    <xs:element name="faultUrl" type="xs:string" minOccurs="0"/>
                    <xs:element name="faultDetail" type="tns:ExchangeLogData" minOccurs="0" maxOccurs="unbounded"/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name="ExchangeLogData">
                <xs:sequence>
                    <xs:element name="severity" type="xs:string" minOccurs="0"/>
                    <xs:element name="text" type="xs:string"/>
                    <xs:element name="url" type="xs:string" minOccurs="0"/>
                    <xs:element name="id" type="xs:string" minOccurs="0"/>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name="dt_busca_plano_viagem_request">
                <xs:sequence>
                    <xs:element name="usuario">
                        <xs:annotation>
                            <xs:documentation>Usuario usado para consulta</xs:documentation>
                        </xs:annotation>
                        <xs:simpleType>
                            <xs:restriction base="xs:string" />
                        </xs:simpleType>
                    </xs:element>
                    <xs:element name="senha">
                        <xs:annotation>
                            <xs:documentation>Senha do Usuario</xs:documentation>
                        </xs:annotation>
                        <xs:simpleType>
                            <xs:restriction base="xs:string" />
                        </xs:simpleType>
                    </xs:element>
                    <xs:element name="codigoOperacao">
                        <xs:annotation>
                            <xs:documentation>Código da Operação a ser consultada</xs:documentation>
                        </xs:annotation>
                        <xs:simpleType>
                            <xs:restriction base="xs:string">
                                <xs:pattern value="\d*"/>
                                <xs:enumeration value="11"/>
                                <xs:enumeration value="228"/>
                                <xs:enumeration value="291"/>
                                <xs:enumeration value="297"/>
                                <xs:maxLength value="3"/>
                            </xs:restriction>
                        </xs:simpleType>
                    </xs:element>
                    <xs:element name="dataInicial" type="xs:dateTime">
                        <xs:annotation>
                            <xs:documentation>Data/Hora inicial para pesquisa</xs:documentation>
                        </xs:annotation>
                    </xs:element>
                    <xs:element name="dataFinal" type="xs:dateTime">
                        <xs:annotation>
                            <xs:documentation>Data/Hora final para pesquisa</xs:documentation>
                        </xs:annotation>
                    </xs:element>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name="dt_busca_plano_viagem_response">
                <xs:sequence>
                    <xs:element name="PlanoViagens" minOccurs="0">
                        <xs:annotation>
                            <xs:documentation>Lista de Planos de Viagem</xs:documentation>
                        </xs:annotation>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element name="PlanoViagem" maxOccurs="unbounded" type="tns:PlanoViagemResponseBean">
                                    <xs:annotation>
                                        <xs:documentation>Plano de Viagem</xs:documentation>
                                    </xs:annotation>
                                </xs:element>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>
                </xs:sequence>
            </xs:complexType>
            <xs:complexType name="PlanoViagemResponseBean">
                <xs:sequence>
                    <xs:element name="OrdensTransporte" minOccurs="0">
                        <xs:annotation>
                            <xs:documentation>Lista de Ordens de Transporte</xs:documentation>
                        </xs:annotation>
                        <xs:complexType>
                            <xs:sequence>
                                <xs:element name="OrdemTransporte" maxOccurs="unbounded">
                                    <xs:annotation>
                                        <xs:documentation>Ordem de Transporte</xs:documentation>
                                    </xs:annotation>
                                    <xs:complexType>
                                        <xs:sequence>
                                            <xs:element name="dataSaidaFabrica" type="xs:dateTime">
                                                <xs:annotation>
                                                    <xs:documentation>Data de Saida da Fábrica</xs:documentation>
                                                </xs:annotation>
                                            </xs:element>
                                            <xs:element name="dataChegadaCliente" type="xs:dateTime">
                                                <xs:annotation>
                                                    <xs:documentation>Data de Chegada no Cliente</xs:documentation>
                                                </xs:annotation>
                                            </xs:element>
                                            <xs:element name="dataSaidaCliente" type="xs:dateTime">
                                                <xs:annotation>
                                                    <xs:documentation>Data de Saida do Cliente</xs:documentation>
                                                </xs:annotation>
                                            </xs:element>
                                        </xs:sequence>
                                        <xs:attribute name="sequenciaPlanoViagem" type="xs:integer" use="required">
                                            <xs:annotation>
                                                <xs:documentation>Indicador de sequencia da Ordem de Transporte no Plano de Viagem</xs:documentation>
                                            </xs:annotation>
                                        </xs:attribute>
                                        <xs:attribute name="Id" use="required">
                                            <xs:annotation>
                                                <xs:documentation>Identificador da Ordem de Transporte</xs:documentation>
                                            </xs:annotation>
                                            <xs:simpleType>
                                                <xs:restriction base="xs:string">
                                                    <xs:pattern value="\d*"/>
                                                    <xs:maxLength value="10"/>
                                                </xs:restriction>
                                            </xs:simpleType>
                                        </xs:attribute>
                                    </xs:complexType>
                                </xs:element>
                            </xs:sequence>
                        </xs:complexType>
                    </xs:element>
                </xs:sequence>
                <xs:attribute name="Id" use="required">
                    <xs:annotation>
                        <xs:documentation>Identificador do Plano de Viagem</xs:documentation>
                    </xs:annotation>
                    <xs:simpleType>
                        <xs:restriction base="xs:string">
                            <xs:pattern value="\d*"/>
                            <xs:maxLength value="10"/>
                        </xs:restriction>
                    </xs:simpleType>
                </xs:attribute>
            </xs:complexType>

        </xs:schema>
    </types>
    <message name="Exception">
        <part element="tns:Exception" name="Exception" />
    </message>
    <message name="PlanWebService_criarPlanoViagemResponse">
        <part element="tns:criarPlanoViagemResponse" name="criarPlanoViagemResponse" />
    </message>
    <message name="PlanWebService_criarPlanoViagem">
        <part element="tns:criarPlanoViagem" name="criarPlanoViagem" />
    </message>
    <message name="mt_busca_plano_viagem_request">
        <documentation/>
        <part name="mt_busca_plano_viagem_request" element="tns:mt_busca_plano_viagem_request"/>
    </message>
    <message name="mt_busca_plano_viagem_response">
        <documentation/>
        <part name="mt_busca_plano_viagem_response" element="tns:mt_busca_plano_viagem_response"/>
    </message>
    <message name="fmt_busca_plano_viagem_exception">
        <documentation/>
        <part name="fmt_busca_plano_viagem_exception" element="tns:fmt_busca_plano_viagem_exception"/>
    </message>  
    <portType name="PlanWebService">
        <documentation/>
        <operation name="criarPlanoViagem" parameterOrder="criarPlanoViagem">
            <input message="tns:PlanWebService_criarPlanoViagem" />
            <output message="tns:PlanWebService_criarPlanoViagemResponse" />
            <fault message="tns:Exception" name="Exception" />
        </operation>
        <operation name="BuscaPlanoViagens">
            <documentation>Busca Plano de Viagens de acordo com parâmetros informados no SOAP Request</documentation>
            <input message="tns:mt_busca_plano_viagem_request"/>
            <output message="tns:mt_busca_plano_viagem_response"/>
            <fault name="fmt_busca_plano_viagem_exception" message="tns:fmt_busca_plano_viagem_exception"/>
        </operation>        
    </portType>
    <binding name="PlanWebServiceBinding" type="tns:PlanWebService">
        <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http" />
        <operation name="criarPlanoViagem">
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
        <operation name="BuscaPlanoViagens">
            <soap:operation soapAction="" />
            <input>
                <soap:body use="literal" />
            </input>
            <output>
                <soap:body use="literal" />
            </output>
            <fault name="fmt_busca_plano_viagem_exception">
                <soap:fault use="literal" name="fmt_busca_plano_viagem_exception" />
            </fault>
        </operation>        
    </binding>
    <service name="PlanService">
        <port binding="tns:PlanWebServiceBinding" name="PlanService">
            <soap:address location="http://<?php echo $_SERVER['HTTP_HOST']; ?>/portal/soap/planwebservice_soap" />
        </port>
    </service>
</definitions>