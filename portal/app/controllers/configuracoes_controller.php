<?php
class ConfiguracoesController extends AppController {
    public $name = 'Configuracoes';
    var $uses = array(
        'Configuracao',
        'ClienteConfiguracao'
    );

    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    function index() {
        $this->pageTitle = 'Configurações';
        
        if($this->RequestHandler->isPost()) {
            if($this->data){
                $validationErrors = array();

                try{
                    $this->Configuracao->query('begin transaction');
                    
                    foreach ($this->data['Configuracao'] as $key => $value) {
                        $dados = array('Configuracao' => $value);

                        if(!$this->Configuracao->atualizar($dados)){
                            $validationErrors[$key] = $this->Configuracao->invalidFields();
                        }
                        
                    }

                    if($validationErrors)
                        throw new Exception();
                        
                    $this->Configuracao->commit();
                    $this->BSession->setFlash('save_success');
                } 
                catch( Exception $ex ){
                    $this->Configuracao->rollback();
                    $this->Configuracao->validationErrors = $validationErrors;
                    $this->BSession->setFlash('save_error');
                }           
        

            
                // if($this->Configuracao->atualizarTodos($this->data)){
                //     $this->BSession->setFlash('save_success');
                // } else {
                //     $this->BSession->setFlash('save_error');
                // }
            }

        }
        
        $dados = $this->Configuracao->find('all',array('order' => array('codigo')));        
        $this->set(compact('dados'));


    }

    /**
     * [index_param_cargos description]
     * 
     * metodo index para parametrização de cargos
     * é o metodo que define se na api irá usar a finalização do cargo automático ou não
     * 
     * @return [type] [description]
     */
    public function index_param_cargos()
    {
        $this->pageTitle = 'Parametrização de Cargos';
    }//fim index_param_cargos

    /**
     * [listagem_param_cargos description]
     * 
     * metodo para listar as empresas que irá utilziar para finalização do cargo automatico
     * 
     * @return [type] [description]
     */
    public function listagem_param_cargos()
    {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteConfiguracao->name);
        $conditions = $this->ClienteConfiguracao->converteFiltroEmCondition($filtros);
        $fields = array('ClienteConfiguracao.codigo', 'ClienteConfiguracao.codigo_cliente_matricula','Cliente.razao_social','Cliente.codigo_documento','ClienteConfiguracao.finaliza_setor_cargo');
        $order = 'ClienteConfiguracao.codigo_cliente_matricula';

        $this->ClienteConfiguracao->bindModel( 
            array(
                'belongsTo' => array(
                    'Cliente' => array(
                        'foreignKey' => false, 
                        'conditions' => array('Cliente.codigo = ClienteConfiguracao.codigo_cliente_matricula')
                        ),
                    )
                ), false
            );


        $this->paginate['ClienteConfiguracao'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
            );
        
        $configuracao = $this->paginate('ClienteConfiguracao');
        $this->set(compact('configuracao'));

    }//fim listagem_param_cargos

    /**
     * [atualiza_status_param_cargos description]
     * 
     * metodo para ativar ou desativar a configuracao de finalizar setor cargo automatico por empresa
     * 
     * @param  [type] $codigo_cleinte [description]
     * @param  [type] $status         [description]
     * @return [type]                 [description]
     */
    public function atualiza_status_param_cargos($codigo, $status)
    {
        $this->layout = 'ajax';
        
        //seta os campos apra atualizacao
        $this->data['ClienteConfiguracao']['codigo'] = $codigo;
        $this->data['ClienteConfiguracao']['finaliza_setor_cargo'] = ($status == 0) ? 1 : 0;

        //parametro de retorno para a mensagem na tela
        $retorno = 0;
        //verifica se foi feita a atualizacao
        if ($this->ClienteConfiguracao->atualizar($this->data, false)) {               
            $retorno = 1;
        }//fim atualizar

        //printa o retorno
        // 0 -> ERRO | 1 -> SUCESSO
        print $retorno;

        $this->render(false,false);

    }//fim atualiza_status_param_cargos

    /**
     * [incluir_param_cargos description]
     * @return [type] [description]
     */
    public function incluir_param_cargos() 
    {
        $this->pageTitle = 'Incluir Parâmetros Cargos';

        if($this->RequestHandler->isPost()) {
            
            //seta o campo para entrar como ativo
            $this->data['ClienteConfiguracao']['finaliza_setor_cargo'] = 1;

            if ($this->ClienteConfiguracao->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_param_cargos', 'controller' => 'Configuracoes'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }//fim if post

    }//fim incluir_param_cargos

}