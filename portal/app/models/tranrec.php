<?php

class Tranrec extends AppModel {

    var $name = 'Tranrec';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'tranrec';
    var $primaryKey = 'numero';
    var $actsAs = array('Secure');

    const STATUS_EM_ABERTO = 1;
    const STATUS_PAGO = 2;

    const AGRP_CLIENTE = 1;
    const AGRP_CORRETORA = 2;
    const AGRP_FILIAL = 3;
    const AGRP_SEGURADORA = 4;

    function listAgrupamentos() {
        return array(
            self::AGRP_CLIENTE => 'Cliente',
            self::AGRP_CORRETORA => 'Corretora',
            self::AGRP_FILIAL => 'Filial',
            self::AGRP_SEGURADORA => 'Seguradora',
        );
    }

    function listStatus() {
        return array(
            self::STATUS_EM_ABERTO => 'Em aberto',
            self::STATUS_PAGO => 'Pago',
        );
    }

    function buscaDataPagamentoRPS($emitente,$numero_rps){
        $condicao_emitente = array('emitente' => $emitente);
        $condicao_numero = array('numero' => str_pad($numero_rps, 6, '0', STR_PAD_LEFT));
        $condicao_data_pagamento = array('not' => array('dtpagto' => NULL));
        $condicoes = array_merge($condicao_numero,$condicao_emitente,$condicao_data_pagamento);
        return $this->find('first',array('fields' => 'dtpagto','conditions' => $condicoes));
    }
    
    function buscaCodigoBancoPorRPS($numero_rps,$codigo_empresa) {
        $this->bindModel(array('belongsTo' => array('Banco' => array('className' => 'Banco', 'foreignKey' => 'banco'))));
        $resultado = $this->find('first',array('conditions' => array('Tranrec.numero' => str_pad($numero_rps, 6, '0', STR_PAD_LEFT), 'Tranrec.empresa' => $codigo_empresa)));
        if ($resultado) 
            return $resultado['Banco']['banco'];
            
        return false;
    }
    
    function linkBoleto($codigo_banco, $cnpj_cliente, $numero_notafis) {
        $link = null;
        if ($codigo_banco == '341')
            $link = $this->linkItau($cnpj_cliente);
        if ($codigo_banco == '001')
            $link = $this->linkBB($cnpj_cliente, $numero_notafis);
        if ($codigo_banco == '033')
            $link = $this->linkSantander($cnpj_cliente, $numero_notafis);
        return $link;
    }
    
    function linkItau($cnpj_cliente){
        $hash = Comum::encriptarLink('boleto_341|'.$cnpj_cliente);
        $link = "<a href='";
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
        $link .= "http://{$host}/portal/clientes/gerar_boleto?key=".urlencode($hash);
        $link .= "'>Visualizar</a>";
        return $link;
    }
    
    function geraFormularioItau($link){
        $link = explode("|", $link);
        $cmd = 'java -cp ' . APP . 'vendors' . DS . 'itau Itau ' . $link[1];
        $hash = Comum::getShellExec($cmd);
        $link = '<form method="post" action="https://ww2.itau.com.br/2viabloq/pesquisa.asp" name="form" id="boletoItau"><input type="hidden" name="DC" value="'.$hash.'"><input type="submit" name="1via" value="Redirecionando para o site do banco. Se isso não ocorer, clique aqui."></form>';
        return $link;
    }
    
