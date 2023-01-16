<?php
class ListasDePrecoController extends AppController {
    public $name = 'ListasDePreco';
    var $uses = array('ListaDePreco', 'Fornecedor', 'ListaDePrecoProduto', 'ListaDePrecoProdutoServico', 'Servico');
    
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->BAuth->allow(array('importar_planilha', 'limpa_base'));
    }
    
    public function index() {
        $this->pageTitle = 'Listas de Preço';
        $this->data['ListaDePreco'] = $this->Filtros->controla_sessao($this->data, $this->ListaDePreco->name);
    }
    
    public function listagem() {
    	$this->pageTitle = 'Listas de Preço';
    	$this->layout = 'ajax';
    	
    	$filtros = $this->Filtros->controla_sessao($this->data, 'ListaDePreco');
    	$conditions = $this->ListaDePreco->converteFiltroEmCondition($filtros);
    	
    	$this->set('listas_de_preco', $this->ListaDePreco->find('all', array('conditions' => $conditions)));
    	
    }

    private function carrega_combos() {
        $fornecedores = $this->Fornecedor->find('list', array('order' => 'nome', 'conditions' => array('ativo' => '1', 'nome <>' => '')));
        $this->set(compact('fornecedores'));
    }

    public function incluir() {
        if (!empty($this->data)) {
            if ($this->ListaDePreco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }

        }
        $this->carrega_combos();
    }

    public function editar($codigo) {
        if (!empty($this->data)) {
            if ($this->ListaDePreco->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ListaDePreco->carregar($codigo);
        }
        $this->carrega_combos();
    }

    public function excluir($codigo) {

    	$this->ListaDePreco->id = $codigo;

		if(!$this->ListaDePreco->exists()) {
			$this->BSession->setFlash('delete_error');
			$this->redirect(array('action' => 'index'));
		}

		if($this->ListaDePreco->excluir($codigo)) {
			$this->BSession->setFlash('delete_success');
		} else {
			$this->BSession->setFlash('delete_error');
		}
		$this->redirect(array('action' => 'index'));
    }

    public function limpa_base() {
    	
    	ini_set("memory_limit", "-1");
    	ini_set("max_execution_time","1800");
    	
    	$dados = $this->ListaDePreco->query("select codigo_lista_de_preco_produto, codigo_servico, count(*) from listas_de_preco_produto_servico group by codigo_lista_de_preco_produto, codigo_servico having count(*) > 1");
    	
    	foreach($dados as $k => $item) {
    		$dados_filtrado = $this->ListaDePreco->query("select 
    					listas_de_preco.codigo_fornecedor,
    					fornecedores.nome,
    					servico.descricao,
    					listas_de_preco_produto_servico.valor,
    					listas_de_preco_produto_servico.codigo
    				from 
    					listas_de_preco_produto_servico
    					inner join servico ON (servico.codigo = listas_de_preco_produto_servico.codigo_servico)
	    				inner join listas_de_preco_produto ON (listas_de_preco_produto.codigo = listas_de_preco_produto_servico.codigo_lista_de_preco_produto)
	    				inner join listas_de_preco ON (listas_de_preco.codigo = listas_de_preco_produto.codigo_lista_de_preco)
	    				inner join fornecedores ON (fornecedores.codigo = listas_de_preco.codigo_fornecedor)
    				where listas_de_preco_produto_servico.codigo_lista_de_preco_produto = {$item[0]['codigo_lista_de_preco_produto']} AND listas_de_preco_produto_servico.codigo_servico = {$item[0]['codigo_servico']} ORDER BY 1 DESC");
    		
    		
			echo  $dados_filtrado[0][0]['codigo_fornecedor'] . ";" . $dados_filtrado[0][0]['nome'] . ";" . $dados_filtrado[0][0]['descricao'] . ";" . $dados_filtrado[0][0]['valor'] . ";" . $dados_filtrado[1][0]['valor'] . ";" . $dados_filtrado[0][0]['codigo'] . ";<br />";
    	}
    	
    	exit;
    	
    }
  
    public function importar_planilha() {
    	
    	ini_set("memory_limit", "-1");
    	ini_set("max_execution_time", 0);
    	
    	$filename = APP.'..'.DS.'docs'.DS.'20170511_lista_de_preco.csv';
    	
    	if(!file_exists($filename))
    		exit("ARQUIVO NAO LOCALIZADO");
    	
    	if ($handle = fopen($filename, "r")) {
    		$conteudo = Comum::trata_nome(utf8_encode(fread($handle, filesize($filename))));
    				
    		try {
    			
    			$this->ListaDePreco->query('begin transaction');
    			
	    		$array_organizado = array();
	    		
	    		$lista_de_preco = $this->ListaDePreco->find('first', array('order' => 'codigo DESC', 'recursive' => -1));
	    		$lista_de_preco_produto = $this->ListaDePrecoProduto->find('first', array('order' => 'codigo DESC', 'recursive' => -1));
	    		$lista_de_preco_produto_servico = $this->ListaDePrecoProdutoServico->find('first', array('order' => 'codigo DESC', 'recursive' => -1));
	    		$servico = $this->Servico->find('first', array('order' => 'codigo DESC', 'recursive' => -1));
	    		
	    		$codigo_servico = $lista_de_preco_produto_servico['ListaDePrecoProdutoServico']['codigo'];
	    		$codigo_produto = $lista_de_preco_produto['ListaDePrecoProduto']['codigo'];
	    		$codigo_lista = $lista_de_preco['ListaDePreco']['codigo'];
	    		$codigo_exame = $servico['Servico']['codigo'];
	    		
	    		foreach(explode("\n", $conteudo) as $key => $linha) {
	    			
	    			$conteudo_exploded = explode(";", $linha);
	    			$dadosFornecedor = $this->Fornecedor->find('first', array('conditions' => array('codigo_soc' => $conteudo_exploded[0]), 'recursive' => -1));
	    			
	    			if($dadosFornecedor) {
	    				
	    				$dadosListaDePreco = $this->ListaDePreco->find('first', array('recursive' => -1, 'conditions' => array('ListaDePreco.codigo_fornecedor' => $dadosFornecedor['Fornecedor']['codigo'])));
	    				if(!$dadosListaDePreco) {
	    					
	    					$codigo_lista++;
	    					$array_inclusao_lista_preco = array(
	    						'codigo' => $codigo_lista,
		    					'codigo_fornecedor' => $dadosFornecedor['Fornecedor']['codigo'],
	    						'descricao' => 'Fornecedor: ' . $dadosFornecedor['Fornecedor']['nome'],
	    						'data_inclusao' => '2017-05-11',
	    						'codigo_usuario_inclusao' => '61608'
	    					);
	    					
	    					
	    					#############################################################################################
	    					$this->ListaDePreco->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco] ON");
	    					if($this->ListaDePreco->save($array_inclusao_lista_preco)) {
	    						
	    						echo "========================================================================================================<br />";
	    						echo "****** INSERT ******* codigo lista de preco " . $this->ListaDePreco->getLastInsertId() . " <br />";
	    						echo "========================================================================================================<br />";
	    						
	    						$dadosListaDePreco = $this->ListaDePreco->find('first', array('recursive' => -1, 'conditions' => array('ListaDePreco.codigo_fornecedor' => $dadosFornecedor['Fornecedor']['codigo'])));
	    					} else {
	    						
	    						pr($array_inclusao_lista_preco);
	    						pr($this->ListaDePreco->validationErrors);
	    						
	    						exit('erro ao inserir lista de preco');
	    					}
	    					$this->ListaDePreco->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco] OFF");
	    					#############################################################################################
	    					
	    				}
	    				
	    				if(isset($dadosListaDePreco['ListaDePreco']['codigo'])) {
	    					$dadosListaDePrecoProduto = $this->ListaDePrecoProduto->find('first', array('recursive' => -1, 'conditions' => array('codigo_lista_de_preco' => $dadosListaDePreco['ListaDePreco']['codigo'])));
	    					
	    					if(!$dadosListaDePrecoProduto['ListaDePrecoProduto']['codigo']) {
	    							
	    						$codigo_produto++;
	    						$array_inclusao_ListaDePrecoProduto = array(
	    								'codigo' => $codigo_produto,
	    								'codigo_lista_de_preco' => $dadosListaDePreco['ListaDePreco']['codigo'],
	    								'codigo_produto' => '59', 
	    								'valor_premio_minimo' => '0',
	    								'qtd_premio_minimo' => '0',
	    								'data_inclusao' => '2017-05-11',
	    								'codigo_usuario_inclusao' => '61608'
	    						);
	    							
	    						
	    						##############################################################################################################
	    						$this->ListaDePrecoProduto->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco_produto] ON");
	    						if($this->ListaDePrecoProduto->save($array_inclusao_ListaDePrecoProduto)) {
	    							
	    							echo "========================================================================================================<br />";
	    							echo "****** INSERT ******* codigo lista de preco produto " . $this->ListaDePrecoProduto->getLastInsertId() . " <br />";
	    							echo "========================================================================================================<br />";
	    							
	    							
	    							$dadosListaDePrecoProduto = $this->ListaDePrecoProduto->find('first', array('recursive' => -1, 'conditions' => array('codigo_lista_de_preco' => $dadosListaDePreco['ListaDePreco']['codigo'])));
	    						} else {
	    							
	    							pr($array_inclusao_ListaDePrecoProduto);
	    							pr($this->ListaDePrecoProduto->validationErrors);
	    							
	    							exit('erro ao inserir ListaDePrecoProduto');
	    						}
	    						$this->ListaDePrecoProduto->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco_produto] OFF");
	    						##############################################################################################################
	    						
	    					}
	    					
	    					
	    					if(isset($conteudo_exploded[2]) && isset($conteudo_exploded[3])) {
	    							
	    						$dadosServico = $this->Servico->find('first', array('recursive' => -1, 'conditions' => array('descricao' => $conteudo_exploded[2])));
	    						
	    						if(!$dadosServico) {
	    							
	    							$codigo_exame++;
	    							$array_inclusao_Servico = array(
    									'codigo' => $codigo_exame,
    									'descricao' => $conteudo_exploded[2],
	   									'tipo_servico' => 'S',
	    								'ativo' => '1',
	   									'codigo_usuario_inclusao' => '61608'
	    							);
	    							
	    							
	    							##################################################################################
	    							$this->Servico->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[servico] on");
	    							if($this->Servico->save($array_inclusao_Servico)) {
	    								
	    								echo "========================================================================================================<br />";
	    								echo "****** INSERT ******* codigo servico " . $this->Servico->getLastInsertId() . " <br />";
	    								echo "========================================================================================================<br />";
	    								
	    								$dadosServico = $this->Servico->find('first', array('recursive' => -1, 'conditions' => array('codigo' => $this->Servico->getLastInsertId())));
	    							} else {
	    									
	    								pr($array_inclusao_Servico);
	    								pr($this->Servico->validationErrors);
	    								
	    								exit('erro ao inserir Servico');
	    							}
	    							$this->Servico->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[servico] OFF");
	    							##################################################################################
	    							
	    							
	    						}
	    						
	    						$dadosListaDePrecoProdutoServico = $this->ListaDePrecoProdutoServico->find('all', array('recursive' => -1, 'conditions' => array('codigo_lista_de_preco_produto' => $dadosListaDePrecoProduto['ListaDePrecoProduto']['codigo'], 'codigo_servico' => $dadosServico['Servico']['codigo'])));
	    							
	    						if(!$dadosListaDePrecoProdutoServico) {
	    							
	    							$codigo_servico++;
	    							$array_inclusao_ListaDePrecoProdutoServico['ListaDePrecoProdutoServico'] = array(
	    								'codigo' => $codigo_servico,
	    								'codigo_servico' => $dadosServico['Servico']['codigo'],
	    								'codigo_lista_de_preco_produto' => $dadosListaDePrecoProduto['ListaDePrecoProduto']['codigo'],
	    								'valor_premio_minimo' => '0',
	    								'qtd_premio_minimo' => '0',
	    								'valor_maximo' => '0',
	    								'valor' => (float) str_replace(",", ".", $conteudo_exploded[3]),
	    								'codigo_usuario_inclusao' => '61608',
	    								'data_inclusao' => '02/03/2017 11:11:11'
	    							);
	    								 
	    							if(!$retorno = $this->ListaDePrecoProdutoServico->find('all', array('recursive' => -1, 'conditions' => array('codigo_servico' => $dadosServico['Servico']['codigo'], 'codigo_lista_de_preco_produto' => $dadosListaDePrecoProduto['ListaDePrecoProduto']['codigo'])))) {
	    								
	    								
	    								###########################################################################################################################
	    								$this->ListaDePrecoProdutoServico->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco_produto_servico] ON");
	    								if($this->ListaDePrecoProdutoServico->save($array_inclusao_ListaDePrecoProdutoServico)) {
	    									echo "========================================================================================================<br />";
	    									echo "****** INSERT ******* codigo lista de preco produto servico " . $this->ListaDePrecoProdutoServico->getLastInsertId() . " ----- lista de preço: " . $dadosListaDePreco['ListaDePreco']['codigo'] . " --- serviço: " . $dadosServico['Servico']['codigo'] . " --- valor: " . $conteudo_exploded[3] . " <br />";
	    									echo "========================================================================================================<br />";
	    								} else {
	    									
	    									pr($array_inclusao_ListaDePrecoProdutoServico);
	    									pr($this->ListaDePrecoProdutoServico->validationErrors);
	    										
	    									exit('erro ao inserir ListaDePrecoProdutoServico');
	    								}
	    								$this->ListaDePrecoProdutoServico->query("SET IDENTITY_INSERT  [RHhealth].[dbo].[listas_de_preco_produto_servico] OFF");
	    								###########################################################################################################################
	    								
	    							}
	    						} else {
	    								
	    							$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['valor'] = (float) str_replace(",", ".", $conteudo_exploded[3]);
	    								
	    							$achou = $this->ListaDePrecoProdutoServico->query("select count(1) from listas_de_preco_produto_servico where valor = '{$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['valor']}' AND codigo = {$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['codigo']}");
	    							
	    							if(isset($achou[0][0]['computed']) && ($achou[0][0]['computed'] == 0)) {
	    								
	    								if($this->ListaDePrecoProdutoServico->query("update listas_de_preco_produto_servico set valor = {$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['valor']} where codigo = {$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['codigo']}")) {
	    									
	    									echo "========================================================================================================<br />";
	    									echo "ATUALIZADO!!!!!!!! --------------------- ListaDePrecoProdutoServico: ". $dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['codigo'] ." - ListaDePreco: " . $dadosListaDePreco['ListaDePreco']['codigo'] . " --- Servico: " . $dadosServico['Servico']['codigo'] . " --- valor: " . $conteudo_exploded[3] . " <br />";
	    									echo "========================================================================================================<br />";
	    									
	    								} else {
	    									
	    									pr($this->ListaDePrecoProdutoServico);
	    									pr($dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']);
	    									pr("update listas_de_preco_produto_servico set valor = {$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['valor']} where codigo = {$dadosListaDePrecoProdutoServico[0]['ListaDePrecoProdutoServico']['codigo']}");
	    								
	    									pr($this->ListaDePrecoProdutoServico->validationErrors);
	    									exit('erro ao atualizar ListaDePrecoProdutoServico');
	    								}
	    							}
	    						}
	    					}
	    				}
	    				
	    			} else {
	    				$fornecedores_nao_cadastrados[] = $linha;
	    			}
	    		}
	    		
	    		$this->ListaDePreco->query("COMMIT");
	    		
	    		echo "*************************************************************************************************************<br />";
	    		pr($fornecedores_nao_cadastrados);
	    		echo "*************************************************************************************************************<br />";
	    		
	    		
	    		
    		} catch(Exception $e) {
    			
				$this->ListaDePreco->rollback();
				echo "ESTAMOS EM EXCEPTION!";
			}	
			
			exit('script finalizado');
    	}
    	
    }
}
