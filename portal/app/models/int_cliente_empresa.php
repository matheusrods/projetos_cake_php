<?php
class IntClienteEmpresa extends AppModel
{
	public $name          = 'IntClienteEmpresa';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_empresa';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Empresa";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_empresa'));
	public $fillable      = array(
		'codigo_externo_empresa',
		'cnpj',
		'nome_fantasia',
		'razao_social',
		'tipo_empresa',
		'inscricao_estadual',
		'inscricao_municipal',
		'regime_tributario',
		'cnae',
		'ddd',
		'telefone',
		'nome_contato_telefonico',
		'email',
		'nome_contato_email',
		'cep',
		'logradouro',
		'numero',
		'complemento',
		'bairro',
		'cidade',
		'estado',
		'ativo',
	);


	// public $validate = array(
	// 	'nome_fantasia' => array(
	// 		'rule' => 'notEmpty',
	// 		'message' => 'Nome fantasia vazio',
	// 	),
	// 	'razao_social' => array(
	// 		'rule' => "notEmpty",
	// 		'message' => "Razão social vazio"
	// 	),
	// );

	public function incluir($data = null, $validate = true, $fieldList = array()){
		$notEmpty = array('nome_fantasia', 'razao_social');
		$obs = "";
		foreach($notEmpty as $key => $field) {
			if(strlen($data[$field]) == 0) {
				$obs .= "{$field} está vazio;"; 
			}
		}

		if(strlen($obs) >= 1) {
			$data['codigo_status_transferencia'] = 6;
		}
		$data['observacao'] = $obs;
		return parent::incluir($data, $validate, $fieldList);
	}

	/**
	 * [set_clientes metodo para processamento dos dados de arquivos importados ou carregados ]
	 */
	public function set_clientes($codigo_int_upload_cliente = null, $codigo_int_cliente_empresa =null)
	{
		
		//seta uma variável de erro
		$erros = array();
		
		//busca todos os arquivos de todos os clientes para processar de clientes, tanto operacionais quanto fiscais, 
		$conditions['codigo_status_transferencia'] = 3;
		//$conditions['ativo'] = 1;

		//para processar o arquivo inteiro
		if(!is_null($codigo_int_upload_cliente)) {
			$conditions['codigo_int_upload_cliente'] = $codigo_int_upload_cliente;
		}

		//para processar somente a linha
		if(!is_null($codigo_int_cliente_empresa)) {
			$conditions['codigo'] = $codigo_int_cliente_empresa;
		}

		//pegando todos os arquivos que estão prontos para processar status_transferencia 3
		$dadosIntClienteEmpresa = $this->find('all', array("conditions" => $conditions ));

		$campos = array();
        $retorno = array();
        
		//verifica se existe registros
		if(!empty($dadosIntClienteEmpresa)) {

			//instancia as models
			$this->Cliente =& ClassRegistry::init('Cliente');
			$this->ClienteContato =& ClassRegistry::init('ClienteContato');
			$this->ClienteEndereco =& ClassRegistry::init('ClienteEndereco');
			$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');

			$this->Cnae =& ClassRegistry::init('Cnae');        
	        $this->Medico =& ClassRegistry::init('Medico');
	        $this->LastId =& ClassRegistry::init('LastId');

	        $cnpj_matriz = '';
			//varre os dados para serem processados
			foreach($dadosIntClienteEmpresa AS $dados) {

				$codigo_upload = $dados['IntClienteEmpresa']['codigo_int_upload_cliente'];
				$codigo_int_cliente_empresa = $dados['IntClienteEmpresa']['codigo'];

				//seta o staus que está processando os dados do arquivo
				$dados['IntClienteEmpresa']['codigo_status_transferencia'] = 4; //incluindo estrtutura
				$this->atualizar($dados);

				//pelo cnpj buscar na tabela de cliente
				//caso encontre atualizar o dado / caso nao encontre inclui o dado
				$cnpj = $dados['IntClienteEmpresa']['cnpj'];
				
				//busca na tabela de cliente
				$dadosCliente = $this->Cliente->find('first',array('conditions' => array('codigo_documento' => $cnpj, 'ativo' => 1)));
				// debug($dadosCliente);exit;

				//seta as variaveis auxiliares
				$tipo_empresa = $dados['IntClienteEmpresa']['tipo_empresa'];
				$tipo_alocacao = 'F'; //fiscal

				//verifica se é matriz para guardar o cnpj_matriz
				if($tipo_empresa == 'M') {
					$cnpj_matriz = $cnpj;
				}//fim verificacao
				else if($tipo_empresa == 'F') {
					//verifica se tem valor no cnpj matriz e se o cnpj_matriz igual ao cnpj da filial
					if($cnpj_matriz == $cnpj) {
						$tipo_alocacao = 'O'; //operacional
					}//fim matriz
				}//fim else if filial

				//busca se tem o grupo economico pelo codigo de cliente matriz 
				$codigo_cliente_dadosCliente = '';
				if(!empty($dadosCliente)) {
					$codigo_cliente_dadosCliente = $dadosCliente['Cliente']['codigo'];
				}
				else {
					if(!empty($cnpj_matriz)) {
						//pega os dados do cliente matriz antes informado
						$dadosClienteMatriz = $this->Cliente->find('first',array('conditions' => array('codigo_documento' => $cnpj_matriz)));
						
						//verifica se tem dados pelo cnpj da matriz
						if(!empty($dadosClienteMatriz)) {
							//seta o codigo do cliente achado no banco
							$codigo_cliente_dadosCliente = $dadosClienteMatriz['Cliente']['codigo'];
						}//fim empty $dadosClienteMatriz

					}//fim cnpj_matriz
				}//fim else
				
				$dadosGE = '';
				if(!empty($codigo_cliente_dadosCliente)) {
					//verifica se ele está relacionado ao grupo economico do codigo da empresa que ele subiu
					//grupos_economicos
					$dadosGE = $this->GrupoEconomicoCliente->find('first',array('fields' => array('codigo_grupo_economico'),'conditions' =>array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente_dadosCliente),'recursive' => -1));
				}//fim codigo_cliente_dadosCliente

				if(empty($dadosGE)) {
					$campos[$codigo_upload][] = "Grupo Economico não econtrado no layout carregado, para o cliente que foi importado: ".$dados['IntClienteEmpresa']['codigo_cliente']."!";
					$retorno[$codigo_upload]['codigo_grupo_economico'] = false;
					$retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

					$this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);

					continue;
				}
				

				//seta o grupo
				$codigo_grupo_economico = $dadosGE['GrupoEconomicoCliente']['codigo_grupo_economico'];

				//seta os dados da sessao
				$_SESSION['Auth']['Usuario']['codigo'] 			= $dados['IntClienteEmpresa']['codigo_usuario_inclusao'];
				$_SESSION['Auth']['Usuario']['codigo_empresa']	= $dados['IntClienteEmpresa']['codigo_empresa'];


				if ( (strlen($dados['IntClienteEmpresa']['cep']) != 8) || !is_numeric($dados['IntClienteEmpresa']['cep']) ){
		            echo "O cep informado para a alocação é inválido"."\n";
		            $campos[$codigo_upload][] = 'O cep informado para a alocação é inválido diferene de 8 caracteres ou não é numerico';
		            $retorno[$codigo_upload]['cep'] = false;
		            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		            $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		            continue;
		        }

		        ###################### cliente ######################
				//monta o array de organização para atualizar ou não
				$arr_dados_cliente = array(
					'Cliente' => array(
						'codigo_externo_cliente' => $dados['IntClienteEmpresa']['codigo_externo_empresa'],
						'codigo_documento' => $cnpj,
						'razao_social' => $dados['IntClienteEmpresa']['razao_social'],
						'nome_fantasia' => $dados['IntClienteEmpresa']['nome_fantasia'],
						'ativo' => $dados['IntClienteEmpresa']['ativo'],
						'cnae' => $dados['IntClienteEmpresa']['cnae'],
						'codigo_regime_tributario' => $dados['IntClienteEmpresa']['regime_tributario'],
						'tipo_unidade' => $tipo_alocacao,

						// 'codigo_empresa' => $dados['IntClienteEmpresa']['codigo_empresa'],
						// 'codigo_usuario_inclusao' => $dados['IntClienteEmpresa']['codigo_usuario_inclusao'],

					),
					'ClienteEndereco' => array(
						'cep' => $dados['IntClienteEmpresa']['cep'],
						'logradouro' => $dados['IntClienteEmpresa']['logradouro'],
						'numero' => $dados['IntClienteEmpresa']['numero'],
						'complemento' => $dados['IntClienteEmpresa']['complemento'],
						'bairro' => $dados['IntClienteEmpresa']['bairro'],
						'cidade' => $dados['IntClienteEmpresa']['cidade'],
						'estado_abreviacao' => $dados['IntClienteEmpresa']['estado'],
						'estado_descricao' => $dados['IntClienteEmpresa']['estado'],
					)
				);

				//if feito aqui porque pode ser que já tenhamos esse dado populado na base e se vier vazio irá atualziar com o valor vazio implicando no faturamento
				if(trim($dados['IntClienteEmpresa']['inscricao_estadual']) != '') {
					$arr_dados_cliente['Cliente']['inscricao_estadual'] = $dados['IntClienteEmpresa']['inscricao_estadual'];
				}
				//if feito aqui porque pode ser que já tenhamos esse dado populado na base e se vier vazio irá atualziar com o valor vazio implicando no faturamento
				if(trim($dados['IntClienteEmpresa']['inscricao_municipal']) != '') {
					$arr_dados_cliente['Cliente']['ccm'] = $dados['IntClienteEmpresa']['inscricao_municipal'];
				}

				// debug($dadosCliente);exit;
				
				//verifica se existe dados de cliente ja cadastrado
				if(empty($dadosCliente)) {

		            $incluir = true;
		            $codigo_documento_real = null;
		            if ($tipo_alocacao == 'F') {

		                $cnpj_alocacao = $cnpj;
		                $conditions = array('codigo_documento' => $cnpj, 'ativo' => 1);
		                $cliente = $this->Cliente->find('first', compact('conditions'));
		                if (!empty($cliente)) {
		                    if (empty($dadosCliente['Cliente']['codigo_externo'])) {
		                        echo "CNPJ encontrado sem código externo, atualizando"."\n";
		                        $this->Cliente->read(null, $dadosCliente['Cliente']['codigo']);
		                        $this->Cliente->set('codigo_externo', $unidade['codigo_externo_alocacao']);
		                        $this->Cliente->set('codigo_naveg', $LastId->last_id('Cliente'));
		                        $this->Cliente->save();
		                    }
		                    $incluir = false;
		                }

		            }
		            elseif ($tipo_alocacao == 'O') {
		                $conditions = array('codigo_documento' => $cnpj);
		                if ($this->Cliente->find('count', compact('conditions')) == 0) {
		                    echo "Utilizar CNPJ Alocação informado"."\n";
		                    $cnpj_alocacao = $cnpj;
		                } else {
		                    $conditions = array('codigo_documento LIKE' => substr($cnpj, 0, 8).'9%');
		                    $group = 'LEFT(codigo_documento,8)';
		                    $fields = array(
		                        "MAX(RIGHT(LEFT(Cliente.codigo_documento, 12),3))+1 AS filial"
		                    );
		                    echo "Gerar CNPJ Alocação"."\n";
		                    $cnpj_alocacao = $this->Cliente->find('first', compact('conditions', 'fields', 'group'));
		                    $cnpj_alocacao = substr($cnpj, 0, 8).'9'.str_pad($cnpj_alocacao[0]['filial'], 3, '0', STR_PAD_LEFT).'00';
		                    echo 'Gerar novo CNPJ Alocação '.$cnpj_alocacao."\n";

		                    //seta o codigo_documento_real com o codigo da matriz, quando a unidade é operacional
		                    $codigo_documento_real = $cnpj;

		                }
		            } //FINAL SE $tipo_alocacao É IGAL A 'F'

		            
		            if ($incluir) {
		            	
		            	//sem profissional
		            	// $conditions['codigo'] = 11860;
			            // $codigo_medico_pcmso = $Medico->find('first', array('conditions' => $conditions));
			            $codigo_medico_pcmso = 11860; //(!empty($codigo_medico_pcmso) ? $codigo_medico_pcmso['Medico']['codigo'] : null);

						$arr_dados_cliente['GrupoEconomicoCliente'] = array(
	                        'codigo_grupo_economico' => $codigo_grupo_economico
	                    );
	                    $arr_dados_cliente['Cliente']['codigo_medico_pcmso'] = $codigo_medico_pcmso;

	                    if(!isset($arr_dados_cliente['Cliente']['inscricao_estadual'])) {
	                    	$arr_dados_cliente['Cliente']['inscricao_estadual'] = "ISENTO";
	                    }

	                    if(!isset($arr_dados_cliente['Cliente']['ccm'])) {
	                    	$arr_dados_cliente['Cliente']['ccm'] = "ISENTO";
	                    }

						// echo 'Incluir'."\n";
		                if (!$this->Cliente->incluir($arr_dados_cliente)) {
		                    echo "Falha ao incluir unidade de alocação"."\n";
		                    $campos[$codigo_upload] = $this->Cliente->validationErrors;
		                    $campos[$codigo_upload][] = 'Falha ao incluir unidade de alocação';
		                    
		                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		                    continue;
		                }
					}//fim incluir

					$dadosCliente['Cliente']['codigo'] = $this->Cliente->id;
					$codigo_cliente = $this->Cliente->id;

		            
		            //seta o status transferencia 

		            // $retorno['codigo_externo'] = $unidade['codigo_externo_alocacao'];
		            // $retorno['codigo_alocacao'] = $dadosCliente['Cliente']['codigo'];
		            // return $retorno;
				
				}//fim empty dadosCliente
				else {

		           	###################### atualiza cliente ######################
					
					//seta o codigo do cliente com os dados de alteracao
					$arr_dados_cliente['Cliente']['codigo'] = $dadosCliente['Cliente']['codigo'];
					$codigo_cliente = $dadosCliente['Cliente']['codigo'];

					//pega o endereco do cliente 
					$clienteEnd = $this->ClienteEndereco->find('first',array('fields'=>array('codigo'),'conditions' => array('codigo_cliente' => $dadosCliente['Cliente']['codigo'])));

					if(!empty($clienteEnd)) {
						$arr_dados_cliente['ClienteEndereco']['codigo'] = $clienteEnd['ClienteEndereco']['codigo'];
					}
					
					if(!isset($arr_dados_cliente['Cliente']['inscricao_estadual'])) {
                    	$arr_dados_cliente['Cliente']['inscricao_estadual'] = $dadosCliente['Cliente']['inscricao_estadual'];
                    }

                    if(!isset($arr_dados_cliente['Cliente']['ccm'])) {
                    	$arr_dados_cliente['Cliente']['ccm'] = $dadosCliente['Cliente']['ccm'];
                    }

                    // debug($arr_dados_cliente);exit;
					// $this->log('atualizar','debug');
	                if (!$this->Cliente->atualizar($arr_dados_cliente)) {

	                	// debug($arr_dados_cliente);
	                    // $this->log("Falha ao atualizar unidade de alocação",'debug');
	                    $campos[$codigo_upload] = $this->Cliente->validationErrors;
	                    $campos[$codigo_upload][] = 'Falha ao atualizar unidade de alocação';
	                    
	                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
	                    // debug($retorno);exit;
	                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
	                    continue;
	                }

		        }//FINAL SE $cliente é VAZIO

		        if (!empty($dados['IntClienteEmpresa']['telefone'])) {
	                $dadosTelefonico['ClienteContato'] = array(
	                    'codigo_cliente' => $codigo_cliente,
	                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
	                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_TELEFONE,
	                    'nome' => $dados['IntClienteEmpresa']['nome_contato_telefonico'],
	                    'descricao' => $dados['IntClienteEmpresa']['telefone'],
	                );

	                //verifica se existe esse contato para esse cliente
	                $clienteContatoTel = $this->ClienteContato->find('first',array(
	                	'fields' => array('codigo'),
	                	'conditions' => array(
	                		'codigo_cliente' => $codigo_cliente,
	                		'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
	                    	'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_TELEFONE,
	                	)
	                ));

	                if(!empty($clienteContatoTel)) {
	                	$dadosTelefonico['ClienteContato']['codigo'] = $clienteContatoTel['ClienteContato']['codigo'];

	                	if (!$this->ClienteContato->atualizar($dadosTelefonico)) {		                    
		                    $campos[$codigo_upload][] = 'Falha ao atualizar telefone de contato da alocação';
		                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		                }
	                }
	                else {
	                    // $dadosTelefonico['ClienteContato']['codigo_usuario_inclusao'] = $dados['IntClienteEmpresa']['codigo_usuario_inclusao'];
		                
		                if (!$this->ClienteContato->incluir($dadosTelefonico)) {
		                    // $this->log("Falha ao incluir telefone de contato da alocação",'debug');
		                    $campos[$codigo_upload][] = 'Falha ao incluir telefone de contato da alocação';
		                    
		                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		                }
	                }

	            }//FINAL SE $dados['IntClienteEmpresa']['telefone'] NÃO É VAZIO

	            if (!empty($dados['IntClienteEmpresa']['email'])) {
	                $dadosEmail['ClienteContato'] = array(
	                    'codigo_cliente' => $codigo_cliente,
	                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
	                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
	                    'nome' => $dados['IntClienteEmpresa']['nome_contato_email'],
	                    'descricao' => $dados['IntClienteEmpresa']['email'],
	                );
	                
	                 //verifica se existe esse contato para esse cliente
	                $clienteContatoEmail = $this->ClienteContato->find('first',array(
	                	'fields' => array('codigo'),
	                	'conditions' => array(
	                		'codigo_cliente' => $codigo_cliente,
	                		'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
	                    	'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
	                	)
	                ));

	                if(!empty($clienteContatoEmail)) {
	                	$dadosEmail['ClienteContato']['codigo'] = $clienteContatoEmail['ClienteContato']['codigo'];

	                	if (!$this->ClienteContato->atualizar($dadosEmail)) {		                    
		                    $campos[$codigo_upload][] = 'Falha ao atualizar email de contato da alocação';
		                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		                }
	                }
	                else {
	                	
	                	// $dadosEmail['ClienteContato']['codigo_usuario_inclusao'] = $dados['IntClienteEmpresa']['codigo_usuario_inclusao'];
	                	// debug($dadosEmail);exit;
	                	
		                if (!$this->ClienteContato->incluir($dadosEmail)) {		                    
		                    $campos[$codigo_upload][] = 'Falha ao incluir email de contato da alocação';		                    
		                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                    $this->log_erros_int_cliente_empresa($codigo_int_cliente_empresa,$retorno);
		                }
	                }
	            }//FINAL SE $dados['IntClienteEmpresa']['email'] NÃO É VAZIO

		        
		        //seta o staus que está processando os dados do arquivo
				$dados['IntClienteEmpresa']['codigo_status_transferencia'] = 8; //importacao estrutura processado
				$this->atualizar($dados);

			}//fim foreach $dadosIntClienteEmpresa

	        // $retorno['codigo_externo'] = $dadosCliente['Cliente']['codigo_externo'];
	        // $retorno['codigo_alocacao'] = $dadosCliente['Cliente']['codigo'];

		}//fim verificacao se tem arquivos para serem processados
	    
	    return $retorno;
	
	}// fim set_clientes

	public function log_erros_int_cliente_empresa($codigo_int_cliente_empresa, $erro)
	{

		$int_cliente_empresa['IntClienteEmpresa']['codigo'] = $codigo_int_cliente_empresa;
		$int_cliente_empresa['IntClienteEmpresa']['codigo_status_transferencia'] = 6; // estrutura falhou
		$int_cliente_empresa['IntClienteEmpresa']['observacao'] = json_encode($erro);

		$this->atualizar($int_cliente_empresa);

	}//fim log_erros_int_cliente_empresa

}
