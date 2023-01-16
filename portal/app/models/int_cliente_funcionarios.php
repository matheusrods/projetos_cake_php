<?php
class IntClienteFuncionarios extends AppModel
{
	public $name          = 'IntClienteFuncionarios';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_funcionarios';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Funcionários";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_funcionario'));
	public $fillable      = array(
		'nome',
		'cpf',
		'data_nascimento',
		'estado_civil',
		'deficiente_fisico',
		'nome_mae',
		'tipo_sangue',
		'rg',
		'orgao_expedidor_rg',
		'data_emissao',
		'carteira_trabalho',
		'carteira_trabalho_serie',
		'carteira_trabalho_estado',
		'carteira_trabalho_emissao',
		'nit_pis_pasep',
		'cartao_nacional_saude',
		'gfip',
		'cep',
		'telefone',
		'nome_contato_telefonico',
		'email',
		'nome_contato_email',
		'escolaridade',
		'logradouro',
		'numero',
		'complemento',
		'bairro',
		'cidade',
		'estado',
		'ativo',
		'sexo',
		'raca',
		'data_emissao_rg',
		'ddd'
	);

	/**
	 * [set_funcionario para verificar se deve cadastrar ou atualizar um funcionario]
	 * @param [type] $codigo_int_upload_cliente      [description]
	 * @param [type] $codigo_int_cliente_funcionario [description]
	 */
	public function set_funcionario($codigo_int_upload_cliente = null,$codigo_int_cliente_funcionario = null)
	{

		//seta uma variável de erro
		$erros = array();

		$campos = array();
		$retorno = array();
		
		$total_funcionarios_process = 10000;

		//busca todos os arquivos de todos os cargos
		$conditions_int['codigo_status_transferencia'] = 3;
		// $conditions_int['ativo'] = 1;

		//para processar o arquivo inteiro
		if(!is_null($codigo_int_upload_cliente)) {
			$conditions_int['codigo_int_upload_cliente'] = $codigo_int_upload_cliente;
		}

		//para processar somente a linha
		if(!is_null($codigo_int_cliente_funcionario)) {
			$conditions_int['codigo'] = $codigo_int_cliente_funcionario;
		}

		//pega a quantidade total a processar
		$totalFunc = $this->find('count', array("conditions" => $conditions_int ));
		$loop_qtd = ceil($totalFunc/$total_funcionarios_process);
		// echo($loop_qtd."\n");exit;

		//varre quantidade que vai pegar 10k de registros para ficar mais leve o processamento
		for($i=0;$i<=$loop_qtd;$i++) {
			
			//set_time_limit(300);
			ini_set('memory_limit', '-1');
  			// ini_set('max_execution_time', 300); // 5min


			//pegando todos os arquivos que estão prontos para processar status_transferencia 3		
			$dadosIntClienteFuncionario = $this->find('all', array("conditions" => $conditions_int,'limit' => $total_funcionarios_process ));
			// debug($dadosIntClienteFuncionario);exit;
			//verifica se existe registros
			if(!empty($dadosIntClienteFuncionario)) {

				$this->IntUploadCliente =& ClassRegistry::init('IntUploadCliente');
				
				$this->Cliente =& ClassRegistry::init('Cliente');
		        $this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		        $this->Funcionario =& ClassRegistry::init('Funcionario');
		        $this->FuncionarioEndereco =& ClassRegistry::init('FuncionarioEndereco');
		        $this->FuncionarioContato =& ClassRegistry::init('FuncionarioContato');

		        $arr_cnpjs_codigo_cliente = array();

		        $array_estado_civil = array(
		        	'SO'=> 1,
		        	'CA'=> 2,
		        	'SE'=> 3,
		        	'DI'=> 4,
		        	'VI'=> 5,
		        	'OU'=> 6
		        );
		        
		        //varre os setores da staging
		        foreach($dadosIntClienteFuncionario AS $dadosFunc) {
			        
			        $campos = array();
			        $retorno = array();

		        	$dados_funcionario = $dadosFunc['IntClienteFuncionarios'];

		        	$codigo_upload = $dadosFunc['IntClienteFuncionarios']['codigo_int_upload_cliente'];

					$codigo = $dadosFunc['IntClienteFuncionarios']['codigo'];

					//seta os dadosFunc da sessao
					$_SESSION['Auth']['Usuario']['codigo'] 			= $dadosFunc['IntClienteFuncionarios']['codigo_usuario_inclusao'];
					$_SESSION['Auth']['Usuario']['codigo_empresa']	= $dadosFunc['IntClienteFuncionarios']['codigo_empresa'];

		        	//verifica se tem nome no centro de resultado
		        	$cpf = $dadosFunc['IntClienteFuncionarios']['cpf'];
		        	if(empty($cpf)) {
		        		$campos[$codigo_upload][] = 'Dado carregado sem o CPF do Funcionário: '. $cpf;
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cliente_funcionario($codigo, $retorno);
		                continue;
		        	}//fim erro de descricao

		        	//seta o staus que está processando os dados do arquivo
					$dadosFunc['IntClienteFuncionarios']['codigo_status_transferencia'] = 4; //incluindo estrtutura
					$this->atualizar($dadosFunc);

					//busca o funcionario pela chave cpf
			        $conditions_func = array('Funcionario.cpf' => $cpf);
			        $funcionario = $this->Funcionario->find('first', array('conditions' => $conditions_func));

			        //para inserir ou atualizar
	        		$dados = array(
		        		'Funcionario' => array(
			                'nome' => $dados_funcionario['nome'],
			                'cpf' => $cpf,
			                'sexo' => $dados_funcionario['sexo'],
			                'data_nascimento' => $dados_funcionario['data_nascimento'],
			                'estado_civil' => (isset($array_estado_civil[$dados_funcionario['estado_civil']])) ? $array_estado_civil[$dados_funcionario['estado_civil']] : null,
			                'deficiencia' => $dados_funcionario['deficiente_fisico'] == 'S' ? 1 : 0,
			                'nome_mae' => $dados_funcionario['nome_mae'],
			                'tipo_sangue' => $dados_funcionario['tipo_sangue'],
			                'rg' => $dados_funcionario['rg'],
			                'rg_orgao' => $dados_funcionario['orgao_expedidor_rg'],
			                'rg_uf' => $dados_funcionario['orgao_expedidor_rg'],
			                'rg_data_emissao' => $dados_funcionario['data_emissao_rg'],
			                'ctps' => $dados_funcionario['carteira_trabalho'],
			                'ctps_serie' => $dados_funcionario['carteira_trabalho_serie'],
			                'ctps_uf' => $dados_funcionario['carteira_trabalho_estado'],
			                'ctps_data_emissao' => $dados_funcionario['carteira_trabalho_emissao'],
			                'nit' => $dados_funcionario['nit_pis_pasep'],
			                'cartao_nacional_saude' => $dados_funcionario['cartao_nacional_saude'],
			                'gfip' => $dados_funcionario['gfip'],
			                'escolaridade' => $dados_funcionario['escolaridade'],
			                'raca' => (isset($dados_funcionario['raca'])) ? $dados_funcionario['raca'] : null,
		            	)
		        	);
	                
			        //atualiza os dados
			        if (!empty($funcionario)) {
			        	if($this->temDiferencaFuncionario($dados,$funcionario)) {
			        		
			        		// echo "Atualizar Funcionário"."\n";

			                $dados['Funcionario']['codigo'] = $funcionario['Funcionario']['codigo'];

				        	if (!$this->Funcionario->atualizar($dados)) {
				                $campos[$codigo_upload][] = 'Falha ao atualizar o Funcionário cpf = ('.$cpf.') :' . json_encode($this->Funcionario->validationErrors);
				                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

				                // debug($this->Funcionario->validationErrors);

				                $this->log_erros_int_cliente_funcionario($codigo, $retorno);
				                continue;
				            }
			        	}

			        }//inclui os dados
			        else {

			        	// echo "Incluindo funcionario: " . $dados_funcionario['nome']."\n";

			            if (!$this->Funcionario->incluir($dados)) {
			                $campos[$codigo_upload][] = 'Falha ao cadastrar o Funcionário cpf = ('.$cpf.') :' . json_encode($this->Funcionario->validationErrors);
			                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

			                $this->log_erros_int_cliente_funcionario($codigo, $retorno);
			                continue;
			            }

			            $dados['Funcionario']['codigo'] = $this->Funcionario->id;
			        }//fim inclusao atualizacao


			        //verifica se tem o codigo do funcionario
			        if(empty($dados['Funcionario']['codigo'])) {
			        	$campos[$codigo_upload][] = 'Falha para cadastrar/atualizar o Funcionário cpf = ('.$cpf.') :' . json_encode($this->Funcionario->validationErrors);
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cliente_funcionario($codigo, $retorno);
		                continue;
			        }

			        // debug($dados['Funcionario']['codigo']);
			        // debug($dados);



			        //endereco
			        //Se o campo cep do funcionario esta preenchido
			        if (!empty($dados_funcionario['cep'])) {

			            //Verifica se o CEP é valido
			            if (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $dados_funcionario['cep'])) {
			                // echo "Falha ao atualizar endereço do funcionário"."\n";
			                $campos[$codigo_upload][] = 'Falha ao atualizar endereço do funcionário: CPF ('.$cpf.') CEP inválido ('.$dados_funcionario['cep'].')';
			                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                
			                $this->log_erros_int_cliente_funcionario($codigo, $retorno);
			                continue;
			            }

			            $funcionario_endereco = $this->FuncionarioEndereco->find('first', array('conditions' => array(
			                'FuncionarioEndereco.codigo_empresa' => ((isset($_SESSION['Auth']['Usuario']['codigo_empresa'])) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : 1),
			                'FuncionarioEndereco.codigo_funcionario' => $dados['Funcionario']['codigo'],
			                'FuncionarioEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL
			            )));


			            //Se o funcionario nao possui endereco cadastrado
			            if (empty($funcionario_endereco)) {
			                // echo 'Incluir Endereço do Funcionário'."\n";

			                $dados_endereco = array('FuncionarioEndereco' => array(
			                    'codigo_endereco' => NULL,
			                    'codigo_funcionario' => $dados['Funcionario']['codigo'],
			                    'logradouro' =>  $dados_funcionario['logradouro'],
			                    'numero' => $dados_funcionario['numero'],
			                    'complemento' => $dados_funcionario['complemento'],
			                    'bairro' => $dados_funcionario['bairro'],
			                    'cidade' => $dados_funcionario['cidade'],
			                    'estado_abreviacao' => $dados_funcionario['estado'],
			                    'cep' => $dados_funcionario['cep'],
			                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
			                ));

			                if (!$this->FuncionarioEndereco->incluir($dados_endereco)) {
			                    echo "Falha ao incluir endereço do funcionário"."\n";
			                    $campos[$codigo_upload][] = 'Falha ao incluir endereço do funcionário: CPF ('.$cpf.')';
			                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                    
			                    $this->log_erros_int_cliente_funcionario($codigo, $retorno);
			                	continue;
			                }
			            } else {
			                if ($this->temDiferencaFuncionarioEndereco($funcionario_endereco, $dados_funcionario, $endereco = null)) {
			                    // echo "Atualizar Funcionário Endereço"."\n";
			                    $funcionario_endereco['FuncionarioEndereco']['numero'] = $dados_funcionario['numero'];
			                    $funcionario_endereco['FuncionarioEndereco']['complemento'] = $dados_funcionario['complemento'];
			                    $funcionario_endereco['FuncionarioEndereco']['logradouro'] = $dados_funcionario['logradouro'];
			                    $funcionario_endereco['FuncionarioEndereco']['bairro'] = $dados_funcionario['bairro'];
			                    $funcionario_endereco['FuncionarioEndereco']['cidade'] = $dados_funcionario['cidade'];
			                    $funcionario_endereco['FuncionarioEndereco']['estado_abreviacao'] = $dados_funcionario['estado'];
			                    $funcionario_endereco['FuncionarioEndereco']['cep'] = $dados_funcionario['cep'];
			                    $funcionario_endereco['FuncionarioEndereco']['codigo_endereco'] = NULL;

			                    if (!$this->FuncionarioEndereco->atualizar($funcionario_endereco)) {
			                        // echo "Falha ao atualizar endereço do funcionário"."\n";
			                        $campos[$codigo_upload][] = 'Falha ao atualizar endereço do funcionário CPF ('.$cpf.')';
			                        $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                        
			                        $this->log_erros_int_cliente_funcionario($codigo, $retorno);
			                		continue;
			                    }
			                } 
			                // // else {
			                //     echo "Funcionário Endereço sem atualização pendente"."\n";
			                // }
			            } //else atualizar endereco

			        } //if cep funcionario preenchido
			        
			        //contato telefonico
			        if (!empty($dados_funcionario['telefone'])) {
			            
			            $conditions_tel = array(
			                'FuncionarioContato.codigo_funcionario' => $dados['Funcionario']['codigo'],
			                'FuncionarioContato.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
			                'FuncionarioContato.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA,
			            );

			            $funcionario_contato = $this->FuncionarioContato->find('first', array('conditions' => $conditions_tel));
			            if (empty($funcionario_contato)) {
			                // echo 'Incluir Funcionário Contato Celular'."\n";
			                $dados_contato = array('FuncionarioContato' => array(
			                    'codigo_funcionario' => $dados['Funcionario']['codigo'],
			                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
			                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA,
			                    'descricao' => $dados_funcionario['ddd'].$dados_funcionario['telefone'],
			                    'nome' => $dados_funcionario['nome_contato_telefonico'],
			                    'autoriza_envio_sms' => 0, //$dados_funcionario['autoriza_envio_sms_funcionario'] == 'S' ? 1 : 0,
			                ));
			                if (!$this->FuncionarioContato->incluir($dados_contato)) {
			                    // echo "Falha ao incluir celular do funcionário"."\n";
			                    $campos[$codigo_upload][] = 'Falha ao incluir celular do funcionário: CPF ('.$cpf.')';
			                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                    
			                    $this->log_erros_int_cliente_funcionario($codigo, $retorno);
		                		continue;

			                }
			            } else {
			                if ($this->temDiferencaFuncionarioContatoCelular($funcionario_contato, $dados_funcionario)) {
			                    // echo 'Atualizar Funcionário Contato Celular'."\n";
			                    $funcionario_contato['FuncionarioContato']['descricao'] = $dados_funcionario['ddd'].$dados_funcionario['telefone'];
			                    $funcionario_contato['FuncionarioContato']['nome'] = $dados_funcionario['nome_contato_telefonico'];
			                    $funcionario_contato['FuncionarioContato']['autoriza_envio_sms'] = 0;// $funcionario['autoriza_envio_sms_funcionario'] == 'S' ? 1 : 0;
			                    if (!$this->FuncionarioContato->atualizar($funcionario_contato)) {
			                        // echo "Falha ao atualizar celular do funcionário"."\n";
			                        $campos[$codigo_upload][] = 'Falha ao atualizar celular do funcionário: CPF ('.$cpf.')';
			                        $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                        
			                        $this->log_erros_int_cliente_funcionario($codigo, $retorno);
		                			continue;
			                    }
			                } 
			                // else {
			                //     echo 'Funcionário Contato Celular sem atualização pendente'."\n";
			                // }
			            }
			        }

			        //contato email
			        if (!empty($dados_funcionario['email'])) {
			            
			            $conditions_mail = array(
			                'FuncionarioContato.codigo_funcionario' => $dados['Funcionario']['codigo'],
			                'FuncionarioContato.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
			                'FuncionarioContato.codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
			            );

			            $funcionario_contato = $this->FuncionarioContato->find('first', array('conditions' => $conditions_mail));
			            if (empty($funcionario_contato)) {
			                // echo 'Incluir Funcionário Contato Email'."\n";
			                $dados_email = array('FuncionarioContato' => array(
			                    'codigo_funcionario' => $dados['Funcionario']['codigo'],
			                    'codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
			                    'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL,
			                    'descricao' => $dados_funcionario['email'],
			                    'nome' => $dados_funcionario['nome_contato_email'],
			                    'autoriza_envio_email' => 0,//$dados_funcionario['autoriza_envio_email_func'] == 'S' ? 1 : 0,
			                ));
			                if (!$this->FuncionarioContato->incluir($dados_email)) {
			                    // echo "Falha ao incluir email do funcionário"."\n";
			                    $campos[$codigo_upload][] = 'Falha ao incluir email do funcionário: CPF ('.$cpf.')';
			                    $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

			                    $this->log_erros_int_cliente_funcionario($codigo,$retorno);
			                    continue;
			                }
			            } else {
			                if ($this->temDiferencaFuncionarioContatoEmail($funcionario_contato, $dados_funcionario)) {
			                    // echo 'Atualizar Funcionário Contato Email'."\n";
			                    $funcionario_contato['FuncionarioContato']['descricao'] = $dados_funcionario['email'];
			                    $funcionario_contato['FuncionarioContato']['nome'] = $dados_funcionario['nome_contato_email'];
			                    $funcionario_contato['FuncionarioContato']['autoriza_envio_email'] = 0; //$dados_funcionario['autoriza_envio_email_func'] == 'S' ? 1 : 0;

			                    if (!$this->FuncionarioContato->atualizar($funcionario_contato)) {
			                        // echo "Falha ao atualizar email do funcionário"."\n";
			                        $campos[$codigo_upload][] = 'Falha ao atualizar email do funcionário: CPF ('.$cpf.')';
			                        $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);
			                        
			                        $this->log_erros_int_cliente_funcionario($codigo,$retorno);
			                    	continue;
			                    }
			                } 
			                // else {
			                //     echo 'Funcionário Contato Email sem atualização pendente'."\n";
			                // }
			            }
			        }//fim email 

			        //muda o status para processado
			        //seta o staus que está processando os dados do arquivo
					$dadosFunc['IntClienteFuncionarios']['codigo_status_transferencia'] = 8; //importacao estrutura processado
					$this->atualizar($dadosFunc);
		        
		        }//fim foeach int_cliente_funcionario
		        
		        

			}//fim dados tabela vazio
		}//fim for loop_qtd

	    return $retorno;


	}//fim set_funcionario

	private function temDiferencaFuncionario($dados_stage, $dados) {

		if ($dados['Funcionario']['nome'] != $dados_stage['Funcionario']['nome']) return true;
        if ($dados['Funcionario']['sexo'] != $dados_stage['Funcionario']['sexo']) return true;
        if ($dados['Funcionario']['data_nascimento'] != $dados_stage['Funcionario']['data_nascimento']) return true;
        if ($dados['Funcionario']['estado_civil'] != $dados_stage['Funcionario']['estado_civil']) return true;
        if ($dados['Funcionario']['nit'] != $dados_stage['Funcionario']['nit']) return true;
        if ($dados['Funcionario']['rg'] != $dados_stage['Funcionario']['rg']) return true;
        if ($dados['Funcionario']['rg_uf'] != $dados_stage['Funcionario']['rg_uf']) return true;
        if ($dados['Funcionario']['rg_orgao'] != $dados_stage['Funcionario']['rg_orgao']) return true;
        if ($dados['Funcionario']['ctps'] != $dados_stage['Funcionario']['ctps']) return true;
        if ($dados['Funcionario']['ctps_serie'] != $dados_stage['Funcionario']['ctps_serie']) return true;
        if ($dados['Funcionario']['ctps_uf'] != $dados_stage['Funcionario']['ctps_uf']) return true;
        if ($dados['Funcionario']['gfip'] != $dados_stage['Funcionario']['gfip']) return true;
        if ($dados['Funcionario']['deficiencia'] != $dados_stage['Funcionario']['deficiencia']) return true;
        
        if($dados['Funcionario']['nome_mae'] != $dados_stage['Funcionario']['nome_mae']) return true;
        if($dados['Funcionario']['tipo_sangue'] != $dados_stage['Funcionario']['tipo_sangue']) return true;
        if($dados['Funcionario']['rg_data_emissao'] != $dados_stage['Funcionario']['rg_data_emissao']) return true;
        if($dados['Funcionario']['ctps_data_emissao'] != $dados_stage['Funcionario']['ctps_data_emissao']) return true;
        if($dados['Funcionario']['cartao_nacional_saude'] != $dados_stage['Funcionario']['cartao_nacional_saude']) return true;
        if($dados['Funcionario']['escolaridade'] != $dados_stage['Funcionario']['escolaridade']) return true;
        if($dados['Funcionario']['raca'] != $dados_stage['Funcionario']['raca']) return true;

        return false;

    }//FINAL FUNCTION temDiferencaCargo

	public function log_erros_int_cliente_funcionario($codigo, $erro)
	{

		$dados['IntClienteFuncionarios']['codigo'] = $codigo;
		$dados['IntClienteFuncionarios']['codigo_status_transferencia'] = 6; // estrutura falhou
		$dados['IntClienteFuncionarios']['observacao'] = json_encode($erro);

		$this->atualizar($dados);

	}//fimlog_erros_int_cliente_funcionario

	private function temDiferencaFuncionarioEndereco($funcionario_endereco, $funcionario, $endereco) {
        if ($funcionario_endereco['FuncionarioEndereco']['numero'] != $funcionario['numero']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['complemento'] != $funcionario['complemento']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['logradouro'] != $funcionario['logradouro']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['bairro'] != $funcionario['bairro']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['cidade'] != $funcionario['cidade']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['estado_abreviacao'] != $funcionario['estado']) return true;
        if ($funcionario_endereco['FuncionarioEndereco']['cep'] != $funcionario['cep']) return true;
        return false;
    }//FINAL FUNCTION temDiferencaFuncionarioEndereco

    private function temDiferencaFuncionarioContatoCelular($funcionario_contato, $funcionario) {
        if ($funcionario_contato['FuncionarioContato']['descricao'] != $funcionario['telefone']) return true;
        if ($funcionario_contato['FuncionarioContato']['nome'] != $funcionario['nome_contato_telefonico']) return true;
        return false;
    }//FINAL FUNCTION temDiferencaFuncionarioContatoCelular

    private function temDiferencaFuncionarioContatoEmail($funcionario_contato, $funcionario) {
        if ($funcionario_contato['FuncionarioContato']['descricao'] != $funcionario['email']) return true;
        if ($funcionario_contato['FuncionarioContato']['nome'] != $funcionario['nome_contato_email']) return true;
        return false;
    }//FINAL FUNCTION temDiferencaFuncionarioContatoEmail

}
