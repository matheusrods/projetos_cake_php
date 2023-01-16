<?php
class IntClienteCentroResultado extends AppModel
{
	public $name          = 'IntClienteCentroResultado';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_centro_resultado';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Centro resultado";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_centro_resultado'));
	public $fillable      = array(
		'cnpj',
		'cnpj_alocacao',
		'codigo_externo_centro_resultado',
		'nome_centro_resultado',
		'codigo_bu',
		'nome_bu',
		'codigo_division',
		'nome_division',
		'codigo_depht_structure',
		'nome_depht_structure',
		'ativo'
	);

	/**
	 * [set_centro_resultado metodo para validar e subir os dados de centro de custo]
	 * @param [type] $codigo_int_upload_cliente           [description]
	 * @param [type] $codigo_int_cliente_centro_resultado [description]
	 */
	public function set_centro_resultado($codigo_int_upload_cliente = null, $codigo_int_cliente_centro_resultado =null)
	{

		$this->log("INICIANDO OS CENTRO DE RESULTADO",'debug');

		//seta uma variável de erro
		$erros = array();
				
		$this->setBuOpcoDs($codigo_int_upload_cliente);

		$dadosIntClienteCR = $this->getDadosIncluirAtualizar($codigo_int_upload_cliente);
		// debug($dadosIntClienteCR);exit;

		$campos = array();
		$retorno = array();

		//verifica se existe registros
		if(!empty($dadosIntClienteCR)) {


	        $arr_cnpjs_codigo_cliente = array();

	        $contador_exec_query_i = 0;//insert 
	        $contador_exec_query_a = 0;//atualizar
	        $contador_regs = 0;
	        $val_exec = 1000;

	        $insert = "INSERT INTO RHHealth.dbo.centro_resultado (codigo_cliente_matriz,codigo_cliente_alocacao,codigo_externo_centro_resultado,nome_centro_resultado,codigo_cliente_bu,codigo_cliente_opco,codigo_cliente_ds,ativo,codigo_empresa,codigo_usuario_inclusao,data_inclusao) VALUES ";
	        $query_insert = "";
	        $atualizar = "UPDATE RHHealth.dbo.centro_resultado SET ";
	        $query_atualizar = "";

	        $codigo_atualizar_inc = array();
	        $codigo_atualizar_atu = array();
	        
	        //varre os centro_resultado da staging
	        foreach($dadosIntClienteCR AS $dadosCR) {
		        $campos = array();
		        $retorno = array();

	        	//variavel auxiliar
	        	$codigo_cliente_matriz = $dadosCR['0']['codigo_cliente_matriz'];
	        	$codigo_cliente_alocacao = $dadosCR['0']['codigo_cliente_alocacao'];

	        	$codigo_upload = $dadosCR['0']['codigo_int_upload_cliente'];
				$codigo = $dadosCR['0']['codigo'];

				//seta os dadosCR da sessao
				$codigo_usuario_inclusao = $dadosCR['0']['codigo_usuario_inclusao'];
				$codigo_empresa	= $dadosCR['0']['codigo_empresa'];

	        	//verifica se tem nome no centro de resultado
	        	$nome_cr = $dadosCR['0']['nome_centro_resultado'];
	        	if(empty($nome_cr)) {
	        		$campos[$codigo_upload][] = 'Dado carregado sem o nome do Centro Resultado';
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cliente_centro_resultado($codigo, $retorno);
	                continue;
	        	}//fim erro de descricao


	        	//seta o staus que está processando os dados do arquivo
				// $dadosCR['IntClienteCentroResultado']['codigo_status_transferencia'] = 4; //incluindo estrtutura
				// $this->atualizar($dadosCR);


	        	// echo "ds: {$codigo_cliente_ds}\n";
	        	
	        	$codigo_cliente_bu = (!empty($dadosCR['0']['bu_codigo'])) ? "'".$dadosCR['0']['bu_codigo']."'" : "null";
	        	$codigo_cliente_opco = (!empty($dadosCR['0']['opco_codigo'])) ? "'".$dadosCR['0']['opco_codigo']."'" : "null";
	        	$codigo_cliente_ds = (!empty($dadosCR['0']['ds_codigo'])) ? "'".$dadosCR['0']['ds_codigo']."'" : "null";

	        	//gravar dados no centro de resultado com os ids da bu/opco/ds
		         //seta os dados do setor
	            $dados = array(
	               	'codigo_cliente_matriz' => "'".$codigo_cliente_matriz."'",
	                'codigo_cliente_alocacao' => "'".$codigo_cliente_alocacao."'",
	                'codigo_externo_centro_resultado' => "'".$dadosCR['0']['codigo_externo_centro_resultad']."'",
	                'nome_centro_resultado' => "'".$dadosCR['0']['nome_centro_resultado']."'",
	                'codigo_cliente_bu' => $codigo_cliente_bu,
	                'codigo_cliente_opco' => $codigo_cliente_opco,
	                'codigo_cliente_ds' => $codigo_cliente_ds,
	                'ativo' => "'".$dadosCR['0']['ativo']."'",
	            );
        		
		        if($dadosCR['0']['incluir'] == 1) {
		        	//seta o staus que está processando os dados do arquivo
		        	$codigo_atualizar_inc[] = $codigo;

	        		$contador_exec_query_i++;
	        		
	                $dados['codigo_empresa'] = "'".$codigo_empresa."'";
	                $dados['codigo_usuario_inclusao'] = "'".$codigo_usuario_inclusao."'";
	            	$dados['data_inclusao'] = "'".date('Y-m-d H:i:s')."'";

	            	$query_insert .= $insert . "(".implode(",",$dados).");\n";

	            	// $this->log($contador_exec_query_i,'debug');

	            	if($contador_exec_query_i == $val_exec) {

						// $query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
      //   				$this->query($query_atualiza_status);
						

	            		$this->query($query_insert);
	            		$this->log("CentroResultado: Inserindo {$contador_exec_query_i} centro_resultado",'debug');
	            		$query_insert = "";
	            		$contador_exec_query_i = 0;
						
						$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_inc = array();

	            	}

	            }
	            else if($dadosCR['0']['atualizar'] == 1) {
	        		
	            	$codigo_atualizar_atu[] = $codigo;

	        		$contador_exec_query_a++;

	            	$dados['codigo_usuario_alteracao'] = "'".$dadosCR['0']['codigo_usuario_inclusao']."'";
	                $dados['data_alteracao'] = "'".date('Y-m-d H:i:s')."'";
	                
	                $set = "codigo_cliente_matriz = ".$dados['codigo_cliente_matriz'].",codigo_cliente_alocacao = ".$dados['codigo_cliente_alocacao'].",codigo_externo_centro_resultado = ".$dados['codigo_externo_centro_resultado'].",nome_centro_resultado = ".$dados['nome_centro_resultado'].",codigo_cliente_bu = ".$dados['codigo_cliente_bu'].",codigo_cliente_opco = ".$dados['codigo_cliente_opco'].",codigo_cliente_ds=".$dados['codigo_cliente_ds'].",ativo =".$dados['ativo'].",codigo_usuario_alteracao=".$dados['codigo_usuario_alteracao'].", data_alteracao=".$dados['data_alteracao'];
	            	
	            	$query_atualizar .= $atualizar." {$set} WHERE codigo = {$codigo};\n";

	            	if($contador_exec_query_a == $val_exec) {

	            		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				$this->query($query_atualiza_status);

	            		$this->query($query_atualizar);
	            		$this->log("CentroResultado: Atualizando {$contador_exec_query_a} centro_resultado",'debug');
	            		$query_atualizar = "";
	            		$contador_exec_query_a = 0;

	            		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_atu = array();

	            	}

	            }
		        
	        }//fim foeach int_cliente_cargo
	        
	        if(!empty($query_insert)) {

				// $query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
				// $this->query($query_atualiza_status);
				

        		$this->query($query_insert);
        		$this->log("Inserindo {$contador_exec_query_i} centro_resultado",'debug');
        		$query_insert = "";
        		$contador_exec_query_i = 0;
				
				$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_inc = array();

        	}

        	if($contador_exec_query_a == $val_exec) {

    //     		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
				// $this->query($query_atualiza_status);

        		$this->query($query_atualizar);
        		$this->log("Atualizando {$contador_exec_query_a} centro_resultado",'debug');
        		$query_atualizar = "";
        		$contador_exec_query_a = 0;

        		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_atu = array();

        	}

        	//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);
	        
		}//fim dados tabela vazio
		else {
			//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_centro_resultado SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);
		}

		$this->log("FINALIZANDO OS CENTRO DE RESULTADO",'debug');

	    return $retorno;

	}//fim set_centro_resultado

	private function temDiferencaCR($dados_stage, $dados) {

		$diferente = false;

		if($dados_stage['CentroResultado']['codigo_cliente_matriz'] != $dados['CentroResultado']['codigo_cliente_matriz']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['codigo_cliente_alocacao'] != $dados['CentroResultado']['codigo_cliente_alocacao']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['nome_centro_resultado'] != $dados['CentroResultado']['nome_centro_resultado']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['codigo_externo_centro_resultado'] != $dados['CentroResultado']['codigo_externo_centro_resultado']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['ativo'] != $dados['CentroResultado']['ativo']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['codigo_cliente_bu'] != $dados['CentroResultado']['codigo_cliente_bu']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['codigo_cliente_opco'] != $dados['CentroResultado']['codigo_cliente_opco']) {
			$diferente = true;
		}

		if($dados_stage['CentroResultado']['codigo_cliente_ds'] != $dados['CentroResultado']['codigo_cliente_ds']) {
			$diferente = true;
		}
		
        return $diferente;

    }//FINAL FUNCTION temDiferencaCargo

	public function log_erros_int_cliente_centro_resultado($codigo, $erro)
	{

		$dados['IntClienteCentroResultado']['codigo'] = $codigo;
		$dados['IntClienteCentroResultado']['codigo_status_transferencia'] = 6; // estrutura falhou
		$dados['IntClienteCentroResultado']['observacao'] = json_encode($erro);

		$this->atualizar($dados);

	}//fimlog_erros_int_cliente_centro_resultado

	/**
	 * [set_cliente_bu metodo para verificar/gravar/atualizar e retornar o codigo de cliente_bu]
	 * @param [type] $params [description]
	 */
	public function set_cliente_bu_opco_ds($model, $params)
	{
		//instancia os metodos
		$this->$model =& ClassRegistry::init($model);

		$campos = array();
	    $retorno = array();

		//seta as variaveis
		$codigo_retorno = null;
		$codigo_upload = $params['codigo_upload'];
		$codigo = $params['codigo'];

		$codigo_cliente_matriz = $params['codigo_cliente'];
		$descricao = $params['descricao'];
		$codigo_cliente_externo = $params['codigo_cliente_externo'];

		//seta os dados
        $dados = array(
        	$model => array(
	            'codigo_cliente' => $codigo_cliente_matriz,
	            'codigo_cliente_externo' => $codigo_cliente_externo,
	            'descricao' => $descricao,
	            'ativo' => 1,
        	)
        );
        // debug($dados);

        //busca o setor na base de dados
        $conditions = array(
            'codigo_cliente' => $codigo_cliente_matriz,
            'codigo_cliente_externo' => $codigo_cliente_externo,
            'descricao' => $descricao
        );
        $busca_dados = $this->$model->find('first', compact('conditions'));

        // echo "busca dados: {$model}\n";debug($busca_dados);

        //atualiza os dados
        if (!empty($busca_dados)) {

	        $codigo_retorno = $busca_dados[$model]['codigo'];

        	if($this->temDiferencaBuOpcoDs($model,$dados,$busca_dados)) {

	        	$dados[$model]['codigo'] = $busca_dados[$model]['codigo'];

	        	if (!$this->$model->atualizar($dados)) {
	        		$campos[$codigo_upload] = $this->$model->validationErrors;
	                $campos[$codigo_upload][] = "Falha ao atualizar o {$model}";
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cliente_centro_resultado($codigo, $retorno);
	                continue;
	            }
        	}

        }//inclui os dados
        else {
        	// echo "aqui {$model}\n";debug($dados);
            if (!$this->$model->incluir($dados)) {
            	$campos[$codigo_upload] = $this->$model->validationErrors;
                $campos[$codigo_upload][] = "Falha ao cadastrar {$model}";
                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

                $this->log_erros_int_cliente_centro_resultado($codigo, $retorno);
                continue;
            }

            $codigo_retorno = $this->$model->id;

        }//fim inclusao atualizacao

        return $codigo_retorno;

	}//fim set_cliente_bu_opco_ds

	private function temDiferencaBuOpcoDs($model,$dados_stage, $dados) {

		$diferente = false;

		if($dados_stage[$model]['codigo_cliente'] != $dados[$model]['codigo_cliente']) {
			$diferente = true;
		}

		if($dados_stage[$model]['descricao'] != $dados[$model]['descricao']) {
			$diferente = true;
		}

		if($dados_stage[$model]['codigo_cliente_externo'] != $dados[$model]['codigo_cliente_externo']) {
			$diferente = true;
		}

        return $diferente;

    }//FINAL FUNCTION temDiferencaCargo

    /**
	 * [getDadosIncluirAtualizarBuOpcoDs metodo para buscar os dados carregados do upload e atualizar a base principal do cliente]
	 * @param  [int] $codigo_int_upload_cliente [codigo do upload para processamento]
	 * @return [array]                            [retornando os dados dos setores que devem ser atualizados/incluidos]
	 */
	public function getDadosIncluirAtualizarBuOpcoDs($codigo_int_upload_cliente)
	{

		$fields_int = "ge.codigo_cliente as codigo_cliente_matriz,cliAlo.codigo as codigo_cliente_alocacao,cr.codigo,cr.codigo_empresa,cr.codigo_cliente,cr.cnpj,cr.cnpj_alocacao,cr.codigo_externo_centro_resultado,cr.nome_centro_resultado,cr.codigo_bu,cr.nome_bu,cr.codigo_division,cr.nome_division,cr.codigo_depht_structure,cr.nome_depht_structure,cr.ativo,cr.data_inclusao,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente";

		$fields_bu = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_bu as codigo_externo,cr.nome_bu as nome_rel,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,bu.codigo as codigo_rel,bu.ativo as ativo_rel, 'bu' AS tipo";
		$group_bu = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_bu,cr.nome_bu,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,bu.codigo,bu.ativo";
		$fields_opco = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_division as codigo_externo,cr.nome_division as nome_rel,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,opco.codigo as codigo_rel,opco.ativo as ativo_rel, 'opco' AS tipo";
		$group_opco = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_division,cr.nome_division,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,opco.codigo,opco.ativo";
		$fields_ds = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_depht_structure as codigo_externo,cr.nome_depht_structure as nome_rel,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,ds.codigo as codigo_rel,ds.ativo as ativo_rel, 'ds' AS tipo";
		$group_ds = "cr.codigo_cliente_matriz,cr.codigo_empresa,cr.codigo_depht_structure,cr.nome_depht_structure,cr.ativo,cr.codigo_usuario_inclusao,cr.codigo_int_upload_cliente,ds.codigo,ds.ativo";

		$query = "
			with cteIntClienteCR AS (
				select 
					{$fields_int}
				from int_cliente_centro_resultado cr
					INNER JOIN cliente cli on cr.cnpj = cli.codigo_documento
					INNER JOIN grupos_economicos_clientes gec  on cli.codigo = gec.codigo_cliente
					INNER JOIN grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo
					INNER JOIN cliente cliAlo on cr.cnpj_alocacao = cliAlo.codigo_documento
				where cr.codigo_status_transferencia = 3
					AND cr.codigo_int_upload_cliente = {$codigo_int_upload_cliente}
			),
			cteClienteBuAtualizar AS (
				select 
					{$fields_bu}
					,'1' AS atualizar,'0' AS inserir
				from cteIntClienteCR cr
					INNER JOIN cliente_bu bu on cr.codigo_cliente_matriz = bu.codigo_cliente
						and bu.descricao = cr.nome_bu
						and bu.codigo_cliente_externo = cr.codigo_bu
				where (cr.nome_bu <> bu.descricao OR cr.codigo_bu <> bu.codigo_cliente_externo)
				GROUP BY {$group_bu}
			),
			cteClienteBuIncluir AS (
				select 
					{$fields_bu}
					,'0' AS atualizar,'1' AS inserir
				from cteIntClienteCR cr
					LEFT JOIN cliente_bu bu on cr.codigo_cliente_matriz = bu.codigo_cliente
						and bu.descricao = cr.nome_bu
						and bu.codigo_cliente_externo = cr.codigo_bu
				where cr.nome_bu <> '' AND bu.codigo is null
				GROUP BY {$group_bu}
			),
			cteClienteOpcoAtualizar AS (
				select 
					{$fields_opco}
					,'1' AS atualizar,'0' AS inserir
				from cteIntClienteCR cr
					INNER JOIN cliente_opco opco on cr.codigo_cliente_matriz = opco.codigo_cliente
						and opco.descricao = cr.nome_division
						and opco.codigo_cliente_externo = cr.codigo_division
				where (cr.nome_division <> opco.descricao OR cr.codigo_division <> opco.codigo_cliente_externo)
				GROUP BY {$group_opco}
			),
			cteClienteOpcoIncluir AS (
				select 
					{$fields_opco}
					,'0' AS atualizar,'1' AS inserir
				from cteIntClienteCR cr
					LEFT JOIN cliente_opco opco on cr.codigo_cliente_matriz = opco.codigo_cliente
						and opco.descricao = cr.nome_division
						and opco.codigo_cliente_externo = cr.codigo_division
				where cr.nome_division <> '' AND opco.codigo is null
				GROUP BY {$group_opco}
			),
			cteClienteDsAtualizar AS (
				select 
					{$fields_ds}
					,'1' AS atualizar,'0' AS inserir
				from cteIntClienteCR cr
					INNER JOIN cliente_ds ds on cr.codigo_cliente_matriz = ds.codigo_cliente
						and ds.descricao = cr.nome_depht_structure
						and ds.codigo_cliente_externo = cr.codigo_depht_structure
				where (cr.nome_depht_structure <> ds.descricao OR cr.codigo_depht_structure <> ds.codigo_cliente_externo)
				GROUP BY {$group_ds}
			),
			cteClienteDsIncluir AS (
				select 
					{$fields_ds}
					,'0' AS atualizar,'1' AS inserir
				from cteIntClienteCR cr
					LEFT JOIN cliente_ds ds on cr.codigo_cliente_matriz = ds.codigo_cliente
						and ds.descricao = cr.nome_depht_structure
						and ds.codigo_cliente_externo = cr.codigo_depht_structure
				where cr.nome_depht_structure <> '' AND ds.codigo is null
				GROUP BY {$group_ds}
			)

			select * from cteClienteBuAtualizar
			union all 
			select * from cteClienteBuIncluir
			union all
			select * from cteClienteOpcoAtualizar
			union all 
			select * from cteClienteOpcoIncluir
			union all
			select * from cteClienteDsAtualizar
			union all 
			select * from cteClienteDsIncluir
		";

		// debug($query);exit;
		$dados = $this->query($query);

		return $dados;

	}//fim getDadosIncluirAtualizarBuOpcoDs

	/**
	 * metodo para inserir/atualizar bu/opco/ds
	 */
	public function setBuOpcoDs($codigo_int_upload_cliente)
	{
		$this->log("INICIANDO OS BU_OPCO_DS",'debug');

		$dados_bu_opco_ds = $this->getDadosIncluirAtualizarBuOpcoDs($codigo_int_upload_cliente);

		// debug($dados_bu_opco_ds);exit;

		$campos = array();
		$retorno = array();

		//verifica se existe registros
		if(!empty($dados_bu_opco_ds)) {

	        $arr_cnpjs_codigo_cliente = array();

	        $insert_bu = "INSERT INTO RHHealth.dbo.cliente_bu (codigo_cliente, codigo_cliente_externo, descricao, ativo, codigo_empresa, codigo_usuario_inclusao, data_inclusao) VALUES ";
	        $insert_opco = "INSERT INTO RHHealth.dbo.cliente_opco (codigo_cliente, codigo_cliente_externo, descricao, ativo, codigo_empresa, codigo_usuario_inclusao, data_inclusao) VALUES ";
	        $insert_ds = "INSERT INTO RHHealth.dbo.cliente_ds (codigo_cliente, codigo_cliente_externo, descricao, ativo, codigo_empresa, codigo_usuario_inclusao, data_inclusao) VALUES ";

	        $query_insert = "";

	        $altualizar_bu = "UPDATE RHHealth.dbo.cliente_bu SET ";
	        $altualizar_opco = "UPDATE RHHealth.dbo.cliente_opco SET ";
	        $altualizar_ds = "UPDATE RHHealth.dbo.cliente_ds SET ";

	        $query_atualizar = "";

	        $contador_exec_query_i = 0;//insert 
	        $contador_exec_query_a = 0;//atualizar
	        $contador_regs = 0;
	        $val_exec = 1000;
			
			$codigo_atualizar = array();
	        
	        //varre os setores da staging
	        foreach($dados_bu_opco_ds AS $dadosBuOpcoDs) {
		        $campos = array();
		        $retorno = array();

		        $contador_regs++;

     	        $codigo_upload = $dadosBuOpcoDs['0']['codigo_int_upload_cliente'];
     	        // $codigo = $dadosBuOpcoDs['0']['ccodigo'];

				//seta os dados
		        $dados = array(
		            'codigo_cliente' => "'".$dadosBuOpcoDs['0']['codigo_cliente_matriz']."'",
		            'codigo_cliente_externo' => "'".$dadosBuOpcoDs['0']['codigo_externo']."'",
		            'descricao' => "'".$dadosBuOpcoDs['0']['nome_rel']."'",
		            'ativo' => "1",
		        );

		        if($dadosBuOpcoDs['0']['inserir'] == 1) {

		        	$contador_exec_query_i++;

		        	$dados['codigo_empresa'] = "'".$dadosBuOpcoDs['0']['codigo_empresa']."'";
		        	$dados['codigo_usuario_inclusao'] = "'".$dadosBuOpcoDs['0']['codigo_usuario_inclusao']."'";
	                $dados['data_inclusao'] = "'".date('Y-m-d H:i:s')."'";

		        	switch ($dadosBuOpcoDs[0]['tipo']) {
		        		case 'bu':
		        			$query_insert .= $insert_bu . "(".implode(",",$dados).");\n";
		        			break;
		        		case 'opco':
		        			$query_insert .= $insert_opco . "(".implode(",",$dados).");\n";
		        			break;
		        		case 'ds':
		        			$query_insert .= $insert_ds . "(".implode(",",$dados).");\n";
		        			break;
		        	}//fim switch

		        	if($contador_exec_query_i == $val_exec) {
	            		$this->query($query_insert);
	            		$this->log("Inserindo {$contador_exec_query_i} BU_OPCO_DS",'debug');
	            		$query_insert = "";
	            		$contador_exec_query_i = 0;
	            	}//fim if

		        }
		        else if($dadosBuOpcoDs['0']['atualizar'] == 1) {
		        	$contador_exec_query_a++;

		        	$dados['codigo_usuario_alteracao'] = "'".$dadosBuOpcoDs['0']['codigo_usuario_inclusao']."'";
	                $dados['data_alteracao'] = "'".date('Y-m-d H:i:s')."'";
	                
	                $codigo = $dadosBuOpcoDs['0']['codigo_rel'];

	                $set = "codigo_cliente = ".$dados['codigo_cliente'].",descricao = ".$dados['descricao'].",codigo_cliente_externo = ".$dados['codigo_cliente_externo'].",ativo = ".$dados['ativo'].",codigo_usuario_alteracao = ".$dados['codigo_usuario_alteracao'].", data_alteracao = ".$dados['data_alteracao'];

		        	switch ($dadosBuOpcoDs[0]['tipo']) {
		        		case 'bu':
		        			$query_atualizar .= $altualizar_bu." {$set} WHERE codigo = {$codigo};\n";
		        			break;
		        		case 'opco':
		        			$query_atualizar .= $altualizar_opco." {$set} WHERE codigo = {$codigo};\n";
		        			break;
		        		case 'ds':
		        			$query_atualizar .= $altualizar_ds." {$set} WHERE codigo = {$codigo};\n";
		        			break;
		        	}//fim switch

		        	if($contador_exec_query_a == $val_exec) {
	            		$this->query($query_atualizar);
	            		$this->log("Atualizando {$contador_exec_query_a} BU_OPCO_DS",'debug');
	            		$query_atualizar = "";
	            		$contador_exec_query_a = 0;
	            	}//fim atualizacao

		        }
		
		    }//fim foreach

		    if(!empty($query_insert)) {

		    	// $this->log($query_insert,'debug');

        		$this->query($query_insert);
        		$this->log("Inserindo {$contador_exec_query_i} BU_OPCO_DS",'debug');
        		$query_insert = "";
        		$contador_exec_query_i = 0;
        	}//fim if

        	if(!empty($query_atualizar)) {

        		// $this->log($query_atualizar,'debug');

        		$this->query($query_atualizar);
        		$this->log("Atualizando {$contador_exec_query_a} BU_OPCO_DS",'debug');
        		$query_atualizar = "";
        		$contador_exec_query_a = 0;
        	}//fim atualizacao
		
		}//fim if dados_bu_opco_ds

		$this->log("FINALIZANDO OS BU_OPCO_DS",'debug');
	
	}// fim setbuOpcpDs


	/**
	 * [getDadosIncluirAtualizar metodo para buscar os dados carregados do upload e atualizar a base principal do cliente]
	 * @param  [int] $codigo_int_upload_cliente [codigo do upload para processamento]
	 * @return [array]                            [retornando os dados dos setores que devem ser atualizados/incluidos]
	 */
	public function getDadosIncluirAtualizar($codigo_int_upload_cliente)
	{

		$query = "
			WITH cteIntClienteCR AS
				  (SELECT ge.codigo_cliente AS codigo_cliente_matriz,
				          cliAlo.codigo AS codigo_cliente_alocacao,
				          cr.codigo,
				          cr.codigo_empresa,
				          cr.codigo_cliente,
				          cr.cnpj,
				          cr.cnpj_alocacao,
				          cr.codigo_externo_centro_resultado,
				          cr.nome_centro_resultado,
				          cr.codigo_bu,
				          cr.nome_bu,
				          cr.codigo_division,
				          cr.nome_division,
				          cr.codigo_depht_structure,
				          cr.nome_depht_structure,
				          cr.ativo,
				          cr.data_inclusao,
				          cr.codigo_usuario_inclusao,
				          cr.codigo_int_upload_cliente
				   FROM int_cliente_centro_resultado cr
					   INNER JOIN cliente cli ON cr.cnpj = cli.codigo_documento
					   INNER JOIN grupos_economicos_clientes gec ON cli.codigo = gec.codigo_cliente
					   INNER JOIN grupos_economicos ge ON gec.codigo_grupo_economico = ge.codigo
					   INNER JOIN cliente cliAlo ON cr.cnpj_alocacao = cliAlo.codigo_documento
				   WHERE cr.codigo_status_transferencia = 3
				     AND cr.codigo_int_upload_cliente = {$codigo_int_upload_cliente}
				),
				cteCentroResultadoAtualizar AS (
					select
						icr.*
						,cr.codigo as cr_codigo
						,bu.codigo as bu_codigo
				        ,opco.codigo as opco_codigo
				        ,ds.codigo as ds_codigo
						,'1' as atualizar
						,'0' as incluir
					from cteIntClienteCR icr 
						inner join centro_resultado cr on icr.codigo_cliente_matriz = cr.codigo_cliente_matriz
							and icr.codigo_cliente_alocacao = cr.codigo_cliente_alocacao
							and icr.codigo_externo_centro_resultado = cr.codigo_externo_centro_resultado
							and icr.nome_centro_resultado = icr.nome_centro_resultado
						inner join cliente_bu bu on bu.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_bu = bu.codigo_cliente_externo 
							and icr.nome_bu = bu.descricao
						inner join cliente_opco opco on opco.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_division = opco.codigo_cliente_externo 
							and icr.nome_division = opco.descricao
						inner join cliente_ds ds on ds.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_depht_structure = ds.codigo_cliente_externo 
							and icr.nome_depht_structure = ds.descricao
					WHERE (icr.ativo <> cr.ativo OR icr.codigo_externo_centro_resultado <> cr.codigo_externo_centro_resultado OR icr.nome_centro_resultado <> cr.nome_centro_resultado)
				),
				cteCentroResultadoIncluir AS (
					select
						icr.*
						,cr.codigo as cr_codigo
						,bu.codigo as bu_codigo
				        ,opco.codigo as opco_codigo
				        ,ds.codigo as ds_codigo
						,'0' as atualizar
						,'1' as incluir
					from cteIntClienteCR icr 
						left join centro_resultado cr on icr.codigo_cliente_matriz = cr.codigo_cliente_matriz
							and icr.codigo_cliente_alocacao = cr.codigo_cliente_alocacao
							and icr.codigo_externo_centro_resultado = cr.codigo_externo_centro_resultado
							and icr.nome_centro_resultado = icr.nome_centro_resultado
						left join cliente_bu bu on bu.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_bu = bu.codigo_cliente_externo 
							and icr.nome_bu = bu.descricao
						left join cliente_opco opco on opco.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_division = opco.codigo_cliente_externo 
							and icr.nome_division = opco.descricao
						left join cliente_ds ds on ds.codigo_cliente = icr.codigo_cliente_matriz 
							and icr.codigo_depht_structure = ds.codigo_cliente_externo 
							and icr.nome_depht_structure = ds.descricao
					where cr.codigo is null
				)

				select * from cteCentroResultadoAtualizar
				union all 
				select * from cteCentroResultadoIncluir
		";

		$dados = $this->query($query);

		return $dados;

	}//fim getDadosIncluirAtualizar

}