    function linkBB($cnpj_cliente, $numero_notafis) {
        $hash = Comum::encriptarLink('boleto_001|'.$cnpj_cliente.'|'.$numero_notafis);
        $link = '<a href="';
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "rhhealth.localhost"));
        $link .= "http://{$host}/portal/clientes/gerar_boleto?key=".urlencode($hash);
        $link .= '">Visualizar</a>';
        return $link;
    }
    
    function linkSantander($cnpj_cliente, $numero_notafis) {
        $hash = Comum::encriptarLink('boleto_033|'.$cnpj_cliente.'|'.$numero_notafis);
        $link = '<a href="';
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "rhhealth.localhost"));
        $link .= "http://{$host}/portal/clientes/gerar_boleto?key=".urlencode($hash);
        $link .= '">Visualizar</a>';
        return $link;
    }
    
    function linkBoletoPago($link){
        $retorno = null;
        $dados   = explode('|', $link);
        if(count($dados) < 3)
            return NULL;
        $numero_rps = isset($dados[2])?$dados[2]:NULL;
        $retorno    = $this->find('first',array('fields'=>array('Tranrec.seq'),'conditions' => array('Tranrec.numero'=>str_pad($numero_rps, 6, '0', STR_PAD_LEFT),'Tranrec.seq'=>'02')));
        return $retorno;
    }


    function dadosBoleto($link) {
        $retorno = null;

        $dados   = explode('|', $link);
        if(count($dados) < 3)
            return NULL;

        $tipo           = isset($dados[0])?$dados[0]:NULL;
        $cnpj_cliente   = isset($dados[1])?$dados[1]:NULL;
        $numero_rps     = isset($dados[2])?$dados[2]:NULL;
        //list($tipo, $cnpj_cliente, $numero_rps) = explode('|', $link);
        
        $this->bindModel(array('belongsTo' => array('Banco' => array('className' => 'Banco', 'foreignKey' => 'banco'))));
        $tranrec = $this->find('first',array('conditions' => array('Tranrec.debcred' => 'C', 'Tranrec.numero' => str_pad($numero_rps, 6, '0', STR_PAD_LEFT))));
        //$tranrec = $this->find('first',array('conditions' => array('Tranrec.emitente' => '0000007093', 'Tranrec.ordem' => '04')));
           
            if ($tranrec) {
                $ClienteEndereco = ClassRegistry::init('ClienteEndereco');
                $ParametroBoleto = ClassRegistry::init('ParametroBoleto');
                $parametros = $ParametroBoleto->find('first');
                $informacoes_cliente = explode("\n", $parametros['ParametroBoleto']['informacoes_cliente']);
                $instrucoes_caixa = explode("\n", $parametros['ParametroBoleto']['instrucoes_caixa']);
                $informacoes = array_merge($informacoes_cliente, $instrucoes_caixa);
                $enderecos = $ClienteEndereco->listaEnderecoByCodigoCliente(intval($tranrec['Tranrec']['emitente']), TipoContato::TIPO_CONTATO_FINANCEIRO);
                $data_documento = $tipo == 'boleto_033' || $tipo == 'boleto_santander' ? $tranrec['Tranrec']['dtemiss']: $tranrec['Tranrec']['dtbord'];
                if (count($enderecos) < 1) {
                    $enderecos = $ClienteEndereco->listaEnderecoByCodigoCliente(intval($tranrec['Tranrec']['emitente']), TipoContato::TIPO_CONTATO_COMERCIAL);
                }
                if (count($enderecos) > 0) {
                    $endereco = $enderecos[0];
                    $convenio = '1216390';
                    $retorno = array(
                        "dias_de_prazo_para_pagamento" => "",
                        "taxa_boleto" => $parametros['ParametroBoleto']['taxa_boleto'],
                        "valor_cobrado" => number_format($tranrec['Tranrec']['valor'],2,'.',''),
                        "valor_boleto" => number_format($tranrec['Tranrec']['valor'],2,'.',''),
                        
                        "nosso_numero" => substr($tranrec['Tranrec']['nossonum'],7),
                        // "nosso_numero" => substr($tranrec['Tranrec']['numbanc'],7),

                        "numero_documento" => str_pad(intval($tranrec['Tranrec']['numero']),6, "0", STR_PAD_LEFT).$parametros['ParametroBoleto']['numero_documento'],
                        "data_vencimento" => substr($tranrec['Tranrec']['dtvencto'],0,10),
                        "data_documento" => substr($data_documento,0,10),
                        "data_processamento" => substr($tranrec['Tranrec']['dtlan'],0,10),
                        "sacado" => $tranrec['Tranrec']['razao'],
                        "endereco1" => $endereco['ClienteEndereco']['logradoura'].', '.$endereco['ClienteEndereco']['numero'].(empty($endereco['ClienteEndereco']['endereco_complemento']) ? '' : ' - ' . $endereco['ClienteEndereco']['endereco_complemento']),
                        "endereco2" => $endereco['ClienteEndereco']['bairro'].' - '.$endereco['ClienteEndereco']['cidade'].' - '.$endereco['ClienteEndereco']['estado_abreviacao'].' - '.$endereco['ClienteEndereco']['cep'],
                        "demonstrativo1" => (isset($informacoes[0]) ? $informacoes[0] : ''),
                        "demonstrativo2" => (isset($informacoes[1]) ? $informacoes[1] : ''),
                        "demonstrativo3" => (isset($informacoes[2]) ? $informacoes[2] : ''),
                        "instrucoes1" => (isset($informacoes[3]) ? $informacoes[3] : ''),
                        "instrucoes2" => (isset($informacoes[4]) ? $informacoes[4] : ''),
                        "instrucoes3" => (isset($informacoes[5]) ? $informacoes[5] : ''),
                        "instrucoes4" => (isset($informacoes[6]) ? $informacoes[6] : ''),
                        "quantidade" => "",
                        "valor_unitario" => "",
                        "aceite" => $parametros['ParametroBoleto']['aceite'],
                        "especie" => $parametros['ParametroBoleto']['especie'],
                        "especie_doc" => $parametros['ParametroBoleto']['especie_doc'],
                        "agencia" => intval($tranrec['Banco']['agencia']).'-'.$tranrec['Banco']['digagen'],
                        "conta" => intval($tranrec['Banco']['conta']).'-'.$tranrec['Banco']['digiconta'],
                        "convenio" => $convenio,
                        "contrato" => $parametros['ParametroBoleto']['contrato'],
                        "carteira" => $tranrec['Tranrec']['cartbanco'],
                        "variacao_carteira" => '',
                        "formatacao_convenio" => strlen($convenio),
                        "formatacao_nosso_numero" => ((strlen($convenio) <= 5) ? 1 : 2),
                        "identificacao" => "Buonny Projetos e Serviços Ltda.",
                        "cpf_cnpj" => "06326025000166",
                        "endereco" => "Alameda dos Guatas, 191",
                        "cidade_uf" => "SAO PAULO SP",
                        "cedente" => "BUONNY PROJETOS E SERVICOS DE RISCOS SEC",
                    );
                    if(substr($link, 0, 10) == 'boleto_033' || substr($link, 0, 16) == 'boleto_santander' ) {
                        $retorno["carteira"] = '101';
                        $retorno["carteira_descricao"] = "101 - RÁPIDA COM REGISTRO";
                        $retorno["codigo_cliente"] = "0962910";
                        $retorno["nosso_numero"] = $tranrec['Tranrec']['nossonum'];//.'0';//$tranrec['Tranrec']['ordem'];
                        $retorno["ponto_venda"] = intval($tranrec['Banco']['agencia']).$tranrec['Banco']['digagen'];
                        $retorno["valor_boleto"] = preg_replace( '#[^0-9]#', ',', $retorno["valor_boleto"]);
                        $retorno["sacado"] .= ' '.$cnpj_cliente;
                        $retorno["numero_documento"] = str_pad(intval($tranrec['Tranrec']['numero']),6, "0", STR_PAD_LEFT);
                    }
                }
            }
            return $retorno;
        }
    
    function prazoMedioRecebimento($filtros) {
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $this->FornecNaveg = ClassRegistry::init('FornecNaveg');
        
        if ($this->useDbConfig != 'test_suite') {
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->FornecNaveg->databaseTable = 'dbNavegarqLider';
                $this->LojaNaveg->databaseTable = 'dbNavegarqLider';
            }
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->FornecNaveg->databaseTable = 'dbNavegarqNatec';
                $this->LojaNaveg->databaseTable = 'dbNavegarqNatec';
            }
        }
        
        $fields = array(
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) AS ano_mes',
            'AVG(datediff(dd, dtemiss, dtvencto)) AS dias_medio',
            'AVG(datediff(dd, dtemiss, dtpagto)) AS pagamento_medio',
            'count(numero) AS qtd_titulos'
        );
        
        $group = array('SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)');
        
        $conditions = array(
            'YEAR(Tranrec.dtemiss)' => $filtros['ano']
        );
        
        if (isset($filtros['empresa']) && !empty($filtros['empresa'])) {
            $conditions['Tranrec.empresa'] = $filtros['empresa'];
        }
        
        $order = array('ano_mes ASC');
        
        return $this->find('all', compact('conditions', 'fields', 'group', 'order'));
        
    }

    function listaEstatiscaInadimplentes($filtros){
        $Notafis  = $this->Notafis    = ClassRegistry::init('Notafis');
        $this->LojaNaveg              = ClassRegistry::init('LojaNaveg');        
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
            $this->databaseTable            = 'dbNavegarqLider';
            $this->Notafis->databaseTable   = 'dbNavegarqLider';
        }
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
        }



        $fields  = array(
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) AS ano_mes',
            'SUM(Tranrec.valor / notafis.vlnota * notafis.vlmerc)   AS faturamento',
            'SUM(Tranrec.valor)                                      AS liquido',
            'SUM( 
                CASE 
                    WHEN Pagamento.valor IS NULL AND Tranrec.dtvencto < \''.date('Y').'-'.date('m').'-'.date('d').'\'
                    THEN Tranrec.valor
                    ELSE 0 
                END  )                                          AS inadimplentes', 
            '(Sum(
                CASE 
                    WHEN Pagamento.valor IS NULL AND Tranrec.dtvencto < \''.date('Y').'-'.date('m').'-'.date('d').'\'
                    THEN Tranrec.valor
                    ELSE 0 
                END)* 100/ SUM(Tranrec.valor)
            ) as porcento_inadimplente'
        );

        $group = array('SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) ');

        $conditions = array(
            
            'Tranrec.debcred' => 'C' ,
            'Tranrec.dtemiss between ? AND ?' =>  array($filtros['ano'].'-01-01 00:00:00',$filtros['ano'].'-12-31 23:59:59'),
            'Notafis.cancela' =>'N'
            
        );
        
        if(isset($filtros['empresa'])&& $filtros['empresa'] !=null){
            $conditions['Tranrec.empresa'] = $filtros['empresa'];
        } 

        $order = array('ano_mes ASC');

        $joins =  array(
             array(
                'table' => "{$Notafis->databaseTable}.{$Notafis->tableSchema}.{$Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => array(
                    'Notafis.empresa = Tranrec.empresa ',
                    'Notafis.numero = Tranrec.numero',
                    'Notafis.serie = Tranrec.serie',
                    'Notafis.seq = Tranrec.seqn',
                    'Notafis.cancela' => 'N'),
                'type' => 'INNER'
            ),   
            array(
                'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias' => 'Pagamento',
                'conditions' => array('Pagamento.empresa = Tranrec.empresa' , 'Pagamento.numero = Tranrec.numero ',' Pagamento.ordem = Tranrec.ordem',"Pagamento.debcred = 'D'"),
                'type'=> 'LEFT' 
                                       
            ),
        );
        return $this->find('all',compact('conditions','joins','fields','group','order')); 
    }

    function listaTotalTitulosClientes($filtros){
        $NCliente = $this->NCliente     =ClassRegistry::init('NCliente');
        $Notafis = $this->Notafis       = ClassRegistry::init('Notafis');
        $this->LojaNaveg                = ClassRegistry::init('LojaNaveg');        
        $this->Tranrec                  = ClassRegistry::init('Tranrec');   
        $Pagamento = $this->Tranrec     = ClassRegistry::init('Tranrec');
        
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
            $this->databaseTable            = 'dbNavegarqLider';
            $this->Notafis->databaseTable   = 'dbNavegarqLider';
        }    
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
        }

        if ($this->useDbConfig == 'test_suite') {
            $this->NCliente->useTable = 'n_cliente';
        }
        
        $fields  = array(           
            "SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) as ano_mes",
            "notafis.cliente as codigo",
            "NCliente.razaosocia",
            "count(*) AS Titulos ",
            "SUM(CASE WHEN Tranrec.razao = Pagamento.razao AND Tranrec.valor IS NOT NULL THEN 0 ELSE Tranrec.valor END) AS valor_total",
             
        );

        $group = array(
            'NCliente.razaosocia',
            'notafis.cliente',
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)'
        );

        $conditions = $this->conditionsTotalTitulosClientes($filtros);
        $order = array('valor_total DESC');
        $joins = $this->joinsTotalTitulosClientes($filtros);
              
        return $this->find('all',compact('conditions','joins','fields','group','order')); 
    }

    ///////////////////////// TotalTitulosClientes  //////////////////////////////////////////////////////
    
    function joinsTotalTitulosClientes($filtros){
        $Notafis = $this->Notafis       = ClassRegistry::init('Notafis');
        $NCliente = $this->NCliente     =ClassRegistry::init('NCliente');
        $this->LojaNaveg                = ClassRegistry::init('LojaNaveg');        
        $this->Tranrec                  = ClassRegistry::init('Tranrec');   
        $Pagamento = $this->Tranrec     = ClassRegistry::init('Tranrec');
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
            $this->databaseTable            = 'dbNavegarqLider';
            $this->Notafis->databaseTable   = 'dbNavegarqLider';
        }    
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
            $NCliente->databaseTable        = 'dbNavegarqNatec';
        }

        $joins =  array(
             array(
                'table' => "{$Notafis->databaseTable}.{$Notafis->tableSchema}.{$Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => array('Notafis.empresa = Tranrec.empresa ','Notafis.numero = Tranrec.numero'),
                'type' => 'INNER'
            ),   
            array(
                'table' => "{$Pagamento->databaseTable}.{$Pagamento->tableSchema}.{$Pagamento->useTable}",
                'alias' => 'Pagamento',
                'conditions' => array('Pagamento.empresa = Tranrec.empresa' , 'Pagamento.numero = Tranrec.numero ',' Pagamento.ordem = Tranrec.ordem',"Pagamento.debcred = 'D'"),
                'type'=> 'LEFT' 
                                       
            ),
            array(
                'table' => "{$NCliente->databaseTable}.{$NCliente->tableSchema}.{$NCliente->useTable}",
                'alias' => 'NCliente',
                'conditions' => 'NCliente.codigo = Notafis.cliente' ,
                'type'=> 'INNER' 
                                       
            ),

        );
         return $joins;
    }
    function fieldsTotalTitulosClientes(){
        $fields  = array(
            
            "SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) as ano_mes",
            "Notafis.cliente as codigo",
            "NCliente.razaosocia AS Cliente",               
            "SUM(CASE WHEN Tranrec.razao = Pagamento.razao AND Tranrec.valor IS NOT NULL THEN 0 ELSE Tranrec.valor END) AS valor_total",
            "count(*) as Titulos",
        );
        return $fields;
    }   

    function conditionsTotalTitulosClientes($filtros){
        $conditions = array(
            
            'Tranrec.debcred' => 'C' ,
            'Tranrec.dtemiss between ? AND ?' =>  array($filtros['ano'].'-01-01 00:00:00',$filtros['ano'].'-12-31 23:59:59'),
            'Notafis.cancela' =>'N',
            'Tranrec.valor is not null',
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)' => $filtros['ano_mes'],//'01/2013',
            'Pagamento.valor is null',
            'Tranrec.dtvencto < ' => date('Y-m-d'),
        );
        
        if(isset($filtros['empresa'])&& $filtros['empresa'] !=null){
            $conditions['Tranrec.empresa'] = $filtros['empresa'];
        } 
        return $conditions;
    } 

    //////////////////////////////////// fim TotalTitulosClientes//////////////////////////////////////

    function fieldsTitulosClientes(){
        $fields  = array(
            "SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7) as ano_mes",
            "Tranrec.razao as clientes",
            "Tranrec.numero as nota_numero",
            "Tranrec.ordem as ordem",
            "Tranrec.obs as obs",
            "Tranrec.seq as seq",
            "CONVERT(varchar,Tranrec.dtemiss,103) as data_emiss",
            "CONVERT(varchar,Tranrec.dtvencto,103) as data_venc",
            "Tranrec.valor as valor",
            "DATEDIFF(day, Tranrec.dtvencto, getdate()) as dias_venc"
        );
        return $fields;
    }

    function conditionsTitulosClientes($filtros){
        $conditions = array(
            
            'Tranrec.debcred' => 'C' ,
            'Tranrec.dtemiss between ? AND ?' =>  array($filtros['ano'].'0101 00:00:00',$filtros['ano'].'1231 23:59:59'),
            'Notafis.cancela' =>'N',
            'Tranrec.valor is not null',
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)' => $filtros['ano_mes'],
            'Pagamento.valor is null',
            'Tranrec.dtvencto < ' => date('Y-m-d'),
            'Notafis.cliente' => $filtros['codigo'],
        );

        if(isset($filtros['empresa'])&& $filtros['empresa'] !=null){
            $conditions['Tranrec.empresa'] = $filtros['empresa'];
        } 
        return $conditions;
    }

    function joinsTitulosClientes($filtros){
        $Notafis  = $this->Notafis    = ClassRegistry::init('Notafis');
        $this->LojaNaveg              = ClassRegistry::init('LojaNaveg');        
        $this->Tranrec              = ClassRegistry::init('Tranrec');   
        $Pagamento = $this->Tranrec             = ClassRegistry::init('Tranrec');
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
            $this->databaseTable            = 'dbNavegarqLider';
            $this->Notafis->databaseTable   = 'dbNavegarqLider';
        }    
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
        }
        $joins =  array(
             array(
                'table' => "{$Notafis->databaseTable}.{$Notafis->tableSchema}.{$Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => array('Notafis.empresa = Tranrec.empresa ','Notafis.numero = Tranrec.numero'),
                'type' => 'INNER'
            ),   
            array(
                'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias' => 'Pagamento',
                'conditions' => array('Pagamento.empresa = Tranrec.empresa' , 'Pagamento.numero = Tranrec.numero ',' Pagamento.ordem = Tranrec.ordem',"Pagamento.debcred = 'D'"),
                'type'=> 'LEFT' 
                                       
            ),
        ); return $joins;
    }
    
    function listaTitulosClientes($filtros){
        $Notafis  = $this->Notafis    = ClassRegistry::init('Notafis');
        $this->LojaNaveg              = ClassRegistry::init('LojaNaveg');        
        $this->Tranrec              = ClassRegistry::init('Tranrec');   
        $Pagamento = $this->Tranrec             = ClassRegistry::init('Tranrec');
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
            $this->databaseTable            = 'dbNavegarqLider';
            $this->Notafis->databaseTable   = 'dbNavegarqLider';
        }    
        
        if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
            $this->databaseTable            = 'dbNavegarqNatec';
            $this->Notafis->databaseTable   = 'dbNavegarqNatec';
        }
        
        if ($this->useDbConfig == 'test_suite') {
            $this->NCliente->useTable = 'n_cliente';
        }

        $fields = $this->fieldsTitulosClientes();
        
        $group = array(
            'SUBSTRING(CONVERT(VARCHAR,Tranrec.dtemiss, 103), 4, 7)',
            "Tranrec.dtemiss",
            "Tranrec.dtvencto",
            "Tranrec.ordem",
            "notafis.cliente",
            "Tranrec.empresa",
            "Tranrec.numero",
            "Tranrec.razao",
            "Tranrec.seq",
            "Tranrec.valor",
            "Tranrec.dtvencto",
            "Tranrec.obs",
        );

        $conditions = $this->conditionsTitulosClientes($filtros);
        $order = array('dias_venc DESC');

        $joins =  array(
             array(
                'table' => "{$Notafis->databaseTable}.{$Notafis->tableSchema}.{$Notafis->useTable}",
                'alias' => 'Notafis',
                'conditions' => array('Notafis.empresa = Tranrec.empresa ','Notafis.numero = Tranrec.numero'),
                'type' => 'INNER'
            ),   
            array(
                'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias' => 'Pagamento',
                'conditions' => array('Pagamento.empresa = Tranrec.empresa' , 'Pagamento.numero = Tranrec.numero ',' Pagamento.ordem = Tranrec.ordem',"Pagamento.debcred = 'D'"),
                'type'=> 'LEFT' 
                                       
            ),
        ); 
        return $this->find('all',compact('conditions','joins','fields','group','order')); 
    }

    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {
        if( isset($extra['method']) && $extra['method'] == 'analitico' )
            $this->bindModels();
        if( isset($extra['method']) && $extra['method'] == 'comissoes' ) 
            return $this->analiticoComissoes('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'extra'));
        if( isset($extra['method']) && $extra['method'] == 'comissoes_por_corretora_sintetico' )
            return $this->sinteticoComissoesPorCorretora($conditions,$limit,$order,$page);
        if( isset($extra['method']) && $extra['method'] == 'comissoes_por_corretora_analitico' )
            return $this->analiticoComissoesPorCorretoraAgrupado($conditions,$limit,$order,$page);
        if( isset($extra['extra']['joins']) && isset($extra['extra']['group']) ){ 
            $joins = $extra['extra']['joins'];
            $group = $extra['extra']['group'];
            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins', 'group'));
        }
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive'));
    }

    function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        if( isset($extra['method']) && $extra['method'] == 'analitico' )
            $this->bindModels();
        if( isset($extra['method']) && $extra['method'] == 'comissoes' )
            return $this->analiticoComissoes('count', compact('conditions'));
        if( isset($extra['method']) && $extra['method'] == 'comissoes_por_corretora_analitico' )
            return count($this->analiticoComissoesPorCorretoraAgrupado($conditions));
        if( isset($extra['method']) && $extra['method'] == 'comissoes_por_corretora_sintetico' )
            return count($this->sinteticoComissoesPorCorretora($conditions));
        if( isset($extra) &&  isset($extra['extra']['countTitulosTotalCliente'])  ){
            $joins = $extra['extra']['joins'];
            return $this->countTitulosTotalCliente($joins,$conditions);                     
        }
        if(isset($extra['countTitulosCliente'])){
            $joins = $extra['extra']['joins'];
            return $this->find('count', compact('conditions', 'recursive','joins'));
        }   
        return $this->find('count', compact('conditions', 'recursive'));
    }

    private function bindModels() {
        $this->bindModel(array('belongsTo' => array(
            'Notafis'       =>  array('foreignKey' => false, 'conditions' => array('Notafis.empresa = Tranrec.empresa', 'Notafis.numero = Tranrec.numero', 'Notafis.seq = Tranrec.seqn', 'Notafis.serie = Tranrec.serie')),
            'Gernfe'        =>  array('foreignKey' => false, 'conditions' => array('Notafis.empresa = Gernfe.empresa', 'Notafis.numero = Gernfe.numero', 'Notafis.seq = Gernfe.seq', 'Notafis.serie = Gernfe.serie')),
            'Cliente'       =>  array('foreignKey' => false, 'conditions' => array("Cliente.codigo = Notafis.cliente")),
            'Corretora'     =>  array('foreignKey' => false, 'conditions' => array("Corretora.codigo = Cliente.codigo_corretora")),
            'EnderecoRegiao'=>  array('foreignKey' => false, 'conditions' => array("EnderecoRegiao.codigo = cliente.codigo_endereco_regiao")),
            'Seguradora'    =>  array('foreignKey' => false, 'conditions' => array("Seguradora.codigo = Cliente.codigo_seguradora")),
        )));
    }

    private function countTitulosTotalCliente($joins,$conditions){
        $total = $this->find( 'all', array('fields'=>'COUNT(distinct([Notafis].[cliente])) AS total','joins'=>$joins,'conditions'=>$conditions) );
        return $total[0][0]['total'];
    }

    function analiticoComissoes($findType, $options) {
        $this->bindModel(array('belongsTo' => array(
            'Notaite' => array('foreignKey' => false, 'conditions' => array('Tranrec.empresa = Notaite.empresa', 'Tranrec.seqn = Notaite.seq', 'Tranrec.serie = Notaite.serie', 'Tranrec.numero = Notaite.nnotafis')),
            'Notafis' => array('foreignKey' => false, 'conditions' => array('Tranrec.empresa = Notafis.empresa', 'Tranrec.seqn = Notafis.seq', 'Tranrec.serie = Notafis.serie', 'Tranrec.numero = Notafis.numero')),
            'NProduto' => array('foreignKey' => false, 'conditions' => array('Notaite.produto = NProduto.codigo')),
            'NotafisComplemento' => array('foreignKey' => false, 'conditions' => array('NotafisComplemento.empresa = Notaite.empresa', 'NotafisComplemento.seq = Notaite.seq', 'NotafisComplemento.serie = Notaite.serie', 'NotafisComplemento.numero = Notaite.nnotafis')),
            'Cliente' => array('foreignKey' => false, 'conditions' => array('Cliente.codigo = Notaite.cliente')),
            'ConfiguracaoComissao' => array('foreignKey' => false, 'conditions' => array(
                'ConfiguracaoComissao.codigo_endereco_regiao = NotafisComplemento.endereco_regiao',
                'ConfiguracaoComissao.codigo_produto_naveg = Notaite.produto',
                'ConfiguracaoComissao.regiao_tipo_faturamento = Cliente.regiao_tipo_faturamento')
            ),
            'EnderecoRegiao' => array('foreignKey' => false, 'conditions' => array('EnderecoRegiao.codigo = NotafisComplemento.endereco_regiao')),
            'Seguradora' => array('foreignKey' => false, 'conditions' => array('Seguradora.codigo = NotafisComplemento.codigo_seguradora')),
            'Corretora' => array('foreignKey' => false, 'conditions' => array('Corretora.codigo = NotafisComplemento.codigo_corretora')),
            'Usuario' => array('foreignKey' => false, 'conditions' => array('Usuario.codigo = NotafisComplemento.codigo_gestor')),
        )));
        
        if ($findType != 'count') {
            $options['fields'] = array(
                'Cliente.codigo AS cliente_codigo',
                'Cliente.razao_social AS cliente_nome',
                'NotafisComplemento.endereco_regiao AS codigo_endereco_regiao',
                'Cliente.regiao_tipo_faturamento AS tipo_faturamento',
                'NProduto.descricao AS produto_nome',
                'Tranrec.dtpagto AS dtpagto',
                'Tranrec.empresa AS empresa',
                'Tranrec.numero AS numero',
                'Tranrec.seqn AS seqn',
                'Tranrec.serie AS serie',
                'Tranrec.valor AS valor_recebido',
                'EnderecoRegiao.descricao AS filial_nome',
                'ConfiguracaoComissao.percentual AS percentual',
                'Seguradora.nome AS seguradora_nome',
                'Corretora.nome AS corretora_nome',
                'Usuario.nome AS gestor_nome',
                'Notafis.vlmerc AS vlmerc',
            );
        }
        $options['conditions']['Tranrec.seq'] = '02';
        return $this->find($findType, $options);
    }

    function sinteticoComissoes($conditions, $totalizar = false) {
        $analitico = $this->analiticoComissoes('sql', compact('conditions'));
        $dbo = $this->getDatasource();
        if ($totalizar) {
            $fields = array(
                'SUM(vlmerc) AS valor',
                'SUM(CONVERT(DECIMAL(14,2), vlmerc) * CONVERT(DECIMAL(14,2), percentual) / 100) AS valor_comissao',
            );
            $group = null;
        } else {
            $fields = array(
                'codigo_endereco_regiao',
                'filial_nome',
                'SUM(vlmerc) AS valor',
                'SUM(CONVERT(DECIMAL(14,2), vlmerc) * CONVERT(DECIMAL(14,2), percentual) / 100) AS valor_comissao',
            );
            $group = array(
                'codigo_endereco_regiao',
                'filial_nome',
            );
        }
        $query = $dbo->buildStatement(array(
            'fields' => $fields,
            'table' => "({$analitico})",
            'alias' => 'Analitico',
            'limit' => null,
            'offset' => null,
            'joins' => array(),
            'conditions' => null,
            'order' => null,
            'group' => $group
        ),$this);
        return $this->query($query);
    }

    function sinteticoComissoesPorCorretora($conditions,$limit = null,$order = null,$page = 1,$totalizar = false) {
        $this->Analitico =& ClassRegistry::init('TranrecPago');
        $analitico = $this->Analitico->analiticoComissoesPorCorretora('sql', compact('conditions'));
        $dbo = $this->getDatasource();
        if($totalizar){
            $fields = array(
                'SUM(Analitico.valor_servico) AS valor_servico',
                'SUM(Analitico.valor_servico_liquido) AS valor_servico_liquido',
                'SUM(Analitico.valor_impostos) AS valor_impostos',
                'SUM(Analitico.valor_comissao) AS valor_comissao',
            );
            $group = null;
        }else{
            $fields = array(
                'Analitico.codigo_corretora AS codigo_corretora',
                'Analitico.corretora_nome AS corretora_nome',
                'SUM(Analitico.valor_servico_liquido) AS valor_servico_liquido',
                'SUM(Analitico.valor_impostos) AS valor_impostos',
                'SUM(Analitico.valor_servico) AS valor_servico',
                'SUM(Analitico.valor_comissao) AS valor_comissao',
            );
            $group = array(
                'Analitico.codigo_corretora',
                'Analitico.corretora_nome',
            );
        }
        $query = $dbo->buildStatement(array(
            'fields' => $fields,
            'table' => "({$analitico})",
            'alias' => 'Analitico',
            'limit' => $limit,
            'offset' => ($page-1)*$limit,
            'joins' => array(),
            'conditions' => null,
            'order' => $order,
            'group' => $group,
        ),$this);
        
        return $this->query($query);
    }

    function analiticoComissoesPorCorretoraAgrupado($conditions,$limit = null,$order = null,$page = 1,$export = false) {
        $this->Analitico =& ClassRegistry::init('TranrecPago');
        $analitico = $this->Analitico->analiticoComissoesPorCorretora('sql', compact('conditions'));
        $dbo = $this->getDatasource();
        $fields = array(
            'Analitico.cliente_codigo AS cliente_codigo',
            'Analitico.cliente_nome AS cliente_nome',
            'Analitico.codigo_corretora AS codigo_corretora',
            'Analitico.codigo_produto AS codigo_produto',
            'Analitico.produto_nome AS produto_nome',
            'Analitico.servico_nome AS servico_nome',
            'Analitico.codigo_servico AS codigo_servico',
            'Analitico.valor_unitario AS valor_unitario',
            'Analitico.corretora_nome AS corretora_nome',
            'Analitico.verificar_preco_unitario AS verificar_preco_unitario',
            'Analitico.preco_de AS preco_de',
            'Analitico.preco_ate AS preco_ate',
            'Analitico.percentual_impostos AS percentual_impostos',
            'Analitico.percentual_comissao AS percentual_comissao',
            'Analitico.vlmerc AS vlmerc',
            'SUM(Analitico.valor_servico) AS valor_servico',
            'SUM(Analitico.quantidade) AS quantidade',
            'SUM(Analitico.valor_servico * Analitico.percentual_impostos / 100) AS valor_impostos',
            'SUM(Analitico.valor_servico - (Analitico.valor_servico * Analitico.percentual_impostos / 100)) AS valor_servico_liquido',
            'SUM(CONVERT(DECIMAL(14,2), Analitico.valor_servico) - (CONVERT(DECIMAL(14,2), Analitico.valor_servico) * (CONVERT(DECIMAL(14,2), Analitico.percentual_impostos) / 100)) ) * CONVERT(DECIMAL(14,2), Analitico.percentual_comissao) / 100 AS valor_comissao',
        );
        $group = array(
            'Analitico.cliente_codigo',
            'Analitico.cliente_nome',
            'Analitico.codigo_corretora',
            'Analitico.codigo_produto',
            'Analitico.produto_nome',
            'Analitico.servico_nome',
            'Analitico.codigo_servico',
            'Analitico.valor_unitario',
            'Analitico.corretora_nome',
            'Analitico.verificar_preco_unitario',
            'Analitico.preco_de',
            'Analitico.preco_ate',
            'Analitico.percentual_impostos',
            'Analitico.percentual_comissao',
            'Analitico.vlmerc',
        );

        if($export){
            $query = $dbo->buildStatement(array(
                'fields' => $fields,
                'table' => "({$analitico})",
                'alias' => 'Analitico',
                'order' => $order,
                'group' => $group,
                'conditions' => null,
                'limit' => $limit,
            ),$this);
            return $query;
        }
        $query = $dbo->buildStatement(array(
            'fields' => $fields,
            'table' => "({$analitico})",
            'alias' => 'Analitico',
            'limit' => $limit,
            'offset' => ($page-1)*$limit,
            'joins' => array(),
            'conditions' => null,
            'order' => $order,
            'group' => $group,
        ),$this);

        return $this->query($query);
    }

    function converteFiltrosEmConditions($filtros) {
        $conditions = array('Tranrec.empresa' => '03');
        if (isset($filtros['configuracao_comissao']) && !empty($filtros['configuracao_comissao']) && isset($filtros['mes_faturamento']) && !empty($filtros['mes_faturamento'])) {
            $conditions['Tranrec.dtpagto BETWEEN ? AND ?'] = array(
                date('Ymd 00:00:00', strtotime($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01')),
                date('Ymt 23:59:59', strtotime($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01')),
            );
        }else{
            if (isset($filtros['data_inicial']) && !empty($filtros['data_inicial']))
                $conditions['Tranrec.dtemiss >='] = AppModel::dateToDbDate($filtros['data_inicial']);
            if (isset($filtros['data_final']) && !empty($filtros['data_final']))
                $conditions['Tranrec.dtemiss <='] = AppModel::dateToDbDate($filtros['data_final']);            
        }
        if (isset($filtros['codigo_endereco_regiao']) && !empty($filtros['codigo_endereco_regiao'])) 
            $conditions['NotafisComplemento.endereco_regiao'] = $filtros['codigo_endereco_regiao'];
        if (isset($filtros['codigo_gestor']) && !empty($filtros['codigo_gestor'])) 
            $conditions['Cliente.codigo_gestor'] = $filtros['codigo_gestor'];
        if (isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])) 
            $conditions['Cliente.codigo_seguradora'] = $filtros['codigo_seguradora'];
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])) 
            $conditions['Cliente.codigo_corretora'] = $filtros['codigo_corretora'];
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) 
            $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
        if (isset($filtros['tipo_faturamento']) && !empty($filtros['tipo_faturamento'])) 
            $conditions['Cliente.regiao_tipo_faturamento'] = $filtros['tipo_faturamento'] - 1;
        if (isset($filtros['configuracao_comissao']) && !empty($filtros['configuracao_comissao'])) {
            if ($filtros['configuracao_comissao'] == 2) {
                $conditions[] = 'ConfiguracaoComissao.percentual IS NULL';
            } else {
                $conditions[] = 'ConfiguracaoComissao.percentual > 0';
            }
        }
        if (isset($filtros['status']) && !empty($filtros['status'])) {
            if ($filtros['status'] == Tranrec::STATUS_EM_ABERTO) {
                $conditions[] = "NOT EXISTS(SELECT TOP 1 pagamento.dtpagto FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')";
            } elseif ($filtros['status'] == Tranrec::STATUS_PAGO) {
                $conditions[] = "EXISTS(SELECT TOP 1 pagamento.dtpagto FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')";
            }
        }
        return $conditions;
    }
    
    function converteFiltrosEmConditionsPorCorretora($filtros) {
        $conditions = array();
        if (isset($filtros['configuracao_comissao']) && !empty($filtros['configuracao_comissao']) && isset($filtros['mes_faturamento']) && !empty($filtros['mes_faturamento'])) {
            $conditions['Tranrec.dtpagto BETWEEN ? AND ?'] = array(
                date('Ymd 00:00:00', strtotime($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01')),
                date('Ymt 23:59:59', strtotime($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01')),
            );
        }
        if (isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])) 
            $conditions['Corretora.codigo'] = $filtros['codigo_corretora'];
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) 
            $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
        if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto'])) 
            $conditions['ItemPedido.codigo_produto'] = $filtros['codigo_produto'];
        if (isset($filtros['codigo_servico']) && !empty($filtros['codigo_servico'])) 
            $conditions['DetalheItemPedidoManual.codigo_servico'] = $filtros['codigo_servico'];
        if (isset($filtros['configuracao_comissao']) && !empty($filtros['configuracao_comissao'])) {
            if ($filtros['configuracao_comissao'] == 2) {
                $conditions[] = 'ConfiguracaoComissaoCorre.percentual_comissao IS NULL';
            } else {
                $conditions[] = 'ConfiguracaoComissaoCorre.percentual_comissao >= 0';
            }
        }
        
        return $conditions;
    }

    function sintetico($conditions, $agrupamento) {
        $query_analitico = $this->queryAnalitico('sql', compact('conditions'));
        if ($agrupamento == self::AGRP_CLIENTE) {
            $fields = array(
                'codigo_cliente AS codigo',
                'razao_social AS descricao',
            );
            $group = array(
                'codigo_cliente',
                'razao_social',
            );
        } elseif ($agrupamento == self::AGRP_CORRETORA) {
            $fields = array(
                'codigo_corretora AS codigo',
                'nome_corretora AS descricao',
            );
            $group = array(
                'codigo_corretora',
                'nome_corretora',
            );
        } elseif ($agrupamento == self::AGRP_FILIAL) {
            $fields = array(
                'codigo_endereco_regiao AS codigo',
                'descricao_endereco_regiao AS descricao',
            );
            $group = array(
                'codigo_endereco_regiao',
                'descricao_endereco_regiao',
            );
        } elseif ($agrupamento == self::AGRP_SEGURADORA) {
            $fields = array(
                'codigo_seguradora AS codigo',
                'nome_seguradora AS descricao',
            );
            $group = array(
                'codigo_seguradora',
                'nome_seguradora',
            );
        }
        
        $fields = array_merge($fields, array(
            'SUM(CASE WHEN dtpgto IS NULL THEN valor ELSE 0 END) AS valor_em_aberto',
            'SUM(CASE WHEN dtpgto IS NOT NULL THEN valor ELSE 0 END) AS valor_pago',
        ));
        $dbo = $this->getDatasource();
        $query = $dbo->buildStatement(array(
            'table' => "({$query_analitico})",
            'alias' => 'Analitico',
            'fields' => $fields,
            'joins' => array(),
            'group' => $group,
            'order' => null,
            'conditions' => null,
            'limit' => null,
        ),$this);
        return $this->query($query);
    }

    private function queryAnalitico($findType, $options) {
        $this->bindModels();
        $fields = array(
            'Cliente.codigo AS codigo_cliente',
            'Cliente.razao_social AS razao_social',
            'Tranrec.valor AS valor',
            "(SELECT TOP 1 CONVERT(VARCHAR,pagamento.dtpagto,120) FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02') AS dtpgto",
            'Seguradora.codigo AS codigo_seguradora',
            'Seguradora.nome AS nome_seguradora',
            'EnderecoRegiao.codigo AS codigo_endereco_regiao',
            'EnderecoRegiao.descricao AS descricao_endereco_regiao',
            'Corretora.codigo AS codigo_corretora',
            'Corretora.nome AS nome_corretora',
        );
        $options['fields'] = $fields;
        return $this->find($findType, $options);
    }

    function totais($conditions) {
        $this->bindModels();
        $fields = array(
            'SUM(valor) AS valor',
        );
        return $this->find('first', compact('conditions', 'fields'));
    }
}
?>