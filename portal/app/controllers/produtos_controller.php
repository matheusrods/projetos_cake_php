<?php
class ProdutosController extends AppController {
    public $name = 'Produtos';
    var $uses = array('Produto','Servico', 'ProdutoServico');

    function precos_faturados() {
        $this->pageTitle = 'Preços Faturados por Produto';
        if (!empty($this->data)) {
            if ($this->_precos_faturados_validate()) {
                $faturamentos = $this->Produto->precosFaturados($this->data);
                $this->set(compact('faturamentos'));
            }
        } else {
            $this->data['Produto']['data_inicial'] = Date('01/m/Y');
            $this->data['Produto']['data_final'] = Date('d/m/Y');
        }
        $this->set('produtos', $this->Produto->find('list', array('conditions' => array('codigo' => array(1, 2, 30)))));
    }

    private function _precos_faturados_validate() {
        if (empty($this->data['Produto']['codigo'])) 
            $this->Produto->invalidate('codigo', 'Informe o Produto');
        if (empty($this->data['Produto']['data_inicial']))
            $this->Produto->invalidate('data_inicial', 'Informe a Data');
        if (empty($this->data['Produto']['data_final']))
            $this->Produto->invalidate('data_final', 'Informe a Data');
        return (count($this->Produto->invalidFields()) == 0);
    }


    function clientes_por_produto_e_preco_faturado() {
        $this->pageTitle = 'Clientes por Produto e Preço Unitário Faturado';
        $this->loadModel('ProdutoServico');
        if (!empty($this->data)) {
            if ($this->_validate_clientes_por_produto_e_preco_faturado()) {
                $clientes = $this->Produto->clientesPorProdutoEPrecoFaturado($this->data);
                $produto = $this->ProdutoServico->Produto->carregar($this->data['Produto']['codigo']);
                $servico = $this->ProdutoServico->Servico->carregar($this->data['Produto']['codigo_servico']);
            }
            $codigo_produto = $this->data['Produto']['codigo'];
        } else {
            $codigo_produto = null;
            $this->data['Produto']['data_inicial'] = Date('01/m/Y');
            $this->data['Produto']['data_final'] = Date('d/m/Y');
        }
        $produtos = $this->Produto->find('list');
        $servicos = $this->ProdutoServico->servicosPorProduto($codigo_produto);
        $this->set(compact('produtos', 'servicos', 'clientes', 'produto', 'servico'));
    }

    private function _validate_clientes_por_produto_e_preco_faturado() {
        if (empty($this->data['Produto']['data_inicial']))
            $this->Produto->invalidate('data_inicial', 'Dt.Inicial não informada');
        if (empty($this->data['Produto']['data_final']))
            $this->Produto->invalidate('data_final', 'Dt.Final não informada');
        if (empty($this->data['Produto']['codigo']))
            $this->Produto->invalidate('codigo', 'Produto não informado');
        if (empty($this->data['Produto']['codigo_servico']))
            $this->Produto->invalidate('codigo_servico', 'Servico não informado');
        return (count($this->Produto->invalidFields()) == 0);
    }

