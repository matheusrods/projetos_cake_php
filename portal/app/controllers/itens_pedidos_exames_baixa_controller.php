<?php
class ItensPedidosExamesBaixaController extends AppController {
    public $name = 'ItensPedidosExamesBaixa';

    public function index() {
        $this->pageTitle = 'Baixa de Pedidos';
        
    	$this->data['PedidoExame'] = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
    	$this->set('lista_status_pedidos_exames', $this->__comboStatus());
    }
    
    var $uses = array(
    	'PedidoExame',
    	'ItemPedidoExame',
    	'StatusPedidoExame',
    	'ItemPedidoExameBaixa',
        'ClienteFuncionario',
        'FuncionarioSetorCargo',
        'Cliente',
        'Funcionario',
        'TipoExamePedido',
        'Exame',
        'Fornecedor',
        'ClienteContato',
        'Configuracao',
        'TiposResultadosExames'
    );

    public function beforeFilter() {
		parent::beforeFilter();		
		$this->BAuth->allow();
	}
    
    public function __comboStatus() {
    	return array('' => 'TODOS OS STATUS') + $this->StatusPedidoExame->find('list', array('order' => array('StatusPedidoExame.codigo ASC'), 'fields' => array('StatusPedidoExame.codigo', 'StatusPedidoExame.descricao')));
    }    

