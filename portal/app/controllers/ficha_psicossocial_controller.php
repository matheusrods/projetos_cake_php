<?php
class FichaPsicossocialController extends AppController {
  public $name = 'FichaPsicossocial';
  public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');
  var $uses = array( 
      'FichaPsicossocial',
      'FichaPsicossocialLog',
      'FichaPsicossocialRespostaLog',
      'GrupoEconomicoCliente',
      'FuncionarioSetorCargo',
      'PedidoExame',
      'MotivoCancelamento',
      'FichaClinica',
      'FichaPsicossocialResposta',
      'FichaPsicossocialPergunta',
      'Configuracao',
    );
  
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('ficha_psicossocial_terceiros','lista_psicossocial_terceiros'));
    }

    public function index($codigo_unidade =  null) {
        $this->pageTitle = 'Ficha Psicossocial';
        
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaPsicossocial');
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $this->data['FichaPsicossocial'] = $filtros;
        $this->set(compact('status_matricula'));
        $this->carrega_combos_grupo_economico('FichaPsicossocial');
    }

    public function listagem() {
        $this->layout = 'ajax';

        $this->loadModel('FuncionarioSetorCargo');
        $this->loadModel('GrupoEconomicoCliente');    

        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaPsicossocial');
        
        $authUsuario = $this->BAuth->user();
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $listagem = array();
        
        if(!empty($filtros['codigo_cliente'])) {
            
            $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
          
            if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
                $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
            }

            $conditions = $this->FuncionarioSetorCargo->converteFiltrosEmConditions($filtros);

            //condicao incluida para nao deixar apresentar um setor e cargo que esteja com a data fim do setor/cargo preenchida
            // $conditions['OR'] = array('FuncionarioSetorCargo.data_fim IS NULL','ClienteFuncionario.ativo'=>'0');
            $conditions[] = array('FuncionarioSetorCargo.data_fim IS NULL');

            $order = array('Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Funcionario.nome');
        
            $joins = array(
                array(
                    'table' => 'cliente_funcionario',
                    'alias' => 'ClienteFuncionario',
                    'type' => 'INNER',
                    'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
                ),
                array(
                    'table' => 'funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'INNER',
                    'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
                ),
                array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'type' => 'INNER',
                    'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
                ),
                array(
                    'table' => 'cargos',
                    'alias' => 'Cargo',
                    'type' => 'INNER',
                    'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
                ),
                array(
                    'table' => 'pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo and PedidoExame.codigo = (select ItemPedidoExame.codigo_pedidos_exames from itens_pedidos_exames ItemPedidoExame where PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames and ItemPedidoExame.codigo_exame = '.$this->Configuracao->getChave('INSERE_EXAME_CLINICO').')',
                ),
            );

            $fields = array(
                'Funcionario.nome',
                'Funcionario.codigo',
                'Cliente.codigo', 
                'Cliente.razao_social', 
                'Cliente.nome_fantasia',
                'Cargo.codigo', 
                'Cargo.descricao',
                'Setor.codigo', 
                'Setor.descricao', 
                'FuncionarioSetorCargo.codigo', 
                'FuncionarioSetorCargo.codigo_cliente_alocacao', 
                'FuncionarioSetorCargo.codigo_cliente_funcionario', 
                'ClienteFuncionario.ativo'
            );

            $this->paginate['FuncionarioSetorCargo'] = array(
                'recursive' => -1,  
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50,
                'group' => $fields,
                'order' => $order
            );

            $listagem = $this->paginate('FuncionarioSetorCargo');

            $this->set(compact('listagem'));
            
            $this->set('selecao_em_massa', array('' => 'Selecionar Ação em Massa', '1' => 'Inclusão em Massa'));
            
            $this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

            $this->Filtros->limpa_sessao($this->FuncionarioSetorCargo->name);
        }
    }

    public function listagem_ficha_psicossocial($codigo_funcionario_setor_cargo, $id_pedido = 0, $codigo_cliente_funcionario) {

        $this->pageTitle = 'Listagem de Ficha Psicossocial';

        $this->loadModel('FuncionarioSetorCargo');    
        $this->loadModel('PedidoExame');    
        $this->loadModel('GrupoEconomicoCliente');    
        $this->loadModel('MotivoCancelamento');
        $this->loadModel('FichaClinica');

        $conditions['PedidoExame.codigo_cliente_funcionario'] = $codigo_cliente_funcionario;

        $dados_consulta = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo), 'recursive' => -1));
        /***************************************************
         * validacao adicionado para evitar o cliente de
         * burlar o acesso e ver dados de outros clientes;
         ***************************************************/
        if(!is_null($this->BAuth->user('codigo_cliente'))) {

            //verifica se esse usuario é multicliente
            $Bauth = $this->BAuth->user();          
            if(!isset($Bauth['Usuario']['multicliente'])) {

                $dados_grupo_economico_cliente = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->BAuth->user('codigo_cliente')), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));

                $dados_grupo_economico_solicitado = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_alocacao']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
                
                if($dados_grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] != $dados_grupo_economico_solicitado['GrupoEconomicoCliente']['codigo_grupo_economico']) {
                    $this->BSession->setFlash('acesso_nao_permitido');
                    $this->redirect(array('controller' => 'ficha_psicossocial', 'action' => 'index'));
                
                }//verifica se é o mesmo grupo economico


            }//fim multicliente
            
            $conditions['ClienteFuncionario.codigo_cliente_matricula'] = $this->BAuth->user('codigo_cliente');
        }

        $order = 'FichaPsicossocial.codigo';
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo = FichaPsicossocial.codigo_pedido_exame'   
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'    
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'    
            ),
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'    
            ),
            array(
                'table' => 'RHHealth.dbo.medicos',
                'alias' => 'Medico',
                'type' => 'INNER',
                'conditions' => 'Medico.codigo = FichaPsicossocial.codigo_medico'    
            )
        );
          
        $fields = array(
            'FichaPsicossocial.*',
            'Cliente.razao_social',
            'Funcionario.nome',
            'Funcionario.codigo',
            'Medico.nome',
            'PedidoExame.codigo'
        );

        $this->paginate['FichaPsicossocial'] = array(
            'conditions' => $conditions,
            'fields'     => $fields,
            'limit'      => 50,
            'joins'      => $joins,
            'order'      => $order,
            'recursive'  => 1
        );

        // pr($this->FichaPsicossocial->find('sql', $this->paginate['FichaPsicossocial'] ));exit;

        $dados_grupo_economico = $this->GrupoEconomicoCliente->find(
            'first', array(
                'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_alocacao']), 
                'recursive' => '-1', 
                'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'
            )
        );

        $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];

        $flag_aviso_sugestao = 0;
        if($id_pedido != 0) {
              foreach($this->ItemPedidoExame->find('all', array('conditions' => array('codigo_pedido_exame' => $id_pedido))) as $item) {
                if($item['ItemPedidoExame']['tipo_agendamento'] == '1') {
                  $flag_aviso_sugestao = 1;
              }
          }
        }

        $options = array('conditions' => array('ativo' => 1), 'order' => array('descricao ASC'));
        $motivos_cancelamento = $this->MotivoCancelamento->find('list', $options );
        $motivos_cancelamento[0] = 'Selecionar um Motivo';
        ksort($motivos_cancelamento);

        $dados_cliente_funcionario = $this->PedidoExame->retornaEstrutura($codigo_funcionario_setor_cargo);
        // $codigo_cliente_funcionario = $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_funcionario'];

        $ficha_psicossocial = $this->paginate('FichaPsicossocial');



        $this->set(compact(
            'lista_ficha_psicossocial', 
            'ficha_psicossocial',
            'dados_cliente_funcionario', 
            'codigo_funcionario_setor_cargo',
            'codigo_cliente_funcionario', 
            'flag_aviso_sugestao', 
            'codigo_grupo_economico', 
            'motivos_cancelamento','pedido_bloqueado'
        ));
    }//FINAL FUNCTION listagem_ficha_psicossocial

    public function selecionar_pedido_de_exame() {
    }

    public function listagem_pedido_de_exame($codigo_funcionario_setor_cargo,$codigo_cliente_funcionario) {

        $this->pageTitle = 'Selecionar pedido de exame';

        $order = 'PedidoExame.codigo';

        $joins = array(
            array(
                'table'         => 'RHHealth.dbo.cliente_funcionario',
                'alias'         => 'ClienteFuncionario',
                'type'          => 'INNER',
                'conditions'    => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
            ),
            array(
                'table'         => 'RHHealth.dbo.cliente',
                'alias'         => 'Cliente',
                'type'          => 'INNER',
                'conditions'    => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula'
            ),
            array(
                'table'         => 'RHHealth.dbo.funcionarios',
                'alias'         => 'Funcionario',
                'type'          => 'INNER',
                'conditions'    => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
            ),
            array(
                'table'         => 'RHHealth.dbo.itens_pedidos_exames',
                'alias'         => 'ItemPedidoExame',
                'type'          => 'INNER',
                'conditions'    => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
            ),
            array(
                'table'         => 'RHHealth.dbo.ficha_psicossocial',
                'alias'         => 'FichaPsicossocial',
                'type'          => 'LEFT',
                'conditions'    => 'FichaPsicossocial.codigo_pedido_exame = PedidoExame.codigo'
            )
        );

        $fields = array(
            'PedidoExame.codigo',
            'Cliente.razao_social',
            'Funcionario.nome'
        );

        //pega o codigo da ficha psicossocial
        $codigo_exame_psicossocial = $this->Configuracao->getChave('FICHA_PSICOSSOCIAL');
        
        // $conditions['PedidoExame.codigo_func_setor_cargo'] = $codigo_funcionario_setor_cargo;
        $conditions['PedidoExame.codigo_cliente_funcionario'] = $codigo_cliente_funcionario;
        $conditions['ItemPedidoExame.codigo_exame'] = $codigo_exame_psicossocial; //'27';
        $conditions['FichaPsicossocial.codigo'] = NULL;

        $this->paginate['PedidoExame'] = array(   
            'conditions' => $conditions,
            'joins'      => $joins,
            'fields'     => $fields,
            'group'      => $fields,
            'limit'      => 50,
            'order'      => $order,
        );

        // pr($this->PedidoExame->find('sql', $this->paginate['PedidoExame'] ));exit;

        $pedidosExames = $this->paginate('PedidoExame');

        $this->set(compact('pedidosExames','codigo_funcionario_setor_cargo','codigo_cliente_funcionario'));
        $this->Filtros->limpa_sessao($this->FichaPsicossocial->name);
    }

    public function retorna_codigo_grupo_economico() {

        /***************************************************
         * validacao adicionado para evitar o cliente de
         * burlar o acesso e ver dados de outros clientes;
         ***************************************************/
        if(!is_null($this->BAuth->user('codigo_cliente'))) {
            $codigo_unidade = $this->BAuth->user('codigo_cliente');
        } else {
            $codigo_unidade = $this->params['form']['codigo_unidade'];
        }

        $this->GrupoEconomicoCliente->virtualFields = false;
        $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
        echo json_encode(array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']));
        exit;
    }

    public function autocomplete_funcionario() {
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $codigo_cliente = $this->passedArgs['codigo'];

        $codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

        $codigos_unidades = $this->GrupoEconomicoCliente->lista($codigo_matriz);
        
        $conditions = array('ClienteFuncionario.codigo_cliente' => array_keys($codigos_unidades), 'Funcionario.nome LIKE' => $_GET['term'].'%');
        
        $fields = array('Funcionario.codigo', 'Funcionario.nome');
        
        $recursive = 1;
        
        $order = array('Funcionario.nome');
        
        $list = $this->ClienteFuncionario->find('list', compact('conditions', 'fields', 'recursive', 'order'));
        
        $result = array();
        
        foreach ($list as $key => $value) {
            $result[] = array('value' => $key, 'label' => $value);
        }

        echo json_encode($result);
        die();
    }

    public function carrega_combos_grupo_economico($model) {
        $this->loadModel('Cargo');
        $this->loadModel('Setor');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $unidades = array();
        $setores = array();
        $cargos = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
            $cargos = $this->Cargo->lista($codigo_cliente);
        }  
        
        $this->set(compact('unidades', 'setores', 'cargos'));
    }

    public function incluir($codigoPedidoExame = null, $redir = null) {

        /***************************************************
         * validacao adicionado para evitar o cliente de
         * burlar o acesso e ver dados de outros clientes;
         ***************************************************/
        if(!is_null($this->BAuth->user('codigo_cliente'))) {
            $codigo_cliente = $this->BAuth->user('codigo_cliente');
            $dados_pedido = $this->PedidoExame->retornaPedido($codigoPedidoExame);
            
            // se for array é multicliente
            if(is_array($codigo_cliente)  ){
                $matricula_valida = in_array($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'], $codigo_cliente);
            } else {
                $matricula_valida = ($dados_pedido['ClienteFuncionario']['codigo_cliente_matricula'] == $codigo_cliente);
            }
            if(!$matricula_valida) {
                $this->BSession->setFlash('acesso_nao_permitido');
                $this->redirect(array('controller' => 'fichas_clinicas', 'action' => 'selecionarPedidoDeExame'));
            }
        }

        if($this->RequestHandler->isPost()) {
            
            $msg = "save_success"; //variavel que contem a mensagem de erro ou sucesso

            if($this->FichaPsicossocial->incluir($this->data)) {
                        //pegando o codigo da tabela ficha psicossocial
                $codigo_ficha_psicossocial = $this->FichaPsicossocial->id;
                        //varre as perguntas
                foreach ($this->data['FichaPsicossocialPergunta'] as $codigo => $resposta) {
                            //seta as respostas
                    $dadosRespostas['FichaPsicossocialResposta'] = array( 
                        'codigo_ficha_psicossocial' => $codigo_ficha_psicossocial,
                        'codigo_ficha_psicossocial_perguntas' => $codigo,
                        'resposta' => $resposta,
                        'ativo' => '1'
                    );
                    if(!$this->FichaPsicossocialResposta->incluir($dadosRespostas)) {
                        //seta o erro
                        $msg ='save_error';
                    }
                }//fim perguntas/respostas
            } else {
                //seta o erro
                $msg = 'save_error';
            }

            //pega o pedido para pegar o codigo da funcionario setores cargos
            $pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('PedidoExame.codigo' => $this->data['FichaPsicossocial']['codigo_pedido_exame'])));
            //seta a mensagem de sucesso depois de incluir
            $this->BSession->setFlash($msg);

            if ($redir == "consulta_agendada") {
                $this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
                return;
            }
            //redireciona depois de incluir para a tela de listagem de ficha psicossocial
            $this->redirect(array('controller' => 'ficha_psicossocial','action' => 'listagem_ficha_psicossocial', $pedido_exame['PedidoExame']['codigo_func_setor_cargo'],0,$pedido_exame['PedidoExame']['codigo_cliente_funcionario']));
        } //fim isPost


        $fields = array(
            'FichaPsicossocialPergunta.codigo',
            'FichaPsicossocialPergunta.ordem',
            'FichaPsicossocialPergunta.pergunta'
        );
        $perguntas = $this->FichaPsicossocialPergunta->find('all', array('fields' => $fields));

            //valida se existe o pedido de exame selecionado, senao retorna a index e exibe erro
        $ficha_psicossocial = $this->FichaPsicossocial->find('first', array('conditions' => array('codigo_pedido_exame' => $codigoPedidoExame)));

        $this->pageTitle = 'Incluir Ficha Psicossocial';

        $dados = $this->FichaPsicossocial->obtemDadosComplementaresFPS($codigoPedidoExame);

        $this->set(compact('dados','redir','perguntas'));
    }//fim incluir

    /**
     * 
     * metodo para edição da ficha psicossocial 
     */
    public function editar($codigo_pedido_exame,$codigo_ficha_psicossocial = null, $redir = null)
    {
        $this->pageTitle = 'Editar Ficha Psicossocial';
        $this->set(compact('codigo_ficha_psicossocial'));

        $pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('PedidoExame.codigo' => $this->data['FichaPsicossocial']['codigo_pedido_exame'])));

        if($this->RequestHandler->isPost()) {

            $msg = "save_success";

            if($this->FichaPsicossocial->atualizar($this->data)) {

                //deletar todas as respostas para atualizar
                if(!$this->FichaPsicossocialResposta->deleteAll(array('FichaPsicossocialResposta.codigo_ficha_psicossocial' => $codigo_ficha_psicossocial), false)) {
                    //seta erro
                    $msg = "save_error";
                }

                //varre as perguntas
                foreach ($this->data['FichaPsicossocialPergunta'] as $codigo => $resposta) {

                    //seta as respostas
                    $dadosRespostas['FichaPsicossocialResposta'] = array( 
                        //'codigo' => $codigo_ficha_psicossocial_resposta,
                        'codigo_ficha_psicossocial' => $codigo_ficha_psicossocial,
                        'codigo_ficha_psicossocial_perguntas' => $codigo,
                        'resposta' => $resposta,
                        'ativo' => '1'
                    );
                    if(!$this->FichaPsicossocialResposta->incluir($dadosRespostas)) {
                        $msg = "save_error";
                    }
                }//fim perguntas/respostas
            } else {
                //seta o erro
                $msg = 'save_error';
            }
            $this->BSession->setFlash($msg); //exibe a mensagem de sucesso

            if ($redir == "consulta_agendada") {
                $this->redirect(array('controller' => 'consultas_agendas','action' => 'index2'));
                return;
            }

            $this->redirect(array('controller' => 'ficha_psicossocial','action' => 'listagem_ficha_psicossocial', $pedido_exame['PedidoExame']['codigo_func_setor_cargo'],0,$pedido_exame['PedidoExame']['codigo_cliente_funcionario'])); //rediciona para a tela da listagem de ficha psicossocial
        } //fim isPost

        //executa a query para pegar as perguntas
        $perguntas = $this->FichaPsicossocialPergunta->find('all', array('conditions' => array('ativo' => 1), 'order' => array('ordem')));

        //dados dos médicos relacionados no cliente
        $dados = $this->FichaPsicossocial->obtemDadosComplementaresFPS($codigo_pedido_exame);


        $field_pergunta = array(
            'FichaPsicossocial.codigo',
            'FichaPsicossocial.codigo_pedido_exame',
            'FichaPsicossocial.codigo_medico',
            'FichaPsicossocial.total_sim',
            'FichaPsicossocial.total_nao',
            'FichaPsicossocialResposta.codigo',
            'FichaPsicossocialResposta.codigo_ficha_psicossocial',
            'FichaPsicossocialResposta.codigo_ficha_psicossocial_perguntas',
            'FichaPsicossocialResposta.resposta',
            'Medico.codigo',
            'Medico.nome',
            'ItemPedidoExame.respondido_lyn',
        );

        $joins = array(        
            array(
                'table'         => 'RHHealth.dbo.ficha_psicossocial_respostas',
                'alias'         => 'FichaPsicossocialResposta',
                'type'          => 'INNER',
                'conditions'    => 'FichaPsicossocial.codigo = FichaPsicossocialResposta.codigo_ficha_psicossocial'
            ),
            array(
                'table'         => 'RHHealth.dbo.medicos',
                'alias'         => 'Medico',
                'type'          => 'INNER',
                'conditions'    => 'FichaPsicossocial.codigo_medico = Medico.codigo'
            ),
            array(
                'table' => 'pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'FichaPsicossocial.codigo_pedido_exame = PedidoExame.codigo',
            ),
            array(
                'table' => 'itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo and ItemPedidoExame.codigo_exame = '.$this->Configuracao->getChave('INSERE_EXAME_CLINICO'),
            ),
        );

        $conditions = array('FichaPsicossocial.codigo' => $codigo_ficha_psicossocial);

        $respostas = $this->FichaPsicossocial->find('all', array('fields' => $field_pergunta, 'joins' => $joins, 'conditions' => $conditions));

        foreach($respostas as $val){
            $this->data['FichaPsicossocial'] = $val['FichaPsicossocial'];
            $this->data['FichaPsicossocialResposta'][$val['FichaPsicossocialResposta']['codigo_ficha_psicossocial_perguntas']] = $val['FichaPsicossocialResposta']['resposta'];
            $this->data['Medico'] = $val['Medico'];
            $this->data['ItemPedidoExame'] = $val['ItemPedidoExame'];
        }

        $this->set(compact('dados','redir','perguntas','codigo_pedido_exame')); 
    }//fim editar


    /**
     * metodo para chamar o jasper e gerar o pdf da ficha psicossocial
     */
    public function imprimir_relatorio($codigo_ficha_psicossocial)
    {
        $this->autoRender = false;

        $this->__jasperConsulta($codigo_ficha_psicossocial);

    }//fim imprimir_relatorio

    private function __jasperConsulta($codigo) {

        // opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME'=>'/reports/RHHealth/ficha_psicossocial', // especificar qual relatório
			'FILE_NAME'=> basename( 'avaliacao_psicossocial.pdf' ) // nome do relatório para saida
		);

		// parametros do relatorio
        $parametros = array('CODIGO_FICHA_PSICOSSOCIAL' => $codigo);
        
		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
        $this->loadModel('MultiEmpresa');
        //codigo empresa emulada
        $codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
        //url logo da multiempresa
        $parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

		try {
            
            // envia dados ao componente para gerar
			$url = $this->Jasper->generate( $parametros, $opcoes );	

			if($url){
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url; exit;
			}

		} catch (Exception $e) {
			// se ocorreu erro
			debug($e); exit;
		}		

        exit;
        
    }

    public function ficha_psicossocial_terceiros($codigo_unidade =  null) {
        $this->pageTitle = 'Ficha Psicossocial';
        
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaPsicossocial');
        
        $authUsuario = $this->BAuth->user();

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        if(empty($filtros['data_inicio'])) {            
            $filtros['data_inicio'] = '01/'.date('/m/Y');
            $filtros['data_fim']    = date('d/m/Y');
        }

        $this->data['FichaPsicossocial'] = $filtros;
        
        $this->carrega_combos_grupo_economico('FichaPsicossocial');
        $this->set(compact('status_matricula'));
    }

    public function lista_psicossocial_terceiros($destino, $export = false) {
        $this->layout = 'ajax'; 

        $this->loadModel('FuncionarioSetorCargo');
        $this->loadModel('GrupoEconomicoCliente');    

        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaPsicossocial');

        $authUsuario = $this->BAuth->user();
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {        
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $listagem = array();

        if(!empty($filtros['codigo_cliente'])) {
            
            $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
          
            if(isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
                $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
            }

            $conditions = $this->FichaPsicossocial->converteFiltrosEmConditionsTerceiros($filtros);

            $conditions[] = array('ItemPedidoExame.codigo_exame = '.$this->Configuracao->getChave('INSERE_EXAME_CLINICO'));
            
            $fields = array(
                'Cliente.codigo',
                'Cliente.nome_fantasia',
                'Cliente.razao_social',
                'Setor.descricao',
                'Cargo.descricao',
                'Funcionario.nome',
                'Funcionario.cpf',
                'ClienteFuncionario.codigo',
                'ClienteFuncionario.matricula',
                'PedidoExame.codigo',
                'FichaPsicossocialPerguntas.pergunta',
                'CASE 
                        WHEN FichaPsicossocialRespostas.resposta = 0 THEN \'Não\'
                        WHEN FichaPsicossocialRespostas.resposta = 1 THEN \'Sim\'
                    ELSE \'\'
                END AS resposta',
                'ItemPedidoExame.data_realizacao_exame'
            );
        
            $joins = array(
                array(
                    'table' => 'ficha_psicossocial_respostas',
                    'alias' => 'FichaPsicossocialRespostas',
                    'type' => 'INNER',
                    'conditions' => 'FichaPsicossocial.codigo = FichaPsicossocialRespostas.codigo_ficha_psicossocial',
                ),
                array(
                    'table' => 'ficha_psicossocial_perguntas',
                    'alias' => 'FichaPsicossocialPerguntas',
                    'type' => 'INNER',
                    'conditions' => 'FichaPsicossocialPerguntas.codigo = FichaPsicossocialRespostas.codigo_ficha_psicossocial_perguntas',
                ),
                array(
                    'table' => 'pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'INNER',
                    'conditions' => 'FichaPsicossocial.codigo_pedido_exame = PedidoExame.codigo',
                ),
                array(
                    'table' => 'funcionario_setores_cargos',
                    'alias' => 'FuncionarioSetorCargo',
                    'type' => 'INNER',
                    'conditions' => 'PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo',
                ),
                array(
                    'table' => 'cliente_funcionario',
                    'alias' => 'ClienteFuncionario',
                    'type' => 'INNER',
                    'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                ),
                array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'type' => 'INNER',
                    'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
                ),
                array(
                    'table' => 'cargos',
                    'alias' => 'Cargo',
                    'type' => 'INNER',
                    'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo',
                ),
                array(
                    'table' => 'funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'INNER',
                    'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente',
                ),
                array(
                    'table' => 'itens_pedidos_exames',
                    'alias' => 'ItemPedidoExame',
                    'type' => 'INNER',
                    'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
                ),
            );


            $this->paginate['FichaPsicossocial'] = array(
                'recursive' => -1,  
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50
            );

            if($export){                
                $query = $this->FichaPsicossocial->find('sql',array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));            
                $this->export_lista_psicossocial($query);
            } else {
                $listagem = $this->paginate('FichaPsicossocial');  
            }

            $this->set(compact('listagem'));
            
            $this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));
        }
    }

    public function export_lista_psicossocial($query){

        //instancia o dbo
        $dbo = $this->FichaPsicossocial->getDataSource();
        
        //pega todos os resultados
        $dbo->results = $dbo->rawQuery($query);

        //headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="ficha_psicossocial.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        echo utf8_decode('"Código Unidade";"Nome Unidade";"Razão Social Unidade";"Setor";"Cargo";"Funcionário";"CPF";"Cód. Matrícula";"Matrícula";"Cód. Pedido de Exame";"Pergunta";"Resposta";"Data de Realização";')."\n";
        
        // varre todos os registros da consulta no banco de dados
        while($lista_psicossocial = $dbo->fetchRow()){

            $linha  = $lista_psicossocial['Cliente']['codigo'].';';
            $linha .= $lista_psicossocial['Cliente']['nome_fantasia'].';';
            $linha .= $lista_psicossocial['Cliente']['razao_social'].';';
            $linha .= $lista_psicossocial['Setor']['descricao'].';';
            $linha .= $lista_psicossocial['Cargo']['descricao'].';';
            $linha .= $lista_psicossocial['Funcionario']['nome'].';';
            $linha .= $lista_psicossocial['Funcionario']['cpf'].';';
            $linha .= $lista_psicossocial['ClienteFuncionario']['codigo'].';';
            $linha .= $lista_psicossocial['ClienteFuncionario']['matricula'].';';
            $linha .= $lista_psicossocial['PedidoExame']['codigo'].';';
            $linha .= $lista_psicossocial['FichaPsicossocialPerguntas']['pergunta'].';';
            $linha .= (utf8_decode($lista_psicossocial[0]['resposta'].';'));
            $linha .= $lista_psicossocial['ItemPedidoExame']['data_realizacao_exame'].';';
            $linha .= "\n";
            
            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }//fim while
        
        //mata o metodo
        die();
    }//fim export_lista_psicossocial
}//FINAL CLASS FichaPsicossocialController