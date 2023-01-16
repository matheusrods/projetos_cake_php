<?php

class EnderecosController extends AppController {
    public $uses = array('VEndereco'     ,
                         'Endereco'      ,
                         'EnderecoEstado',
                         'EnderecoCidade',
                         'EnderecoBairro',
                         'EnderecoTipo');

    public $components = array('Maplink');

    
    function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('listar_por_cep','sincroniza','busca_endereco');
        $this->BAuth->allow('listar_por_cep','buscar_endereco_cep', 'carrega_combo_cidade','listar_por_cep2','carrega_combo_cidade_abreviacao');
    }


    function bindEnderecoEstado() {
        $this->bindModel(array(
            'belongsTo' => array(
                'EnderecoEstado' => array(
                    'class' => 'EnderecoEstado',
                    'foreignKey' => 'codigo_endereco_estado'
                )
            )
        ));
    }



    /**
     * Busca um endereço pelo CEP.
     *
     * @param int $cep
     */
    public function listar_por_cep($cep = null) {
        $this->layout = 'ajax';
        $this->data = $this->VEndereco->listarParaComboPorCep($cep);
    }

    public function listar_por_cep2($cep = null) {
        $this->layout = 'ajax';
        $this->data = $this->VEndereco->listarDadosEndereco($cep);
        exit($this->data);
    }

    public function listar_por_cep_modal($cep = null) {
        $this->layout = 'ajax';        
        $this->data = $this->VEndereco->listarParaComboPorCepModal($cep);        
    }

    public function buscar_endereco_cep( $cep ){
        $this->layout = 'ajax';
        $endereco     = $this->VEndereco->findByEnderecoCep( $cep );
        die(json_encode( $endereco ));
    }

    function index() {
        $this->pageTitle = 'Endereços';
        $this->set('isAjax', $this->RequestHandler->isAjax());
        $this->set('enderecos', $this->paginate());
    }

    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Endereco->name);
        $conditions = $this->VEndereco->converteFiltroEmCondition($filtros);

        $this->paginate['VEndereco'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'VEndereco.endereco_cep,VEndereco.endereco_codigo',
        );

        $enderecos = $this->paginate('VEndereco');

        $this->set(compact('enderecos'));
    }

    function carrega_combo_tipo(){
        $tipos = $this->EnderecoTipo->combo();

        $this->set(compact('tipos'));
    }

    function carrega_combo_estado(){
        $estados = $this->EnderecoEstado->comboPorPais(1);
        $this->set(compact('estados'));
    }

    function carrega_combo_cidade($codigo_estado){
        $this->layout = 'ajax';
        $this->data = $this->EnderecoCidade->combo($codigo_estado);
    }

    function carrega_combo_cidade_nome($codigo_cidade){       
        $this->layout = 'ajax';
        $this->data = $this->EnderecoCidade->combo_cidade($codigo_cidade);
     
    }
    
    function carrega_combo_t_estado($codigo_pais=NULL){
        $this->loadModel('TEstaEstado');
        if( $codigo_pais ){
            $estados = $this->TEstaEstado->comboPorPais( $codigo_pais );            
        } else {
            $estados = $this->TEstaEstado->combo();            
        }
        $this->set(compact('estados'));
        // $this->render('carrega_combo_estado');
    }
    
    function carrega_combo_t_cidade($codigo_estado){
    	$this->layout = 'ajax';
    	$this->loadModel('TCidaCidade');
        
        $this->data = $this->TCidaCidade->combo($codigo_estado);
        $this->render('carrega_combo_cidade');
    }

    function carrega_combo_bairro($codigo_cidade){
        $this->layout = 'ajax';

        $this->data = $this->EnderecoBairro->combo($codigo_cidade);
    }

    function buscarEnderecoPeloCep($cep){
        $this->layout = 'ajax';

        $this->data = $this->Endereco->buscarEnderecoPeloCep($cep);

        echo json_encode($this->data);

        exit;
    }

    function editar($codigo_endereco) {
         $this->pageTitle = 'Atualizar Endereco';

         if($this->Endereco->vericaSePodeEditar($codigo_endereco)){
             if(!empty($this->data)){
                 if($this->Endereco->atualizar($this->data)){
                     $this->BSession->setFlash('save_success');
                     $this->redirect(array('action' => 'index'));
                 }else{
                     $this->BSession->setFlash('save_error');
                 }
             }else{
                 $this->data = $this->Endereco->carregarParaEdicao($codigo_endereco);
             }
             $this->carrega_combos();
         }
         else{
            $this->BSession->setFlash(array(MSGT_ERROR, 'Não é possível editar endereços importados da base dos Correios ou que não foram incluídos hoje.'));
            $this->redirect(array('action' => 'index'));
         }
    }

    function incluir($cep = NULL, $id_campo_retorno=NULL ) {
        $this->pageTitle  = 'Incluir Endereço';
        $this->EnderecoCep= ClassRegistry::init('EnderecoCep');
        if($this->RequestHandler->isPost()) {
            $dados = $this->data;     
            if( isset($dados['EnderecoCep']['cep']) && isset($dados['EnderecoCidade']['codigo_endereco_estado']) && $dados['EnderecoCep']['cep'] && $dados['EnderecoCidade']['codigo_endereco_estado'] ){
                $insere_sn = isset( $dados['Endereco']['dialog'] ) ? FALSE : TRUE;
                $codigo_cep = $this->EnderecoCep->verificaCepExiste($dados['EnderecoCep']['cep'],$dados['EnderecoCidade']['codigo_endereco_estado']);
                $dados['Endereco']['codigo_endereco_cep'] = $codigo_cep;
            }            
            $cep = $this->data['EnderecoCep']['cep'];
            $id_campo_retorno = isset($this->data['Endereco']['campo_retorno']) ? $this->data['Endereco']['campo_retorno'] : NULL;            
            
            if(isset($dados['Endereco']['nome_bairro'])){
                $tem_bairro = $this->EnderecoBairro->find('count', array(
                'conditions' => array('EnderecoBairro.codigo_endereco_cidade' => $dados['Endereco']['codigo_endereco_cidade'],'EnderecoBairro.descricao'=>$dados['Endereco']['nome_bairro'])));
                $array_dados_bairro['EnderecoBairro']['codigo_endereco_cidade'] = $this->data['Endereco']['codigo_endereco_cidade'];
                $array_dados_bairro['EnderecoBairro']['descricao']              =$dados['Endereco']['nome_bairro'];
                if($tem_bairro=='0'){
                    if($this->EnderecoBairro->incluir($array_dados_bairro)){                         
                        $this->BSession->setFlash('save_success');
                    }else{
                        $this->BSession->setFlash('save_error');
                    } 
                }
               // Procurando codigo do novo bairro
               $tem_bairro_novo = $this->EnderecoBairro->find('all', array(
                    'conditions' => array(
                        'EnderecoBairro.codigo_endereco_cidade' => $dados['Endereco']['codigo_endereco_cidade'],
                        'EnderecoBairro.descricao'              => $dados['Endereco']['nome_bairro'])));
               $dados['Endereco']['codigo_endereco_bairro_inicial'] = $tem_bairro_novo[0]['EnderecoBairro']['codigo'];                         
            }
            if ($this->Endereco->incluir($dados)) { 
                //Caso a inclusao venha de um dialog               
                $this->BSession->setFlash('save_success');
                if( !isset($dados['Endereco']['dialog'])){
                    $this->redirect(array('action' => 'index'));                    
                }else{
                    $endereco_id = $this->Endereco->id;
                    $this->set(compact('endereco_id'));
                }
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        if($cep!=''){
            $cidade_estado = $this->VEndereco->listarPorCepEstadoCidade($cep);
            $this->data['EnderecoCidade']['codigo_endereco_estado'] = $cidade_estado['VEndereco']['endereco_codigo_estado'];
            $this->data['Endereco']['codigo_endereco_cidade'] = $cidade_estado['VEndereco']['endereco_codigo_cidade'];
        } else {
            $cidade_estado =array();
        }
        $this->set(compact('cidade_estado','cep', 'id_campo_retorno'));
        $this->carrega_combos();
    }

    function carrega_combos(){
        $this->carrega_combo_tipo();
        $this->carrega_combo_estado();

        $cidades = array();
        $bairros = array();

        if (!empty($this->data['EnderecoCidade']['codigo_endereco_estado'])) {
           $cidades = $this->EnderecoCidade->combo($this->data['EnderecoCidade']['codigo_endereco_estado']);
        }
        if (!empty($this->data['Endereco']['codigo_endereco_cidade'])) {
           $bairros = $this->EnderecoBairro->combo($this->data['Endereco']['codigo_endereco_cidade']);
        }
        $this->set(compact('cidades','bairros'));
    }

    function busca_endereco($cep) {
		$this->loadModel('TCidaCidade');
		$this->loadModel('TEstaEstado');
		
		$endereco = $this->VEndereco->listaPorCepJson($cep);
		
		if($endereco){
			$endereco = $endereco[0];

			echo json_encode($endereco);
		} else {
			echo false;
		}
		exit;
	}

    function busca_endereco_maplink($cep) {
		$this->loadModel('TCidaCidade');
		$this->loadModel('TEstaEstado');
		
		$new_local = array();
		$new_local['cep'] = $cep;

		$endereco = $this->Maplink->busca_endereco($new_local);
		
		if($endereco){
			echo json_encode($endereco->findAddressResult->addressLocation->AddressLocation[0]->address);	
		} else {
			echo false;
		}
		//
		exit;
	}

	function buscar_cep() {
		$this->layout = 'ajax_placeholder';
		$input_id = $this->passedArgs['searcher'];
		$this->data['VEndereco'] = $this->Filtros->controla_sessao($this->data, $this->VEndereco->name);
		
		$estados = $this->EnderecoEstado->combo();
		
		$cidades = array();
		if(isset($this->data['VEndereco']['endereco_codigo_estado'])) {
			$cidades = $this->EnderecoCidade->combo($this->data['VEndereco']['endereco_codigo_estado']);
		}
		
		$this->set(compact('input_id', 'estados', 'cidades'));
	}
	
	function listagem_cep() {
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->VEndereco->name);
		$conditions = $this->VEndereco->converteFiltroEmConditionBuscaEndereco($filtros);
		$this->paginate['VEndereco'] = array(
            'conditions' => $conditions,
            'limit' => 10,
            'order' => 'VEndereco.endereco_cep,VEndereco.endereco_codigo',
        );
		$ceps = $this->paginate('VEndereco');
		$this->set(compact('ceps'));
		if (isset($this->passedArgs['searcher']))
			$this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
	}

    function busca_cidades(){
        if($this->RequestHandler->isAjax()){
            $this->autoRender = false;
            $conditions = array('EnderecoCidade.descricao LIKE' => iconv('utf-8', 'iso-8859-1', $_GET['term']).'%', 'EnderecoCidade.invalido' => 0);
            $cidades    = $this->EnderecoCidade->find('all',array('conditions'=>$conditions));
            $i=0;
            $response = array();

            foreach($cidades as $cidade){
                $response[$i]['label'] = $cidade['EnderecoCidade']['descricao']." - ". $cidade['EnderecoEstado']['abreviacao'];
                $response[$i]['value'] = $cidade['EnderecoCidade']['codigo'];
                $response[$i]['uf_label'] = $cidade['EnderecoEstado']['abreviacao'];
                $i++;
            }
            echo json_encode($response);
        }
    }
    public function autocompletar(){
        $response = null;
        $conditions = array(
            "EnderecoCidade.descricao LIKE '".urldecode( COMUM::trata_nome($_GET['term'] ) )."%' collate Latin1_General_CI_AI",
            "EnderecoCidade.invalido" => 0
        );
        $resultados = $this->EnderecoCidade->find('all', array(
            'conditions' => $conditions,
            'order' => array( 'EnderecoCidade.descricao' => 'ASC' )
        ));
        $i=0;
        foreach($resultados as $cidade) {
            $response[$i]['label']       = $cidade['EnderecoCidade']['descricao']." - ". $cidade['EnderecoEstado']['abreviacao'];
            $response[$i]['value']       = $cidade['EnderecoCidade']['codigo'];
            $response[$i]['uf_value']    = $cidade['EnderecoEstado']['codigo'];
            $response[$i]['codigo_pais'] = $cidade['EnderecoEstado']['codigo_endereco_pais'];
            $i++;
        }     
        echo json_encode($response);die;
    }    

    public function carregar_lat_lgn_por_endereco( $endereco ){        
        $filtros    = array('endereco' => $endereco );
        if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        }
        else if(Ambiente::TIPO_MAPA == 2) {
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();
        }
        $lat_lgn = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco);
        $retorno = array('latitude' => $lat_lgn[0], 'longitude' => $lat_lgn[1]);
        echo json_encode($retorno);
        exit;
    }

    /**
     * [carrega_combos_fornecedores description]
     * 
     * metodo para carregar combo de cidades passando o estado como abreviacao
     * 
     * @return [type] [description]
     */
    public function carrega_combo_cidade_abreviacao($estado){
        $this->layout = 'ajax';
        //codigo estado
        $est = $this->EnderecoEstado->find('first', array('conditions' => array('abreviacao' => $estado)));
        // $this->data = $this->EnderecoCidade->combo();
        //cidades
        $this->data = $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $est['EnderecoEstado']['codigo']), 'fields' => array('descricao', 'descricao'),'order' => 'descricao'));

    } //fim carrega_combo_abreviacao

    
}
?>
