<?php
class RiscosAtributosDetalhesController extends AppController {
    //atributos da class
    public $name = 'RiscosAtributosDetalhes';

    var $uses = array(	'RiscoAtributoDetalhe', 
    					'RiscoAtributo',
                        'GrupoExpRiscoAtribDet',
                        'GrupoExposicaoRisco',
                        'RiscoAtributoDetalheExterno');
    
    public function beforeFilter(){
    	parent::beforeFilter();
             $this->BAuth->allow('index',
             'listagem', 
             'incluir', 
             'editar',
             'atualiza_status', 
             'buscar_efeitos_criticos', 
             'listagem_buscar_efeitos_criticos', 
             'buscar_fontes_geradoras_riscos', 
             'listagem_fontes_geradoras_riscos'
             //'index_externo',
             //'listagem_externo',
             //'editar_externo'   
             );
    }

    /**
     * Geracao do Index do sistemas
     */ 
    function index() {
        $this->pageTitle = 'Cadastro de Efeitos Críticos';        
    }

    /**
     * Montagem do grid do sistema com as acoes
     */
    function listagem() {
        //seta se ira usar ajax
        $this->layout = 'ajax'; 
        //pega os campos da model
        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscoAtributoDetalhe->name);
        
        //seta o filtro como 2
        $filtros["codigo_risco_atributo"] = 2;

        //condicoes         
        $conditions = $this->RiscoAtributoDetalhe->converteFiltroEmCondition($filtros);
        $fields = array('RiscoAtributoDetalhe.codigo', 'RiscoAtributoDetalhe.descricao', 'RiscoAtributoDetalhe.ativo');
        $order = 'RiscoAtributoDetalhe.descricao';

