<?php
class FontesGeradorasController extends AppController {
    public $name = 'FontesGeradoras';
    var $uses = array(
        'FonteGeradora', 
        'FonteGeradoraExterno',
        'Risco',
        'FonteGeradoraRisco',
        'GrupoRisco',

        'GrupoEconomico',
        'GrupoEconomicoCliente'
    ); 
    
    function index() {
        $this->pageTitle = 'Cadastro de Fontes Geradoras';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FonteGeradora->name);
        
        $conditions = $this->FonteGeradora->converteFiltroEmCondition($filtros);
        $fields = array('FonteGeradora.codigo', 'FonteGeradora.nome', 'FonteGeradora.ativo', 'FonteGeradora.codigo_empresa');
        $order = 'FonteGeradora.nome';
        
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        if(isset($codigo_empresa)){
            $conditions = array('FonteGeradora.codigo_empresa' => $codigo_empresa);
		}

        $this->paginate['FonteGeradora'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $fontes_geradoras = $this->paginate('FonteGeradora');
        $this->set(compact('fontes_geradoras'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Fonte Geradora';

        if($this->RequestHandler->isPost()) {
            
            if ($this->FonteGeradora->incluir($this->data)) {
                $codigo_fonte_geradora = $this->FonteGeradora->id;
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'editar', 'controller' => 'fontes_geradoras',$codigo_fonte_geradora));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Fonte Geradora'; 
        
         if($this->RequestHandler->isPost()) {

         	$this->data['FonteGeradora']['codigo'] = $codigo;
         	
			if ($this->FonteGeradora->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'fontes_geradoras'));
			} else {
				
				$lista_riscos = $this->Risco->find('list', array('fields' => array('codigo', 'nome_agente'), 'order' => array('nome_agente ASC')));
				if(count($this->data['FonteGeradora']['riscos_selecionados'])) {
					foreach($this->data['FonteGeradora']['riscos_selecionados'] as $key => $campo) {
						$lista_selecionados[$campo] = $lista_riscos[$campo];
						unset($lista_riscos[$campo]);
					}
				}
				
				$this->set('array_opcoes', $lista_riscos);
				$this->set('array_selecionados', $lista_selecionados);
				
				$this->BSession->setFlash('save_error');
			}
        } 
        	
        if (isset($this->passedArgs[0])) {
            
            $this->data = $this->FonteGeradora->find('first', array('conditions' => array('codigo' => $this->passedArgs[0])));
        	
            $riscos =  $this->FonteGeradoraRisco->find('first', array('conditions' => array('codigo_fonte_geradora' => $this->passedArgs[0])));      	
        }
        $this->set(compact('riscos', 'codigo'));
        
    }

    function atualiza_status($codigo, $status) {
        $this->layout = 'ajax';
        
        $this->data['FonteGeradora']['codigo'] = $codigo;
        $this->data['FonteGeradora']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->FonteGeradora->save($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    function buscar_fonte_geradora($linha, $codigo_risco){
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->FonteGeradora->name);

        $this->set(compact('codigo_risco','linha'));
    }

    function listagem_buscar_fonte_geradora($linha, $codigo_risco){
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FonteGeradora->name);
        $conditions = $this->FonteGeradora->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions,array('codigo_risco' => $codigo_risco));
        
        $fields = array('FonteGeradora.codigo', 'FonteGeradora.nome', 'FonteGeradoraRisco.codigo_risco');
        $order = array('FonteGeradora.nome');

        $joins  = array(
            array(
              'table' => $this->FonteGeradoraRisco->databaseTable.'.'.$this->FonteGeradoraRisco->tableSchema.'.'.$this->FonteGeradoraRisco->useTable,
              'alias' => 'FonteGeradoraRisco',
              'type' => 'LEFT',
              'conditions' => 'FonteGeradoraRisco.codigo_fonte_geradora = FonteGeradora.codigo',
            ),
        );