    public function listagem() {
        
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        /*****************************************/
        // solicitação feita pelo elcio 09/04/2019
        // 61613 => eliesy
        // 30/07/2019 -> duda solicitou para incluir os usuario fernanda.duda e ingrid.nascimento
        // alteracao para pegar da tabela de configuracao do sistema qual usuario pode reverter a baixa
        $configUserRevert = $this->Configuracao->getChave('CODIGO_USUARIO_REVERTER_BAIXA');
        //seta a variavel para nao dar erro quando não tiver a chave para a empresa
        $codigos_usuarios_liberados_para_reversao = array();
        //verifica se existe valor de qual usuario pode reverter a baixa
        if(!empty($configUserRevert)) {            
            //seta os usuarios que podem reverter a baixa, com os valores separados
            $codigos_usuarios_liberados_para_reversao = explode(",", $configUserRevert);
        }
        
        //variavel para controle na view
        $revert = "0";
        //pega o usuario logado
        if(in_array($this->authUsuario['Usuario']['codigo'], $codigos_usuarios_liberados_para_reversao)){
            $revert = '1';
        }
        /****************************************/

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])){
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $conditions = $this->PedidoExame->converteFiltroEmConditionBaixa($filtros);

        $fields = array(
        	'PedidoExame.codigo',
            'StatusPedidoExame.codigo',
        	'StatusPedidoExame.descricao',
        	'Cliente.razao_social',
        	'Funcionario.nome',
            'PedidoExame.allow_revert'
        );

        /* 
        SUBQUERY Que avalia se pode ser feito o Revert
        Somente Pedidos com status != Pendente e que tenham exames baixados com data de hoje
        */
        $this->PedidoExame->virtualFields['allow_revert'] = "   SELECT CASE WHEN convert(varchar, getdate(), 105) = convert(varchar, A.data_inclusao, 105) THEN 'yes' ELSE 'no' END
                                                                FROM itens_pedidos_exames_baixa A JOIN
                                                                     itens_pedidos_exames B ON A.codigo_itens_pedidos_exames = B.codigo
                                                                WHERe B.codigo_pedidos_exames = PedidoExame.codigo
                                                                ORDER BY A.codigo
                                                                OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY ";

        $joins  = array(
            array(
              	'table' => 'status_pedidos_exames',
              	'alias' => 'StatusPedidoExame',
              	'type' => 'LEFT',
              	'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames',
            ),
            array(
              	'table' => 'cliente_funcionario',
              	'alias' => 'ClienteFuncionario',
              	'type' => 'LEFT',
              	'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
            ),
        	array(
        		'table' => 'cliente',
        		'alias' => 'Cliente',
        		'type' => 'LEFT',
        		'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
        	),
        	array(
        		'table' => 'funcionarios',
        		'alias' => 'Funcionario',
        		'type' => 'LEFT',
        		'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
        	) 	
        );  
        
        $order = array('PedidoExame.codigo DESC');

        $this->paginate['PedidoExame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => $order
        );

        $pedidos_exames_baixa = $this->paginate('PedidoExame');
        $this->set(compact('pedidos_exames_baixa','revert'));
    }
    
    public function baixa($codigo_pedidos_exames, $edit = false, $lista_pedidos = null, $codigo_funcionario_setor_cargo = null) {

        /*        
        ################## NOTA ATENÇÃO ##################
        As alterações desse método afetam o seguinte método: modal_reverte_baixa
        */
        
        if(empty($lista_pedidos)){
            $lista_pedidos = 'lista_pedidos_exames';
        }

        $this->pageTitle = 'Baixar Itens do Pedido';
        
        if($this->RequestHandler->isPost()) {
            try {
                $this->ItemPedidoExameBaixa->query('begin transaction');

                foreach ($this->data['ItemPedidoExameBaixa'] as $chave => $dados) {
                    
                    if($dados['status_item'] == 0 && !empty($dados['resultado']) && !empty($dados['data_realizacao_exame'])){

                        $dados['data_realizacao_exame'] = AppModel::dateToDbDate($dados['data_realizacao_exame']);
                        
                       $descricao = trim($dados['descricao']);
                       if($dados['resultado'] == 2 && empty($descricao)){
                            $this->ItemPedidoExameBaixa->validationErrors[$chave]['descricao'] = 'Informe as Anormalidades do Exame';
                            throw new Exception();
                        }
                        
                        if (!$this->ItemPedidoExameBaixa->incluir($dados)) {

                            if(!empty($this->ItemPedidoExameBaixa->validationErrors)){
                                $this->ItemPedidoExameBaixa->validationErrors[$chave]['resultado'] = implode(',',$this->ItemPedidoExameBaixa->validationErrors);
                            } else {
                                $this->ItemPedidoExameBaixa->validationErrors[$chave]['resultado'] = 'Não é possível gravar os dados';
                            }
                            throw new Exception();
                        } else {

                            $item_pedido = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedidos_exames, 'codigo' => $dados['codigo_itens_pedidos_exames'])));

                            if($item_pedido){
                                $item_pedido['ItemPedidoExame']['compareceu'] = 1;
                                $item_pedido['ItemPedidoExame']['data_realizacao_exame'] = $dados['data_realizacao_exame'];

                                $this->ItemPedidoExame->atualizar($item_pedido);
                            }
                        } 

                    } else if($dados['status_item'] == 0 && !empty($dados['resultado']) && empty($dados['data_realizacao_exame'])){
                        $this->ItemPedidoExameBaixa->validationErrors[$chave]['data_realizacao_exame'] = 'Informe a data de realização do exame';
                        throw new Exception();
                    } else if($dados['status_item'] == 0 && empty($dados['resultado']) && !empty($dados['data_realizacao_exame'])){
                        $this->ItemPedidoExameBaixa->validationErrors[$chave]['resultado'] = 'Informe o Resultado do Exame';
                        throw new Exception();
                    }
                }

                if(empty($this->ItemPedidoExameBaixa->validationErrors)){

                    $status = $this->PedidoExame->statusBaixasExames( $this->data['PedidoExame']['codigo'] );

                    $this->data['PedidoExame']['codigo_status_pedidos_exames'] = $status;

                    if(!$this->PedidoExame->atualizar($this->data)) {
                        throw new Exception();
                    }

                    $this->ItemPedidoExameBaixa->commit();

                    //valida esocial
                    $this->PedidoExame->enviaEmailsESocial($this->data['PedidoExame']['codigo']);

                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                }
                else {
                    $this->ItemPedidoExameBaixa->rollback();
                }
            } 
            catch(Exception $e) {
                $this->BSession->setFlash('save_error');
                $this->ItemPedidoExameBaixa->rollback();
            }
        }

        $conditions = array('PedidoExame.codigo' => $codigo_pedidos_exames);
        // No modo de edição não mostra os itens não baixados
        if( $edit ){
            if( $this->Session->read('ErrorItemPedidoExameBaixa') ){
                $this->ItemPedidoExameBaixa->validationErrors = $this->Session->read('ErrorItemPedidoExameBaixa') ;
                $this->Session->delete('ErrorItemPedidoExameBaixa');
            }
            $conditions[] = ' ItemPedidoExameBaixa.codigo IS NOT NULL ';
        } 

        $joins  = array(
            array(
                'table' => $this->PedidoExame->databaseTable.'.'.$this->PedidoExame->tableSchema.'.'.$this->PedidoExame->useTable,
                'alias' => 'PedidoExame',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => $this->StatusPedidoExame->databaseTable.'.'.$this->StatusPedidoExame->tableSchema.'.'.$this->StatusPedidoExame->useTable,
                'alias' => 'StatusPedidoExame',
                'type' => 'LEFT',
                'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames',
            ),
            array(
                'table' => $this->ClienteFuncionario->databaseTable.'.'.$this->ClienteFuncionario->tableSchema.'.'.$this->ClienteFuncionario->useTable,
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
            ),
            array(
                'table' => $this->FuncionarioSetorCargo->databaseTable.'.'.$this->FuncionarioSetorCargo->tableSchema.'.'.$this->FuncionarioSetorCargo->useTable,
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
            ),
            array(
                'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                'alias' => 'Cliente',
                'type' => 'LEFT',
                'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
            ),
            array(
                'table' => $this->Funcionario->databaseTable.'.'.$this->Funcionario->tableSchema.'.'.$this->Funcionario->useTable,
                'alias' => 'Funcionario',
                'type' => 'LEFT',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ), 
            array(
                'table' => $this->ItemPedidoExameBaixa->databaseTable.'.'.$this->ItemPedidoExameBaixa->tableSchema.'.'.$this->ItemPedidoExameBaixa->useTable,
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
            ), 
            array(
                'table' => $this->Exame->databaseTable.'.'.$this->Exame->tableSchema.'.'.$this->Exame->useTable,
                'alias' => 'Exame',
                'type' => 'LEFT',
                'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
            ),   
            array(
                'table' => $this->Fornecedor->databaseTable.'.'.$this->Fornecedor->tableSchema.'.'.$this->Fornecedor->useTable,
                'alias' => 'Fornecedor',
                'type' => 'LEFT',
                'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
            )
        ); 
        
        $this->ItemPedidoExame->virtualFields['status_item'] = '(CASE 
                WHEN (SELECT count(*) FROM '.$this->ItemPedidoExameBaixa->databaseTable.'.'.$this->ItemPedidoExameBaixa->tableSchema.'.'.$this->ItemPedidoExameBaixa->useTable.' WHERE codigo_itens_pedidos_exames = ItemPedidoExame.codigo) = 0 THEN 0
            ELSE
                1
            END)';
       
        $fields = array(
            'PedidoExame.codigo', 'PedidoExame.codigo_cliente_funcionario', 'PedidoExame.codigo_cliente', 'PedidoExame.codigo_funcionario', 'PedidoExame.codigo_status_pedidos_exames',
            'StatusPedidoExame.codigo', 'StatusPedidoExame.descricao',
            'ClienteFuncionario.codigo', 'ClienteFuncionario.codigo_cliente', 'ClienteFuncionario.codigo_funcionario', 'ClienteFuncionario.ativo', 
            'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Cliente.ativo',
            'Funcionario.codigo', 'Funcionario.nome', 'Funcionario.cpf', 'Funcionario.status', 
            'ItemPedidoExame.codigo', 'ItemPedidoExame.codigo_pedidos_exames', 'ItemPedidoExame.codigo_exame', 'ItemPedidoExame.codigo_fornecedor', 'ItemPedidoExame.codigo_tipos_exames_pedidos',
            'ItemPedidoExameBaixa.codigo', 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames', 'ItemPedidoExameBaixa.resultado', 'ItemPedidoExameBaixa.data_validade', 'ItemPedidoExameBaixa.descricao', 'ItemPedidoExameBaixa.data_realizacao_exame',
            'Exame.codigo', 'Exame.codigo_servico', 'Exame.descricao',  'Exame.ativo',
            'Fornecedor.codigo', 'Fornecedor.codigo_documento', 'Fornecedor.nome', 'Fornecedor.ativo', 'Fornecedor.razao_social',
            'ItemPedidoExame.status_item',
            'ItemPedidoExameBaixa.fornecedor_particular',
            'ItemPedidoExame.codigo_pedidos_exames'
        );

        $order = array('Exame.descricao ASC');

        /*$this->ItemPedidoExame->bindModel(array(
            'belongsTo' => array(
                'PedidoExame' => array('foreignKey' => 'codigo_pedidos_exames'),
                'StatusPedidoExame' => array('foreignKey' => false, 'conditions' => 'StatusPedidoExame.codigo = PedidoExame.codigo_status_pedidos_exames'),
                'ClienteFuncionario' => array('foreignKey' => false, 'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'),
                'FuncionarioSetorCargo' => array('foreignKey' => false, 'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo'),
                'ClienteFuncionario' => array('foreignKey' => false, 'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'),
                'Funcionario' => array('foreignKey' => false, 'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'),
                'Exame' => array('foreignKey' => 'codigo_exame'),
                'Fornecedor' => array('foreignKey' => 'codigo_fornecedor'),
            ),
            'hasOne' => array(
                'ItemPedidoExameBaixa' => array('foreignKey' => 'codigo_itens_pedidos_exames')
            ),
        ), false);*/

        $resultados = array(
            '1' => 'Normal', 
            '2' => 'Alterado',
            '3' => 'Estável',
            '4' => 'Agravamento',
            '5' => 'Referencial',
            '6' => 'Sequencial'
        );


        $itens_pedidos_exames = $this->ItemPedidoExame->find('all', compact('fields', 'conditions', 'order', 'joins'));

        if (!empty($itens_pedidos_exames)) {

            foreach ($itens_pedidos_exames as $key => $exame) {

                $fields = array(                    
                   "TiposResultados.codigo",
                    "TiposResultados.descricao",
                );

                $joins = array(
                    array(
                        'table' => 'tipos_resultados',
                        'alias' => 'TiposResultados',
                        'type' => 'INNER',
                        'conditions' => "TiposResultadosExames.codigo_exame = {$exame['Exame']['codigo']}",
                    )
                );

                $conditions = array(
                    "TiposResultadosExames.codigo_tipo_resultado = TiposResultados.grupo"
                );

                $get_tipos_resultados_exames = $this->TiposResultadosExames->find('list', array(
                    'fields' => $fields,
                    'joins' => $joins,
                    'conditions' => $conditions
                ));

                $itens_pedidos_exames[$key]['TiposResultados'] = $get_tipos_resultados_exames;
                
            }
        }
        //debug($itens_pedidos_exames);
        $this->set(compact('codigo_pedidos_exames', 'itens_pedidos_exames', 'edit','resultados', 'lista_pedidos', 'codigo_funcionario_setor_cargo'));
    }


    public function editar( $codigo_pedidos_exames ){
        $this->pageTitle = 'Editar Itens do Pedido';
        if($this->RequestHandler->isPost()) {
            $message = "";
            try {
                $this->ItemPedidoExameBaixa->query('begin transaction');

                foreach ($this->data['ItemPedidoExameBaixa'] as $chave => $dados) {

                    // Obriga data realização
                    if( empty($dados['data_realizacao_exame']) ){
                        $message = 'Informe a data de realização do exame';
                        $this->ItemPedidoExameBaixa->validationErrors[$chave]['data_realizacao_exame'] = $message;
                        throw new Exception();
                    }

                    // Obriga descricao anormalidade
                    $descricao = trim($dados['descricao']);
                    if($dados['resultado'] == 2 && empty($descricao)){
                        $message = 'Informe as Anormalidades do Exame';
                        $this->ItemPedidoExameBaixa->validationErrors[$chave]['descricao'] = $message;
                        throw new Exception();
                    }

                    if(empty($dados['resultado'])){
                        $message = 'Informe o Resultado do Exame';
                        $this->ItemPedidoExameBaixa->validationErrors[$chave]['resultado'] = $message;
                        throw new Exception();
                    }

                    unset($dados['status_item']);
                    
                    $dados['data_realizacao_exame'] = AppModel::dateToDbDate($dados['data_realizacao_exame']);
                    
                    $update['ItemPedidoExameBaixa'] = $dados;
                    
                    if (!$this->ItemPedidoExameBaixa->atualizar($update)) {

                        $message = 'Não é possível alterar os dados';
                        throw new Exception();
                    } else {

                        $item_pedido = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedidos_exames, 'codigo' => $dados['codigo_itens_pedidos_exames'])));

                        if($item_pedido){
                            $item_pedido['ItemPedidoExame']['compareceu'] = 1;
                            $item_pedido['ItemPedidoExame']['data_realizacao_exame'] = $update['ItemPedidoExameBaixa']['data_realizacao_exame'];
                            $this->ItemPedidoExame->atualizar($item_pedido);
                        }
                    }
                }

                $this->ItemPedidoExameBaixa->commit();
                $this->BSession->setFlash('save_success');
            }
            catch(Exception $e) {
                $this->Session->write('ErrorItemPedidoExameBaixa', $this->ItemPedidoExameBaixa->validationErrors );
                $this->BSession->setFlash('save_error');
                $this->ItemPedidoExameBaixa->rollback();
            }
        }        

        $this->autoRender = false;
        $this->redirect(array('action' => 'baixa', $codigo_pedidos_exames, 1 ));

    }

    public function reverte_baixa($codigo_pedidos_exames) {

        $itens =  ( isset($_POST['itens']) ? $_POST['itens'] : null );

        $this->layout = 'ajax';
        $this->autoRender = false;

        $this->loadModel('ConsolidadoNfsExame');
        $exames_bloqueados = $this->ConsolidadoNfsExame->validaReverteExame($itens);

        $descricao_exames = "";
        if($exames_bloqueados){
            foreach($exames_bloqueados as $key => $exame_bloqueado){
                $descricao_exames .= "\n";
                $descricao_exames .= $exame_bloqueado['Exames']['descricao'];
                if(end($exames_bloqueados) != $exame_bloqueado){
                    $descricao_exames .= ", ";
                }   
            }

            echo $descricao_exames;

        }else{
            if($this->ItemPedidoExameBaixa->reverte_baixa($codigo_pedidos_exames, $itens )){
                echo 1;
            } else {
                echo 0;
            }
        }

        $this->render(false,false);
    }

    /**
     * [modal_reverte_baixa description]
     * @param  [type] $codigo_pedidos_exames [description]
     * @return [type]                        [description]
     */
    public function modal_reverte_baixa($codigo_pedidos_exames)
    {         

        /*
        Reutilização do método "baixa", mas com novo .ctp: "modal_reverte_baixa.ctp"
        */

        $this->baixa( $codigo_pedidos_exames, true );

    }//fim modal_reverte_baixa
    
}