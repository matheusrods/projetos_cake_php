<?php
class TipoDigitalizacaoController extends AppController {
    public $name = 'TipoDigitalizacao';
    public $components = array('Filtros', 'RequestHandler','ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Highcharts','Buonny');
    var $uses = array(
        'TipoDigitalizacao', 
        'AnexoDigitalizacao',
        'GrupoEconomicoCliente',
        'GrupoEconomico',
        'Cliente', 
        'Setor',
        'Cargo',
    );    

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    /**
     * [index description]
     * 
     * metodo para montar os filtros
     * 
     * @return [type] [description]
     */
	public function index() {

	   $this->pageTitle = 'Tipos Digitalização';
	}

    /**
     * [listagem description]
     * 
     * metodo para montar a listagem das 
     * 
     * @return [type] [description]
     */
    public function listagem() 
    {

        $this->layout = 'ajax'; 

        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoDigitalizacao->name);
        
        $conditions = $this->TipoDigitalizacao->converteFiltroEmCondition($filtros);

        $fields = array('TipoDigitalizacao.codigo','TipoDigitalizacao.descricao','TipoDigitalizacao.ativo', 'TipoDigitalizacao.codigo_empresa');
        
        $order = 'TipoDigitalizacao.descricao';

        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        if(isset($codigo_empresa)){
            $conditions = array('TipoDigitalizacao.codigo_empresa' => $codigo_empresa);
		}

        $this->paginate['TipoDigitalizacao'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
            );

        $tipo_digitalizacao = $this->paginate('TipoDigitalizacao');
        
