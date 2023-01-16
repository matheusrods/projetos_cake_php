<?php
class IntClienteSetores extends AppModel
{
	public $name          = 'IntClienteSetores';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_setores';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Setores";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_setores'));
	public $fillable      = array(
		'cnpj',
		'codigo_externo_setor',
		'descricao',
		'ativo',
	);


	public function set_setor($codigo_int_upload_cliente = null, $codigo_int_cliente_setor =null)
	{

		$this->log("ENTRANDO NOS SETORES A SEREM PROCESSADOS",'debug');

		//seta uma variável de erro
		$erros = array();
		
		$dadosIntClienteSetor = $this->getDadosIncluirAtualizar($codigo_int_upload_cliente);
		// debug($dadosIntClienteSetor);exit;

        $campos = array();
        $retorno = array();

		//verifica se existe registros
		if(!empty($dadosIntClienteSetor)) {

			$codigo_documento_aux = null;
	        $codigo_cliente = null;

	        $contador_exec_query_i = 0;//insert 
	        $contador_exec_query_a = 0;//atualizar
	        $contador_regs = 0;
	        $val_exec = 1000;

	        $insert_setor = "INSERT INTO RHHealth.dbo.setores(codigo_cliente,descricao,codigo_externo_setor,ativo, codigo_usuario_inclusao,codigo_empresa,data_inclusao) VALUES ";
	        $query_insert = "";
	        $atualizar_setor = "UPDATE RHHealth.dbo.setores SET ";
	        $query_atualizar = "";

	        $codigo_atualizar_inc = array();
	        $codigo_atualizar_atu = array();

	        //varre os setores da staging
	        foreach($dadosIntClienteSetor AS $dadosSetor) {

	        	// debug($dadosSetor);exit;

	        	$contador_regs++;

	        	$campos = array();
        		$retorno = array();

	        	$codigo_upload = $dadosSetor['0']['codigo_int_upload_cliente'];
				$codigo = $dadosSetor['0']['cs_codigo'];

	        	//verifica se tem descricao do setor
	        	$desc_setor = $dadosSetor['0']['setor_descricao'];
	        	if(empty($desc_setor)) {
	        		$campos[$codigo_upload][] = 'Dado carregado sem a descrição do Setor';
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cliente_setores($codigo, $retorno);
	                continue;
	        	}//fim erro de descricao


		        //pega o cliente do grupo economico
		        $codigo_cliente = $dadosSetor['0']['codigo_cliente_ge'];

		        //seta os dados do setor
	            $dados = array(
	                'codigo_cliente' => "'".$codigo_cliente."'",
	                'descricao' => "'".$desc_setor."'",
	                'codigo_externo_setor' => "'".$dadosSetor['0']['codigo_externo_setor']."'",
	                'ativo' => "'".$dadosSetor['0']['setor_atv']."'",
	            );	                
		        
	            if($dadosSetor['0']['setor_incluir'] == 1) {
		        	//seta o staus que está processando os dados do arquivo
		        	$codigo_atualizar_inc[] = $codigo;

	        		$contador_exec_query_i++;

	        		// if($dadosSetor['0']['setor_atv'] == 0) {
	        		// 	continue;
	        		// }

	                $dados['codigo_usuario_inclusao'] = "'".$dadosSetor['0']['codigo_usuario_inclusao']."'";
	                $dados['codigo_empresa'] = "'".$dadosSetor['0']['codigo_empresa']."'";
	            	$dados['data_inclusao'] = "'".date('Y-m-d H:i:s')."'";

	            	$query_insert .= $insert_setor . "(".implode(",",$dados).");\n";

	            	// $this->log($contador_exec_query_i,'debug');

	            	if($contador_exec_query_i == $val_exec) {

						// $query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
      					// $this->query($query_atualiza_status);
						

	            		$this->query($query_insert);
	            		$this->log("Setores: Inserindo {$contador_exec_query_i} setores",'debug');
	            		$query_insert = "";
	            		$contador_exec_query_i = 0;
						
						$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_inc = array();

	            	}

	            }
	            
	            if($dadosSetor['0']['setor_atualizar'] == 1) {
	        		
	        		//seta o staus que está processando os dados do arquivo
		        	$codigo_atualizar_atu[] = $codigo;

	        		$contador_exec_query_a++;

	            	$dados['codigo_usuario_alteracao'] = "'".$dadosSetor['0']['codigo_usuario_inclusao']."'";
	                $dados['data_alteracao'] = "'".date('Y-m-d H:i:s')."'";
	                $codigo_setor = $dadosSetor['0']['s_codigo'];

	                $set = "codigo_cliente = ".$dados['codigo_cliente'].",descricao = ".$dados['descricao'].",codigo_externo_setor = ".$dados['codigo_externo_setor'].",ativo = ".$dados['ativo'].",codigo_usuario_alteracao = ".$dados['codigo_usuario_alteracao'].", data_alteracao = ".$dados['data_alteracao'];
	            	
	            	$query_atualizar .= $atualizar_setor." {$set} WHERE codigo = {$codigo_setor};\n";

	            	if($contador_exec_query_a == $val_exec) {

	           			// $query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				// $this->query($query_atualiza_status);

	            		$this->query($query_atualizar);
	            		$this->log("Setores: Atualizando {$contador_exec_query_a} setores",'debug');
	            		$query_atualizar = "";
	            		$contador_exec_query_a = 0;

	            		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_atu = array();

	            	}

	            }
	            

		        // debug($setor);
		        // debug($dados);
		        // exit;
		        	        
	        }//fim foeach int_cliente_setor
	        
	        if(!empty($query_insert)) {

        		$this->query($query_insert);
        		$this->log("Inserindo {$contador_exec_query_i} setores",'debug');
        		$query_insert = "";
        		$contador_exec_query_i = 0;

        		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_inc = array();
        	}

        	if(!empty($query_atualizar)) {
        		$this->query($query_atualizar);
        		$this->log("Atualizando {$contador_exec_query_a} setores",'debug');
        		$query_atualizar = "";
        		$contador_exec_query_a = 0;

        		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_atu = array();
        	}
	        
        	//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);

		}//fim dados tabela vazio
		else {
			//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_setores SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);
		}

		$this->log("FINALIZANDO OS SETORES",'debug');

	    return $retorno;

	}//fim set_setor


	private function temDiferencaSetor($stage, $dado_banco) {

		if($stage['Setor']['descricao'] != $dado_banco['Setor']['descricao']) {
			return true;
		}

		if($stage['Setor']['codigo_externo_setor'] != $dado_banco['Setor']['codigo_externo_setor']) {
			return true;
		}

		if($stage['Setor']['ativo'] != $dado_banco['Setor']['ativo']) {
			return true;
		}

        return false;

    }//FINAL FUNCTION temDiferencaSetor

	public function log_erros_int_cliente_setores($codigo, $erro)
	{

		$int_cliente_setor['IntClienteSetores']['codigo'] = $codigo;
		$int_cliente_setor['IntClienteSetores']['codigo_status_transferencia'] = 6; // estrutura falhou
		$int_cliente_setor['IntClienteSetores']['observacao'] = json_encode($erro);

		$this->atualizar($int_cliente_setor);

	}//fim log_erros_int_cliente_setores

	/**
	 * [getDadosIncluirAtualizar metodo para buscar os dados carregados do upload e atualizar a base principal do cliente]
	 * @param  [int] $codigo_int_upload_cliente [codigo do upload para processamento]
	 * @return [array]                            [retornando os dados dos setores que devem ser atualizados/incluidos]
	 */
	public function getDadosIncluirAtualizar($codigo_int_upload_cliente)
	{

		$query = "
			WITH cteIntClienteSetores AS (
				select 
					cs.codigo as cs_codigo,
					cs.codigo_int_upload_cliente,
					cs.codigo_usuario_inclusao,
					cs.codigo_empresa,
					cli.codigo as codigo_cliente_cs,
					ge.codigo_cliente as codigo_cliente_ge,
					cs.codigo as codigo_int_cliente_setor,
					CAST(cs.descricao AS varchar(256)) as setor_descricao,
					cs.codigo_cliente as setor_codigo_cliente,
					cli.codigo_documento,
					cs.cnpj as setor_cnpj,
					CAST(cs.codigo_externo_setor AS varchar(256)) as codigo_externo_setor,
					cs.ativo as setor_ativo
				from int_cliente_setores cs
					INNER JOIN cliente cli on cs.cnpj = cli.codigo_documento
					inner join grupos_economicos_clientes gec  on cli.codigo = gec.codigo_cliente
					inner join grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo
				where cs.codigo_status_transferencia = 3
					AND cs.codigo_int_upload_cliente = {$codigo_int_upload_cliente}
			),
			cteSetorAtualizar AS (
				select 
					cs.*
					,s.ativo as s_ativo
					,(CASE WHEN s.ativo = cs.setor_ativo THEN s.ativo else cs.setor_ativo end) as setor_atv
					,s.codigo as s_codigo
					,'1' as setor_atualizar
					,'0' as setor_incluir
				from cteIntClienteSetores cs
					left join setores s on cs.codigo_cliente_ge = s.codigo_cliente
						and cs.setor_descricao = s.descricao
				where (s.ativo <> cs.setor_ativo or s.codigo_externo_setor <> cs.codigo_externo_setor)
			),
			cteSetorIncluir AS (
				select 
					cs.*
					,s.ativo as s_ativo
					,(CASE WHEN s.ativo = cs.setor_ativo THEN s.ativo else cs.setor_ativo end) as setor_atv
					,s.codigo as s_codigo
					,'0' as setor_atualizar
					,'1' as setor_incluir
				from cteIntClienteSetores cs
					left join setores s on cs.codigo_cliente_ge = s.codigo_cliente
						and cs.setor_descricao = s.descricao
				where s.codigo is null
			)
			select * from cteSetorAtualizar
			union all
			select * from cteSetorIncluir
		";

		$dados = $this->query($query);

		return $dados;

	}//fim getDadosIncluirAtualizar

}
