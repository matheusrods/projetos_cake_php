<?php

class ClientesProdutosServicosController extends AppController {

    public $name = 'ClientesProdutosServicos';
    public $layout = 'cliente';
    public $components = array('EmailAlteracaoCliente');
    public $uses = array('ClienteProduto', 'PServicoProfissionalTipo', 'ProfissionalTipo','Produto', 'ProdutoServico', 'Servico');

    
    public function lista_produtos($codigo_cliente) {
        $this->layout = 'ajax';
        $produtos = $this->ClienteProduto->produtosServicosProfissionaisPorCliente($codigo_cliente);
        $this->set(compact('produtos'));
    }    
    
    /**
     * Atualiza os dados de um ProdutoServicoProfissional
     * 
     * @return void
     */
    public function atualizar_profissional_tipo($codigo_cliente,$codigo_produto,$codigo_servico,$codigo_profissional_tipo,$codigo_cliente_produto_servico) {
        
        if (!empty($this->data)) {
            if ($codigo_profissional_tipo == 'todos') {
                //$produtos = $this->ClienteProduto->produtosServicosProfissionaisPorCliente($codigo_cliente);
                $result = $this->ClienteProdutoServico2->atualizarParaTodosProfissionaisTipo($codigo_cliente, $codigo_produto, $codigo_servico, $this->data);
            } else {
                
                $cliente_produto_servico = $this->ClienteProdutoServico2->getByCodigo($this->data['ClienteProdutoServico2']['codigo']);
                $cliente_produto = $this->ClienteProduto->getClienteProdutoByCodigo($cliente_produto_servico['ClienteProdutoServico2']['codigo_cliente_produto']);
                $result = $this->ClienteProdutoServico2->atualizar($this->data);
            }

            if ($result) {
                if ($this->ClienteProdutoServico2->alteracaoValores) {
                    $this->EmailAlteracaoCliente->informaAlteracaoCliente('alteracao_valores', $codigo_cliente, $this->ClienteProdutoServico2->alteracaoValores);
                }
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $produto = $this->Produto->getProdutoByCodigo($codigo_produto);
        $produto_nome = $produto['Produto']['descricao'];

        $servico = $this->Servico->getServicoByCodigo($codigo_servico);
        $servico_nome = $servico['Servico']['descricao'];

        $placeholder = $default_placeholder = array('valor' => '', 'validade' => '', 'tempo_pesquisa' => '', 'codigo_cliente_pagador' => '');

        if ($codigo_profissional_tipo != 'todos') {
            $profissional_tipo = $this->ProfissionalTipo->getProfissionalTipoByCodigo($codigo_profissional_tipo);
            $profissional_tipo_nome = $profissional_tipo['ProfissionalTipo']['descricao'];
            $cliente_produto_servico = $this->ClienteProdutoServico2->getByCodigo($codigo_cliente_produto_servico);        
            $data = $cliente_produto_servico['ClienteProdutoServico2'];
        } else {
            $profissional_tipo_nome = 'Todos';
            $cliente_produto_servico = $this->ClienteProdutoServico2->getByCodigo($codigo_cliente_produto_servico);
            $servicos_por_tipo_profissional = $this->ClienteProdutoServico2->listarPorCodigoProdutoEServico($cliente_produto_servico['ClienteProdutoServico2']['codigo_cliente_produto'], $servico['Servico']['codigo']);
            
            $valores_padrao = array();
            foreach ($servicos_por_tipo_profissional as $servico_tipo_profissional) {
                foreach ($servico_tipo_profissional['ClienteProdutoServico2'] as $key => $val) {
                    $valores_padrao[$key][] = $val;
                }
            }
            
            $result = array();
            foreach ($valores_padrao as $key => $val) {
                 $valores = (array) array_values(array_combine(array_values($valores_padrao[$key]), array_values($valores_padrao[$key])));
                 if (count($valores) == 1) {
                     $result[$key] = $valores[0];
                 } else {
                     $placeholder[$key] = 'valores_diferentes';
                 }
            }

            $data = $result;
            $placeholder = array_merge($default_placeholder, $placeholder);
        }
        
        if (empty($this->data)) {
            $this->data['ClienteProdutoServico2'] = $data;
        }
        
        $this->set(compact(
        	'codigo',
            'codigo_produto',
            'codigo_servico',
            'codigo_profissional_tipo',
            'codigo_cliente_produto_servico',
            'produto_nome',
            'servico_nome',
            'profissional_tipo_nome',
            'codigo_cliente',
        	'placeholder'
        ));
    }

    public function atualizar_servico_assinatura($codigo_cliente, $codigo_produto, $codigo_servico, $codigo) {
		$this->pageTitle = 'Atualizar Serviço';
		$this->loadModel("ClienteProdutoServico2");
        $this->loadModel("Servico");

        $cliente_produto = $this->ClienteProduto->find('first',array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => $codigo_produto)));
        if (!empty($this->data) && !empty($this->data['ClienteProdutoServico2']['codigo_cliente'])) {
            $this->data['ClienteProdutoServico2']['valor']          = ($this->data['ClienteProdutoServico2']['valor'] == null) ? 0 : ($this->data['ClienteProdutoServico2']['valor']);
            $dadosClienteProdutoServico2['ClienteProdutoServico2'] = $this->data['ClienteProdutoServico2'];

            $cliente_produto_servico = $this->ClienteProdutoServico2->getByCodigoClienteProdutoEServico($cliente_produto['ClienteProduto']['codigo'],$codigo_servico);
            
            if ($this->data['ClienteProdutoServico2']['tipo_premio_minimo'] == 1) {
                $dadosClienteProdutoServico2['ClienteProdutoServico2']['valor_premio_minimo'] = 0;
                $dadosClienteProdutoServico2['ClienteProdutoServico2']['qtd_premio_minimo']   = 0;

                $cliente_produto['ClienteProduto']['valor_premio_minimo'] = ($this->data['ClienteProdutoServico2']['valor_premio_minimo'] == null) ? 0 : ($this->data['ClienteProdutoServico2']['valor_premio_minimo']);
                $cliente_produto['ClienteProduto']['qtd_premio_minimo']   = ($this->data['ClienteProdutoServico2']['qtd_premio_minimo'] == null)   ? 0 : ($this->data['ClienteProdutoServico2']['qtd_premio_minimo']);

                unset($cliente_produto['Produto']);
                unset($cliente_produto['MotivoBloqueio']);
                unset($cliente_produto['ClienteProduto']['data_faturamento']);
                unset($cliente_produto['ClienteProduto']['data_inclusao']);
                unset($cliente_produto['ClienteProduto']['possui_contrato']);
                unset($cliente_produto['ClienteProduto']['codigo_usuario_inclusao']);
                unset($cliente_produto['ClienteProduto']['codigo_motivo_bloqueio']);
                unset($cliente_produto['ClienteProduto']['codigo_produto']);

                $this->ClienteProduto->atualizar($cliente_produto,true);

            }

            unset($this->data['ClienteProdutoServico2']['valor_premio_minimo']);
            unset($this->data['ClienteProdutoServico2']['qtd_premio_minimo']);
            unset($this->data['ClienteProdutoServico2']['codigo_cliente_produto']);
            unset($this->data['ClienteProdutoServico2']['tipo_premio_minimo']);

            unset($dadosClienteProdutoServico2['ClienteProdutoServico2']['tipo_premio_minimo']);

            $dadosClienteProdutoServico2['ClienteProdutoServico2']['valor_premio_minimo'] == null ? $dadosClienteProdutoServico2['ClienteProdutoServico2']['valor_premio_minimo'] = 0 : true;
            $dadosClienteProdutoServico2['ClienteProdutoServico2']['qtd_premio_minimo']   == null ? $dadosClienteProdutoServico2['ClienteProdutoServico2']['qtd_premio_minimo']   = 0 : true;
			if($this->ClienteProdutoServico2->atualizar($dadosClienteProdutoServico2)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'clientes_produtos', 'action' => 'assinatura'));
			} else {
            	$this->ClienteProdutoServico2->validationErrors;
                $this->BSession->setFlash('save_error');
			}
        }
        
        $produto_quantitativo = in_array($codigo_produto, $this->Produto->produtos_quantitativos());

        $produto             = $this->Produto->getProdutoByCodigo($codigo_produto);
        $produto_nome        = $produto['Produto']['descricao'];
        
        $servico 		= $this->Servico->getServicoByCodigo($codigo_servico);
        $servico_nome 	= $servico['Servico']['descricao'];

        $cliente_produto_servico2 = $this->ClienteProdutoServico2->find('first', array('conditions' => array('codigo' => $codigo)));

        if ($cliente_produto_servico2['ClienteProdutoServico2']['valor_premio_minimo'] == 0) {
			$this->data['ClienteProdutoServico2']['tipo_premio_minimo'] = 1;

			$valor_premio_minimo = $cliente_produto['ClienteProduto']['valor_premio_minimo'];
			$qtd_premio_minimo   = $cliente_produto['ClienteProduto']['qtd_premio_minimo'];
        } else {
            $this->data['ClienteProdutoServico2']['tipo_premio_minimo'] = 2;

            $valor_premio_minimo = $cliente_produto_servico2['ClienteProdutoServico2']['valor_premio_minimo'];
            $qtd_premio_minimo   = $cliente_produto_servico2['ClienteProdutoServico2']['qtd_premio_minimo'];
        }

        $valor_maximo           = $cliente_produto_servico2['ClienteProdutoServico2']['valor_maximo'];
        $quantidade             = $cliente_produto_servico2['ClienteProdutoServico2']['quantidade'];
        $valor                  = $cliente_produto_servico2['ClienteProdutoServico2']['valor'];
        $codigo_cliente_pagador = $cliente_produto_servico2['ClienteProdutoServico2']['codigo_cliente_pagador'];
        $codigo_cliente_produto = $cliente_produto_servico2['ClienteProdutoServico2']['codigo_cliente_produto'];
        $consulta_motorista = Servico::CONSULTA_MOTORISTA == $codigo_servico;
        $consulta_embarcador = $cliente_produto_servico2['ClienteProdutoServico2']['consulta_embarcador'] ? 1: 0;

        $this->set(compact(
        	'codigo',
            'codigo_produto',
            'codigo_cliente_produto',
            'codigo_servico',
            'codigo_cliente_produto_servico',
            'codigo_cliente_pagador',
            'valor_premio_minimo',
            'valor_maximo',
            'qtd_premio_minimo',
            'valor',
            'produto_nome',
            'servico_nome',
            'codigo_cliente',
            'consulta_motorista',
            'consulta_embarcador',
            'produto_quantitativo',
            'quantidade'
        ));

	}

	public function atualizar_servico() {
        $codigo_cliente = $this->data['Outros']['codigo_cliente'];
        
        if($this->RequestHandler->isPut()) {     
            $result = $this->ClienteProdutoServico2->atualizar($this->data);

            if ($result) {
                if ($this->ClienteProdutoServico2->alteracaoValores) {
                    $this->EmailAlteracaoCliente->informaAlteracaoCliente('alteracao_valores', $codigo_cliente, $this->ClienteProdutoServico2->alteracaoValores);
                }
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
                
        $this->redirect(array('controller' => 'gerenciar_clientes_produtos', 'action' => 'index', $codigo_cliente));        
    }
    
    public function atualizar_servicos_profissionais() {
        $codigo_cliente = $this->data['Outros']['codigo_cliente'];
        
        if($this->RequestHandler->isPut()) { 
            $result = $this->ClienteProdutoServico2->atualizar($this->data);

            if ($result) {
                if ($this->ClienteProdutoServico2->alteracaoValores) {
                    $this->EmailAlteracaoCliente->informaAlteracaoCliente('alteracao_valores', $codigo_cliente, $this->ClienteProdutoServico2->alteracaoValores);
                }
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
                
        $this->redirect(array('controller' => 'gerenciar_clientes_produtos', 'action' => 'index', $codigo_cliente));        
    }
    
    
    public function incluir($codigo_cliente_produto) {
        $this->layout = 'ajax';
        $cliente_produto = $this->ClienteProduto->find('all', array('recursive' => 2, 'conditions' => array('ClienteProduto.codigo' => $codigo_cliente_produto)));
        
        if (empty($cliente_produto)) {
            $this->BSession->setFlash('save_error');
            exit;
        }
        $cliente_produto = $cliente_produto[0];
        
        if (!empty($this->data)) {
            $this->data['Produto'] = $cliente_produto['Produto'];

            $this->ClienteProdutoServico2->set($this->data['ClienteProdutoServico2']);
            if ($this->ClienteProdutoServico2->validates()) {
                $result = $this->ClienteProdutoServico2->incluirParaTipoProfissional($this->data);
                if ($result) {
                    if ($this->ClienteProdutoServico2->alteracaoValores) {
                        $this->EmailAlteracaoCliente->informaAlteracaoCliente('alteracao_valores', $cliente_produto['ClienteProduto']['codigo_cliente'], $this->ClienteProdutoServico2->alteracaoValores);
                    }
                    $this->BSession->setFlash('save_success');
                }
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $cliente_produto;
            $this->data['ClienteProdutoServico2']['codigo_cliente_produto'] = $codigo_cliente_produto;
        }
        
        $servicos = $this->ProdutoServico->servicosPorProduto($cliente_produto['Produto']['codigo']);
        $this->set(compact('servicos'));
    }
    
    /**
     *
     * @param type $codigo_cliente_produto
     * @param type $codigo_servico 
     */
    public function excluir($codigo_cliente_produto, $codigo_servico) {
        $this->layout = 'ajax';

        $result = $this->ClienteProdutoServico2->excluirPorServicoEProduto($codigo_cliente_produto, $codigo_servico);
        if ($result) {
            //$this->BSession->setFlash('save_success');
        } else {
            //$this->BSession->setFlash('save_error');
        }

        die;
    }

    public function validate_cliente_pagador($codigo_cliente_pagador) {
		$validations = array();
		
		$this->loadModel('ClienteProdutoServico2');
		$this->loadModel('Cliente');

		if(!$this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $codigo_cliente_pagador)))) {
			$validations['codigo_cliente_pagador'] = 'Cliente não encontrado';
		}
		
		$this->ClienteProdutoServico2->validationErrors = $validations;
		return (count($validations) == 0);
	}


    function gg_por_cliente() {
        $filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
        $filtros = explode('|', $filtros);
        $filtros = array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]);
        $this->set('produtos_servicos', $this->ClienteProdutoServico2->produtosEServicos($filtros['codigo_cliente']));
    }

}

 
