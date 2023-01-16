<?php
class IntClienteFe extends AppModel
{
	public $name          = 'IntClienteFe';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_funcionarios_empresa';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Funcionários x Empresa";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_funcionarios_empresa'));
	public $fillable      = array(
		'codigo_empresa',
		'codigo_cliente_funcionarios_empresa',
		'cpf',
		'data_admissao',
		'data_demissao',
		'matricula',
		'status_matricula',
		'numero_registro',
		'turno',
		'categoria_colaborador',
		'teletrabalho',
		'matricula_chefia_imediata',
		'numero_registro_chefia_imediata',
		'codigo_bu',
		'chave_tipo_afastamento',
		'cnpj',
		'codigo_externo_centro_resultado',
		'cnpj_alocado',
		'codigo_externo_setor',
		'codigo_externo_cargo',
		'cnpj_chefia_imediata',
		'data_inicio_cargo',
		'hora_inicio_afastameto',
		'hora_fim_afastamento',
		'data_inicio_afastameto',
		'data_fim_afastamento',
		'ativo',
	);

	public $status_matricula = array('AT'=> 1,'IN'=> 0,'AF'=> 3,'FE'=> 2);
	public $teletrabalho = array('S'=> 1,'N'=> 0);
	public $arr_cnpjs_codigo_cliente = array();

	/**
	 * [set_funcionario_empresa metodo para cadastrar e atualizar as matriculas e funções dos colaboradores nos clientes]
	 * @param [type] $codigo_int_upload_cliente [description]
	 * @param [type] $codigo_int_cfe            [description]
	 */
	public function set_funcionario_empresa($codigo_int_upload_cliente = null,$codigo_int_cfe = null)
	{
		//seta uma variável de erro
		$erros = array();
		
		//busca todos os arquivos de todos os cargos
		$conditions['codigo_status_transferencia'] = 3;
		// $conditions['ativo'] = 1;

		//para processar o arquivo inteiro
		if(!is_null($codigo_int_upload_cliente)) {
			$conditions['codigo_int_upload_cliente'] = $codigo_int_upload_cliente;
		}

		//para processar somente a linha
		if(!is_null($codigo_int_cfe)) {
			$conditions['codigo'] = $codigo_int_cfe;
		}

		//pegando todos os arquivos que estão prontos para processar status_transferencia 3
		$dadosICFE = $this->find('all', array("conditions" => $conditions ));

		$retorno = array();
		
		//verifica se existe registros
		if(!empty($dadosICFE)) {

			$this->IntUploadCliente =& ClassRegistry::init('IntUploadCliente');
			
			$this->Cliente =& ClassRegistry::init('Cliente');
	        $this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
	        $this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
	        $this->Funcionario =& ClassRegistry::init('Funcionario');
	        	        

	        //varre os setores da staging
	        foreach($dadosICFE AS $dadosFuncEmp) {

	        	// debug($dadosFuncEmp);

	        	############# PADRAO DAS INTS #############
		        
		        $campos = array();
		        $retorno = array();

	        	$dados_fe = $dadosFuncEmp['IntClienteFe'];
	        	$codigo_upload = $dadosFuncEmp['IntClienteFe']['codigo_int_upload_cliente'];

	        	//atualiza o codigo do arquivo na tabela upload 
				// $dados_int_upload_cliente['IntUploadCliente']['codigo'] = $dadosICFE[0]['codigo_int_upload_cliente'];
				// $this->IntUploadCliente->troca_status(4, $dados_int_upload_cliente);//incluindo estrutura


				$codigo = $dadosFuncEmp['IntClienteFe']['codigo'];

				//seta os dadosFuncEmp da sessao
				$_SESSION['Auth']['Usuario']['codigo'] 			= $dadosFuncEmp['IntClienteFe']['codigo_usuario_inclusao'];
				$_SESSION['Auth']['Usuario']['codigo_empresa']	= $dadosFuncEmp['IntClienteFe']['codigo_empresa'];

	        	//verifica se tem nome no centro de resultado
	        	$cpf = $dadosFuncEmp['IntClienteFe']['cpf'];
	        	if(empty($cpf)) {
	        		$campos[$codigo_upload][] = 'Dado carregado sem o CPF do Funcionário: '. $cpf;
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cfe($codigo, $retorno);
	                continue;
	        	}//fim erro de descricao

	        	//seta o staus que está processando os dados do arquivo
				$dadosFuncEmp['IntClienteFe']['codigo_status_transferencia'] = 4; //incluindo estrtutura
				$this->atualizar($dadosFuncEmp);


				############# FIM PADRAO DAS INTS #############


				############# CLIENTE FUNCIONARIO MATRICULA #############

				//busca o funcionario pela chave cpf
		        $conditions = array('Funcionario.cpf' => $cpf);
		        $funcionario = $this->Funcionario->find('first', array('conditions' => $conditions,'recursive' => -1));

		        if(empty($funcionario)) {
		        	$campos[$codigo_upload][] = 'CPF do Funcionário não encontrado: '. $cpf;
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cfe($codigo, $retorno);
	                continue;
		        }
		        //pega o codigo do funcionario
		        $codigo_funcionario = $funcionario['Funcionario']['codigo'];


		        //pega o codigo do cliente alocacao
	        	$cnpj_matriz = $dados_fe['cnpj'];
	        	//verifica se tem dentro do array o cnpj
	        	if(!isset($this->arr_cnpjs_codigo_cliente[$cnpj_matriz])) {
	        		//buscar o setor pela descricao e pelo cnpj do cliente
			        //pega o cliente do grupo economico
			        $cliente = $this->Cliente->find('first',array('fields' => array('codigo'),'conditions'=>array('codigo_documento' => $cnpj_matriz)));
			        $ge = $this->GrupoEconomico->obterCodigoMatrizPeloCodigoFilial($cliente['Cliente']['codigo']);

			        if(empty($ge)) {
			        	$campos[$codigo_upload][] = 'FuncionarioxEmpresa - CNPJ matriz não encontrado: {$cnpj_matriz}';
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cfe($codigo, $retorno);
		                continue;
			        }

			        $this->arr_cnpjs_codigo_cliente[$cnpj_matriz] = $ge[0]['codigo_cliente_matriz'];

	        	}//fim verificacao se existe cnpj alocacao
		        $codigo_cliente_matriz = $this->arr_cnpjs_codigo_cliente[$cnpj_matriz];

		        //cadastro de matricula
		        $matricula = $this->importarMatricula($dados_fe, $codigo_cliente_matriz, $codigo_funcionario);
		        if(!isset($matricula['codigo_matricula'])) {
		        	$retorno[$codigo_upload] = $matricula;
		        	continue;
		        }
		        $codigo_matricula = $matricula['codigo_matricula'];

		        ############# FIM CLIENTE FUNCIONARIO MATRICULA #############

		        ############# UNIDADE SETOR E CARGO FUNCIONARIO_SETORES_CARGOS #############
		        //funcao
		        $codigo_cliente_alocacao = null;
		        //busca o codigo_alocacao pelo cpnj_alocacao
		        $cnpj_alocado = $dados_fe['cnpj_alocado'];

		    	//verifica se tem dentro do array o cnpj
		    	if(!isset($this->arr_cnpjs_codigo_cliente[$cnpj_alocado])) {
		    		//buscar o setor pela descricao e pelo cnpj do cliente
			        //pega o cliente
			        $cliente = $this->Cliente->find('first',array('fields' => array('codigo'),'conditions'=>array('codigo_documento' => $cnpj_alocado)));
			        if(empty($cliente)) {
			        	$campos[$codigo_upload][] = "FuncionarioxEmpresa - CNPJ Alocacao não encontrado: {$cnpj_alocado}";
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cfe($codigo, $retorno);
		                continue;
			        }

			        $this->arr_cnpjs_codigo_cliente[$cnpj_alocado] = $cliente['Cliente']['codigo'];

		    	}//fim verificacao se existe cnpj alocacao
		        $codigo_cliente_alocacao = $this->arr_cnpjs_codigo_cliente[$cnpj_alocado];

		        $fsc = $this->importarSetorCargo($dados_fe, $codigo_cliente_matriz, $codigo_matricula, $codigo_cliente_alocacao, $codigo_funcionario);
		        // debug($fsc);
		        if(!isset($fsc['codigo_setor_cargo'])) {
		        	$retorno[$codigo_upload] = $fsc;
		        	continue;
		        }
		        $codigo_fsc = $fsc['codigo_setor_cargo'];

		        ############# FIM UNIDADE SETOR E CARGO FUNCIONARIO_SETORES_CARGOS #############		       

		        ############# ATESTADOS  #############
		        


		        ############## FIM ATESTADOS  #############

		        
		        //muda o status para processado
		        //seta o staus que está processando os dados do arquivo
				$dadosFuncEmp['IntClienteFe']['codigo_status_transferencia'] = 8; //importacao estrutura processado
				$this->atualizar($dadosFuncEmp);
	        
	        }//fim foeach int_cliente_funcionario
	        
	        //atualiza o codigo do arquivo na tabela upload 
			// $dados_int_upload_cliente['IntUploadCliente']['codigo'] = $dadosICFE[0]['codigo_int_upload_cliente'];
			// $this->IntUploadCliente->troca_status(8, $dados_int_upload_cliente);//importacao estrutura processado

		}//fim dados tabela vazio

	    return $retorno;

	}//fim set_funcionario_empresa

	public function log_erros_int_cfe($codigo, $erro)
	{

		$dados['IntClienteFe']['codigo'] = $codigo;
		$dados['IntClienteFe']['codigo_status_transferencia'] = 6; // estrutura falhou
		$dados['IntClienteFe']['observacao'] = json_encode($erro);

		$this->atualizar($dados);

	}//fimlog_erros_int_cfe

	/**
	 * [importarMatricula metodo para importar a matricula do colaborador vinculando ao grupo economico]
	 * @param  [type] $matricula              [description]
	 * @param  [type] $codigo_grupo_economico [description]
	 * @param  [type] $codigo_funcionario     [description]
	 * @return [type]                         [description]
	 */
	public function importarMatricula($matricula, $codigo_cliente_matriz, $codigo_funcionario) 
	{
        $ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');
        $CentroResultado =& ClassRegistry::init('CentroResultado');
        $Cliente =& ClassRegistry::init('Cliente');
        $Esocial =& ClassRegistry::init('Esocial');

        //var auxiliares
        $codigo_upload = $matricula['codigo_int_upload_cliente'];
		$codigo = $matricula['codigo'];

        $campos = array();
        $retorno = array();
        $cliente_funcionario = array();

        //buscar o centro de resultado pelo codigo_externo_centro_resultado
        $matricula['centro_custo'] = null;
        $matricula['codigo_centro_resultado'] = null;
        $matricula['codigo_cliente_bu'] = null;
		$matricula['codigo_cliente_ds'] = null;
		$matricula['codigo_cliente_opco'] = null;

        $centro_resultado = $CentroResultado->find('first',array('conditions' => array('codigo_externo_centro_resultado' => $matricula['codigo_externo_centro_resultado'])));

        if(!empty($centro_resultado)) {
	        $matricula['codigo_centro_resultado'] = $centro_resultado['CentroResultado']['codigo'];
	        $matricula['centro_custo'] = $centro_resultado['CentroResultado']['nome_centro_resultado'];

	        $matricula['codigo_cliente_bu'] = $centro_resultado['CentroResultado']['codigo_cliente_bu'];
	        $matricula['codigo_cliente_ds'] = $centro_resultado['CentroResultado']['codigo_cliente_ds'];
	        $matricula['codigo_cliente_opco'] = $centro_resultado['CentroResultado']['codigo_cliente_opco'];
	    }
        //fim centro de resultado
        
	    //pega o codigo do cliente da chefia imediata
        $codigo_cliente_chefia_imediata = null;
        //pega o codigo do cliente da chegia imediata
    	$cnpj_chefia_imediata = trim($matricula['cnpj_chefia_imediata']);

    	//verifica se tem dentro do array o cnpj
    	if($cnpj_chefia_imediata != '') {
	    	if(!isset($this->arr_cnpjs_codigo_cliente[$cnpj_chefia_imediata])) {
	    		//buscar o setor pela descricao e pelo cnpj do cliente
		        //pega o cliente
		        $cliente = $Cliente->find('first',array('fields' => array('codigo'),'conditions'=>array('codigo_documento' => $cnpj_chefia_imediata)));
		        if(empty($cliente)) {
		        	$campos[$codigo_upload][] = "FuncionarioxEmpresa - CNPJ da chefia imediata não encontrado: {$cnpj_chefia_imediata}";
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cfe($codigo, $retorno);
	                return $retorno;
		        }

		        $this->arr_cnpjs_codigo_cliente[$cnpj_chefia_imediata] = $cliente['Cliente']['codigo'];

	    	}//fim verificacao se existe cnpj alocacao
        	$codigo_cliente_chefia_imediata = $this->arr_cnpjs_codigo_cliente[$cnpj_chefia_imediata];
        }

        //categoria do colaborador pegar pelo codigo do esocial
        $codigo_esocial_01 = null;
        if(!empty($matricula['categoria_colaborador'])) {

        	//busca no esocial
        	$esocial = $Esocial->find('first',array('conditions' => array('codigo_descricao' => $matricula['categoria_colaborador'])));
        	if(!empty($esocial)) {
        		$codigo_esocial_01 = $esocial['Esocial']['codigo'];
        	}
        }
        //fim codigo_esocial_01
        
        $matricula_candidato = null;
        //busca o funcionario e vê sse ele tem uma matricula ativa como candidato para atualizar ela
		$conditions_candidato = array(
            'ClienteFuncionario.codigo_funcionario' => $codigo_funcionario,
            'ClienteFuncionario.codigo_cliente_matricula' => $codigo_cliente_matriz,
            'ClienteFuncionario.matricula_candidato' => 1,
            'ClienteFuncionario.ativo <> 0'
        );
        $cliente_funcionario_candidato = $ClienteFuncionario->find('first', array('conditions' => $conditions_candidato));

        if(empty($cliente_funcionario_candidato)) {
	        $conditions = array(
	            'ClienteFuncionario.codigo_funcionario' => $codigo_funcionario,
	            'ClienteFuncionario.codigo_cliente_matricula' => $codigo_cliente_matriz,
	            'ClienteFuncionario.matricula' => $matricula['matricula']
	        );
	        $cliente_funcionario = $ClienteFuncionario->find('first', array('conditions' => $conditions));
        }
        else {
        	$cliente_funcionario = $cliente_funcionario_candidato;
        	$matricula_candidato = 0;
        }

        $matricula['data_admissao'] = substr($matricula['data_admissao'],0,10);
        $matricula['data_demissao'] = substr($matricula['data_demissao'],0,10);

        if (empty($cliente_funcionario)) {
            echo "\n Incluir Matrícula" . "\n";
            
            $dados = array(
            	'codigo_cliente' => $codigo_cliente_matriz,
                'codigo_cliente_matricula' => $codigo_cliente_matriz,
                'codigo_funcionario' => $codigo_funcionario,
                'matricula' => $matricula['matricula'],
                'ativo' => $this->status_matricula[$matricula['status_matricula']],
                'admissao' => $matricula['data_admissao'],
                'data_demissao' => $matricula['data_demissao'],
                
                'centro_custo' => $matricula['centro_custo'],
                'codigo_centro_resultado' => $matricula['codigo_centro_resultado'],
                
                'codigo_cliente_bu' => $matricula['codigo_cliente_bu'],
                'codigo_cliente_ds' => $matricula['codigo_cliente_ds'],
                'codigo_cliente_opco' => $matricula['codigo_cliente_opco'],

                'numero_registro_cliente' => $matricula['numero_registro'],
                
                'codigo_cliente_chefia_imediata' => $codigo_cliente_chefia_imediata,
                'matricula_chefia_imediata' => $matricula['matricula_chefia_imediata'],
                'numero_registro_chefia_imediata' => $matricula['numero_registro_chefia_imediata'],
                
                'turno_importacao' => $matricula['turno'],
                'codigo_esocial_01' => $codigo_esocial_01,
            );

            if (!$ClienteFuncionario->incluir($dados)) {

                $msg_erro_matricula = "";
                if(!empty($ClienteFuncionario->validationErrors)){
                    $msg_erro_matricula = ": ".implode(",",$ClienteFuncionario->validationErrors);
                }
                echo "[1] Falha ao incluir matrícula"."\n";

            	$campos[$codigo_upload][] = 'Falha ao incluir matrícula :' . $msg_erro_matricula;
                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

                $this->log_erros_int_cfe($codigo, $retorno);
                
                return $retorno;
            }

            $cliente_funcionario['ClienteFuncionario']['codigo'] = $ClienteFuncionario->id;

        }
        else {

            if ($cliente_funcionario['ClienteFuncionario']['codigo_funcionario'] != $codigo_funcionario) {
                echo "Matrícula de outro funcionário"."\n";
                $campos[$codigo_upload][] = 'Matrícula de outro funcionário :' . $matricula['cpf'];
                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

                $this->log_erros_int_cfe($codigo, $retorno);
                
                return $retorno;

            }
            elseif ($cliente_funcionario['ClienteFuncionario']['codigo_cliente_matricula'] <> $codigo_cliente_matriz ) {
                /**
                 * Se o codigo_cliente_matricula da cliente_funcionario for
                 * diferente do codigo_cliente no grupo economico
                 * - não permitir continuar se a empresa for diferente com cnpj diferente
                 * - permitir continuar atualizando os dados do funcionario na cliente_funcionario, se for o mesmo
                 *  codigo_documento entre cliente_funcionario x grupo economico na tabela cliente e uma delas estiver inativa
                 */

                $conditions = array('Cliente.codigo' => $cliente_funcionario['ClienteFuncionario']['codigo_cliente_matricula']);
                // carrega os dados antigos na tabela cliente
                $cliente_1 = $Cliente->find('first', array('conditions' => $conditions));

                $conditions = array('Cliente.codigo' => $codigo_cliente_matriz);
                // carrega dados atuais na tabela cliente
                $cliente_2 = $Cliente->find('first', array('conditions' => $conditions));

                /**
                 * ATUALIZA EM CLIENTE_FUNCIONARIO nas condições:
                 * 1) se cliente com dados antigos estiver inativo
                 * 2) se cnpj for igual
                 */
                if( $cliente_1['Cliente']['ativo'] == 0
                    && ($cliente_1['Cliente']['codigo_documento'] == $cliente_2['Cliente']['codigo_documento'])){

                    $dataCF = array();
                    $dataCF['ClienteFuncionario']['codigo']                   = $cliente_funcionario['ClienteFuncionario']['codigo'];
                    $dataCF['ClienteFuncionario']['codigo_cliente']           = $codigo_cliente_matriz;
                    $dataCF['ClienteFuncionario']['codigo_cliente_matricula'] = $codigo_cliente_matriz;
                    $dataCF['ClienteFuncionario']['ativo']                    = 1;
                    $dataCF['ClienteFuncionario']['matricula']                = $cliente_funcionario['ClienteFuncionario']['matricula'];
                    $dataCF['ClienteFuncionario']['codigo_funcionario']       = $cliente_funcionario['ClienteFuncionario']['codigo_funcionario'];
                    $dataCF['ClienteFuncionario']['admissao']                 = $cliente_funcionario['ClienteFuncionario']['admissao'];

                    if(!$ClienteFuncionario->atualizar($dataCF)) {
                        
                        echo "Não foi possível atualizar"."\n";
                        echo "Matrícula do funcionário não corresponde a este grupo econômico"."\n";                        
		                $campos[$codigo_upload][] = 'Atualiza -> Matrícula do funcionário não corresponde a este grupo econômico :' . $matricula['cpf'];
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cfe($codigo, $retorno);
		                
		                return $retorno;

                    }

                    $conditions = array(
                        'ClienteFuncionario.codigo' => $cliente_funcionario['ClienteFuncionario']['codigo'],
                    );
                    // agora que atualizou os dados
                    // carrega novamente $cliente_funcionario pois lá
                    // embaixo vai retonar e devem ser o que acaba de ser atualizado
                    $cliente_funcionario = $ClienteFuncionario->find('first', array('conditions' => $conditions));


                } else {
                	
                    echo "else -> Matrícula do funcionário não corresponde a este grupo econômico"."\n";                        
	                $campos[$codigo_upload][] = 'Atualizar -> Matrícula do funcionário não corresponde a este grupo econômico :' . $matricula['cpf'];
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cfe($codigo, $retorno);
	                
	                return $retorno;
                }

            }
            else {

                if ($this->temDiferencaClienteFuncionario($cliente_funcionario, $matricula)) {

                    // Antes de inserir nuva matrica com chave externa,
                    // echo "\n Editando matricula! \n";
                    
                    $cliente_funcionario['ClienteFuncionario']['matricula'] = $matricula['matricula'];
                    $cliente_funcionario['ClienteFuncionario']['ativo'] = $this->status_matricula[$matricula['status_matricula']];
                    $cliente_funcionario['ClienteFuncionario']['admissao'] = $matricula['data_admissao'];
                    $cliente_funcionario['ClienteFuncionario']['data_demissao'] = $matricula['data_demissao'];
                	$cliente_funcionario['ClienteFuncionario']['codigo_centro_resultado'] = $matricula['codigo_centro_resultado'];
                    $cliente_funcionario['ClienteFuncionario']['centro_custo'] = $matricula['centro_custo'];
                    $cliente_funcionario['ClienteFuncionario']['turno_importacao'] = $matricula['turno'];
	                $cliente_funcionario['ClienteFuncionario']['codigo_cliente_bu'] = $matricula['codigo_cliente_bu'];
	                $cliente_funcionario['ClienteFuncionario']['codigo_cliente_ds'] = $matricula['codigo_cliente_ds'];
	                $cliente_funcionario['ClienteFuncionario']['codigo_cliente_opco'] = $matricula['codigo_cliente_opco'];
	                $cliente_funcionario['ClienteFuncionario']['numero_registro_cliente'] = $matricula['numero_registro'];
	                $cliente_funcionario['ClienteFuncionario']['codigo_cliente_chefia_imediata'] = $codigo_cliente_chefia_imediata;
	                $cliente_funcionario['ClienteFuncionario']['matricula_chefia_imediata'] = $matricula['matricula_chefia_imediata'];
	                $cliente_funcionario['ClienteFuncionario']['numero_registro_chefia_imediata'] = $matricula['numero_registro_chefia_imediata'];
	                $cliente_funcionario['ClienteFuncionario']['codigo_esocial_01'] = $codigo_esocial_01;
	                $cliente_funcionario['ClienteFuncionario']['matricula_candidato'] = $matricula_candidato;

                    if (!$ClienteFuncionario->atualizar($cliente_funcionario)) {
                        echo "Falha ao atualizar matrícula"."\n";

                        $msg_erro_matricula = "";
                        if(!empty($ClienteFuncionario->validationErrors)){
                            $msg_erro_matricula = ": ".implode(",",$ClienteFuncionario->validationErrors);
                        }
		                $campos[$codigo_upload][] = 'Falha ao atualizar matrícula'.$msg_erro_matricula;
		                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		                $this->log_erros_int_cfe($codigo, $retorno);
		                
		                return $retorno;
                    }
                }//fim temDiferencaCLienteFuncionario
            }
        }

        $retorno['codigo_matricula'] = $cliente_funcionario['ClienteFuncionario']['codigo'];
        return $retorno;
    }//FINAL FUNCTION importarMatricula

	private function temDiferencaClienteFuncionario($cliente_funcionario, $matricula) {

		// debug($cliente_funcionario);exit;
       
        if ($cliente_funcionario['ClienteFuncionario']['matricula'] != $matricula['matricula']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['ativo'] != $this->status_matricula[$matricula['status_matricula']]) return true;
        if ($cliente_funcionario['ClienteFuncionario']['admissao'] != $matricula['data_admissao']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['data_demissao'] != $matricula['data_demissao']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['codigo_centro_resultado'] != $matricula['codigo_centro_resultado']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['centro_custo'] != $matricula['centro_custo']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['numero_registro_cliente'] != $matricula['numero_registro']) return true;
		if ($cliente_funcionario['ClienteFuncionario']['matricula_chefia_imediata'] != $matricula['matricula_chefia_imediata']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['numero_registro_chefia_imediata'] != $matricula['numero_registro_chefia_imediata']) return true;
        
        if ($cliente_funcionario['ClienteFuncionario']['turno_importacao'] != $matricula['turno']) return true;
        if ($cliente_funcionario['ClienteFuncionario']['codigo_esocial_01'] != $matricula['categoria_colaborador']) return true;

        // if ($cliente_funcionario['ClienteFuncionario']['codigo_cargo_externo'] != $matricula['codigo_cargo_externo']) return true;
        

        return false;
    }//FINAL FUNCTION temDiferencaClienteFuncionario


    public function importarSetorCargo($setor_cargo, $codigo_cliente_matriz, $codigo_cliente_funcionario, $codigo_alocacao, $codigo_funcionario) 
    {

        $FuncionarioSetorCargo =& ClassRegistry::init('FuncionarioSetorCargo');
        $Setor =& ClassRegistry::init('Setor');
        $Cargo =& ClassRegistry::init('Cargo');
        $GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
        $GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
        $ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');
        $ClienteSetorCargo =& ClassRegistry::init('ClienteSetorCargo'); //hierarquia

        //var auxiliares
        $codigo_upload = $setor_cargo['codigo_int_upload_cliente'];
		$codigo = $setor_cargo['codigo'];

        $campos = array();
        $retorno = array();

        $setor_cargo['data_inicio_cargo'] = substr($setor_cargo['data_inicio_cargo'],0,10);

        //busca o setor pelo codigo externo
        $cargo = $Cargo->find('first', array('conditions' => array('Cargo.codigo_cliente' => $codigo_cliente_matriz,'Cargo.codigo_externo_cargo' => $setor_cargo['codigo_externo_cargo'])));
        if(empty($cargo)) {
        	//atribui erro
        	// echo "FSC Cargo nao encontrado"."\n";
            $campos[$codigo_upload][] = 'FSC Cargo com codigo externo nao encontrado para atualizar a funcao';
            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

            $this->log_erros_int_cfe($codigo, $retorno);
            
            return $retorno;
        }

        //busca o setores pelo codigo externo
        $setor = $Setor->find('first', array('conditions' => array('Setor.codigo_cliente' => $codigo_cliente_matriz,'Setor.codigo_externo_setor' => $setor_cargo['codigo_externo_setor'])));
        if(empty($setor)) {
        	//atribui erro
        	// echo "FSC Setor com codigo externo nao encontrado"."\n";
            $campos[$codigo_upload][] = 'FSC Setor com o codigo externo nao encontrado para atualizar a funcao';
            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

            $this->log_erros_int_cfe($codigo, $retorno);
            
            return $retorno;
        }

        ############# HIERARQUIA CLIENTE SETORES CARGOS #############
        //busca a hierarquia na tabela
        $hierarquia = $ClienteSetorCargo->find('first',array('conditions' => array('codigo_cliente_alocacao' => $codigo_alocacao,'codigo_setor' => $setor['Setor']['codigo'],'codigo_cargo' => $cargo['Cargo']['codigo'])));

        if(empty($hierarquia)) {
        	// echo "PREPARANDO PARA INCLUIR HIERARQUIA\n";
        	$dados_hierarquia = array(
        		'ClienteSetorCargo'=> array(
        			'codigo_cliente' => $codigo_alocacao,
        			'codigo_cliente_alocacao' => $codigo_alocacao,
        			'codigo_setor' => $setor['Setor']['codigo'],
        			'codigo_cargo' => $cargo['Cargo']['codigo']
        		)
        	);

        	//verifica se conseguiu incluir
        	if (!$ClienteSetorCargo->incluir($dados_hierarquia)) {
            	$msg_erro_hierarquia = "";
                if(!empty($ClienteSetorCargo->validationErrors)){
                    $msg_erro_hierarquia = ": ".implode(",",$ClienteSetorCargo->validationErrors);
                }
                
                // echo 'Falha ao incluir hierarquia: '.$msg_erro_hierarquia."\n";

				$campos[$codigo_upload][] = 'Falha ao incluir hierarquia: ' . $msg_erro_hierarquia;
	            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	            $this->log_erros_int_cfe($codigo, $retorno);
	            
	            return $retorno;
            }//fim validacao para incluir hierarquia

        }//fim validacao da hierarquia
		############# HIERARQUIA CLIENTE SETORES CARGOS #############
        
        $conditions = array('FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario);
        $fields = array(
            'MAX(FuncionarioSetorCargo.codigo) AS codigo',
        );
        $ultimo_setor_cargo = $FuncionarioSetorCargo->find('sql', array('conditions' => $conditions, 'fields' => $fields));
        $conditions = array(
            'FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario,
            "FuncionarioSetorCargo.codigo = ({$ultimo_setor_cargo})"
        );
        $FuncionarioSetorCargo->bindModel(array('belongsTo' => array(
            'Setor' => array('foreignKey' => 'codigo_setor'),
            'Cargo' => array('foreignKey' => 'codigo_cargo'),
        )));
        $funcionario_setor_cargo = $FuncionarioSetorCargo->find('first', array('conditions' => $conditions));
        
        $why = 0;

        if (empty($funcionario_setor_cargo)) {
            
            if (!empty($funcionario_setor_cargo)) {
                // echo 'Encontrado setor e cargo ativo, finalizar'."\n";

                $ultimo_fun_setor_cargo = $FuncionarioSetorCargo->read(null, $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo']);
                $data_inicio_cargo_anterior = $ultimo_fun_setor_cargo['FuncionarioSetorCargo']['data_inicio'];
                $data_fim_cargo = AppModel::dateToDbDate2($setor_cargo['data_inicio_cargo']).' -1 day';

                //Se a data de início do cargo anterior for maior que a data fim
                if(new DateTime(AppModel::dateToDbDate2($data_inicio_cargo_anterior)) > new DateTime(date('Y-m-d',strtotime($data_fim_cargo)))){
                    echo 'Falha ao finalizar cargo anterior - data de início maior que data final'."\n";
		            $campos[$codigo_upload][] = 'Falha ao finalizar cargo anterior - data de início maior que data final';
		            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		            $this->log_erros_int_cfe($codigo, $retorno);
		            
		            return $retorno;
                }


                $FuncionarioSetorCargo->set('data_fim', date('d/m/Y',strtotime($data_fim_cargo)));

                if (!$FuncionarioSetorCargo->save(null,false)) {
                    echo 'Falha ao finalizar cargo anterior'."\n";
					$campos[$codigo_upload][] = 'Falha ao finalizar cargo anterior';
		            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		            $this->log_erros_int_cfe($codigo, $retorno);
		            
		            return $retorno;
                }
            }

            // echo "Incluir Funcionario Setor Cargo"."\n";
            $dados = array('FuncionarioSetorCargo' => array(
                'codigo_cliente_funcionario' => $codigo_cliente_funcionario,
                'codigo_cliente_alocacao' => $codigo_alocacao,
                'codigo_setor' => $setor['Setor']['codigo'],
                'codigo_cargo' => $cargo['Cargo']['codigo'],
                'data_inicio' => AppModel::dateToDbDate($setor_cargo['data_inicio_cargo']),
                'teletrabalho' => $this->teletrabalho[$setor_cargo['teletrabalho']],

            ));

            if (!$FuncionarioSetorCargo->incluir($dados)) {
            	$msg_erro_matricula = "";
                if(!empty($FuncionarioSetorCargo->validationErrors)){
                    $msg_erro_matricula = ": ".implode(",",$FuncionarioSetorCargo->validationErrors);
                }
                
                // echo 'Falha ao incluir setor e cargo'.$msg_erro_matricula."\n";

				$campos[$codigo_upload][] = 'Falha ao incluir setor e cargo: ' . $msg_erro_matricula;
	            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	            $this->log_erros_int_cfe($codigo, $retorno);
	            
	            return $retorno;
            }
            $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo'] = $FuncionarioSetorCargo->id;
        
        }//fim verificacao inclusao 
        else {

            if ($this->temDiferencaFuncionarioSetorCargo($funcionario_setor_cargo, $codigo_alocacao, $setor['Setor']['codigo'], $cargo['Cargo']['codigo'], $setor_cargo)) {
                // echo "Atualizar Funcionario Setor Cargo"."\n";
                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $codigo_alocacao;
                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'] = $setor['Setor']['codigo'];
                $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo'] = $cargo['Cargo']['codigo'];
                $funcionario_setor_cargo['FuncionarioSetorCargo']['data_inicio'] = AppModel::dateToDbDate($setor_cargo['data_inicio_cargo']);
                $funcionario_setor_cargo['FuncionarioSetorCargo']['data_fim'] = AppModel::dateToDbDate($setor_cargo['data_demissao']);
                $funcionario_setor_cargo['FuncionarioSetorCargo']['teletrabalho'] = $this->teletrabalho[$setor_cargo['teletrabalho']];

                if (!$FuncionarioSetorCargo->atualizar($funcionario_setor_cargo)) {
                    $msg_erro_matricula = "";
	                if(!empty($FuncionarioSetorCargo->validationErrors)){
	                    $msg_erro_matricula = ": ".implode(",",$FuncionarioSetorCargo->validationErrors);
	                }
	                
	                // echo 'Falha ao atualizar setor e cargo'.$msg_erro_matricula."\n";

					$campos[$codigo_upload][] = 'Falha ao atualizar setor e cargo: ' . $msg_erro_matricula;
		            $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

		            $this->log_erros_int_cfe($codigo, $retorno);
		            
		            return $retorno;
                }
            } 
        }//fim verificacao atualizacao

        $retorno['codigo_setor_cargo'] = $funcionario_setor_cargo['FuncionarioSetorCargo']['codigo'];
        return $retorno;

    }//FINAL FUNCTION importarSetorCargo

    private function temDiferencaFuncionarioSetorCargo($funcionario_setor_cargo, $codigo_alocacao, $codigo_setor, $codigo_cargo, $dados) {
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cliente_alocacao'] != $codigo_alocacao) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_setor'] != $codigo_setor) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['codigo_cargo'] != $codigo_cargo) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['data_inicio'] != AppModel::dateToDbDate($dados['data_inicio_cargo'])) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['data_fim'] != AppModel::dateToDbDate($dados['data_demissao'])) return true;
        if ($funcionario_setor_cargo['FuncionarioSetorCargo']['teletrabalho'] != $this->teletrabalho[$dados['teletrabalho']]) return true;
        return false;
    }//FINAL FUNCTION temDiferencaFuncionarioSetorCargo
    
    /*public function importarAtestado($dados) 
    {
        
                    
	    echo "Processar registro ".$key."({$registro[0]['codigo']}) ".date('Y-m0d H:i:s')."\n";

	    
	    $retorno_tipo_afastamento = $this->ImportacaoAtestadosRegistros->importarTipoAfastamento($registro[0]);
	    $codigo_tipo_afastamento = $retorno_tipo_afastamento['codigo_tipo_afastamento'];
	    // if (!$codigo_tipo_afastamento) {}
	    
	    $registro[0]['tipo_afastamento'] = $codigo_tipo_afastamento;
	    
	    $retorno_motivo_licenca = $this->ImportacaoAtestadosRegistros->importarMotivoLicenca($registro[0]);
	    $codigo_motivo_licenca = $retorno_motivo_licenca['codigo_motivo_licenca'];
	    if (!$codigo_motivo_licenca) {
	        throw new Exception("Erro no Processamento do Motivo de Afastamento " . $retorno_motivo_licenca['invalidFields'], 1);
	    }
	    // echo "RETORNO MOTIVO LICENCA ".date('Y-m0d H:i:s')."\n";
	    
	    $especialidade = $registro[0]['especialidade1']? $registro[0]['especialidade1'] : '';

	    $retorno_conselho = $this->ImportacaoAtestadosRegistros->importarConselhoProfissional($registro[0]);
	    $codigo_conselho = $retorno_conselho['codigo_conselho'];
	    if (!$codigo_conselho) {
	        throw new Exception("Erro no Processamento do Conselho Profissional do Médico Solicitante " . $retorno_conselho['invalidFields'], 1);
	    }

	    $registro[0]['codigo_conselho'] = $codigo_conselho;
	    // echo "CONSELHO ".date('Y-m-d H:i:s')."\n";

	    if(!empty($registro[0]['especialidade1']) && !empty($registro[0]['especialidade2'])) {
	        $especialidade .= $registro[0]['especialidade1'] . ';' . $registro[0]['especialidade2'];
	    }

	    $registro[0]['especialidade'] = $especialidade;

	    $retorno_medico = $this->ImportacaoAtestadosRegistros->importarMedico($registro[0]);
	    $codigo_medico = $retorno_medico['codigo_medico'];
	    if (!$codigo_medico) {
	        throw new Exception("Erro no Processamento do Médico" . $retorno_medico['invalidFields'], 1);
	    }

	    // echo "MEDICO ".date('Y-m0d H:i:s')."\n";

	    //*** tratamento para poder achar o codigo esocial que foi informado na importacao para colocar corretamente no atestado
	    //referencia a tabela esocial
	    $Esocial = & ClassRegistry::init('Esocial');
	    $buscar_codigo_esocial = $Esocial->find('first',array('conditions' => array('tabela' => 18,'codigo_descricao' => $registro[0]['tabela_18_esocial'])));
	    //se achar o codigo esocial ele seta para incluir
	    if($buscar_codigo_esocial){
	        $codigo_motivo_esocial = $buscar_codigo_esocial['Esocial']['codigo'];
	    } else{
	        //senao vazio
	        $codigo_motivo_esocial = "";
	    }

	    $atestado['Atestado'] = array(
	        'codigo_cliente_funcionario'=> $registro[0]['codigo_cliente_funcionario'],
	        'data_afastamento_periodo'  => $registro[0]['data_inicio_afastamento'],
	        'data_retorno_periodo'      => $registro[0]['data_retorno_afastamento'],
	        'afastamento_em_horas'      => $registro[0]['horas'],
	        'hora_afastamento'          => $registro[0]['hora_inicio_afastamento'],
	        'hora_retorno'              => $registro[0]['hora_termino_afastamento'],
	        'afastamento_em_dias'       => $registro[0]['dias'],
	        'codigo_func_setor_cargo'   => $registro[0]['codigo_func_setor_cargo'],
	        'codigo_motivo_licenca'     => $codigo_motivo_licenca,
	        'codigo_medico'             => $codigo_medico,
	        'codigo_empresa'            => $importacao_atestados['ImportacaoAtestados']['codigo_empresa'],
	        'cpf'                       => $registro[0]['cpf'],
	        'codigo_cid'                => $registro[0]['codigo_cid'],
	        'dias'                      => $registro[0]['dias'],
	        'horas'                     => $registro[0]['horas'],
	        //este é o nome correto do campo da tabela de atestados
	        'codigo_motivo_esocial'     => $codigo_motivo_esocial,
	        // 'tabela_18_esocial'         => $registro[0]['tabela_18_esocial'],
	        'motivo_afastamento'        => $registro[0]['motivo_afastamento'],
	        'origem_retificacao'        => $registro[0]['origem_retificacao'],
	        'tipo_acidente_transito'    => $registro[0]['tipo_acidente_transito'],
	        'tipo_processo'             => $registro[0]['tipo_processo'],
	        'numero_processo'           => $registro[0]['numero_processo'],
	        'codigo_documento_entidade' => $registro[0]['codigo_documento_entidade'],
	        'onus_remuneracao'          => $registro[0]['onus_remuneracao'],
	        'onus_requisicao'           => $registro[0]['onus_requisicao']
	    );
	    
	    // echo "ENTRADA ATESTADO ".date('Y-m0d H:i:s')."\n";
	    $retorno_atestado = $this->ImportacaoAtestadosRegistros->importarAtestado($atestado);
	    // echo "RETORNO ATESTADO ".date('Y-m0d H:i:s')."\n";
	    $codigo_atestado = $retorno_atestado['codigo_atestado'];
	    if (!$codigo_atestado) {
	        throw new Exception("Erro no Processamento do Atestado " . $retorno_atestado['invalidFields'], 1);
	    }

	    // echo "LEITURA ATESTADO ".date('Y-m0d H:i:s')."\n";
	    $this->ImportacaoAtestadosRegistros->read(null, $registro[0]['codigo']);
	    $this->ImportacaoAtestadosRegistros->set('data_processamento', date('Y-m-d H:i:s'));

	    if($retorno_atestado['invalidFields'] || $retorno_medico['invalidFields'] || $retorno_conselho['invalidFields'] || $retorno_motivo_licenca['invalidFields'] || $retorno_tipo_afastamento['invalidFields']) {
	        $this->ImportacaoAtestadosRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
	        $this->ImportacaoAtestadosRegistros->set('observacao', $ex->getMessage());
	    } else {
	        $this->ImportacaoAtestadosRegistros->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
	        $this->log("Importado registro ".$key."({$registro[0]['codigo']})", 'debug');
	        // echo "Importado registro ".$key."({$registro[0]['codigo']})"."\n";
	       
	    }
	    // echo "COMMIT ATESTADO ".date('Y-m0d H:i:s')."\n";
	    
	    $this->ImportacaoAtestadosRegistros->save();
                    
                
            
        
    }*/


}
