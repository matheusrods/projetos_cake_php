<?php
class SetoresController extends AppController {
    public $name = 'Setores';

    var $uses = array( 
        'Setor',
        'Cliente',
        'Corretora',
        'Gestor',
        'EnderecoRegiao',
        'PlanoDeSaude',
        'SetorExterno'
    );

    /**
     * beforeFilter callback
     * @todo retirar permissão para action: editar_externo
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('obtem_setores_por_ajax', 'por_cliente','editar_externo');
    }
    

    public function obtem_setores_por_ajax()
    {
        $this->autoRender = false;

        ############################COMENTADO PARA SEMPRE TRAZER OS DADOS FILTRADOS 14/09/2020#########################
        
        // $this->loadModel('GrupoEconomicoCliente');
        // $bloqueado = $this->GrupoEconomicoCliente->find('count', array(
        //     'recursive' => -1,
        //     'conditions' => array(
        //         'codigo_cliente' => $this->params['form']['codigo_cliente'], 
        //         'bloqueado' => 1
        //         )
        //     )
        // );
        $setores = array();
        // if($bloqueado) {
            $this->loadModel('ClienteSetorCargo');
            $setores = $this->ClienteSetorCargo->find('list', array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'setores',
                        'alias' => 'Setor',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Setor.codigo = ClienteSetorCargo.codigo_setor'
                            )       
                        )
                    ),
                'fields' => array(
                    'Setor.codigo',
                    'Setor.descricao'
                    ),
                'conditions' => array(
                    'ClienteSetorCargo.codigo_cliente_alocacao' => $this->params['form']['codigo_cliente'],
                    'Setor.ativo' => 1,
                    '(ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)',
                    ),
                'order' => 'Setor.descricao'          
                )
            );
        // } else {
        //     //recupera o código da matriz
        //     $matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->params['form']['codigo_cliente']);

        //     $this->loadModel('Setor');
        //     $setores = $this->Setor->find('list', array(
        //         'recursive' => -1,
        //         'conditions' => array(
        //             'Setor.codigo_cliente' => $matriz['Matriz']['codigo'],
        //             'Setor.ativo' => 1
        //             ),
        //         'fields' => array(
        //             'Setor.codigo',
        //             'Setor.descricao'
        //             ),
        //          'order' => 'Setor.descricao'         
        //         )
        //     );
        // }

        $html = '<option value="">Selecione um Setor</option>';
        if(!empty($setores)) {
            foreach ($setores as $key => $setor) {
                $html .= '<option value="'.$key.'">'.$setor.'</option>';
            }
        }
        unset($setores);
        return $html;
    }
    

    function index($codigo_cliente, $referencia, $terceiros_implantacao = 'interno') {
        $this->pageTitle = 'Setores';

        $this->retorna_dados_cliente($codigo_cliente);
        
        $this->set(compact('referencia', 'terceiros_implantacao'));
    }

    function retorna_dados_cliente($codigo_cliente){
        $this->data = $this->Cliente->find('first', array('conditions' => array('codigo' =>$this->normalizaCodigoCliente($codigo_cliente))));

        $this->set(compact('codigo_cliente'));
    }

    function listagem($codigo_cliente, $referencia, $terceiros_implantacao = 'interno') {
        $this->layout = 'ajax'; 

        $this->retorna_dados_cliente($codigo_cliente);

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Setor->name);
        
        $conditions = $this->Setor->converteFiltroEmCondition($filtros);
        
        $this->loadModel('GrupoEconomico');
        //monta os joins
        $join_ge = array(
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
                    )
            )
        );
        //pega o codigo da matriz
        $codigo_cliente = $this->normalizaCodigoCliente($codigo_cliente);
        $cliente_unidade = $this->GrupoEconomico->find('all', array('joins' => $join_ge, 'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        $matriz_codigo_cliente =array();
        foreach ($cliente_unidade as $key => $value) {
            if(isset($value['GrupoEconomico']['codigo_cliente'])){
                $matriz_codigo_cliente[] = $value['GrupoEconomico']['codigo_cliente'];
            }
        }
       
        $conditions = array_merge($conditions, array('Setor.codigo_cliente' => $matriz_codigo_cliente));

        $fields = array('Setor.codigo', 'Setor.descricao', 'Setor.ativo');
        $order = 'Setor.descricao';

        $this->paginate['Setor'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
            );

        $setores = $this->paginate('Setor');

        $this->set(compact('setores', 'referencia', 'terceiros_implantacao'));
    }
    
    function incluir($codigo_cliente, $referencia, $terceiros_implantacao = 'interno') {
        if(empty($codigo_cliente) || empty($referencia)){
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }
        $this->pageTitle = 'Incluir Setor';

        if($this->RequestHandler->isPost()) {

            if($this->Setor->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                if($terceiros_implantacao == 'terceiros_implantacao'){
                    $this->redirect(array('controller' => 'setores', 'action' => 'index', $codigo_cliente, $referencia, $terceiros_implantacao));
                } else {
                    $this->redirect(array('controller' => 'setores', 'action' => 'index', $codigo_cliente, $referencia));
                }
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->retorna_dados_cliente($codigo_cliente);
        $this->set(compact('referencia', 'terceiros_implantacao'));
    }
    
    function editar($codigo_cliente, $codigo_setor, $referencia, $terceiros_implantacao = 'interno') {
        $this->pageTitle = 'Editar Setor'; 
        
        if($this->RequestHandler->isPost()) {

            if ($this->Setor->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                if($terceiros_implantacao == 'terceiros_implantacao') {
                    $this->redirect(array('controller' => 'setores', 'action' => 'index', $this->data['Setor']['codigo_cliente'], $referencia, $terceiros_implantacao));
                } else {
                    $this->redirect(array('controller' => 'setores', 'action' => 'index', $this->data['Setor']['codigo_cliente'], $referencia));
                }
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        $this->retorna_dados_cliente($codigo_cliente);                      

        if (isset($this->passedArgs[1])) {   
            $setores= $this->Setor->find('first', array('conditions' => array('codigo' => $this->passedArgs[1])));

            $this->data = array_merge($this->data, $setores);   
        }

        $this->set(compact('referencia', 'terceiros_implantacao'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['Setor']['codigo'] = $codigo;
        $this->data['Setor']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Setor->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }

    // public function por_cliente($codigo_cliente) {
    //     $list = $this->Setor->lista($codigo_cliente);
    //     $result = array();
    //     foreach ($list as $key => $value) {
    //         $result[] = array('codigo' => $key, 'descricao' => $value);
    //     }
    //     echo json_encode($result);
    //     die();
    // }

	/**
	 * Obter lista de Setores por código(s) de cliente(s)
	 *
	 * @param [array] $codigo_cliente
	 * @return array
	 * @todo implementar token
	 */
	public function por_cliente($codigo_cliente = null) {

        $this->loadModel('GrupoEconomico');

        if(is_null($codigo_cliente)){
			$this->responseJson();
		}

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

		$dados = $this->Setor->obterLista($codigo_cliente);

		$this->responseJson($dados);
		
    }//FINAL FUNCTION por_cliente
    