        //paginacao
        $this->paginate['RiscoAtributoDetalhe'] = array(
										                'fields' => $fields,
										                'conditions' => $conditions,
										                'limit' => 50,
										                'order' => $order,
										        	);
       	//seta os valores para a view da listagem
        $risco_atributo_detalhe = $this->paginate('RiscoAtributoDetalhe');
        $this->set(compact('risco_atributo_detalhe'));

    } //fim listagem

    /**
     * Metodo que inclui um novo registro na base de dados
     */ 
    function incluir() {
        $this->pageTitle = 'Incluir Efeitos Críticos';

        if($this->RequestHandler->isPost()) {



        	if ($this->RiscoAtributoDetalhe->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', 'controller' => 'riscos_atributos_detalhes', $this->RiscoAtributoDetalhe->id));

			} else { 				
				$this->BSession->setFlash('save_error');
			}
        }
    }//fim incluir
    
    function editar($codigo) {
        $this->pageTitle = 'Editar Efeitos Críticos'; 
        
         if($this->RequestHandler->isPost()) {
         	
         	$this->data['RiscoAtributoDetalhe']['codigo'] = $codigo;
			if($this->RiscoAtributoDetalhe->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'riscos_atributos_detalhes'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } 
        
        if (isset($this->passedArgs[0])) {
        	$this->data = $this->RiscoAtributoDetalhe->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));
			$this->set(compact('riscos'));
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['RiscoAtributoDetalhe']['codigo'] = $codigo;
        $this->data['RiscoAtributoDetalhe']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->RiscoAtributoDetalhe->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    /**
     * Pega os dados do efeito critico
     */ 
    function buscar_efeitos_criticos($linha, $codigo_risco)
    {
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->RiscoAtributoDetalhe->name);

        $this->set(compact('codigo_risco','linha'));

    } //fim buscar_efeitos_criticos

    /**
     * Lista os efeitos criticos
     */ 
    function listagem_buscar_efeitos_criticos($linha, $codigo_risco)
    {
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscoAtributoDetalhe->name);
        
        $conditions = $this->RiscoAtributoDetalhe->converteFiltroEmCondition($filtros);
        
        //efetio critico
        $conditions = array_merge($conditions,array('RiscoAtributoDetalhe.ativo' => '1',
                                                    'RiscoAtributoDetalhe.codigo_risco_atributo' => '2',
                                                    'OR' => array(
                                                                    array('GrupoExposicaoRisco.codigo_risco' => $codigo_risco),
                                                                    array('GrupoExposicaoRisco.codigo_risco' => null)
                                                                )
                                                )
                                    );
        
        $fields = array('RiscoAtributoDetalhe.codigo', 'RiscoAtributoDetalhe.descricao');
        $order = array('RiscoAtributoDetalhe.descricao');

        $joins  = array(
            array(
                'table' => $this->GrupoExpRiscoAtribDet->databaseTable.'.'.$this->GrupoExpRiscoAtribDet->tableSchema.'.'.$this->GrupoExpRiscoAtribDet->useTable,
                'alias' => 'GrupoExpRiscoAtribDet',
                'type' => 'LEFT',
                'conditions' => 'GrupoExpRiscoAtribDet.codigo_riscos_atributos_detalhes = RiscoAtributoDetalhe.codigo',
            ),
            array(
                'table' => $this->GrupoExposicaoRisco->databaseTable.'.'.$this->GrupoExposicaoRisco->tableSchema.'.'.$this->GrupoExposicaoRisco->useTable,
                'alias' => 'GrupoExposicaoRisco',
                'type' => 'LEFT',
                'conditions' => 'GrupoExposicaoRisco.codigo_risco = GrupoExpRiscoAtribDet.codigo_grupos_exposicao_risco',
            ),
        );
    
        $this->paginate['RiscoAtributoDetalhe'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 10,
                'group' => $fields,
                'order' => $order,
        );

        $dados_efeito_critico = $this->paginate('RiscoAtributoDetalhe');
        $this->set(compact('dados_efeito_critico', 'codigo_risco', 'linha'));

    }

    function index_externo() {
        $this->pageTitle = 'Efeitos Críticos Externos';
        $this->data[$this->RiscoAtributoDetalheExterno->name] = $this->Filtros->controla_sessao($this->data, $this->RiscoAtributoDetalheExterno->name);      
    }

    function listagem_externo() {
        $this->layout = 'ajax';
        $riscos_atributos_detalhes = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscoAtributoDetalheExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){
            $conditions = $this->RiscoAtributoDetalheExterno->converteFiltroEmCondition($filtros);

            $fields = array('RiscoAtributoDetalhe.codigo', 'RiscoAtributoDetalheExterno.codigo', 'RiscoAtributoDetalhe.descricao', 'RiscoAtributoDetalhe.ativo', 'RiscoAtributoDetalheExterno.codigo_externo');
            $order = 'RiscoAtributoDetalhe.descricao';

            $this->RiscoAtributoDetalhe->bindModel(
                    array('hasOne' => array(
                        'RiscoAtributoDetalheExterno' => array(
                        'foreignKey' => 'codigo_riscos_atributos_detalhes', 
                        'conditions' => array('RiscoAtributoDetalheExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false);

            $this->paginate['RiscoAtributoDetalhe'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $riscos_atributos_detalhes = $this->paginate('RiscoAtributoDetalhe');
            $listagem = true;
        }
        $this->set(compact('riscos_atributos_detalhes','listagem'));
        $this->set('codigo_cliente_filtro', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'Efeitos Críticos Externos'; 

        $codigoRiscoAtributoDetalhe = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoRiscoAtributoDetalheExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosRiscoAtributoDetalhe = $this->RiscoAtributoDetalhe->carregar($codigoRiscoAtributoDetalhe);
        
        if($this->RequestHandler->isPost()) {
            if($this->RiscoAtributoDetalheExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'riscos_atributos_detalhes'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 


        if (isset($this->passedArgs[2])) {
            $this->data = $this->RiscoAtributoDetalheExterno->find('first',array('conditions' => array('RiscoAtributoDetalheExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosRiscoAtributoDetalhe;
        }      
        $this->set('codigo_cliente', $codigo_cliente);
    }
}