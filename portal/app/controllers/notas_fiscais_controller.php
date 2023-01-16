<?php
class NotasFiscaisController extends AppController {
    public $name = 'NotasFiscais';
    public $helpers = array('Highcharts');
    var $uses = array('Notafis', 'Cliente', 'LojaNaveg', 'Gestor', 'Corretora', 'Seguradora', 'NProduto', 'Notaite');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('clientes_por_gestor'));
    }

    function estatistica_notafis() {
        $this->pageTitle = 'Estatística de Notas Fiscais';
        $year = date('Y');
        $notafis = $this->Notafis->estatisticaNotafis($year);
        $eixo_x = array();
        $series = array(
            0 => array('name' => "'Notas Fiscais'", 'values' => array()),
            1 => array('name' => "'Notas Enviadas'", 'values' => array()),
			2 => array('name' => "'Notas Canceladas'", 'values' => array()),
            3 => array('name' => "'Clientes'", 'values' => array())
        );
        foreach($notafis as $chave => $mes) {
            array_push($eixo_x, "'".$mes['Notafis']['ano_mes']."'");
            if(isset($mes['Notafis']['qtd_nf'])){array_push($series[0]['values'], $mes['Notafis']['qtd_nf']);}
            array_push($series[1]['values'], $mes['Notafis']['qtd_envio']);
			if(isset($mes['Notafis']['qtd_nf_canceladas'])){array_push($series[2]['values'], $mes['Notafis']['qtd_nf_canceladas']);}
            array_push($series[3]['values'], $mes['Notafis']['qtd_cliente']);
        }
        $this->set(compact('notafis', 'eixo_x', 'series'));
    }

    function ranking_faturamento(){
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_GRUPOS_ECONOMICOS;
        $this->data['Notaite']['mes'] = date('m');
        $this->data['Notaite']['ano'] = date('Y');
        $filtros = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('controller' => 'itens_notas_fiscais', 'action' => 'ranking_faturamento'));
    }

    function ranking_gestores() {
        $this->data['Notaite']['mes'] = date('m');
        $this->data['Notaite']['ano'] = date('Y');
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['grupo_empresa'] = 1;
        $this->data['Notaite']['empresa'] = null;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_GESTORES;
        $filtros = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('controller' => 'itens_notas_fiscais', 'action' => 'ranking_faturamento'));
    }

    function ranking_corretora(){
        $this->data['Notaite']['mes'] = date('m');
        $this->data['Notaite']['ano'] = date('Y');
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['grupo_empresa'] = 1;
        $this->data['Notaite']['empresa'] = null;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_CORRETORAS;
        $filtros = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('controller' => 'itens_notas_fiscais', 'action' => 'ranking_faturamento'));
    }

    function ranking_seguradora(){
        $this->data['Notaite']['mes'] = date('m');
        $this->data['Notaite']['ano'] = date('Y');
        $this->data['Notaite']['level'] = 0;
        $this->data['Notaite']['grupo_empresa'] = 1;
        $this->data['Notaite']['empresa'] = null;
        $this->data['Notaite']['agrupamento'] = Notaite::AGRP_SEGURADORAS;
        $filtros = $this->Filtros->controla_sessao($this->data, 'Notaite');
        $this->redirect(array('controller' => 'itens_notas_fiscais', 'action' => 'ranking_faturamento'));
    }

    function comparativo_anual( $nova_janela = null ){
        if ($nova_janela) 
            $this->layout = 'new_window';

        $this->pageTitle = 'Comparativo Anual';
        if($this->RequestHandler->isPost()){
            $data = $this->data['Notafis'];

            $dados2 = $this->Notafis->faturamentoAnual($data);
            $data['ano'] --;
            $dados = $this->Notafis->faturamentoAnual($data);
            
            if (!empty($data['codigo_cliente']))
                                            $cliente = $this->Cliente->find('first', array('fields'=>array('Cliente.razao_social','Cliente.codigo'), 'conditions'=>'Cliente.codigo = '.$data['codigo_cliente']));
            if (!empty($this->data['Notafis']['empresa']))
                    $empresa = $this->LojaNaveg->carregar($this->data['Notafis']['empresa']);
            if (isset($this->data['Notafis']['tipo_ranking']))
                    $this->set('tipo_ranking', $this->data['Notafis']['tipo_ranking']);
            if (isset($this->data['Notafis']['nome']))
                    $this->set('nome', $this->data['Notafis']['nome']);
            if (isset($this->data['Notafis']['total']) && !empty($this->data['Notafis']['total']))
                    $this->set('total', $this->data['Notafis']['total']);
        } else {
                $this->data['Notafis']['ano'] = Date('Y');
                $this->data['Notafis']['grupo_empresa'] = 1;
        }
        $anos = Comum::listAnos();
        $empresas = $this->LojaNaveg->listEmpresas($this->data['Notafis']['grupo_empresa']);
        $grupos_empresas = $this->LojaNaveg->listGrupos();
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId( $this->data['Notafis']['grupo_empresa'] );
        
        $this->set(compact('dados','dados2','cliente', 'anos', 'grupos_empresas', 'empresas', 'empresa', 'nome_grupo'));
    }
	
	function por_banco(){
            $this->pageTitle = 'Notas Fiscais por Banco';
            $filtros = $this->Filtros->controla_sessao($this->data, $this->Notafis->name);
            $this->data['Notafis'] = $filtros;
            $this->set('anos', Comum::listAnos());
            $this->set('meses', Comum::listMeses());
	}
	
	function por_banco_listagem(){
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Notafis->name);
		if($filtros != null){
			if(!empty($filtros['ano']) && !empty($filtros['mes'])){
				$notas_fiscais_por_banco = $this->Notafis->listaNfsPorBanco($filtros);
			}
		}
		$this->set(compact('notas_fiscais_por_banco'));
	}

    function consulta_envio_faturamento() {
        if (!empty($this->data)) {
            $this->loadModel('RetornoNf');
            $this->set('dados', $this->RetornoNf->dadosEnvioFaturamento($this->data));
        }
    }
    
    function faturamento_e_impostos_por_empresa() {
        $this->pageTitle = 'Faturamento e Impostos por Empresa';
        $this->LojaNaveg = ClassRegistry::init('LojaNaveg');
        $grupo_empresa   = LojaNaveg::GRUPO_BUONNY;
        $authUsuario     = $this->BAuth->user();
        $dados           = array();
        
        if(!empty($this->data)){
            $filtros = $this->data;
            
            if(isset($this->data['Notafis']['grupo_empresa']) && !empty($this->data['Notafis']['grupo_empresa']))
                $grupo_empresa  = $this->data['Notafis']['grupo_empresa'];
            
            if(isset($this->data['Notafis']['empresa']) && !empty($this->data['Notafis']['empresa']))
                $filtros['Notafis']['empresa'] = $this->data['Notafis']['empresa'];
            
            $dados = $this->Notafis->faturamentoEImpostosPorEmpresa($filtros, $grupo_empresa);
            
            Configure::write('debug',0); // inicia tratamento para export excel / csv
            header("Content-Type: application/force-download");
            header('Content-Disposition: attachment; filename="faturamento_e_impostos_por_empresa'.time().'.csv"');
            echo    'Emp'.';'.
                    'Seq'.';'.
                    'Sr'.';'.
                    'Numero'.';'.
                    'DtEmissao'.';'.
                    'Item'.';'.
                    'NFEletr'.';'.
                    'Formula'.';'.
                    'Cliente'.';'.
                    'Razao Social'.';'.
                    'Produto'.';'.
                    'Valor'.';'.
                    'Base ISS'.';'.
                    '% ISS'.';'.
                    'Vl, ISS'.';'.
                    'Vl, PIS'.';'.
                    'Vl, Cofins'.';'.
                    '% CSSL'.';'.
                    'CSSL'.';'.
                    'IRRF NF'.';'.
                    'VL INSS'.';'.
                    'RET_ISS'.';'.
                    'Valor1'.';'.
                    'Vencto1'.';'.
                    'Pagto1'.';'.
                    'Valor2'.';'.
                    'Vencto2'.';'.
                    'Pagto2'.';'.
                    'Entrada/Pagto Nota'.';'.
                    'Liquidado'.';'.
                    "\n";
            foreach($dados as $item){
                echo
                    $item['Notafis']['empresa'].';'.
                    $item['Notafis']['seq'].';'.
                    $item['Notafis']['serie'].';'.
                    $item['Notafis']['numero'].';'.
                    substr($item['Notafis']['dtemissao'], 0, 10).';'.
                    $item['Notaite']['item'].';'.
                    ';'.
                    $item['Notaite']['formula'].';'.
                    $item['Notafis']['cliente'].';'.
                    $item['Cliente']['razao_social'].';'.
                    $item['Produto']['descricao'].';'.
                    $item['Notafis']['vlnota'].';'.
                    $item['Notafis']['baseiss'].';'.
                    ';'.
                    $item['Notafis']['vliss'].';'.
                    $item['Notafis']['vlpis'].';'.
                    $item['Notafis']['vlcofins'].';'.
                    ';'.
                    $item['Notafis']['vlcsl'].';'.
                    ';'.
                    ';'.
                    ';'.
                    $item['Tranrec']['valor'].';'.
                    $item['Tranrec']['dtvencto'].';'.
                    $item['Tranrec']['dtpagto'].';'.
                    ';'.
                    ';'.
                    ';'.
                    $item['Tranrec']['seq'].';'.
                    $item['Adrec']['liquidado'].';'.
                    "\n";
            }
            exit;
            
        }else{
            $this->data['Notafis']['grupo_empresa'] = $grupo_empresa;
            $this->data['Notafis']['data_inicial']  = date('01/m/Y');
            $this->data['Notafis']['data_final']    = date('d/m/Y');
        }
        
        $empresas = $this->LojaNaveg->listEmpresas($grupo_empresa);
        $grupos_empresas = $this->LojaNaveg->listGrupos(array(1 => 'Buonny', 2 => 'Líder', 3 => 'Natec'));
        $nome_grupo = $this->LojaNaveg->nomeGrupoPorId($grupo_empresa);
        
        $this->set(compact('grupos_empresas','empresas','nome_grupo'));
    }

    function clientes_por_gestor() {
        if( $this->RequestHandler->isPost() ){            
            $this->paginate = array('conditions' => $this->data, 'limit' => 50, 'tipo_ranking' => 'faturamento');
            $dados = $this->paginate('Notafis');
        }
    }
	
	function comparativo_faturamento_cliente() {
		$this->pageTitle = 'Comparativo Mensal Cliente';
		$this->Gestor = ClassRegistry::init('Gestor');
		$this->Produto = ClassRegistry::init('Produto');
		$this->Notaite = ClassRegistry::init('Notaite');
		
		$meses = Comum::anoMes(0, 1);
		$gestores = $this->Gestor->listarNomesGestoresAtivos();
		$anos = Comum::listAnos(null, 1);
		$produtos = $this->NProduto->listar();
		
		if (!empty($this->data['Notafis'])) {
			if (empty($this->data['Notafis']['variacao'])) {
				$this->Notafis->invalidate('variacao', 'Informe um valor');
			} else {
				$dados = $this->Notaite->comparaFaturamento($this->data['Notafis']);
				ini_set('max_execution_time', 0);
				
				$dt_inicial = strtotime($this->data['Notafis']['ano_inicial'].'-'.$this->data['Notafis']['mes_inicial'].'-01');
				$dt_final   = strtotime($this->data['Notafis']['ano_final'].'-'.$this->data['Notafis']['mes_final'].'-01');

				if ($dt_inicial > $dt_final) {
					$ano_mes_inicial = $meses[$this->data['Notafis']['mes_final']].' de '.$this->data['Notafis']['ano_final'];
					$ano_mes_final  = $meses[$this->data['Notafis']['mes_inicial']].' de '.$this->data['Notafis']['ano_inicial'];
					$ano = substr($ano_mes_final, strlen($ano_mes_final)-4, 4);
				} else {
					$ano_mes_inicial = $meses[$this->data['Notafis']['mes_inicial']].' de '.$this->data['Notafis']['ano_inicial'];
					$ano_mes_final  = $meses[$this->data['Notafis']['mes_final']].' de '.$this->data['Notafis']['ano_final'];
					$ano = substr($ano_mes_final, strlen($ano_mes_final)-4, 4);
				}
				
				$this->set(compact('dados', 'ano_mes_inicial', 'ano_mes_final', 'ano'));
			}
		}

		$this->set(compact('meses', 'anos', 'gestores', 'produtos'));
	}

    function faturamento_anual_cliente_produto() {
        $dados = array();
        $this->layout = 'new_window';
        $this->pageTitle = 'Faturamento por Cliente e Produto';
        $export = $this->passedArgs[0] == 'export';
        if ($export) {
            $this->data['Notafis']['ano'] = $this->passedArgs[1];
            $this->data['Notafis']['mes_cadastro_cliente'] = $this->passedArgs[2];
        }
        if (!empty($this->data)) {
            $dados = $this->Notafis->analiticoFaturamentoPorDataDeCadastro($this->data['Notafis']['ano'], true, false, $this->data['Notafis']['mes_cadastro_cliente']);
            if ($export) {
                $this->faturamento_anual_cliente_produto_export($dados);
            }
        }
        $this->set(compact('dados'));
    }

    function faturamento_anual_cliente_produto_export(&$dados) {
        Configure::write('debug',0);
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="clientes'.time().'.csv"');
        echo "Código;Razão Social;Produto;Jan;Fev;Mar;Abr;Mai;Jun;Jul;Ago;Set;Out;Nov;Dez;Total"."\n";
        foreach ($dados as $dado) {
            echo $dado['Cliente']['codigo'].";".
                $dado['Cliente']['razao_social'].";".
                $dado['NProduto']['descricao'].";".
                number_format($dado[0]['Jan'],2,',','').";".
                number_format($dado[0]['Fev'],2,',','').";".
                number_format($dado[0]['Mar'],2,',','').";".
                number_format($dado[0]['Abr'],2,',','').";".
                number_format($dado[0]['Mai'],2,',','').";".
                number_format($dado[0]['Jun'],2,',','').";".
                number_format($dado[0]['Jul'],2,',','').";".
                number_format($dado[0]['Ago'],2,',','').";".
                number_format($dado[0]['Set'],2,',','').";".
                number_format($dado[0]['Out'],2,',','').";".
                number_format($dado[0]['Nov'],2,',','').";".
                number_format($dado[0]['Dez'],2,',','').";".
                number_format($dado[0]['total_faturado'],2,',','').
                "\n";
        }
        exit;
    }
}
