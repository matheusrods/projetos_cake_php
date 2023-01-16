<?php
class Notafis extends AppModel {
    var $name = 'Notafis';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'notafis';
    var $primaryKey = 'numero';
    var $actsAs = array('Secure');
    var $produtosMonitora = null;

    function dadosServicos($nota_fiscal) {
        $codigo_empresa = '17,18,19';
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'Gernfe' => array(
                        'className' => 'Gernfe', 
                        'foreignKey' => false,
                        'conditions' => array(
                            'Gernfe.empresa = Notafis.empresa and Gernfe.numero = Notafis.numero'
                        )
                    ),
                    'LojaNaveg' => array(
                        'className' => 'LojaNaveg',
                        'foreignKey' => false,
                        'conditions' => array(
                            'LojaNaveg.codigo = Gernfe.empresa'
                        )
                    )
                )
            )
        );
        
        $Tranrec = ClassRegistry::init('Tranrec');
        $nota_fiscal = $this->find('first', array('fields' => array('LojaNaveg.ccm', 'Notafis.numero', 'Gernfe.numnfe', 'Notafis.cliente', 'Notafis.dtemissao','Notafis.empresa'), 'conditions' => array('Gernfe.empresa IN('. $codigo_empresa .')', 'Gernfe.numnfe' => str_pad($nota_fiscal, 8, "0", STR_PAD_LEFT))));
        
        if (!empty($nota_fiscal)) {
            $dtservicos = AppModel::dateToDbDate($nota_fiscal['Notafis']['dtemissao']);
            $nota_fiscal['Notafis']['dtservicos'] = Date('Ym', strtotime('-1 month', strtotime($dtservicos)));
            $codigo_banco = $Tranrec->buscaCodigoBancoPorRPS($nota_fiscal['Notafis']['numero'],$nota_fiscal['Notafis']['empresa']);
            $nota_fiscal['Notafis']['codigo_banco'] = $codigo_banco;
            return $nota_fiscal;
        }
        return false;
    }

    function linksFaturamento($retorno_nf) {

        $dados_servicos = $this->dadosServicos($retorno_nf['RetornoNf']['nota_fiscal']);
        $dados_cliente  = $this->dadosCliente($dados_servicos['Notafis']['cliente']);
        
        //link antigo
        // $link_nota_fiscal = "http://nfe.prefeitura.sp.gov.br/nfe.aspx?ccm=".$dados_servicos['LojaNaveg']['ccm']."&nf=".$retorno_nf['RetornoNf']['nota_fiscal']."&cod=".$retorno_nf['RetornoNf']['codigo_verificacao'];
        
        //novo link da nota fiscal
        $link_nota_fiscal = "https://nfe.prefeitura.sp.gov.br/contribuinte/notaprint.aspx?ccm=".$dados_servicos['LojaNaveg']['ccm']."&nf=".$retorno_nf['RetornoNf']['nota_fiscal']."&cod=".$retorno_nf['RetornoNf']['codigo_verificacao'];

        
        // $links_demonstrativo = array(); // tirado: $this->linksDemonstrativos($dados_servicos);
        $links_demonstrativo = $this->linksDemonstrativos($dados_servicos);

        $link_opcao_faturamento = $this->linkOpcaoFaturamento($dados_servicos);
        $link_boleto = $this->linkBoleto($dados_servicos['Notafis']['codigo_banco'], $dados_cliente['Cliente']['codigo_documento'], $dados_servicos['Notafis']['numero']);
        $emails = $this->emailsFinanceiros($dados_servicos['Notafis']['cliente']);
        $links = array('nf' => $link_nota_fiscal, 'demonstrativos' => $links_demonstrativo, 'boleto' => $link_boleto);
        
        if (strlen($link_opcao_faturamento) > 0){
            $links['opcaofat'] = $link_opcao_faturamento;
        }
        return array(
            'NotaFiscal' => array(
                'numero' => $dados_servicos['Notafis']['numero'],
                'numnfe' => $retorno_nf['RetornoNf']['nota_fiscal'],
                'data_emissao' => $dados_servicos['Notafis']['dtemissao'],
                'mes_servicos' => substr($dados_servicos['Notafis']['dtservicos'],4,2).'/'.substr($dados_servicos['Notafis']['dtservicos'],0,4),
                'codigo_banco' => $dados_servicos['Notafis']['codigo_banco']
            ),
            'Cliente' => array(
                'codigo' => $dados_servicos['Notafis']['cliente'],
                'razao_social' => $dados_cliente['Cliente']['razao_social']
            ),
            'emails' => $emails,
            'links' => $links,
            'model' => 'RetornoNf',
            'foreign_key' => isset($retorno_nf['RetornoNf']['codigo']) ? $retorno_nf['RetornoNf']['codigo'] : null
        );
    }

    private function linksDemonstrativos($dados_servicos) {

        $Notaite = ClassRegistry::init('Notaite');        

        //verifica qual empresa está gerando o demonstrativo
        $codigo_empresa = '1';
        if(isset($_SESSION['Auth']['Usuario']['codigo_empresa'])) {
            $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
        }//fim isset

        //verifica qual o codigo da empresa no naveg
        $codigo_empresa_naveg = 18;
        switch ($codigo_empresa) {
            case '1':
                $codigo_empresa_naveg = 18;
                break;
            case '3':
                $codigo_empresa_naveg = 19;
                break;
            case '5':
                $codigo_empresa_naveg = 21;
                break;
            default:
                $codigo_empresa_naveg = 18;
                break;
        }//fim switch

        $links = array();
        $produtos_nf = $Notaite->itensPorNotaFiscal($dados_servicos['Notafis']['numero'],$codigo_empresa_naveg);

        //verifica se existe produtos da nota fiscal
        if (count($produtos_nf) > 0) {
            //cliente pagador
            if(!empty($dados_servicos['Notafis']['cliente']) && !empty($dados_servicos['Notafis']['dtemissao'])) {
                
                //chama a class de cliente do rhhealth
                $Cliente =& ClassRegistry::init('Cliente');
                //pega o codigo do cliente pagador da cliente do banco rhhealth.
                $cliente = $Cliente->find('first', array('conditions' => array('codigo_naveg' => $dados_servicos['Notafis']['cliente'])));
                //cliente pagador
                $cliente_pagador = $cliente['Cliente']['codigo'];

                //pega o mes passado
                $data_base =  Comum::formataData($dados_servicos['Notafis']['dtemissao'],'dmyhms','ymd');
                $base_periodo = strtotime('-1 month', strtotime($data_base));

                //instancia a class pedido
                $Pedido     = ClassRegistry::init('Pedido');
                $Integfat   = ClassRegistry::init('Integfat');

                $joins_pedido_rhhealth = array(
                    array(
                        'table' => "RHHealth.dbo.itens_pedidos",
                        'alias' => 'ItemPedido',
                        'type' => 'INNER',
                        'conditions' => 'Pedido.codigo = ItemPedido.codigo_pedido',
                    ),
                    array(
                        'table' => "dbNavegarqNatec.dbo.integfat",
                        'alias' => 'Integfat',
                        'type' => 'INNER',
                        'conditions' => 'Pedido.codigo_naveg = Integfat.seq',
                    ),
                );

                //varre os produtos
                foreach($produtos_nf as $pro) {
                    
                    //pega o pedidos que estao como automatico manual=0
                    // $pedidos_rhhealth = $Pedido->find('all', array('conditions' => array('manual' => '0' ,"codigo_naveg IN (SELECT seq FROM {$Integfat->databaseTable}.{$Integfat->tableSchema}.{$Integfat->useTable} WHERE npedido = ".$pro['Notaite']['npedido'].")"))); 
                    $pedidos_rhhealth = $Pedido->find('all', 
                        array(
                            'joins' =>$joins_pedido_rhhealth,
                            'conditions' => 
                                array(
                                    'Pedido.manual' => '0' ,
                                    'ItemPedido.codigo_produto' => array('59','117'),
                                    'Integfat.npedido' => $pro['Notaite']['npedido']
                                )
                        )); 

                    //verifica se existe pedido automatico 
                    if(empty($pedidos_rhhealth)) {
                        continue;
                    }

                    $mes_ped_comp = 0;
                    $ano_ped_comp = 0;
                    $mes_ped_capita = 0;
                    $ano_ped_capita = 0;

                     $codigo_servico_percapita = 0;
                     $codigo_servico_complementar = 0;

                    //Recupera as datas de referencia dos pedidos
                    foreach($pedidos_rhhealth as $ped){

                        //pedido per capita
                        if($ped['Pedido']['codigo_servico'] == '001'){
                            $mes_ped_capita = $ped['Pedido']['mes_referencia'];
                            $ano_ped_capita = $ped['Pedido']['ano_referencia'];
                            // $codigo_servico_percapita = '001';

                            //seta o mes e o ano
                        
                            if(!empty($mes_ped_capita) && !empty($ano_ped_capita)){
                                $mes = $mes_ped_capita;
                                $ano = $ano_ped_capita;

                            } else {
                                $mes =  Date('m', $base_periodo);
                                $ano =  Date('Y', $base_periodo);
                            }

                            $links['001'] = $this->linkDemonstrativoPercapita($cliente_pagador,$mes, $ano);
                        }

                        //pedido de exame complementar
                        if($ped['Pedido']['codigo_servico'] == '002'){
               
                            $mes_ped_comp = $ped['Pedido']['mes_referencia'];
                            $ano_ped_comp = $ped['Pedido']['ano_referencia'];
                            // $codigo_servico_complementar = '002';
                            //seta a data de inicio/fim
                            if(!empty($mes_ped_comp) && !empty($ano_ped_comp)){
                                $data_inicial = Date('Ym01', mktime(0,0,0,$mes_ped_comp,1,$ano_ped_comp));
                                $data_fim = Date('Ymt', mktime(0,0,0,$mes_ped_comp,1,$ano_ped_comp));

                
                            } else {
                                $data_inicial = Date('Ym01', $base_periodo);
                                $data_fim = Date('Ymt', $base_periodo);
                            }                        
                            //demonsrtativo exames complementares
                            $links['002'] = $this->linkDemonstrativoExamesComplementares($cliente_pagador,$data_inicial, $data_fim);
                        }
                    }


                    //verifica qual é o produto   
                    // //per capita codigo naveg 0522 assim como o Pacote mensal, alterado o codigo do produto pois foi alterado tb no naveg              
                    // // if($pro['Notaite']['produto'] == '0522' && $codigo_servico_percapita == '001' ) { 
                    // if($codigo_servico_percapita == '001' ) { 
                       

                    // } 
                    // //exames complementares: codigo produto no naveg 528, alterado o codigo do produto pois foi alterado tb no naveg
                    // // else if ($pro['Notaite']['produto'] == '0528' && $codigo_servico_complementar == '002') { 
                    // else if ($codigo_servico_complementar == '002') { 
                        
                    // }//fim verificacao codigo naveg

                }//fim foreach
            } //fim verificacao se existe cliente e data
        }//fim numero nota fiscal
        return $links;
    }

    /**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [data_inicial] data inicial do demonstrativo
     * @param  [data_final] data final do demonstrativo
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkDemonstrativoExamesComplementares($codigo_cliente, $data_inicial, $data_final) {
        //monta o hash para colocar no link
        $hash = Comum::geraParametroLinkDemonstrativoExameComplementar('demonstrativos', $codigo_cliente, $data_inicial, $data_final);
        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "https://portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "https://tstportal.rhhealth.com.br" : "http://portal.localhost"));
        //monta o link
        $link_demonstrativo = "{$host}/portal/clientes/gera_demonstrativo_exames_complemetares?key=".urlencode($hash);

        //retorno o link a ser acessado
        return $link_demonstrativo;
    }

    /**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [mes] mes
     * @param  [ano] ano
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkDemonstrativoPercapita($codigo_cliente, $mes, $ano) {
        //monta o hash para colocar no link
        $hash = Comum::geraParametroLinkDemonstrativoPercapita('demonstrativos', $codigo_cliente, $mes, $ano);
        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "https://portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "https://tstportal.rhhealth.com.br" : "http://portal.localhost"));
        //monta o link
        $link_demonstrativo = "{$host}/portal/clientes/gera_demonstrativo_percapita?key=".urlencode($hash);

        //retorno o link a ser acessado
        return $link_demonstrativo;
    }

    private function possuiProdutoMonitora($produtos_nf) {
        if ($this->produtosMonitora == null) {
            $ClientEmpresa = ClassRegistry::init('ClientEmpresa');
            $this->produtosMonitora = $ClientEmpresa->produtosMonitoraNaveg();
        }
        foreach ($this->produtosMonitora as $produto) {
            if (in_array($produto, $produtos_nf))
                return true;
        }
        return false;
    }

    private function linkDemonstrativoDicem($codigo_cliente, $anomes_servicos) {
        $hash = Comum::geraParametroLinkDemonstrativoDeServico('dicem', $codigo_cliente, $anomes_servicos);
        if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
            $link_demonstrativo = "http://portal.buonny.com.br/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        } else if(Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO) {
            $link_demonstrativo = "http://tstportal.buonny.com.br/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        } else {
            $link_demonstrativo = "http://portal.localhost/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        }
        return $link_demonstrativo;
    }

    private function linkDemonstrativoTeleconsult($codigo_cliente, $anomes_servicos) {
        $hash = Comum::geraParametroLinkDemonstrativoDeServico('teleconsult', $codigo_cliente, $anomes_servicos);
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.buonny.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.buonny.com.br" : "portal.localhost"));
        $link_demonstrativo = "http://{$host}/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        return $link_demonstrativo;
    }

    private function linkDemonstrativoAutotrac($codigo_cliente, $anomes_servicos) {
        $hash = Comum::geraParametroLinkDemonstrativoDeServico('autotrac', $codigo_cliente, $anomes_servicos);
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
        $link_demonstrativo = "http://{$host}/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        return $link_demonstrativo;
    }

    private function linkDemonstrativoBuonnySat($codigo_cliente, $anomes_servicos) {
        $hash = Comum::geraParametroLinkDemonstrativoDeServico('buonnysat', $codigo_cliente, $anomes_servicos);
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));
        $link_demonstrativo = "http://{$host}/portal/clientes/gera_demonstrativo?key=".urlencode($hash);
        return $link_demonstrativo;
    }

    private function emailsFinanceiros($codigo_cliente_naveg) {
        $Cliente =& ClassRegistry::init('Cliente');
        $codigo_cliente = $Cliente->find('first',array('fields' => array('codigo'),'conditions' => array('codigo_naveg' => $codigo_cliente_naveg)));
        $ClienteContato =& ClassRegistry::init('ClienteContato');
        $quando_nao_existir_utilizar_email_buonny = true;
        return $ClienteContato->emailsFinanceirosPorCliente($codigo_cliente['Cliente']['codigo'], $quando_nao_existir_utilizar_email_buonny);
    }

    private function dadosCliente($codigo_cliente) {
        $Cliente =& ClassRegistry::init('Cliente');
        $dados = $Cliente->find('first', array('fields' => array('codigo_documento', 'razao_social'), 'conditions' => array('Cliente.codigo_naveg' => $codigo_cliente)));
        return $dados;
    }

    private function linkOpcaoFaturamento($dados_servicos) {
        $Cliente = ClassRegistry::init('Cliente');
        $codigo_cliente = $Cliente->find('first',array('fields' => array('codigo'),'conditions' => array('codigo_naveg' => $dados_servicos['Notafis']['cliente']),'recursive' => -1));
        $ClienteOpFat = ClassRegistry::init('ClienteOpFat');
        $ja_optou = ($ClienteOpFat->find('count', array('conditions' => array('codigo_cliente' => $codigo_cliente['Cliente']['codigo']))) > 0);
        $link = '';
        if (!$ja_optou) {
            $hash = Comum::encriptarLink('opcaofat|'.$codigo_cliente['Cliente']['codigo']);
            $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "rhhealth.localhost"));
            $link = "http://{$host}/portal/clientes/opcao_fatura_email?key=".urlencode($hash);
        }
        return $link;
    }

    function linkBoleto($codigo_banco, $cnpj_cliente, $numero_notafis) {
        $Tranrec = ClassRegistry::init('Tranrec');
        return $Tranrec->linkBoleto($codigo_banco, $cnpj_cliente, $numero_notafis);
    }

    function estatisticaNotafis($ano){
        $retorno = array();
        $Gernfe = ClassRegistry::init('Gernfe');
        $RetornoNf = ClassRegistry::init('RetornoNf');
        $options = array(
            'fields' => array(
                'convert(varchar(7), [dtemissao], 120) as ano_mes',
                "SUM(case cancela when 'N' then 1 else 0 end) as qtd_nf",
                "SUM(case cancela when 'S' then 1 else 0 end) as qtd_nf_canceladas",
                'COUNT(data_envio) as qtd_envio',
                'COUNT(distinct cliente) as qtd_cliente'
            ),
            'joins' => array(
                array(
                    'table' => $Gernfe->useTable,
                    'alias' => $Gernfe->name,
                    'tableSchema' => $Gernfe->tableSchema,
                    'databaseTable' => $Gernfe->databaseTable,
                    'type' => 'LEFT',
                    'conditions' => 'Notafis.empresa = Gernfe.empresa AND Notafis.seq = Gernfe.seq AND Notafis.numero = Gernfe.numero'
                    ),
                array(
                    'table' => $RetornoNf->useTable,
                    'alias' => $RetornoNf->name,
                    'tableSchema' => $RetornoNf->tableSchema,
                    'databaseTable' => $RetornoNf->databaseTable,
                    'type' => 'LEFT',
                    'conditions' => 'RetornoNf.nota_fiscal = Gernfe.numnfe'
                )
            ),
            'group' => array('convert(varchar(7), [dtemissao], 120)'),
            'conditions' => array(
                'convert(varchar(4), notafis.dtemissao, 120) =' => $ano,
                'notafis.empresa' => '03'
            ),
            'order' => array('ano_mes')
        );

        $ano_meses = $this->find('all', $options);
        Set::extract($ano_meses, '{n}.0');

        foreach($ano_meses as $chave => $ano_mes) {
            $retorno[$chave] = array('Notafis' => $ano_mes[0]);
        }

        return $retorno;

    }

    function bindCliente(){
        $this->bindModel(array(
            'belongsTo' => array(
                'Cliente' => array(
                    'class' => 'Cliente',
                    'type' => 'INNER',
                    'foreignKey' => 'cliente'
                )
            )
        ));
    }



    function ubindCliente(){
        $this->unbindModel(array(
            'belongsTo' => array(
                'Cliente'
            )
        ));
    }

    function totalRankingFaturamento($filtros) {
        $Cliente  =& ClassRegistry::init('Cliente');
        $Notaite  =& ClassRegistry::init('Notaite');
        $NProduto =& ClassRegistry::init('NProduto');

        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial']);
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final']);
        $conditions = array('Notafis.cancela' => 'N', 'Notafis.dtemissao BETWEEN ? AND ?' => array($filtros['data_inicial'], $filtros['data_final']));
        if (isset($filtros['empresa']) && (!empty($filtros['empresa'])))
            $conditions['Notafis.empresa'] = $filtros['empresa'];

        if (isset($filtros['gestores']) && (!empty($filtros['gestores'])))
            $conditions['Cliente.codigo_gestor'] = $filtros['gestores'];
        if (isset($filtros['corretoras']) && (!empty($filtros['corretoras'])))
            $conditions['Cliente.codigo_corretora'] = $filtros['corretoras'];
        if (isset($filtros['seguradoras']) && (!empty($filtros['seguradoras'])))
            $conditions['Cliente.codigo_seguradora'] = $filtros['seguradoras'];

        if (isset($filtros['empresa']) && (!empty($filtros['empresa'])))
            $conditions['Notafis.empresa'] = $filtros['empresa'];
        if ($this->useDbConfig != 'test_suite') {
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER)
                $this->databaseTable = 'dbNavegarqLider';
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC)
                $this->databaseTable = 'dbNavegarqNatec';
        }

        if (isset($filtros['produtos']) && (!empty($filtros['produtos']))) {
            $totalNotas =  $this->find('first',array(
                'fields'     =>'sum(Notafis.vlmerc) as totalNotas',
                'joins'      => array(
                    array(
                        'table' => $Cliente->useTable,
                        'alias' => $Cliente->name,
                        'tableSchema' => $Cliente->tableSchema,
                        'databaseTable' => $Cliente->databaseTable,
                        'type' => 'INNER',
                        'conditions' => 'Notafis.cliente = Cliente.codigo'
                    ),
                ),
                'conditions' => $conditions
                )
            );
        } else {
            $totalNotas =  $this->find('first',array(
                'fields'     =>'sum(Notafis.vlmerc) as totalNotas',
                'joins'      => array(
                    array(
                        'table' => $Cliente->useTable,
                        'alias' => $Cliente->name,
                        'tableSchema' => $Cliente->tableSchema,
                        'databaseTable' => $Cliente->databaseTable,
                        'type' => 'INNER',
                        'conditions' => 'Notafis.cliente = Cliente.codigo'
                    )
                ),
                'conditions' => $conditions
                )
            );
        }

        // debug($totalNotas);

        if ($this->useDbConfig != 'test_suite')
            $this->databaseTable = 'dbNavegarqNatec';
        return isset($totalNotas[0]['totalNotas']) ? $totalNotas[0]['totalNotas'] : 0;
    }

    function rankingFaturamento($filtros, $limit = 100, $page = 1, $tipo_ranking = 'faturamento', $order = 'ROW_NUMBER() OVER ( ORDER BY registro)'){
        $this->Cliente  =& ClassRegistry::init('Cliente');
        $this->Notaite  =& ClassRegistry::init('Notaite');
        $this->NProduto =& ClassRegistry::init('NProduto');


        $totalNotas = $this->totalRankingFaturamento($filtros);
        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial']);
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final']);
        $this->Notafis = ClassRegistry::init('Notafis');
        $dbo = $this->getDataSource();
        $conditions = array('cancela' => 'N', 'Notafis.dtemissao BETWEEN ? AND ?' => array($filtros['data_inicial'], $filtros['data_final']) );
        if (isset($filtros['empresa']) && (!empty($filtros['empresa'])))
            $conditions['Notafis.empresa'] = $filtros['empresa'];

        /*
        if (isset($filtros['produtos']) && (!empty($filtros['produtos'])))
            $conditions['produtos'] = $filtros['produtos'];
        */

        if ($this->useDbConfig != 'test_suite') {
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER) {
                $this->databaseTable = 'dbNavegarqLider';
                $this->Notafis->databaseTable = 'dbNavegarqLider';
            }
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC) {
                $this->databaseTable = 'dbNavegarqNatec';
                $this->Notafis->databaseTable = 'dbNavegarqNatec';
            }
        }

        if (in_array($tipo_ranking, array('gestores', 'corretoras', 'seguradoras'))) {
            if ($tipo_ranking == 'gestores') {
                $this->ModelBase =  ClassRegistry::init('Gestor');
                $base_foreign_key = 'codigo_gestor';
                $colunas_options = array('Gestor.codigo', 'Gestor.nome');
                $filtros['corretoras'] = NULL;
                $filtros['seguradoras'] = NULL;
            } else if ($tipo_ranking == 'corretoras') {
                $this->ModelBase =  ClassRegistry::init('Corretora');
                $base_foreign_key = 'codigo_corretora';
                $colunas_options = array('Corretora.codigo', 'Corretora.nome');
                $filtros['seguradoras'] = NULL;
                $filtros['gestores'] = NULL;
            } else if ($tipo_ranking == 'seguradoras') {
                $this->ModelBase =  ClassRegistry::init('Seguradora');
                $base_foreign_key = 'codigo_seguradora';
                $colunas_options = array('Seguradora.codigo', 'Seguradora.nome');
                $filtros['corretoras'] = NULL;
                $filtros['gestores'] = NULL;
            }


            $this->ModelBase->recursive = -1;
            $campo_base_select = "isnull({$this->ModelBase->name}.codigo, 0) as $base_foreign_key";
            $conditions_select_notafis = "{$this->ModelBase->name}.codigo = Notafis.$base_foreign_key";
            $group_base_select = $this->ModelBase->name . '.codigo';

            $joins_base_select = array(
                array(
                    'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                    'alias' => $this->Cliente->name,
                    'type' => 'LEFT',
                    'conditions' => 'Cliente.codigo = cliente',
                ),
                array(
                    'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                    'alias' => $this->ModelBase->name,
                    'type' => 'LEFT',
                    'conditions' => "{$this->ModelBase->name}.codigo = Cliente.{$base_foreign_key}"
                )
            );
        } else {
            $this->ModelBase =  ClassRegistry::init('Cliente');
            $campo_base_select = 'Notafis.cliente';
            $joins_base_select = array();

            if (isset($filtros['gestores']) && (!empty($filtros['gestores'])))
                $conditions['Cliente.codigo_gestor']     = $filtros['gestores'];
            if (isset($filtros['corretoras']) && (!empty($filtros['corretoras'])))
                $conditions['Cliente.codigo_corretora']  = $filtros['corretoras'];
            if (isset($filtros['seguradoras']) && (!empty($filtros['seguradoras'])))
                $conditions['Cliente.codigo_seguradora'] = $filtros['seguradoras'];


            if (isset($filtros['produtos']) && (!empty($filtros['produtos']))) {
                $joins_base_select = array(
                    array(
                        'table' => $this->Notaite->databaseTable . '.' . $this->Notaite->tableSchema . '.' . $this->Notaite->useTable,
                        'alias' => 'Notaite',
                        'type' => 'INNER',
                        'conditions' => array(
                            "Notaite.nnotafis = Notafis.numero",
                            "Notafis.empresa = Notaite.empresa",
                            "Notafis.seq = Notaite.seq",
                            "Notafis.serie = Notaite.serie"
                        )
                    ),
                    array(
                        'table' => $this->NProduto->databaseTable . '.' . $this->NProduto->tableSchema . '.' . $this->NProduto->useTable,
                        'alias' => 'NProduto',
                        'type' => 'INNER',
                        'conditions' => array("NProduto.codigo = Notaite.produto","NProduto.codigo" => $filtros['produtos'])
                    ),
                    array(
                        'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "Cliente.codigo = Notafis.cliente",
                        )
                    ),
                );
            } else {
                $joins_base_select = array(
                    array(
                        'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "Cliente.codigo = Notafis.cliente",
                        )
                    ),
                );
            }

            $conditions_select_notafis = $this->ModelBase->name . '.codigo = Notafis.cliente';
            $group_base_select = 'Notafis.cliente';
            $colunas_options = array('Cliente.codigo', 'Cliente.razao_social');
        }

        $max_ou_sum = (isset($filtros['produtos']) && (!empty($filtros['produtos'])))?'MAX(Notafis.vlmerc) as vlmerc':'SUM(Notafis.vlmerc) as vlmerc';
        $order_max_ou_sum = (isset($filtros['produtos']) && (!empty($filtros['produtos'])))?'ROW_NUMBER() OVER (order by MAX(Notafis.vlmerc) desc) as registro':'ROW_NUMBER() OVER (order by sum(Notafis.vlmerc) desc) as registro';

        $base_select = array(
            'fields' => array(
                $order_max_ou_sum,
                $campo_base_select,
                $max_ou_sum,
                'MAX(numero) as numero'
            ),
            'table' => $this->Notafis->useTable,
            'databaseTable' => $this->Notafis->databaseTable,
            'tableSchema' => $this->Notafis->tableSchema,
            'alias' => $this->Notafis->name,
            'limit' => null,
            'offset' => null,
            'joins' => $joins_base_select,
            'conditions' => $conditions,
            'order' => null,
            'group' => $group_base_select
        );

        $select_notafis = $dbo->buildStatement($base_select, $this->Notafis);

        $with_notas = "WITH NOTAS AS (".$select_notafis.")";

        $base_select['alias'] = 'Notafis';
        $coluna_acumulado = "(SELECT sum(vlmerc*100/{$totalNotas}) from ({$dbo->buildStatement($base_select, $this->Notafis)}) as tmp where tmp.registro<={$this->Notafis->name}.registro) as acumulado";

        if (isset($filtros['produtos']) && (!empty($filtros['produtos']))) {
            $options = array(
                'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                'alias' => $this->ModelBase->name,
                'joins' => array(
                    array(
                        'table' => "({$select_notafis})",
                        'alias' => 'Notafis',
                        'type' => 'RIGHT',
                        'conditions' => $conditions_select_notafis,
                    ),

                ),
                'fields' => array_merge(
                    array(
                        'Notafis.vlmerc',
                        'ROW_NUMBER() OVER (    ORDER BY    registro    ) AS registro',
                        '(Notafis.vlmerc*100/'.$totalNotas.') AS porcentagem',
                        $coluna_acumulado,
                    ),
                    $colunas_options
                ),
                'order' => $order,
                'limit' => $limit,
                'page' => $page,
                'group' => null,

                'recursive' => -1
            );
        } else {
            $options = array(
                'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                'alias' => $this->ModelBase->name,
                'joins' => array(
                    array(
                        'table' => "({$select_notafis})",
                        'alias' => 'Notafis',
                        'type' => 'RIGHT',
                        'conditions' => $conditions_select_notafis,
                    )
                ),
                'fields' => array_merge(
                    array(
                        'Notafis.vlmerc',
                        'ROW_NUMBER() OVER (    ORDER BY        registro    ) AS registro',
                        '(Notafis.vlmerc*100/'.$totalNotas.') AS porcentagem',
                        $coluna_acumulado,
                    ),
                    $colunas_options
                ),
                'order' => $order,
                'limit' => $limit,
                'page' => $page,
                'recursive' => -1,
                'group' => null,

            );
        }

        $conditions = null;
        if (isset($filtros['razao_social']) && !empty($filtros['razao_social']))
            $conditions['Cliente.razao_social like'] = '%'.$filtros['razao_social'].'%';
        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
            $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
        if (isset($filtros['gestores']) && (!empty($filtros['gestores'])))
            $conditions['Cliente.codigo_gestor'] = $filtros['gestores'];
        if (isset($filtros['corretoras']) && (!empty($filtros['corretoras'])))
            $conditions['Cliente.codigo_corretora'] = $filtros['corretoras'];
        if (isset($filtros['seguradoras']) && (!empty($filtros['seguradoras'])))
            $conditions['Cliente.codigo_seguradora'] = $filtros['seguradoras'];


        $options['conditions'] = $conditions;

        $results = $this->ModelBase->find('all', $options);

        if ($this->useDbConfig != 'test_suite') {
            $this->databaseTable = 'dbNavegarqNatec';
            $this->Notafis->databaseTable = 'dbNavegarqNatec';
        }
        return $results;
    }

    /**
     * Overridden paginate method - group by week, away_team_id and home_team_id
     */
    function paginate($filtros, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        if (isset($extra['tipo_ranking'])) {
            return $this->rankingFaturamento($filtros, $limit, $page, strtolower($extra['tipo_ranking']), $order);
        }
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group'));
    }

    /**
     * Overridden paginateCount method
     */
    function paginateCount($filtros = null, $recursive = 0, $extra = array()) {
        $base_foreign_key = 'cliente';
        $joins_base_select = array();

        $this->Notaite  =& ClassRegistry::init('Notaite');
        $this->NProduto =& ClassRegistry::init('NProduto');


        if (isset($extra['tipo_ranking'])) {
            $tipo_ranking = $extra['tipo_ranking'];

            $this->Cliente =& ClassRegistry::init('Cliente');

            if (in_array($tipo_ranking, array('gestores', 'corretoras', 'seguradoras'))) {


                if ($tipo_ranking == 'gestores') {
                    $base_foreign_key = 'codigo_gestor';
                    $this->ModelBase = ClassRegistry::init('Gestor');
                } elseif ($tipo_ranking == 'corretoras') {
                    $base_foreign_key = 'codigo_corretora';
                    $this->ModelBase = ClassRegistry::init('Corretora');
                } elseif ($tipo_ranking == 'seguradoras') {
                    $base_foreign_key = 'codigo_seguradora';
                    $this->ModelBase = ClassRegistry::init('Seguradora');
                }

                $joins_base_select = array(
                    array(
                        'table'      => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                        'alias'      => $this->Cliente->name,
                        'type'       => 'LEFT',
                        'conditions' => 'Cliente.codigo = cliente',
                    ),
                    array(
                        'table'      => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                        'alias'      => $this->ModelBase->name,
                        'type'       => 'LEFT',
                        'conditions' => "{$this->ModelBase->name}.codigo = Cliente.{$base_foreign_key}"
                    )
                );
            }


            if (isset($filtros['produtos']) && (!empty($filtros['produtos']))) {
                $joins_base_select = array(
                    array(
                        'table' => $this->Notaite->databaseTable . '.' . $this->Notaite->tableSchema . '.' . $this->Notaite->useTable,
                        'alias' => 'Notaite',
                        'type' => 'INNER',
                        'conditions' => array(
                            "Notaite.nnotafis = Notafis.numero",
                            "Notafis.empresa = Notaite.empresa",
                            "Notafis.seq = Notaite.seq",
                            "Notafis.serie = Notaite.serie"
                        )
                    ),
                    array(
                        'table' => $this->NProduto->databaseTable . '.' . $this->NProduto->tableSchema . '.' . $this->NProduto->useTable,
                        'alias' => 'NProduto',
                        'type' => 'INNER',
                        'conditions' => array("NProduto.codigo = Notaite.produto","NProduto.codigo" => $filtros['produtos'])
                    ),
                    array(
                        'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "Cliente.codigo = Notafis.cliente",
                        )
                    ),
                );
            } else {
                $joins_base_select = array(
                    array(
                        'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "Cliente.codigo = Notafis.cliente",
                        )
                    ),
                );
            }

            $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial']);
            $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final']);
            if ($this->useDbConfig != 'test_suite') {
                if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER)
                    $this->databaseTable = 'dbNavegarqLider';
                if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC)
                    $this->databaseTable = 'dbNavegarqNatec';
            }
            $conditions = array('Notafis.cancela' => 'N', 'Notafis.dtemissao BETWEEN ? AND ?' => array($filtros['data_inicial'], $filtros['data_final']));
            if (isset($filtros['empresa']) && (!empty($filtros['empresa'])))
                $conditions['Notafis.empresa'] = $filtros['empresa'];
            if (isset($filtros['razao_social']) && !empty($filtros['razao_social']))
                $conditions['Cliente.razao_social like'] = '%'.$filtros['razao_social'].'%';
            if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
                $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
            if (isset($filtros['gestores']) && (!empty($filtros['gestores'])))
                $conditions['Cliente.codigo_gestor'] = $filtros['gestores'];
            if (isset($filtros['corretoras']) && (!empty($filtros['corretoras'])))
                $conditions['Cliente.codigo_corretora'] = $filtros['corretoras'];
            if (isset($filtros['seguradoras']) && (!empty($filtros['seguradoras'])))
                $conditions['Cliente.codigo_seguradora'] = $filtros['seguradoras'];


            if($base_foreign_key == 'cliente')
                $base_foreign_key = 'Notafis.cliente';

            $count = $this->find('first', array('fields' => "count(distinct {$base_foreign_key}) as cliente", 'joins' => $joins_base_select, 'conditions' => $conditions));
            if ($this->useDbConfig != 'test_suite')
                $this->databaseTable = 'dbNavegarqNatec';
            return $count[0]['cliente'];
        }



        return $this->find('count', compact('conditions', 'recursive'));
    }


    function faturamentoAnual($filtros){
            $tipo_ranking = isset($filtros['tipo_ranking']) && !empty($filtros['tipo_ranking']) ? $filtros['tipo_ranking'] : 'faturamento';
            $joins_base_select = array();
            $base_foreign_key = null;

            if (in_array($tipo_ranking, array('gestores', 'corretoras', 'seguradoras'))) {
                $this->Cliente = ClassRegistry::init('Cliente');

                if ($tipo_ranking == 'gestores') {
                    $base_foreign_key = 'codigo_gestor';
                    $this->ModelBase = ClassRegistry::init('Gestor');
                } elseif ($tipo_ranking == 'corretoras') {
                    $base_foreign_key = 'codigo_corretora';
                    $this->ModelBase = ClassRegistry::init('Corretora');
                } elseif ($tipo_ranking == 'seguradoras') {
                    $base_foreign_key = 'codigo_seguradora';
                    $this->ModelBase = ClassRegistry::init('Seguradora');
                }

                $joins_base_select = array(
                    array(
                        'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
                        'alias' => $this->Cliente->name,
                        'type' => 'LEFT',
                        'conditions' => 'Cliente.codigo = cliente',
                    ),
                    array(
                        'table' => $this->ModelBase->databaseTable . '.' . $this->ModelBase->tableSchema . '.' . $this->ModelBase->useTable,
                        'alias' => $this->ModelBase->name,
                        'type' => 'LEFT',
                        'conditions' => "{$this->ModelBase->name}.codigo = Cliente.{$base_foreign_key}"
                    )
                );
            }

        $options = array(
            'fields' => array(
                'substring(convert(varchar,Notafis.dtemissao, 103), 4, 7) as ano_mes',
                'SUM(Notafis.vlmerc) as vlmerc',
            ),
                        'joins' => $joins_base_select,
            'conditions' => array(
                'YEAR(Notafis.dtemissao)' => $filtros['ano'],
                'Notafis.cancela' => 'N'
            ),
            'group' => 'substring(convert(varchar, notafis.dtemissao, 103), 4,7)',
            'order' => 'substring(convert(varchar, notafis.dtemissao, 103), 4,7)',
        );

        if (isset($filtros['codigo_cliente'])  && !empty($filtros['codigo_cliente']))
            $options['conditions']['cliente'] = str_pad($filtros['codigo_cliente'], 10, '0', STR_PAD_LEFT);
        if (!empty($filtros[$base_foreign_key]))
            $options['conditions'][$this->ModelBase->name.'.codigo'] = str_pad($filtros[$base_foreign_key], 10, '0', STR_PAD_LEFT);
        if (isset($filtros['empresa'])  && !empty($filtros['empresa']))
            $options['conditions']['empresa'] = str_pad($filtros['empresa'], 2, '0', STR_PAD_LEFT);
        if ($this->useDbConfig != 'test_suite') {
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_LIDER)
                $this->databaseTable = 'dbNavegarqLider';
            if ($filtros['grupo_empresa'] == LojaNaveg::GRUPO_NATEC)
                $this->databaseTable = 'dbNavegarqNatec';
        }


        $faturamentoCliente = $this->find('all', $options);
        if ($this->useDbConfig != 'test_suite')
            $this->databaseTable = 'dbNavegarqNatec';
        $array_ano = array();
        for($i=1; $i<13; $i++){
            array_push($array_ano, array(
                    array(
                        'ano_mes' => $i.'/'.$filtros['ano'],
                        'vlmerc' => 0
                    )
                )
            );
        }
        foreach($faturamentoCliente as $faturaCliente){
            $array_ano = $this->insere_array_ano($array_ano, $faturaCliente);
        }
        return $array_ano;
    }

    function listaNfsPorBanco($filtros){
        $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN, $filtros['mes'], $filtros['ano']);
        $Tranrec = $this->Tranrec = ClassRegistry::init('Tranrec');
        $Banco = $this->Banco = ClassRegistry::init('Banco');
        $options = array(
            'fields' => array(
                'COUNT(distinct Notafis.numero) as numero_de_notas',
                'Tranrec.banco as banco_nota',
                'Banco.banco as numero_banco',
                'Banco.descricao as nome_banco'
            ),
            'joins' => array(
                array(
                    'table' => $Tranrec->useTable,
                    'alias' => $Tranrec->name,
                    'tableSchema' => $Tranrec->tableSchema,
                    'databaseTable' => $Tranrec->databaseTable,
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Tranrec.numero = Notafis.numero',
                        'Tranrec.empresa = Notafis.empresa',
                        'Tranrec.debcred = "C"',
                    ),
                ),
                array(
                    'table' => $Banco->useTable,
                    'alias' => $Banco->name,
                    'tableSchema' => $Banco->tableSchema,
                    'databaseTable' => $Banco->databaseTable,
                    'type' => 'LEFT',
                    'conditions' => array('Tranrec.banco = Banco.codigo'),
                )
            ),
            'conditions' => array(
                'Notafis.dtemissao BETWEEN ? AND ?' => array($filtros['ano'].'-'.$filtros['mes'].'-01 00:00:00', $filtros['ano'].'-'.$filtros['mes'].'-'.$numero_dias_mes.' 23:59:59'),
                'Notafis.empresa' => '03',
                'Notafis.cancela = "N"',
                'Tranrec.ordem = "01"',
            ),
            'group' => array(
                'Tranrec.banco',
                'Banco.codigo',
                'Banco.descricao',
                'Banco.banco',
            ),
            'order' => 'numero_de_notas DESC',
        );
        $nfsPorBanco = $this->find('all', $options);
        return $nfsPorBanco;
    }

    function insere_array_ano($array_ano, $faturaCliente) {
        foreach($faturaCliente as $a){
            if($a['vlmerc'] != 0){
                $mes = (int)substr($a['ano_mes'],0,2);
                $mes--;
                $array_ano[$mes][0]['vlmerc'] = $a['vlmerc'];
            }
        }
        return $array_ano;
    }