    /**
     * [cliente_terceiros METODO PARA MONTAR O FILTRO DOS CLIENTES TERCEIROS ESTE FILTRO É SOMENTE PARA AS UNIDADES DELE.]
     * 
     * @param  [type] $codigo_cliente [CODIGO DO CLIENTE DO GRUPO ECONOMICO]
     * @return [type]                 [description]
     */
    public function setor_terceiros($codigo_cliente=null)
    {
        
        //verifica se o usuario logado é de cliente
        if($this->BAuth->user('codigo_cliente')) {
            $codigo_cliente = $this->BAuth->user('codigo_cliente');
        }
        
        //redireciona para os clientes da unidade.
        if(!empty($codigo_cliente)) {
            $codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
            $this->redirect(array('controller' => 'setores', 'action' => 'index', $codigo_cliente,'implantacao_terceiros'));          
        }

        $this->pageTitle = 'Setores por Cliente';
        $this->carrega_combos();
        $this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

    }//fim cliente_terceiros

    /**
     * [carrega_combos description]
     * 
     * metodo para carregar os combos dos filtros de cliente
     * 
     * @param  boolean $listar_npe_nome [description]
     * @return [type]                   [description]
     */
    private function carrega_combos($listar_npe_nome = false) {
        $this->loadModel('MotivoBloqueio');
        $corretoras         = $this->Corretora->find('list', array('order' => 'nome'));
        $gestores           = $this->Gestor->listarNomesGestoresAtivos();
        $filiais            = $this->EnderecoRegiao->listarRegioes();       
        $somente_buonnysay  = array( 1 => 'Cliente BuonnySat', 2 => 'Todos' );
        $motivos            = $this->MotivoBloqueio->find('list', array('conditions' => array('codigo' => array(1,8,17)), 'order' => 'descricao DESC'));
        $ativo              = 'Ativos';
        $plano_saude        =  $this->PlanoDeSaude->listarPlanosAtivos();
        
        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores','ativo','filiais', 'somente_buonnysay', 'motivos','plano_saude'));

    }//FINAL FUNCTION carrega_combos

    function index_externo () {
        $this->pageTitle = "Setores Externo";
        $this->data[$this->SetorExterno->name] = $this->Filtros->controla_sessao($this->data, $this->SetorExterno->name);
    }

    function listagem_externo() {
        $this->layout = 'ajax';
        $setores = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->SetorExterno->name);

        $this->loadModel('GrupoEconomico');        
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if(!empty($filtros['codigo_cliente'])){

            $conditions = $this->SetorExterno->converteFiltroEmCondition($filtros);

            $fields = array(
                'Setor.codigo', 
                'SetorExterno.codigo', 
                'Setor.descricao', 
                'Setor.ativo', 
                'SetorExterno.codigo_externo'
            );

            $order = 'Setor.codigo';

            $this->Setor->bindModel(
                array('hasOne' => array(
                        'SetorExterno' => array(
                            'foreignKey' => 'codigo_setor', 
                            'conditions' => array('SetorExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ), false
            );

            $this->paginate['Setor'] = array(
                    'fields' => $fields,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => $order,
            );
           
            $setores = $this->paginate('Setor');
            $listagem = true;
        }

        $this->set(compact('setores','listagem'));
        $this->set('codigo_cliente', $codigo_cliente_matriz);
    }

    function editar_externo() {
        $this->pageTitle = 'Setores Externos'; 

        $codigoSetor = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoSetorExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosSetor = $this->Setor->carregar($codigoSetor);
        
        if($this->RequestHandler->isPost()) {

            if($this->SetorExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'setores'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[2])) {
            $this->data = $this->SetorExterno->find('first',array('conditions' => array('SetorExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosSetor;
        }
        $this->set('codigo_cliente', $codigo_cliente);
    }

}
