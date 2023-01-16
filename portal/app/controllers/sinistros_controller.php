<?php

class SinistrosController extends AppController {

public $name            = 'Sinistros';  
    public $components      = array('Filtros');  
    public $uses            = array('Sinistro', 'Seguradora', 'Profissional', 'Veiculo','Recebsm', 'Cliente', 'ClientEmpresa');  
    public $helpers = array('Highcharts');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    public function index() {
        $this->pageTitle = 'Cadastro de Sinistros';    

        $this->carrega_combos();

        $natureza = $this->Sinistro->listNatureza();
        $clientes_embarcador    = array();
        $clientes_transportador = array();        
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);

        $this->set(compact('natureza', 'clientes_embarcador', 'clientes_transportador'));        
    }

    public function listagem(){ 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $conditions = $this->Sinistro->converterFiltrosEmConditions($filtros);
        $dados      = $this->Sinistro->listagem($conditions);         
        $natureza = $this->Sinistro->listNatureza();
        $this->set(compact('dados','natureza'));
    } 

    public function incluir(){
        $this->pageTitle = 'Incluir Sinistro';
        $this->loadModel('EnderecoCidade');

        if( isset($this->data) && !empty($this->data) ){   
            if(!empty($this->data['Sinistro']['codigo_endereco_cidade'])) {
                $cidade = $this->EnderecoCidade->combo_cidade($this->data['Sinistro']['codigo_endereco_cidade']);
                $this->data['Sinistro']['cidade'] = $cidade['EnderecoCidade']['descricao'];
                $this->data['Sinistro']['estado'] = $cidade['EnderecoEstado']['descricao'];
            }
            $this->data['Sinistro']['data_evento'] = $this->data['Sinistro']['data_evento'].' '.$this->data['Sinistro']['hora'].':00';
            $this->completarDados();
            if ($this->Sinistro->validates()) {
               	if($this->Sinistro->incluir($this->data)){
                    $this->BSession->setFlash('save_success');
                    $this->redirect( array( 'action'=>'index' ) );
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $natureza = $this->Sinistro->listNatureza();
        $status_veiculo = $this->Sinistro->listStatusVeiculo();
        $avaliacao_geral = $this->Sinistro->listAvaliacaoGeral();
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();
        $this->set(compact('natureza','status_veiculo','avaliacao_geral','seguradoras'));
    }

    private function completarDados() {
        if (!empty($this->data['Sinistro']['codigo_documento_profissional'])) {
            $profissional = $this->Profissional->buscaPorCPF($this->data['Sinistro']['codigo_documento_profissional']);
            if ($profissional) {
                $this->data['Sinistro']['codigo_profissional'] = $profissional['Profissional']['codigo'];
            } else {
                $this->Sinistro->invalidate('codigo_documento_profissional', 'Profissional não cadastrado');
            }
        }
        if (!empty($this->data['Sinistro']['placa'])) {
            $veiculo = $this->Veiculo->buscaPorPlaca($this->data['Sinistro']['placa']);
            if ($veiculo) {
                $this->data['Sinistro']['codigo_veiculo'] = $veiculo['Veiculo']['codigo'];
            } else {
                if(!$this->TVeicVeiculo->buscaPorPlaca($this->data['Sinistro']['placa']))
                    $this->Sinistro->invalidate('placa', 'Placa não cadastrada');
            }
        }
    }

    private function formataMoeda($valor)
    {
    	return number_format($valor,2);
    }

    public function editar($codigo){
        $this->pageTitle = 'Editar Sinistro';
        $this->loadModel('EnderecoCidade');
        if (!empty($this->data)) {
             if(!empty($this->data['Sinistro']['codigo_endereco_cidade'])) {
                $cidade = $this->EnderecoCidade->combo_cidade($this->data['Sinistro']['codigo_endereco_cidade']);
                $this->data['Sinistro']['cidade'] = $cidade['EnderecoCidade']['descricao'];
                $this->data['Sinistro']['estado'] = $cidade['EnderecoEstado']['descricao'];
            }else {
                $this->Sinistro->invalidate('codigo_endereco_cidade_visual','Cidade invalida');
            }
            $this->data['Sinistro']['data_evento'] = $this->data['Sinistro']['data_evento'].' '.$this->data['Sinistro']['hora'].':00';
            $this->completarDados();
            if ($this->Sinistro->validates()) {
                if($this->Sinistro->atualizar($this->data)){
                    $this->BSession->setFlash('save_success');
                    $this->redirect( array( 'action'=>'index' ) );
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->Sinistro->bindModel(array('belongsTo' => array(
                'Veiculo' => array('foreignKey' => 'codigo_veiculo'),
                'Profissional' => array('foreignKey' => 'codigo_profissional'),
                'Embarcador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_embarcador'),
                'Transportador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_transportador'),
                'Seguradora' => array('foreignKey' => 'codigo_seguradora'),
                'Corretora' => array('foreignKey' => 'codigo_corretora'),
            )));
            $this->data = $this->Sinistro->carregar($codigo);
            $this->data['Sinistro']['hora'] = substr($this->data['Sinistro']['data_evento'], 10);
            $this->data['Sinistro']['placa'] = $this->data['Veiculo']['placa'];
            $this->data['Sinistro']['codigo_documento_profissional'] = $this->data['Profissional']['codigo_documento'];
            $this->data['Sinistro']['nome_profissional'] = $this->data['Profissional']['nome'];
            $this->data['Sinistro']['codigo_embarcador_name'] = $this->data['Embarcador']['razao_social'];
            $this->data['Sinistro']['codigo_transportador_name'] = $this->data['Transportador']['razao_social'];
            $this->data['Sinistro']['codigo_corretora_visual'] = $this->data['Corretora']['nome'];
            $this->data['Sinistro']['codigo_corretora_visual'] = $this->data['Corretora']['nome'];
            if(!empty($this->data['Sinistro']['cidade'])) {
                $cidade = $this->EnderecoCidade->carregar_cidade_nome_completo($this->data['Sinistro']['cidade']);
                if(!empty($cidade)) {
                    $this->data['Sinistro']['codigo_endereco_cidade_visual'] = $cidade['EnderecoCidade']['descricao'] .' - '.  $cidade['EnderecoEstado']['descricao'];
                    $this->data['Sinistro']['codigo_endereco_cidade'] = $cidade['EnderecoCidade']['codigo'];
                }else {
                    $this->data['Sinistro']['codigo_endereco_cidade_visual'] = $this->data['Sinistro']['cidade'] . (isset($this->data['Sinistro']['estado']) ? ' - '. $this->data['Sinistro']['estado'] : '');
                }
            }
        }
        $this->data['Sinistro']['valor_carga']     = number_format ( $this->data['Sinistro']['valor_carga'] , 2 , ',' , '' );
        $this->data['Sinistro']['valor_sinistrado']     = number_format ( $this->data['Sinistro']['valor_sinistrado'] , 2 , ',' , '' );
        $natureza = $this->Sinistro->listNatureza();
        $status_veiculo = $this->Sinistro->listStatusVeiculo();
        $avaliacao_geral = $this->Sinistro->listAvaliacaoGeral();
        $seguradoras = $this->Seguradora->listarSeguradorasAtivas();
        $this->set(compact('natureza','status_veiculo','avaliacao_geral','seguradoras'));
    }

    public function _editar($codigo){

        $this->pageTitle = 'Editar Sinistro';

        if( isset($this->data) && !empty($this->data) ){
			
            $this->data['Sinistro']['data_evento'] = $this->data['Sinistro']['data_evento'] . ' '. $this->data['Sinistro']['hora'].':00';
            if($this->Sinistro->atualizar($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'index') );
            }else{
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->Sinistro->findByCodigo($codigo);
            $hora = substr($this->data['Sinistro']['data_evento'], 11, 5);
            $this->data['Sinistro']['valor_carga']      = $this->formataMoeda($this->data['Sinistro']['valor_carga']);
            $this->data['Sinistro']['valor_sinistrado'] = $this->formataMoeda($this->data['Sinistro']['valor_sinistrado']);
            $this->data['Sinistro']['valor_recuperado'] = $this->formataMoeda($this->data['Sinistro']['valor_recuperado']);
            $this->data['Sinistro']['hora'] = $hora;
        }

        $natureza = $this->Sinistro->listNatureza();
        $status_veiculo = $this->Sinistro->listStatusVeiculo();
        $avaliacao_geral = $this->Sinistro->listAvaliacaoGeral();
        
        $this->set(compact('natureza','status_veiculo','avaliacao_geral'));        
    }

    public function excluir($id){
        if( isset($id) && !is_null($id) ){
            if( $this->Sinistro->excluir($id) ){
                $this->BSession->setFlash('delete_success');
                $this->redirect( array( 'action'=>'index' ) );
            }else{
                $this->BSession->setFlash('delete_error');
            }
        }else{
            $this->BSession->setFlash('delete_error');
        }
    }

    function visualizar_sinistros($codigo){
    	$this->layout 		= 'new_window';
    	$this->pageTitle 	= ' Dados do Sinistro';
    	$this->data = $this->Sinistro->findByCodigo($codigo);
        $hora = substr($this->data['Sinistro']['data_evento'], 11, 5);
        $data = substr($this->data['Sinistro']['data_evento'],0,10);
        $this->data['Sinistro']['valor_carga']      = $this->formataMoeda($this->data['Sinistro']['valor_carga']);
        $this->data['Sinistro']['valor_sinistrado'] = $this->formataMoeda($this->data['Sinistro']['valor_sinistrado']);
        $this->data['Sinistro']['valor_recuperado'] = $this->formataMoeda($this->data['Sinistro']['valor_recuperado']);
        $this->data['Sinistro']['hora'] = $hora;
     	$this->data['Sinistro']['data_evento'] = $data;

        $natureza = $this->Sinistro->listNatureza();
        $status_veiculo = $this->Sinistro->listStatusVeiculo();
        $avaliacao_geral = $this->Sinistro->listAvaliacaoGeral();
        
        $this->set(compact('natureza','status_veiculo','avaliacao_geral'));        
    

    }
    function carrega_combos() {
        $this->loadModel('Corretora');
        $this->loadModel('Seguradora');
        $conditions['nome NOT '] = array('DESATIVADO','DESATIVADA');
        $corretoras  = $this->Corretora->find('list', array('order' => 'nome', 'conditions'=>$conditions ));
        $conditions['nome NOT '] = array('DESATIVADO','DESATIVADA');
        $seguradoras = $this->Seguradora->find('list', array('order' => array('nome') , 'conditions'=>$conditions ) ) ;
        $this->set(compact('corretoras','seguradoras'));
    }

    public function sinistros_analitico() { 
        $this->layout = 'new_window';
        $this->pageTitle = 'Analítico de Sinistros';
        $this->carrega_combos();
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);

        //Filtra os Parametros que vem do Sintetico
        if(!empty($this->params['pass'][0]) && !empty($this->params['pass'][1]) ) { 
            if (is_numeric((int)$this->params['pass'][0]) && is_numeric((int)$this->params['pass'][1])) {
               $filtros['agrupamento'] = $this->params['pass'][0];
                switch ($this->params['pass'][0]) {
                    case Sinistro::AGRP_TRANSPORTADOR:
                        $this->data['Sinistro']['cliente_transporta'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_SEGURADOR:
                        $this->data['Sinistro']['seguradora'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_CORRETOR:
                        $this->data['Sinistro']['corretora'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_TIPO:
                        $this->data['Sinistro']['natureza'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_MOTORISTA:
                        $this->data['Sinistro']['cpf'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_EMBARCADOR:
                        $this->data['Sinistro']['codigo_embarcador'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_SINISTRO:
                        $this->data['Sinistro']['codigo_embarcador'] = $this->params['pass'][1];
                        break;
                    case Sinistro::AGRP_TECNOLOGIA:
                        $this->data['Sinistro']['codigo_embarcador'] = $this->params['pass'][1];
                        break;
                }
            } 
        }
        $natureza = $this->Sinistro->listNatureza();
        $clientes_embarcador    = array();
        $clientes_transportador = array();        
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);

        $this->set(compact('natureza', 'clientes_embarcador', 'clientes_transportador'));      

    }

    public function listagem_sinistros_analitico() { 
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $conditions = $this->Sinistro->converterFiltrosEmConditions($this->data['Sinistro']);           
        $dados      = $this->Sinistro->listagem($conditions);         
        $natureza = $this->Sinistro->listNatureza();
        $this->set(compact('dados','natureza')); 
    }


    public function sinistros_sintetico() {

        $this->pageTitle = 'Sintético de Sinistros';

        $this->carrega_combos();
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);

        $agrupamento            = $this->Sinistro->tiposAgrupamento(); 
        $natureza = $this->Sinistro->listNatureza();
        $clientes_embarcador    = array();
        $clientes_transportador = array();        
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        
        
        $this->set(compact('natureza','agrupamento', 'clientes_embarcador', 'clientes_transportador'));     

    }

    public function listagem_sinistros_sintetico() {
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $conditions = $this->Sinistro->converterFiltrosEmConditions($this->data['Sinistro']);
        
        $dados      = $this->Sinistro->listagem($conditions,$this->data['Sinistro']['agrupamento']);
        if(!empty($dados)) {
            $this->sintetico_grafico_agrupamento($dados, $this->data['Sinistro']['agrupamento']);
        }
        $estados = $this->Sinistro->listagem_estados($conditions);
        if(!empty($estados)) {
            $this->sintetico_grafico_estado($estados);
        }
        $semanal = $this->Sinistro->listagem_semanal($conditions);
        if(!empty($semanal)) {
            $this->sintetico_grafico_semanal($semanal);
        }
        $mensal = $this->Sinistro->listagem_mensal($conditions);
        if(!empty($mensal)) {
            $this->sintetico_grafico_mensal($mensal);
        }
        $natureza = $this->Sinistro->listNatureza();

        $this->data['Sinistro']['codigo'] = (!empty($this->data['Sinistro']['codigo']) ? $this->data['Sinistro']['codigo'] : null);
        $this->data['Sinistro']['sm'] = (!empty($this->data['Sinistro']['sm']) ? $this->data['Sinistro']['sm'] : null);
        $this->data['Sinistro']['natureza'] = (!empty($this->data['Sinistro']['natureza']) ? $this->data['Sinistro']['natureza'] : null);
        $this->data['Sinistro']['codigo_documento_profissional'] = (!empty($this->data['Sinistro']['codigo_documento_profissional'])  ? $this->data['Sinistro']['codigo_documento_profissional'] : null);
        $this->data['Sinistro']['codigo_seguradora'] = (!empty($this->data['Sinistro']['codigo_seguradora']) ? $this->data['Sinistro']['codigo_seguradora'] : null);
        $this->data['Sinistro']['codigo_corretora'] = (!empty($this->data['Sinistro']['codigo_corretora']) ? $this->data['Sinistro']['codigo_corretora'] : null);
        $this->set(compact('dados','natureza','agrupamento'));
    }

    public function mapa_sinistro( ){
        $this->pageTitle = 'Mapa de Sinistros';
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $natureza = $this->Sinistro->listNatureza();                               
		$this->carrega_combos();
		$this->set(compact('natureza'));
    }

    public function listagem_mapa_sinistro( ){
        $this->data['Sinistro'] = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $conditions = $this->Sinistro->converterFiltrosEmConditions($this->data['Sinistro']);
        $this->Sinistro->bindModel(array('belongsTo' => array(
            'Embarcador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_embarcador'),
            'Transportador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_transportador'),
            'Seguradora' => array('foreignKey' => 'codigo_seguradora'),
            'Corretora' => array('foreignKey' => 'codigo_corretora'),
            'Veiculo' => array('foreignKey' => 'codigo_veiculo'),
            'TransportadorPadrao' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array('TransportadorPadrao.codigo = Veiculo.codigo_cliente_transportador_default')),
            'Profissional' => array('foreignKey' => 'codigo_profissional'),
        )), false);

        $fields = array('Sinistro.codigo','Sinistro.sm', 'Sinistro.data_evento', 'Sinistro.natureza', 'Embarcador.razao_social', 
        'Transportador.razao_social', 'Profissional.Nome', 'Veiculo.placa', 'Sinistro.latitude', 'Sinistro.longitude',
        'TransportadorPadrao.razao_social');
        
        $this->paginate['Sinistro'] = array(
             'conditions'   => $conditions,
             'limit'        => 50,
             'order'        => 'Sinistro.data_evento ASC',
             'fields'       => $fields
        );
        $listagem       = $this->paginate('Sinistro');
        $listagem_mapa  = $this->Sinistro->find('all', compact('conditions', 'joins', 'fields'));
        $natureza = $this->Sinistro->listNatureza();
        $this->set(compact('listagem', 'listagem_mapa', 'natureza'));
    }

    function historico_sinistro($codigo_documento){
        $this->loadModel('Sinistro');
        $conditions_sini =array( "Profissional.codigo_documento" => $codigo_documento);
        $sinistro = $this->Sinistro->listagem($conditions_sini);
        
        $natureza = array(0 => 'Recuperado',
                                1 => 'Roubo Parcial',
                                2 => 'Furto Parcial', 
                                3 => 'Roubo Total', 
                                4 => 'Furto Total', 
                                5 => 'Tentativa'
        );
        $this->data['Profissional']['codigo_documento'] = $codigo_documento;
          
        $this->set(compact('sinistro','natureza')); 
    }

    function sintetico_grafico_agrupamento($sinistros,$agrupamento){
        foreach ($sinistros as $key => $sinistro) {
            $dadosGrafico['series'][$key]['values']  = !empty($sinistro[0]['qtd_ocorrencias']) ? $sinistro[0]['qtd_ocorrencias'] : 0;
            if($agrupamento == Sinistro::AGRP_EMBARCADOR OR $agrupamento == Sinistro::AGRP_TRANSPORTADOR) {
                $dadosGrafico['series'][$key]['name']    = !empty($sinistro[0]['codigo']) ? "'".$sinistro[0]['codigo']."'" : "'Não possui'";
                $dadosGrafico['series'][$key]['option']  = !empty($sinistro[0]['descricao']) ? "'".substr($sinistro[0]['descricao'], 0, 35)." ...'" : "'Não possui'";
            }else {
                if(!empty($sinistro[0]['descricao']) && strlen($sinistro[0]['descricao']) > 35) {
                    $dadosGrafico['series'][$key]['name']    = "'".substr($sinistro[0]['descricao'], 0, 35)." ...'";
                }else {
                    $dadosGrafico['series'][$key]['name']    = !empty($sinistro[0]['descricao']) ? "'".$sinistro[0]['descricao']."'" : "'Não possui'";
                }
            }
        }
        $this->set(compact('dadosGrafico'));
    }

    function sintetico_grafico_estado($estados){
        foreach ($estados as $key => $estado) {
            $dadosGraficoEstado['series'][$key]['values'] =  !empty($estado[0]['qtd']) ? $estado[0]['qtd'] : 0;
            $dadosGraficoEstado['series'][$key]['name'] =   !empty($estado[0]['descricao']) ? "'".substr($estado[0]['descricao'], 0, 35)."'" : "'Não possui'";
        }
        $this->set(compact('dadosGraficoEstado'));
    }

    function sintetico_grafico_semanal($dias_da_semana){
        foreach ($dias_da_semana as $key => $dia) {
            $dadosGraficoSemanal['series'][$key]['values'] =  !empty($dia[0]['qtd']) ? $dia[0]['qtd'] : 0;
            $dadosGraficoSemanal['series'][$key]['name'] =   !empty($dia[0]['descricao']) ? "'".substr($dia[0]['descricao'], 0, 35)."'" : "'Não possui'";
        }
        $this->set(compact('dadosGraficoSemanal'));
    }

    function sintetico_grafico_mensal($dias_mensal){
         foreach ($dias_mensal as $dias) {
            $qtd[] = $dias[0]['qtd'];
            $descricao[] = "'".$dias[0]['descricao']."'";
        }
        $dadosGraficoMensal['eixo_x'] = $descricao;
        $dadosGraficoMensal['series'] =  array(
            array(
                'name' => "'Mês'",
                'values' => $qtd
            )
        );  
        $this->set(compact('dadosGraficoMensal'));
    }

    public function carrega_comobos_cidades() {
        $this->loadModel('EnderecoEstado');
        $estados = array();
        $estados = $this->EnderecoEstado->comboPorPais(EnderecoEstado::BRASIL);
        $this->set(compact('estados'));
    }

    public function buscar_codigo_cidade() {
        $this->pageTitle = 'Cidades';
        //$input_id = $this->passedArgs['searcher'];
        $input_id = '543';
        $display = false;
        $this->carrega_comobos_cidades();
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $this->set(compact('input_id', 'display'));
       
    }

    public function listagem_visualizar() {
        $this->loadModel('EnderecoCidade');
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Sinistro->name);
        $this->data['Sinistros'] = $filtros;
        $conditions = $this->EnderecoCidade->convertFiltrosEmConditions($filtros);        
        $this->paginate['EnderecoCidade'] = $this->EnderecoCidade->listagemCidades($conditions);
        $cidades = $this->paginate('EnderecoCidade');
        $this->set(compact('cidades'));
    }
}