        $this->paginate['FonteGeradora'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'limit' => 10,
                'order' => $order,
        );

        $dados_fonte_geradora = $this->paginate('FonteGeradora');        
        $this->set(compact('dados_fonte_geradora', 'codigo_risco', 'linha'));
    }

    function buscar_fontes_geradoras_riscos($codigo_fonte_geradora){
        $this->layout = 'ajax_placeholder';
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $grupo_risco = $this->GrupoRisco->retorna_grupo();
        $this->set(compact('codigo_fonte_geradora','grupo_risco'));
    }
    function listagem_fontes_geradoras_riscos($destino, $codigo_fonte_geradora){
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Risco->name);
        $conditions = $this->Risco->converteFiltroEmCondition($filtros);
        
        $conditions[] = 'Risco.codigo NOT IN (SELECT codigo_risco FROM fontes_geradoras_riscos WHERE codigo_fonte_geradora = '.$codigo_fonte_geradora.')';
        $conditions[] = 'Risco.ativo = 1';

        $fields = array('Risco.codigo', 'Risco.nome_agente', 'GrupoRisco.codigo','GrupoRisco.descricao', );
        $order = array('GrupoRisco.descricao', 'Risco.nome_agente');

        $joins  = array(
            array(
              'table' => $this->GrupoRisco->databaseTable.'.'.$this->GrupoRisco->tableSchema.'.'.$this->GrupoRisco->useTable,
              'alias' => 'GrupoRisco',
              'type' => 'LEFT',
              'conditions' => 'GrupoRisco.codigo = Risco.codigo_grupo',
            ),

        );
        $this->paginate['Risco'] = array(
                'recursive' => 1,
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,  
                'order' => $order,
                'limit' => 10,
        );

        // pr($this->Risco->find('sql',$this->paginate['Risco']));


        $riscos = $this->paginate('Risco');        
        $this->set(compact('riscos', 'destino', 'codigo_fonte_geradora'));
    }


    function index_externo() {
        $this->pageTitle = 'Fontes Geradoras Externas';
        $this->data[$this->FonteGeradoraExterno->name] = $this->Filtros->controla_sessao($this->data, $this->FonteGeradoraExterno->name);
    }

    function listagem_externo() {

        $this->layout = 'ajax';
        $fontes_geradoras = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->FonteGeradoraExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])) {
            
            $conditions = $this->FonteGeradoraExterno->converteFiltroEmCondition($filtros);

            $fields = array(
                'FonteGeradora.codigo', 
                'FonteGeradoraExterno.codigo', 
                'FonteGeradora.nome', 
                'FonteGeradora.ativo', 
                'FonteGeradoraExterno.codigo_externo',
                'FonteGeradoraExterno.codigo_cliente'
            );

            $order = 'FonteGeradora.nome';

            $this->FonteGeradora->bindModel(
                array('hasOne' => array(
                        'FonteGeradoraExterno' => array(
                            'foreignKey' => 'codigo_fontes_geradoras', 
                            'conditions' => array('FonteGeradoraExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false
            );

            $this->paginate['FonteGeradora'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order
            );
           
            $fontes_geradoras = $this->paginate('FonteGeradora');

            $listagem = true;
        }

        $this->set(compact('fontes_geradoras','listagem'));
        $this->set('codigo_cliente', $codigo_cliente_matriz);
    }

    function editar_externo() {

        $this->pageTitle = 'Fontes Geradoras Externas'; 
        
        $codigoFonteGeradora = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoFonteGeradoraExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosFonteGeradora = $this->FonteGeradora->carregar($codigoFonteGeradora);

        if($this->RequestHandler->isPost()) {
            if($this->FonteGeradoraExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'fontes_geradoras'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[2])) {
            $this->data = $this->FonteGeradoraExterno->find('first',array('conditions' => array('FonteGeradoraExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosFonteGeradora;
        }

        $this->set('codigo_cliente', $codigo_cliente);
        
    }

}