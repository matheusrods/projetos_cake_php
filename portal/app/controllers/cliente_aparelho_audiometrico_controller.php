<?php
class ClienteAparelhoAudiometricoController extends AppController {
    
    public $name = 'ClienteAparelhoAudiometrico';
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts', 'Buonny');
    public $components = array('ExportCsv', 'Upload');
    
    var $uses = array(
        'AparelhoAudiometrico',
        'ApAudioFornecedor',
        'Fornecedor',
        'FornecedorUnidade',
        'AparelhoAudioResultado',
        'ClienteFornecedor',
        'Cliente'
        // 'ClienteApAudFornecedor',
        // 'ClienteApaAudioResultado'
    );

    /**
     * beforeFilter callback
     *
     * @return void
     */
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'listagem',
            'incluir',
            'editar',
            'get_prestadores',
            'atualiza_status',
            'get_prestadores_ap_cliente',
        ));
    }//FINAL FUNCTION beforeFilter
    
    function index() {
        $this->pageTitle = 'Aparelhos Audiométricos Clientes';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AparelhoAudiometrico->name);
        $this->cliente_aparelho_audiometrico_filtros($filtros);
    }

    function cliente_aparelho_audiometrico_filtros($thisData = null) {
        // configura no $this->data
        $this->data['AparelhoAudiometrico'] = $thisData;

        if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])){
            $cliente = $this->Cliente->find('first',array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
            $nome_cliente = $cliente['Cliente']['razao_social'];
            $this->set(compact('nome_cliente'));
        }       
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        //filtro da sessao para alimentar a query
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AparelhoAudiometrico->name);
        if(empty($filtros['codigo_cliente']) && !empty($_SESSION['Auth']['Usuario']['codigo_cliente'])){
            $filtros['codigo_cliente'] = $_SESSION['Auth']['Usuario']['codigo_cliente'];
        }
        //verifica se o codigo cliente para fazer a buscar
        if(!empty($filtros['codigo_cliente'])){
            //conditions    
            $conditions = $this->AparelhoAudiometrico->FiltroEmConditionTerceiro($filtros);
            //query
            $dados = $this->AparelhoAudiometrico->getClienteAparelhoAudiometrico($conditions);
            //monsta a query
            $this->paginate['AparelhoAudiometrico'] = array(
                'fields' => $dados['fields'],
                'joins' => $dados['joins'],
                'conditions' => $dados['conditions'],
                'limit' => 50            
            );
            // pr($this->AparelhoAudiometrico->find('sql', $this->paginate['AparelhoAudiometrico']));
            $lista = $this->paginate('AparelhoAudiometrico');        
            $this->set(compact('lista'));      
        }
    }
    
    function carrega_combo(){
        $this->Fornecedor->bindModel(array(
           'belongsTo' => array(
               'Fornecedor' => array(
                   'alias' => 'Fornecedor',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'Fornecedor.codigo = FornecedorUnidade.codigo_fornecedor_unidade'
               )
           )
        ));

        $unidades = $this->Fornecedor->find('list',array(
                'conditions' => array('Fornecedor.ativo' => 2),
                'order' => 'Fornecedor.nome'
            )
        );

        $this->set(compact('unidades'));
    }
   
    function incluir($codigo_cliente) {
        $this->pageTitle = 'Incluir Aparelhos Audiometricos';
        $this->carrega_combo();

        if($this->RequestHandler->isPost()) {
            try{
                $this->AparelhoAudiometrico->query('begin transaction');            

                $this->data['AparelhoAudiometrico']['descricao'] = strtoupper($this->data['AparelhoAudiometrico']['descricao']);
                $this->data['AparelhoAudiometrico']['fabricante'] = strtoupper($this->data['AparelhoAudiometrico']['fabricante']);
                $this->data['AparelhoAudiometrico']['empresa_afericao'] = strtoupper($this->data['AparelhoAudiometrico']['empresa_afericao']);

                $campos = $this->data['AparelhoAudiometrico']['to'];

                if(empty($campos)) {
                    $this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
                    $this->redirect(array('action' => 'incluir', $codigo_cliente));
                }

                $this->data['ApAudioFornecedor']['codigo_fornecedor'] = $this->data['AparelhoAudiometrico']['to'];
                unset($this->data['AparelhoAudiometrico']['to']);
                unset($this->data['AparelhoAudiometrico']['from']);
                         
                if ($this->AparelhoAudiometrico->incluir($this->data)) {
                    $codigo_aparelho_audiometrico = $this->AparelhoAudiometrico->id;
                    $this->data['AparelhoAudioResultado']['codigo_aparelho_audiometrico'] = $codigo_aparelho_audiometrico;
                    $this->data['ApAudioFornecedor']['codigo_aparelho_audiometrico'] = $codigo_aparelho_audiometrico;

                    if ($this->AparelhoAudioResultado->incluir($this->data)){
                        $this->BSession->setFlash('save_success');               
                    } else {
                        $this->BSession->setFlash('save_error');              
                    }

                    $dados_incluir = array();

                    foreach ($this->data['ApAudioFornecedor']['codigo_fornecedor'] as $dados_fornecedor) {
                        # code...
                        $dados_incluir['codigo_aparelho_audiometrico'] = $codigo_aparelho_audiometrico;
                        $dados_incluir['codigo_fornecedor'] = $dados_fornecedor;
                        //incluir com base no codigo cliente aparelho audiometrico todos os fornecedores 
                        if(!$this->ApAudioFornecedor->incluir($dados_incluir)){
                            $this->BSession->setFlash(array('alert alert-error', 'Erro ao incluir Prestador.'));
                            $this->redirect(array('action' => 'incluir', $codigo_cliente));
                        }
                    }
                }
                else{
                    $this->BSession->setFlash('save_error');
                }          
                $this->AparelhoAudiometrico->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cliente_aparelho_audiometrico'));
            } catch(Exception $e) {
                $this->AparelhoAudiometrico->rollback();
                return false;
            }
        }
        $this->set(compact('codigo_cliente'));
    }
    
    function editar($codigo, $codigo_cliente, $codigo_apaudi_cliente) {        
        $this->pageTitle = 'Editar Aparelhos Audiometricos'; 
        $this->carrega_combo();

        if($this->RequestHandler->isPost()) {

            try{
                $this->AparelhoAudiometrico->query('begin transaction');

                $this->data['AparelhoAudiometrico']['codigo'] = $codigo;
                $this->data['AparelhoAudiometrico']['descricao'] = strtoupper($this->data['AparelhoAudiometrico']['descricao']);
                $this->data['AparelhoAudiometrico']['fabricante'] = strtoupper($this->data['AparelhoAudiometrico']['fabricante']);
                $this->data['AparelhoAudiometrico']['empresa_afericao'] = strtoupper($this->data['AparelhoAudiometrico']['empresa_afericao']);

                $campos = $this->data['AparelhoAudiometrico']['to'];

                if(empty($campos)) {
                    $this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
                    $this->redirect(array('action' => 'editar', $codigo,$codigo_cliente,$codigo_apaudi_cliente));
                }

                if (count($campos) > 1) {
                    $this->BSession->setFlash(array('alert alert-error', 'Só pode editar 1 Prestador.'));
                    $this->redirect(Router::url($this->referer(), true));
                }   

                $this->data['ApAudioFornecedor']['codigo_fornecedor'] = $this->data['AparelhoAudiometrico']['to'][0];
                unset($this->data['AparelhoAudiometrico']['to']);
                unset($this->data['AparelhoAudiometrico']['from']);

                if ($this->AparelhoAudiometrico->atualizar($this->data)) {

                    // $codigo_aparelho_audiometrico = $this->AparelhoAudiometrico->id;
                    $this->data['AparelhoAudioResultado']['codigo_aparelho_audiometrico'] = $codigo;

                    $verifica_fornecedor = $this->ApAudioFornecedor->find('first',array(
                        'conditions' => array('ApAudioFornecedor.codigo' => $codigo_apaudi_cliente)));

                    if($verifica_fornecedor){
                        $verifica_fornecedor['ApAudioFornecedor']['codigo_fornecedor'] = $this->data['ApAudioFornecedor']['codigo_fornecedor'];
                        // debug($verifica_fornecedor);exit;
                        //atualiza e poem o codigo usuario que inativou
                        if(!$this->ApAudioFornecedor->atualizar($verifica_fornecedor)){
                            $this->BSession->setFlash(array('alert alert-error', 'Erro ao atualizar Prestador para este aparelho.'));
                            $this->redirect(Router::url($this->referer(), true));
                        }
                    }

                    // debug($verifica_fornecedor);

                    $verifica_resultados = $this->AparelhoAudioResultado->find('first',array(
                        'conditions' => array('AparelhoAudioResultado.codigo_aparelho_audiometrico' => $codigo)));

                    // debug($this->data);exit;

                    if(empty($verifica_resultados)){
                        if ($this->AparelhoAudioResultado->incluir($this->data)) 
                            $this->BSession->setFlash('save_success');               
                        else
                            $this->BSession->setFlash('save_error');
                    }
                    else{
                        if($this->AparelhoAudioResultado->atualizar($this->data))
                            $this->BSession->setFlash('save_success');
                        else
                            $this->BSession->setFlash('save_error');
                    }

                }
                else{
                    $this->BSession->setFlash('save_error');
                }

                // exit;               
                $this->AparelhoAudiometrico->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'cliente_aparelho_audiometrico'));
            } catch(Exception $e) {
                $this->AparelhoAudiometrico->rollback();
                return false;
            }           
        }

        if (isset($codigo)) { 
        $this->AparelhoAudiometrico->bindModel(array(
           'belongsTo' => array(
               'AparelhoAudioResultado' => array(
                   'alias' => 'AparelhoAudioResultado',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'AparelhoAudiometrico.codigo = AparelhoAudioResultado.codigo_aparelho_audiometrico'
               )
           )
        ));      

        $this->data = $this->AparelhoAudiometrico->find('first',array(
            'conditions' => array('AparelhoAudiometrico.codigo' => $codigo)));
        }
        $this->set(compact('codigo', 'codigo_cliente', 'codigo_apaudi_cliente'));
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        //busca na sessao o codigo do usuario logado
        $codigo_usuario = $_SESSION['Auth']['Usuario']['codigo'];
        //verifica se existe codigo_usuario
        if(isset($codigo_usuario) && !empty($codigo_usuario)){
            $buscar_dados = $this->AparelhoAudiometrico->find('first',array('conditions' => array('codigo' => $codigo)));
            //se existir ele verifica e monsta o array para atualizar
            if($status == 0){
                # code...
                if($buscar_dados){
                    $buscar_dados['AparelhoAudiometrico']['codigo_usuario_inativacao'] = null;
                    //atualiza e poem o codigo usuario que inativou
                    $this->AparelhoAudiometrico->atualizar($buscar_dados);
                }
            } elseif ($status == 1) {
                # code...
                if($buscar_dados){
                    $buscar_dados['AparelhoAudiometrico']['codigo_usuario_inativacao'] = $codigo_usuario;
                    //atualiza e poem o codigo usuario que inativou
                    $this->AparelhoAudiometrico->atualizar($buscar_dados);
                }
            }
        }
        
        $this->data['AparelhoAudiometrico']['codigo'] = $codigo;
        $this->data['AparelhoAudiometrico']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->AparelhoAudiometrico->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
    }
    public function get_prestadores($codigo_cliente) {
        //para nao solicitar um ctp
        $this->autoRender = false;
        //a query
        $query = $this->ClienteFornecedor->buscar_prestadores_por_cliente($codigo_cliente);
        //monta a query com o from e faz a buscar           
        $dados_prestadores = $this->ClienteFornecedor->find('all', array(
            'conditions' => $query['conditions'], 
            'fields' => $query['fields'], 
            'joins' => $query['joins'],
            'order' => $query['order']
        ));
        $json = false;
        if(!$dados_prestadores){
            return json_encode($json);
        } else {
            return json_encode($dados_prestadores);
        }
    }

    public function get_prestadores_ap_cliente($codigo) {
        //para nao solicitar um ctp
        $this->autoRender = false;
        //a query
        $query = $this->ApAudioFornecedor->get_prestador_aparelho_audiometrico($codigo);
        //monta a query com o from e faz a buscar           
        $dados_prestadores = $this->ApAudioFornecedor->find('first', array(
            'conditions' => $query['conditions'], 
            'fields' => $query['fields'], 
            'joins' => $query['joins']
        ));
        $json = false;
        if(!$dados_prestadores){
            return json_encode($json);
        } else {
            return json_encode($dados_prestadores);
        }
    }
}