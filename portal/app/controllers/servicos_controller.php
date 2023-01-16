<?php
class ServicosController extends AppController {
    public $name = 'Servicos';
    var $uses = array(
        'Servico',
        'Exame',
        'ProdutoServico'
    );

    function index() {
        $this->pageTitle = 'Serviços';
        $this->Filtros->limpa_sessao($this->Servico->name);
        $this->data['Servico'] = $this->Filtros->controla_sessao($this->data, $this->Servico->name);
    }

    function listagem() {
        $this->layout = 'ajax'; 
        $filtros        = $this->Filtros->controla_sessao($this->data, $this->Servico->name);
        $conditions     = $this->Servico->converteFiltroEmCondition($filtros);

       // $conditions['Exame.codigo_empresa']   = $this->Session->read('Auth.Usuario.codigo_empresa');
        
        $joins          = array(
                            array(
                                'table' => 'exames',
                                'alias' => 'Exame',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Exame.codigo_servico = Servico.codigo',
                                    'Exame.codigo_empresa = Servico.codigo_empresa'
                                )
                            )
        );

        $this->paginate['Servico'] = array(
            'fields' => array (
                'Servico.codigo', 
                'Exame.codigo', 
                'Servico.descricao', 
                'Servico.codigo_externo', 
                'Servico.ativo', 
                'Servico.tipo_servico'
            ),
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'Servico.descricao',
        );
        
        $servicos = $this->paginate('Servico');
        $this->set(compact('servicos'));
    }

    public function editar_status_servicos($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['Servico']['codigo'] = $codigo;
        $this->data['Servico']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Servico->atualizar($this->data, false)) {  
            $servicos = $this->Servico->find('first', array('conditions' => array('codigo' => $codigo))) ;          
            
            if($servicos['Servico']['tipo_servico'] == 'E'){
                //CASO INATIVAR O SERVICO, TAMBÉM É INATIVADO O EXAME.
                if($this->Servico->atualizar_status($codigo,null,$this->data['Servico']['ativo'])){
                    print 1;
                }
                else{
                    print 0;
                }
            }
            else{
                //TIPO DIFERENTE DE EXAMES COMPLEMENTARES                   
                print 1;
            }
        }else{
             print 2; //ERRO AO ATUALIZAR O SERVICO
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }//FINAL FUNCTION editar_status_servicos

    public function incluir() {
        $this->pageTitle = 'Incluir Serviço'; 

        if($this->RequestHandler->isPost()) {
            $this->data ['Servico'] ['descricao'] = strtoupper ( $this->data ['Servico'] ['descricao'] );
            if ($this->Servico->incluir($this->data)) {

                if($this->data['Servico']['tipo_servico'] == 'E'){
                    $dados_exames = array(
                        'Exame' => array(
                            'descricao' => $this->data['Servico']['descricao'],
                            'codigo_servico' => $this->Servico->id,
                            'ativo' => 1
                        )
                    );

                    if($this->Exame->incluir($dados_exames)){
                        $this->BSession->setFlash('save_success');
                        $this->redirect(array('action' => 'index'));
                    }else{
                        $this->BSession->setFlash('save_error');
                    }
                }else{
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                }
            }else{
                $this->BSession->setFlash('save_error');
            }
        } //FINAL IF RequestHandler->isPost()

        $this->loadModel('ClassificacaoServico');
        $classificacao = $this->ClassificacaoServico->find('list');
        $this->set(compact('classificacao'));
    }//FINAL FUNCTION incluir

    public function editar($codigo) {
        $this->pageTitle = 'Editar Serviço'; 
        
        if($this->RequestHandler->isPost()) {

            $this->data ['Servico'] ['descricao'] = strtoupper ( $this->data ['Servico'] ['descricao'] );
            if ($this->Servico->atualizar($this->data)) {

                if($this->data['Servico']['tipo_servico'] == 'E'){
                    //CASO INATIVAR O SERVICO, TAMBÉM É INATIVADO O EXAME.
                    if($this->Servico->atualizar_status($codigo,null,$this->data['Servico']['ativo'])){
                        $this->BSession->setFlash('save_success');
                        $this->redirect(array('action' => 'index'));
                    }else{
                        $this->BSession->setFlash('save_error');
                    }
                }else{
                    //PRODUTO DIFERENTE DE EXAMES COMPLEMENTARES
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                }
            }else{
                $this->BSession->setFlash('save_error');
            }
        }else{
            $this->data = $this->Servico->carregar($codigo);
        }

        $this->loadModel('ClassificacaoServico');
        $classificacao = $this->ClassificacaoServico->find('list');
        $this->set(compact('classificacao'));
    }//FINAL FUNCTION editar


    public function excluir($codigo) {
        if ($this->Servico->delete($codigo)) {

            $this->BSession->setFlash('delete_success');
        } 
        else {
            $this->BSession->setFlash('delete_error');
        }

        $this->redirect(array('action' => 'index'));
    }//FINAL FUNCTION excluir

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('carrega_servicos_por_ajax');
    }//FINAL FUNCTION beforeFilter
    

    public function carrega_servicos_por_ajax()
    {
        $this->autoRender = false;
        $html = false;
        if($this->RequestHandler->isPost()) {

            $servicos = $this->Servico->find('all', array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'listas_de_preco_produto_servico',
                        'alias' => 'ListaDePrecoProdutoServico',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ListaDePrecoProdutoServico.codigo_servico = Servico.codigo',
                            'ListaDePrecoProdutoServico.codigo_empresa = Servico.codigo_empresa'
                        )
                    ),
                    array(
                        'table' => 'listas_de_preco_produto',
                        'alias' => 'ListaDePrecoProduto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto',
                            'ListaDePrecoProduto.codigo_empresa = Servico.codigo_empresa'
                        )
                    ),
                    array(
                        'table' => 'listas_de_preco',
                        'alias' => 'ListaDePreco',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco',
                            'ListaDePreco.codigo_empresa = Servico.codigo_empresa'
                        )
                    ),
                    array(
                        'table' => 'produto',
                        'alias' => 'Produto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Produto.codigo = ListaDePrecoProduto.codigo_produto',
                            'Produto.codigo_empresa = Servico.codigo_empresa'
                        )
                    )
                ),
                'conditions' => array(
                    'ListaDePreco.codigo_fornecedor' => NULL,
                    'OR' => array(
                                'Servico.descricao LIKE' => '%'.$_POST['string'].'%',
                                'Servico.codigo_externo LIKE' => '%'.$_POST['string'].'%'
                            )
                ),
                'fields' => array(
                    'Servico.codigo',
                    'Servico.descricao',
                    'ListaDePrecoProdutoServico.valor_venda',
                    'Produto.descricao',
                    'Produto.codigo',
                    'Servico.codigo_externo'
                ),
                'limit' => 10,
                'order' => 'Servico.descricao ASC'
            )
        );

       
            if(!empty($servicos)) {
                $html = '<table class="table">';
                foreach ($servicos as $key => $servico) {
                    $codigo_externo = trim($servico['Servico']['codigo_externo']);
                    $codigo = !empty($codigo_externo) ? ($codigo_externo . ' - ' ) : '';

                    $html .= '<tr class="js-click" data-codigo-produto="'.$servico['Produto']['codigo'].'" data-codigo="'.$servico['Servico']['codigo'].'" data-valor="'.$servico['ListaDePrecoProdutoServico']['valor_venda'].'">';
                    $html .= '<td>';
                    $html .= $servico['Servico']['codigo'];
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $codigo.$servico['Servico']['descricao'];
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }
        return json_encode($html);
    }//FINAL FUNCTION carrega_servicos_por_ajax
}