        $this->set(compact('tipo_digitalizacao'));
    }

    function incluir() {
    
        $this->pageTitle = 'Incluir Tipo Digitalização';

        if($this->RequestHandler->isPost()) {

            $this->data['TipoDigitalizacao']['ativo'] = 1;            

            if ($this->TipoDigitalizacao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipo_digitalizacao'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }  
    }

    function editar($codigo) {

        $this->pageTitle = 'Editar Tipo Digitalização'; 

        if($this->RequestHandler->isPost()) {

            if ($this->TipoDigitalizacao->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'tipo_digitalizacao', 'action' => 'index'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }

            $this->redirect(array('controller' => 'tipo_digitalizacao', 'action' => 'index'));
        }

        //pega os dados da digitalizacao
        $this->data = $this->TipoDigitalizacao->find('first', array('conditions' => array('codigo' => $codigo)));

        $this->set(compact('codigo'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';

        $this->data['TipoDigitalizacao']['codigo'] = $codigo;
        $this->data['TipoDigitalizacao']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TipoDigitalizacao->atualizar($this->data, false)) {   
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false,false);
    }

    function operacao_digitalizacao_terceiros(){

        $this->pageTitle = 'Digitalização';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoDigitalizacao->name);

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        if(empty($filtros['data_inicio'])) {            
            $filtros['data_inicio'] = '01/'.date('/m/Y');            
            $filtros['data_fim']    = date('d/m/Y');
        }

        $this->data['AnexoDigitalizacao'] = $filtros;

        $tipos_digitalizacao = $this->TipoDigitalizacao->find('list', array('conditions' => array('ativo' => 1),'fields' => 'descricao','order' => 'descricao'));

        $tipos_periodo = array('I' => 'Inclusão', 'V' => 'Validade');
        
        $this->set(compact('tipos_digitalizacao', 'tipos_periodo'));
        $this->carrega_combos_grupo_economico('AnexoDigitalizacao');
    }

    function lista_digitalizacao_terceiros(){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoDigitalizacao->name);

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $conditions = $this->AnexoDigitalizacao->converteFiltroEmCondition($filtros);

        $fields = array(
            'AnexoDigitalizacao.codigo',
            'TipoDigitalizacao.descricao',
            'AnexoDigitalizacao.nome',
            'UsuarioInclusao.nome',
            'AnexoDigitalizacao.data_inclusao',
            'AnexoDigitalizacao.caminho_arquivo'
        );

        $joins = array(
            array(
                'table' => 'tipo_digitalizacao',
                'alias' => 'TipoDigitalizacao',
                'type' => 'INNER',
                'conditions' => 'TipoDigitalizacao.codigo = AnexoDigitalizacao.codigo_tipo_digitalizacao',
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioInclusao.codigo = AnexoDigitalizacao.codigo_usuario_inclusao',
            )
        );
        
        $order = 'AnexoDigitalizacao.codigo';

        $this->paginate['AnexoDigitalizacao'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'order' => $order
            );

        $anexo_digitalizacao = $this->paginate('AnexoDigitalizacao');

        $this->set(compact('anexo_digitalizacao')); 
    }

    function incluir_digitalizacao(){
        $this->pageTitle = 'Incluir Digitalização';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoDigitalizacao->name);
        
        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data['AnexoDigitalizacao'] = $filtros;

        if($this->RequestHandler->isPost()) {

            $this->data['AnexoDigitalizacao']['codigo_cliente_matriz'] = $this->data['AnexoDigitalizacao']['codigo_cliente']; 

            unset($this->data['AnexoDigitalizacao']['codigo_cliente']);
            unset($this->data['Last']['codigo_cliente']);

            $post_params = isset($this->data['AnexoDigitalizacao']['caminho_arquivo']) && !empty($this->data['AnexoDigitalizacao']['caminho_arquivo']) ? $this->data['AnexoDigitalizacao']['caminho_arquivo'] : null ;

            if(empty($post_params)){
                $this->BSession->setFlash('save_error');
                return;
            }

            $this->Upload->setOption('field_name', 'caminho_arquivo');            
            $this->Upload->setOption('size_max', 5242880);
            $this->Upload->setOption('size_max_message', sprintf('Tamanho máximo de 5 Megabytes foi excedido!'));
            $this->Upload->setOption('accept_extensions', array('jpg','png','jpeg','pdf')); 
            $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! É aceito extensões pdf, jpg, jpeg ou png. Por favor tente novamente.');

            $retorno = $this->Upload->fileServer($this->data['AnexoDigitalizacao']);
           
            // se ocorreu algum erro de comunicação com o fileserver
            if (isset($retorno['error']) && !empty($retorno['error']) ){
                $chave = key($retorno['error']);                
                $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
            }
            else {

                $nome_arquivo = $this->data['AnexoDigitalizacao']['caminho_arquivo']['name'];

                unset($this->data['AnexoDigitalizacao']['caminho_arquivo']);

                $this->data['AnexoDigitalizacao']['caminho_arquivo'] = $retorno['data'][$nome_arquivo]['path'];

                if ($this->AnexoDigitalizacao->incluir($this->data)) {                
                    $this->BSession->setFlash('save_success');                
                    $this->redirect(array('controller' => 'tipo_digitalizacao', 'action' => 'operacao_digitalizacao_terceiros'));
                } 
                else {
                    $this->BSession->setFlash('save_error');
                    $this->redirect(array('controller' => 'tipo_digitalizacao', 'action' => 'incluir_digitalizacao'));
                }
            }           

            $this->redirect(array('controller' => 'tipo_digitalizacao', 'action' => 'incluir_digitalizacao'));
        }

        $tipos_digitalizacao = $this->TipoDigitalizacao->find('list', array('conditions' => array('ativo' => 1),'fields' => 'descricao','order' => 'descricao'));
        
        $this->set(compact('tipos_digitalizacao'));
        $this->carrega_combos_grupo_economico('AnexoDigitalizacao');
    }


    public function excluir($codigo) {

        if ($this->AnexoDigitalizacao->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
        } else {
            $this->BSession->setFlash('save_error');
        }
        $this->redirect(array('controller' =>'tipo_digitalizacao', 'action' => 'operacao_digitalizacao_terceiros'));
    }

    public function carrega_combos_grupo_economico($model) {
        $unidades = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        }

        $this->set(compact('unidades'));
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

     function consulta_digitalizacao_terceiros(){

        $this->pageTitle = 'Digitalização';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoDigitalizacao->name);

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        if(empty($filtros['data_inicio'])) {            
            $filtros['data_inicio'] = '01/'.date('/m/Y');            
            $filtros['data_fim']    = date('d/m/Y');
        }

        $this->data['AnexoDigitalizacao'] = $filtros;

        $tipos_digitalizacao = $this->TipoDigitalizacao->find('list', array('conditions' => array('ativo' => 1),'fields' => 'descricao','order' => 'descricao'));

        $tipos_periodo = array('I' => 'Inclusão', 'V' => 'Validade');
        
        $this->set(compact('tipos_digitalizacao', 'tipos_periodo'));
        $this->carrega_combos_grupo_economico('AnexoDigitalizacao');
    }

    function lista_digitalizacao_consulta_terceiros(){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoDigitalizacao->name);

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if(!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }
        
        $conditions = $this->AnexoDigitalizacao->converteFiltroEmConditionTerceiros($filtros);

        $fields = array(
            'AnexoDigitalizacao.codigo',
            'TipoDigitalizacao.descricao',
            'AnexoDigitalizacao.nome',
            'UsuarioInclusao.nome',
            'AnexoDigitalizacao.data_inclusao',
            'AnexoDigitalizacao.caminho_arquivo'
        );

        $joins = array(
            array(
                'table' => 'tipo_digitalizacao',
                'alias' => 'TipoDigitalizacao',
                'type' => 'INNER',
                'conditions' => 'TipoDigitalizacao.codigo = AnexoDigitalizacao.codigo_tipo_digitalizacao',
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'UsuarioInclusao.codigo = AnexoDigitalizacao.codigo_usuario_inclusao',
            )
        );
        
        $order = 'AnexoDigitalizacao.codigo';

        $this->paginate['AnexoDigitalizacao'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'order' => $order
            );

        $anexo_digitalizacao = $this->paginate('AnexoDigitalizacao');

        $this->set(compact('anexo_digitalizacao')); 
    }

    public function excluir_digitalizacao_terceiros($codigo) {

        if ($this->AnexoDigitalizacao->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
        } else {
            $this->BSession->setFlash('save_error');
        }
        $this->redirect(array('controller' =>'tipo_digitalizacao', 'action' => 'operacao_digitalizacao_terceiros'));
    }

}