    function utilizacao() {
        $this->pageTitle = "Valor das Utilizações no Período";
        $utilizacoes = array();
        if (!empty($this->data)) {
            $utilizacoes = $this->Produto->utilizacoes($this->data['Produto']);
        } else {
            $this->data['Produto'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
        }
        $this->set(compact('utilizacoes'));
    }

    function tem_controle_de_volume($codigo_produto) {
        $produto = $this->Produto->carregar($codigo_produto);
        echo json_encode($produto['Produto']['controla_volume'] != 0);
        exit;
    }

    function index() {
        $this->Filtros->limpa_sessao($this->Produto->name);
        $this->data['Produto'] = $this->Filtros->controla_sessao($this->data, $this->Produto->name);
    }

    function listagem() {
        $this->layout = 'ajax';
    	$filtros = $this->Filtros->controla_sessao($this->data, $this->Produto->name);
    	$conditions = $this->Produto->converteFiltroEmCondition($filtros);
    	$this->paginate['Produto'] = array(
    			'conditions' => $conditions,
    			'limit' => 50,
    			'order' => 'Produto.descricao',
    	);
    	
    	$produtos = $this->paginate('Produto');
    	
    	$this->set(compact('produtos'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Produto'; 

        $edit_mode = false;

        if($this->RequestHandler->isPost()) {

            $resultado   = $this->Produto->incluirProduto($this->data);

            if ($resultado) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
                $produto = null;
                $this->set(compact('produto'));
            }
        } else {
            $produto = null;
            $this->set(compact('produto'));
            $this->data = array('Produto' => array(
                'codigo' => null,
                'descricao' => null,
                'codigo_naveg' => null,
                'codigo_ccusto_naveg' => null,
                'codigo_formula_naveg' => null,
                'codigo_formula_naveg_sp' => null
            ));
        }

        $this->set(compact('edit_mode'));
    }

    function incluir_servicos($codigo_produto) {
        $this->pageTitle = 'Incluir Serviços';
        $produto           = $this->Produto->carregar($codigo_produto);
        $produtos_servicos = $this->ProdutoServico->find('all', array('conditions' => array('codigo_produto' => $codigo_produto)));
        
        $servicos          = $this->Servico->find('list',array('order' => 'descricao ASC'));
        /*foreach($produtos_servicos as $key => $produto_servico) {
            $servico = $this->Servico->find('first',array('conditions' => array('codigo' => $produto_servico['ProdutoServico']['codigo'])));
            $produtos_servicos[$key]['ProdutoServico']['descricao'] = $servico['Servico']['descricao'];
        }*/
        
        if($this->RequestHandler->isPost()) {
            $resultado = false;

            if(isset($this->data['Produto']['servicos']) && !empty($this->data['Produto']['servicos'])) {

                if (!$this->ProdutoServico->find('first',array('conditions' => array('codigo_produto' => $codigo_produto, 'codigo_servico' => $this->data['Produto']['servicos'])))) {

                    $produto_servico = array(
                        'ProdutoServico' => array(
                            'codigo_produto' => $codigo_produto,
                            'codigo_servico' => $this->data['Produto']['servicos'],
                        )
                    );
                    $resultado = $this->ProdutoServico->incluir($produto_servico);
                }
            }

            if(isset($this->data['Produto']['servico_novo']) && !empty($this->data['Produto']['servico_novo']))  {

                $servico_existente = $this->Servico->find('first',array('conditions' => array('descricao' => $this->data['Produto']['servico_novo'])));

                if($servico_existente) {

                    if (!$this->ProdutoServico->find('first',array('conditions' => array('codigo_produto' => $codigo_produto, 'codigo_servico' => $servico_existente['Servico']['codigo'])))) {
                        $produto_servico = array(
                            'ProdutoServico' => array(
                                'codigo_produto' => $codigo_produto,
                                'codigo_servico' => $servico_existente['Servico']['codigo'],
                            )
                        );
                        $resultado = $this->ProdutoServico->incluir($produto_servico);
                    }
                } else {

                    $servico = array(
                        'Servico' => array(
                            'descricao' => strtoupper($this->data['Produto']['servico_novo']),
                            'ativo'     => true
                        )
                    );
                    
                    if(!$this->Servico->incluir($servico)) {
                        $this->Servico->invalidate('Produto.servico_novo','Favor escolher outro nome');
                    }

                    $produto_servico = array(
                        'ProdutoServico' => array(
                            'codigo_produto' => $codigo_produto,
                            'codigo_servico' => $this->Servico->id,
                        )
                    );
                    $resultado = $this->ProdutoServico->incluir($produto_servico);
                }
            }

            if ($resultado) {
                $this->BSession->setFlash('save_success');
                $this->redirect($this->referer());
            } else {
                $this->BSession->setFlash('save_error');
               
            }
        } 
        
        $this->set(compact('servicos','produto','produtos_servicos'));
    }

    function editar($codigo_produto) {
        $this->pageTitle = 'Editar Produto'; 

        $edit_mode = true;

        if (!empty($this->data)) {
            if ($this->Produto->atualizarProduto($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'produtos', 'action' => 'index'));
            } else {
                $produto = null;
                $this->BSession->setFlash('save_error');
                $this->set(compact('produto'));
            }
        } else {
            $this->data = $this->Produto->carregar($codigo_produto);
            $this->data['Produto']['percentual_irrf'] = number_format($this->data['Produto']['percentual_irrf'], 2, '.', '');
            $this->data['Produto']['percentual_irrf_acima'] = number_format($this->data['Produto']['percentual_irrf_acima'], 2, '.', '');

            //pega o servico
            $join = array(
                array(
                    'table' => "RHHealth.dbo.produto_servico",
                    'alias' => 'ProdutoServico',
                    'type' => 'INNER',
                    'conditions' => array('Servico.codigo = ProdutoServico.codigo_servico AND ProdutoServico.codigo_produto = '. $this->data['Produto']['codigo'] ) 
                ),
            );
            //busca os servicos
            $servico = $this->Servico->find('first', array('joins' => $join));
            //variavel auxiliar
            $tipo_servico = "";
            $codigo_servico = "";
            //verifica se tem o servico relacionado
            if(!empty($servico)) {
                //atribui o valor do servico
                $tipo_servico = $servico['Servico']['tipo_servico'];
                $codigo_servico = $servico['Servico']['codigo'];
            }//fim verificacao servico

            //seta o tipo de servico
            $this->data['Produto']['tipo_servico'] = $tipo_servico;
            $this->data['Produto']['codigo_servico'] = $codigo_servico;
        }

        $produtos_sem_mensalidade = array(
                Produto::TELECONSULT_STANDARD,
                Produto::TELECONSULT_PLUS,
                Produto::BUONNYSAT,
                Produto::SCORECARD
            );

        $this->set(compact('produtos_sem_mensalidade','edit_mode'));
    }

    function excluir($codigo_produto) {

        $produto = $this->Produto->carregar($codigo_produto);

        if($produto['Produto']['controla_volume']) { //NAO DEIXAR EXCLUIR QUANDO TEM CONTROLE DE VOLUME - TLC, BSAT e BCREDIT

            $this->BSession->setFlash('delete_error');

        } else {
            $produto_servico = $this->ProdutoServico->find('all',array('conditions' => array('codigo_produto' => $produto['Produto']['codigo'])));

            foreach ($produto_servico as $prod_serv) {
                $this->ProdutoServico->excluir($prod_serv['ProdutoServico']['codigo']);
            }

            if ($this->Produto->excluir($codigo_produto)) {
                $this->BSession->setFlash('delete_success');
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }

        $this->redirect(array('controller' => 'produtos', 'action' => 'index'));
    }

  	function editar_status_produtos($codigo, $status){
        $this->layout = 'ajax';
        if(!is_numeric($codigo)){
            print 0;
            exit;
        }
        $codigo = trim($codigo);
        $status= ($status == 0) ? $status = 1 : $status = 0;
        
        $this->data['Produto']['codigo'] = $codigo;        
        $this->data['Produto']['ativo'] = $status;

        if ($this->Produto->atualizar($this->data)) {            
            $this->render(false,false);
            print 1;
        } else {
            $this->render(false,false);
            print 0;
        }
        // 0 -> ERRO | 1 -> SUCESSO
    }

    function script_importacao() {
		$this->Produto->scriptImportaProdutosEServicos();
    }
}