public function segundaViaFaturamento($filtros){
            $conditions = array();
            if ( isset($filtros['Outbox']['codigo_cliente']) && !empty($filtros['Outbox']['codigo_cliente']) )
                $conditions[] = 'NotaFis.cliente = ' . (int)$filtros['Outbox']['codigo_cliente'];

            $this->bindModel(array(
               'belongsTo' => array(
                   'Gernfe' => array(
                       'className' => 'Gernfe',
                       'foreignKey' => 'numero'
                   ),
                   'RetornoNf' => array(
                       'className' => 'RetornoNf',
                       'foreignKey' => false,
                       'conditions' => 'Gernfe.numnfe = RetornoNf.nota_fiscal'
                   ),
                   'Outbox' => array(
                       'className' => 'Mailer.Outbox',
                       'foreignKey' => false,
                       'conditions' => 'RetornoNf.codigo = Outbox.foreign_key and Outbox.model is not null'
                   ),
                )
            ));

            $resultado = $this->find('first', array(
                    'fields' => array(
                         'Outbox.id'
                        ,'Outbox.to'
                        ,'Outbox.sent'
                        ,'Outbox.from'
                        ,'Outbox.subject'
                        ,'Outbox.content'
                    ),
                    'conditions' => $conditions,
                    'order' => 'Outbox.id DESC'
                )
            );
            return $resultado;
        }

        public function lista_emails($filtros){
            $conditions = array();
            if ( isset($filtros['Outbox']['codigo_cliente']) && !empty($filtros['Outbox']['codigo_cliente']) )
                $conditions[] = 'NotaFis.cliente = ' . (int)$filtros['Outbox']['codigo_cliente'];

                $data = date('Y-m-01 00:00:00', strtotime('-6 months'));
                $conditions[] = "Outbox.sent >= '".$data."'";

            $this->bindModel(array(
               'belongsTo' => array(
                   'Gernfe' => array(
                       'className' => 'Gernfe',
                       'foreignKey' => 'numero'
                   ),
                   'RetornoNf' => array(
                       'className' => 'RetornoNf',
                       'foreignKey' => false,
                       'conditions' => 'Gernfe.numnfe = RetornoNf.nota_fiscal'
                   ),
                   'Outbox' => array(
                       'className' => 'Mailer.Outbox',
                       'foreignKey' => false,
                       'conditions' => array('RetornoNf.codigo = Outbox.foreign_key','Outbox.model' => 'RetornoNf'),
                   ),
                )
            ));

            $resultado = $this->find('all', array(
                    'fields' => array(
                         'Outbox.id'
                        ,'Outbox.to'
                        ,'Outbox.sent'
                        ,'Outbox.from'
                        ,'Outbox.subject'
                        ,'Outbox.content'
                    ),
                    'conditions' => $conditions,
                    'order' => 'Outbox.sent DESC'
                )
            );
            return $resultado;
        }

        public function faturamentoEImpostosPorEmpresa($filtros, $grupo_empresa = 1){
            if ($this->useDbConfig != 'test_suite') {
                if ($grupo_empresa == LojaNaveg::GRUPO_LIDER) {
                    $this->databaseTable            = 'dbNavegarqLider';
                    $this->Notafis->databaseTable   = 'dbNavegarqLider';
                }
                if ($grupo_empresa == LojaNaveg::GRUPO_NATEC) {
                    $this->databaseTable            = 'dbNavegarqNatec';
                    $this->Notafis->databaseTable   = 'dbNavegarqNatec';
                }
            }

            $conditions = array();
            $data_inicial = AppModel::dateToDbDate( $filtros['Notafis']['data_inicial'] );
            $data_final   = AppModel::dateToDbDate( $filtros['Notafis']['data_final'] );
            $conditions['Notafis.dtemissao BETWEEN ? AND ?'] = array($data_inicial, $data_final);

            if ( isset($filtros['Notafis']['empresa']) && !empty($filtros['Notafis']['empresa']) )
                $conditions[] = 'NotaFis.empresa = ' . (int)$filtros['Notafis']['empresa'];

            $this->bindLazyNotaiteClienteProduto();

            $this->bindModel(array(
               'belongsTo' => array(
                   'Tranrec' => array(
                        'className' => 'Tranrec'
                       ,'foreignKey' => false
                       ,'conditions' => '
                                            Tranrec.empresa = Notafis.empresa and
                                            Tranrec.seqn    = Notafis.seq and
                                            Tranrec.numero  = Notafis.numero and
                                            Tranrec.serie   = Notafis.serie'
                   ),
                   'Adrec' => array(
                        'className' => 'Adrec'
                       ,'foreignKey' => false
                       ,'conditions' => '
                                            Tranrec.empresa   = Adrec.empresa and
                                            Tranrec.seqn      = Adrec.seqn and
                                            Tranrec.emitente  = Adrec.emitente and
                                            Tranrec.tipodoc   = Adrec.tipodoc and
                                            Tranrec.tipoemit  = Adrec.tipoemit and
                                            Tranrec.serie     = Adrec.serie and
                                            Tranrec.ordem     = Adrec.ordem'
                   ),
                )
            ));

            $resultado = $this->find('all', array(
                    'fields' => array(
                         'Notafis.empresa'
                        ,'Notafis.seq'
                        ,'Notafis.serie'
                        ,'Notafis.numero'
                        ,'Notafis.dtemissao'
                        ,'Notaite.item'
                        ,'Notaite.formula'
                        ,'Notafis.cliente'
                        ,'Notaite.produto' // --(falta descrição, aqui é só o código)
                        ,'Notafis.vlnota'
                        ,'Notafis.baseiss'
                        ,'Notaite.baseiss'
                        ,'Notafis.vliss' // --(falta a %iss)
                        ,'Notafis.vlpis'
                        ,'Notafis.vlcofins'
                        ,'Notafis.vlcsl' // --(na planilha está CSSL)
                        ,'Cliente.razao_social'

                        //--,'Notaite.vlretencao            --(É valor IRRF_NF ?)
                        //--,'Notafis.inss                  --(INSS ??)
                        //--,'Notafis.Ret_INSS              --(TipoNota ? ou Cancela ?)
                        //,'Notaite.nofiscaEletronica   --(não tem aqui)
                        //--,'Notafis.percentual_cssl       --(% CSSL não tem no banco)
                        //--,'Notaite.razao_social          --(precisa buscar em outra tabela)

                        ,'Produto.descricao'

                        ,'Tranrec.valor'
                        ,'Tranrec.dtpagto'
                        ,'Tranrec.dtvencto'
                        ,'Tranrec.seq'
                        ,'Adrec.liquidado'
                    ),
                    //'group' => 'Notafis.empresa',
                    'conditions' => $conditions,
                    'order' => 'Notafis.numero',
                    'group' => array('Notafis.empresa'
                                ,'Notafis.seq'
                                ,'Notafis.serie'
                                ,'Notafis.numero'
                                ,'Notafis.dtemissao'
                                ,'Notaite.item'
                                ,'Notaite.formula'
                                ,'Notafis.cliente'
                                ,'Notaite.produto' // --(falta descrição, aqui é só o código)
                                ,'Notafis.vlnota'
                                ,'Notafis.baseiss'
                                ,'Notaite.baseiss'
                                ,'Notafis.vliss' // --(falta a %iss)
                                ,'Notafis.vlpis'
                                ,'Notafis.vlcofins'
                                ,'Notafis.vlcsl' // --(na planilha está CSSL)
                                ,'Cliente.razao_social'

                                //--,'Notaite.vlretencao            --(É valor IRRF_NF ?)
                                //--,'Notafis.inss                  --(INSS ??)
                                //--,'Notafis.Ret_INSS              --(TipoNota ? ou Cancela ?)
                                //,'Notaite.nofiscaEletronica   --(não tem aqui)
                                //--,'Notafis.percentual_cssl       --(% CSSL não tem no banco)
                                //--,'Notaite.razao_social          --(precisa buscar em outra tabela)

                                ,'Produto.descricao'

                                ,'Tranrec.valor'
                                ,'Tranrec.dtpagto'
                                ,'Tranrec.dtvencto'
                                ,'Tranrec.seq'
                                ,'Adrec.liquidado'
                        )
                )
            );
            return $resultado;
        }

        public function bindLazyNotaiteClienteProduto() {
            $this->bindModel(array(
               'belongsTo' => array(
                   'Notaite' => array(
                        'className' => 'Notaite'
                       ,'foreignKey' => false
                       ,'conditions' => '
                            Notafis.empresa = Notaite.empresa and
                            Notafis.numero = Notaite.nnotafis and
                            Notafis.seq = Notaite.seq and
                            Notafis.serie = Notaite.serie'
                   ),
                   'Cliente' => array(
                        'className' => 'Cliente'
                       ,'foreignKey' => false
                       ,'conditions' => 'Notaite.cliente = Cliente.codigo'
                   ),
                   'Produto' => array(
                        'className' => 'Produto'
                       ,'foreignKey' => false
                       ,'conditions' => 'Notaite.produto = Produto.codigo'
                   )
                )
            ));
        }

        function listaFaturamentoTotal($filtros){
        $Notafis = $this->Notafis = ClassRegistry::init('Notafis');
        $fields  = array(
                        'SUM(Notafis.vlmerc) as valor_total_merc',
                        'SUBSTRING(CONVERT(VARCHAR,Notafis.dtemissao, 103), 4, 7) AS ano_mes',
                    );

        $conditions = array(
            'Notafis.dtemissao between ? AND ?' =>  array(
                $filtros['ano'].'-01-01 00:00:00',$filtros['ano'].'-12-31 23:59:59'
            ),
        );

        if(isset($filtros['empresa'])&& $filtros['empresa'] !=null){
            $conditions['Notafis.empresa'] = $filtros['empresa'];
        }
        $conditions['Notafis.cancela'] = 'N';
        $group = array('SUBSTRING(CONVERT(VARCHAR,dtemissao, 103), 4, 7) ');
        $order = array('ano_mes ASC');

         return $this->find('all',compact('fields','conditions','order','group'));
    }

    function analiticoFaturamentoPorDataDeCadastro($ano, $ordenar = false, $return_querystring = true, $mes_cadastro_cliente = '') {
        $bindOptions = array(
            'belongsTo' => array(
                'Cliente' => array('foreignKey' => 'cliente', 'type' => 'RIGHT'),
            ),
            'hasOne' => array(
                'Notaite' => array(
                    'foreignKey' => false,
                    'conditions' => array(
                        'Notafis.empresa = Notaite.empresa',
                        'Notafis.numero = Notaite.nnotafis',
                        'Notafis.seq = Notaite.seq',
                        'Notafis.serie = Notaite.serie',
                    )
                ),
            ),
        );
        if ($this->useDbConfig != 'test_suite') {
            $bindOptions['hasOne']['NProduto'] = array('foreignKey' => false, 'conditions' => 'Notaite.produto = NProduto.codigo');
        } else {
            $bindOptions['hasOne']['NProduto'] = array('className' => 'NProdutoTest', 'foreignKey' => false, 'conditions' => 'Notaite.produto = NProduto.codigo');
        }
        $this->bindModel($bindOptions);
        $fields = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'NProduto.codigo',
            'NProduto.descricao',
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 1 THEN Notafis.vlmerc ELSE 0 END) AS [Jan]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 2 THEN Notafis.vlmerc ELSE 0 END) AS [Fev]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 3 THEN Notafis.vlmerc ELSE 0 END) AS [Mar]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 4 THEN Notafis.vlmerc ELSE 0 END) AS [Abr]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 5 THEN Notafis.vlmerc ELSE 0 END) AS [Mai]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 6 THEN Notafis.vlmerc ELSE 0 END) AS [Jun]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 7 THEN Notafis.vlmerc ELSE 0 END) AS [Jul]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 8 THEN Notafis.vlmerc ELSE 0 END) AS [Ago]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 9 THEN Notafis.vlmerc ELSE 0 END) AS [Set]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 10 THEN Notafis.vlmerc ELSE 0 END) AS [Out]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 11 THEN Notafis.vlmerc ELSE 0 END) AS [Nov]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} AND MONTH(Notafis.dtemissao) = 12 THEN Notafis.vlmerc ELSE 0 END) AS [Dez]",
            "SUM(CASE WHEN YEAR(Notafis.dtemissao) = {$ano} THEN Notafis.vlmerc ELSE 0 END) AS [total_faturado]",
        );
        $group = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'NProduto.codigo',
            'NProduto.descricao',
        );
        if ($ordenar) {
            $order = array(
                'Cliente.codigo',
                'Cliente.razao_social',
                'NProduto.codigo',
                'NProduto.descricao',
            );
        }
        $conditions = array(
        );
        if (empty($mes_cadastro_cliente)) {
            $conditions['Cliente.data_inclusao BETWEEN ? AND ?'] = array($ano.'0101 00:00:00', $ano.'1231 23:59:59');
        } else {
            $mes_cadastro_cliente = str_pad($mes_cadastro_cliente, 2, '0', STR_PAD_LEFT);
            $conditions['Cliente.data_inclusao BETWEEN ? AND ?'] = array($ano."{$mes_cadastro_cliente}01 00:00:00", date('Ymt H:i:s', strtotime($ano."{$mes_cadastro_cliente}01 23:59:59")));
        }
        if ($return_querystring) {
            return $this->find('sql', compact('fields', 'group', 'conditions', 'order'));
        } else {
            return $this->find('all', compact('fields', 'group', 'conditions', 'order'));
        }
    }

    function sinteticoFaturamentoPorDataDeCadastro($ano) {
        $analitico = $this->analiticoFaturamentoPorDataDeCadastro($ano);
        $dbo = $this->getDataSource();
        $sintetico = $dbo->buildStatement(array(
            'fields' => array(
                "$ano AS ano",
                "SUM([Jan]) AS [Jan]",
                "SUM([Fev]) AS [Fev]",
                "SUM([Mar]) AS [Mar]",
                "SUM([Abr]) AS [Abr]",
                "SUM([Mai]) AS [Mai]",
                "SUM([Jun]) AS [Jun]",
                "SUM([Jul]) AS [Jul]",
                "SUM([Ago]) AS [Ago]",
                "SUM([Set]) AS [Set]",
                "SUM([Out]) AS [Out]",
                "SUM([Nov]) AS [Nov]",
                "SUM([Dez]) AS [Dez]",
                'SUM(total_faturado) AS total',
            ),
            'table' => "({$analitico})",
            'alias' => 'Analitico',
            'limit' => null,
            'offset' => null,
            'joins' => array(),
            'conditions' => null,
            'order' => null,
            'group' => null
        ), $this);
        return $this->query($sintetico);
    }

    function clientesAtivosPorDataFaturamento($ano) {
        $fields = array(
            'MONTH(dtemissao) AS mes',
            'cliente',
        );
        $group = array(
            'MONTH(dtemissao)',
            'cliente',
        );
        $conditions = array('dtemissao BETWEEN ? AND ?' => array($ano.'0101 00:00:00',$ano.'1231 23:59:59'));
        $analitico = $this->find('sql', compact('fields','group','conditions'));
        $dbo = $this->getDataSource();
        $sintetico = $dbo->buildStatement(array(
            'fields' => array(
                "SUM(CASE WHEN mes = 1 THEN 1 ELSE 0 END) AS [Jan]",
                "SUM(CASE WHEN mes = 2 THEN 1 ELSE 0 END) AS [Fev]",
                "SUM(CASE WHEN mes = 3 THEN 1 ELSE 0 END) AS [Mar]",
                "SUM(CASE WHEN mes = 4 THEN 1 ELSE 0 END) AS [Abr]",
                "SUM(CASE WHEN mes = 5 THEN 1 ELSE 0 END) AS [Mai]",
                "SUM(CASE WHEN mes = 6 THEN 1 ELSE 0 END) AS [Jun]",
                "SUM(CASE WHEN mes = 7 THEN 1 ELSE 0 END) AS [Jul]",
                "SUM(CASE WHEN mes = 8 THEN 1 ELSE 0 END) AS [Ago]",
                "SUM(CASE WHEN mes = 9 THEN 1 ELSE 0 END) AS [Set]",
                "SUM(CASE WHEN mes = 10 THEN 1 ELSE 0 END) AS [Out]",
                "SUM(CASE WHEN mes = 11 THEN 1 ELSE 0 END) AS [Nov]",
                "SUM(CASE WHEN mes = 12 THEN 1 ELSE 0 END) AS [Dez]",
            ),
            'table' => "({$analitico})",
            'alias' => 'Analitico',
            'limit' => null,
            'offset' => null,
            'joins' => array(),
            'conditions' => null,
            'order' => null,
            'group' => null
        ), $this);
        return $this->query($sintetico);
    }

    function retorna_nota_status_pedido($pedido) {

        
        $Notaite = ClassRegistry::init('Notaite');
        $Integfat = ClassRegistry::init('Integfat');


        $fields =  array(
                    'Notafis.numero',
                    'Notafis.cancela'
                );


        $joins =  array(
                array(
                    'table' => $Notaite->useTable,
                    'alias' => 'Notaite',
                    'tableSchema' => $Notaite->tableSchema,
                    'databaseTable' => $Notaite->databaseTable,
                    'type' => 'INNER',
                    'conditions' => array(
                        'Notaite.nnotafis = Notafis.numero',
                        'Notaite.empresa = Notafis.empresa'
                    ),
                ),
                array(
                    'table' => $Integfat->useTable,
                    'alias' => 'Integfat',
                    'tableSchema' => $Integfat->tableSchema,
                    'databaseTable' => $Integfat->databaseTable,
                    'type' => 'INNER',
                    'conditions' => array(
                        'Integfat.npedido = Notaite.npedido COLLATE SQL_Latin1_General_CP1_CI_AS',
                        'Integfat.empresa = Notafis.empresa'
                    ),
                )
            );


        $conditions = array(
                'Integfat.seq' => $pedido,
            );

        $group =  array(
                    'Notafis.numero',
                    'Notafis.cancela'
                );


        $notas = $this->find('list',compact('fields','joins','conditions','group'));

        if(in_array('N', $notas) || empty($notas)){
            return 0;
        } else {
            $nota_cancelada = array_search('S', $notas);
            return $nota_cancelada;
        }
        
        return 0;
    }
}

?>