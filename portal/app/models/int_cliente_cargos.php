<?php

class IntClienteCargos extends AppModel
{
	public $name          = 'IntClienteCargos';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_cargos';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Cargos";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_cargos'));
	public $fillable      = array(
		'cnpj',
		'codigo_externo_cargo',
		'descricao',
		'cbo',
		'ativo'
	);

	public function set_cargo($codigo_int_upload_cliente = null, $codigo_int_cliente_cargos =null)
	{
		$this->log("ENTRANDO NOS Cargos A SEREM PROCESSADOS",'debug');

		//seta uma variável de erro
		$erros = array();
		
		$dadosIntClienteCargo = $this->getDadosIncluirAtualizar($codigo_int_upload_cliente);

        $campos = array();
        $retorno = array();

		//verifica se existe registros
		if(!empty($dadosIntClienteCargo)) {

			$this->Cliente =& ClassRegistry::init('Cliente');
	        $this->Cargo =& ClassRegistry::init('Cargo');
	        $this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
	        
	        $codigo_documento_aux = null;
	        $codigo_cliente = null;

	        $contador_exec_query_i = 0;//insert 
	        $contador_exec_query_a = 0;//atualizar
	        $contador_regs = 0;
	        $val_exec = 1000;

	        $insert = "INSERT INTO RHHealth.dbo.cargos(codigo_cliente,descricao,codigo_externo_cargo,ativo,codigo_cbo, codigo_usuario_inclusao,codigo_empresa,data_inclusao) VALUES ";
	        $query_insert = "";
	        $atualizar = "UPDATE RHHealth.dbo.cargos SET ";
	        $query_atualizar = "";

	        $codigo_atualizar_inc = array();
	        $codigo_atualizar_atu = array();

	        //varre os cargos da staging
	        foreach($dadosIntClienteCargo AS $dadosCargo) {

	        	$campos = array();
        		$retorno = array();

	        	$codigo_upload = $dadosCargo['0']['codigo_int_upload_cliente'];
				$codigo = $dadosCargo['0']['cc_codigo'];

	        	//verifica se tem descricao do setor
	        	$desc_cargo = $dadosCargo['0']['cargo_descricao'];
	        	if(empty($desc_cargo)) {
	        		$campos[$codigo_upload][] = 'Dado carregado sem a descrição do Cargo';
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cliente_cargos($codigo, $retorno);
	                continue;
	        	}//fim erro de descricao

	        	$codigo_cliente = $dadosCargo['0']['codigo_cliente_ge'];

		        //seta os dados do setor
	            $dados = array(
	                'codigo_cliente' => "'".$codigo_cliente."'",
	                'descricao' => "'".$desc_cargo."'",
	                'codigo_externo_cargo' => "'".$dadosCargo['0']['codigo_externo_cargo']."'",
	                'ativo' => "'".$dadosCargo['0']['cargo_atv']."'",
	                'codigo_cbo' => "'".$dadosCargo['0']['cargo_cbo']."'",
	            );

	            if($dadosCargo['0']['cargo_incluir'] == 1) {

		        	//seta o staus que está processando os dados do arquivo
		        	$codigo_atualizar_inc[] = $codigo;

	        		$contador_exec_query_i++;

	        		// if($dadosCargo['0']['cargo_atv'] == 0) {
	        		// 	continue;
	        		// }

	                $dados['codigo_usuario_inclusao'] = "'".$dadosCargo['0']['codigo_usuario_inclusao']."'";
	                $dados['codigo_empresa'] = "'".$dadosCargo['0']['codigo_empresa']."'";
	            	$dados['data_inclusao'] = "'".date('Y-m-d H:i:s')."'";

	            	$query_insert .= $insert . "(".implode(",",$dados).");\n";

	            	// $this->log($contador_exec_query_i,'debug');

	            	if($contador_exec_query_i == $val_exec) {

						// $query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
      //   				$this->query($query_atualiza_status);
						

	            		$this->query($query_insert);
	            		$this->log("Cargos: Inserindo {$contador_exec_query_i} Cargos",'debug');
	            		$query_insert = "";
	            		$contador_exec_query_i = 0;
						
						$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_inc = array();

	            	}

	            }
	            else if($dadosCargo['0']['cargo_atualizar'] == 1) {
	        		
	        		$codigo_atualizar_atu[] = $codigo;

	        		$contador_exec_query_a++;

	            	$dados['codigo_usuario_alteracao'] = "'".$dadosCargo['0']['codigo_usuario_inclusao']."'";
	                $dados['data_alteracao'] = "'".date('Y-m-d H:i:s')."'";
	                $codigo_cargo = $dadosCargo['0']['c_codigo'];

	                $set = "codigo_cliente = ".$dados['codigo_cliente'].",descricao = ".$dados['descricao'].",codigo_externo_cargo = ".$dados['codigo_externo_cargo'].",ativo = ".$dados['ativo'].", codigo_cbo = ".$dados['codigo_cbo']." ,codigo_usuario_alteracao = ".$dados['codigo_usuario_alteracao'].", data_alteracao = ".$dados['data_alteracao'];
	            	
	            	$query_atualizar .= $atualizar." {$set} WHERE codigo = {$codigo_cargo};\n";

	            	if($contador_exec_query_a == $val_exec) {

	           //  		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 4 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				// $this->query($query_atualiza_status);

	            		$this->query($query_atualizar);
	            		$this->log("Cargos: Atualizando {$contador_exec_query_a} Cargos",'debug');
	            		$query_atualizar = "";
	            		$contador_exec_query_a = 0;

	            		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
        				$this->query($query_atualiza_status);

        				$codigo_atualizar_atu = array();

	            	}

	            }
	            else {
	            	$campos[$codigo_upload][] = 'Não foi possivel atualizar ou incluir o dado, favor entrar em contato com o administrador.';
	                $retorno[$codigo_upload]['invalidFields'] = implode(',', $campos[$codigo_upload]);

	                $this->log_erros_int_cliente_cargos($codigo, $retorno);
	                continue;
	            }
	        
	        }//fim foeach int_cliente_cargo
	        
	        if(!empty($query_insert)) {

        		$this->query($query_insert);
        		$this->log("Cargos: Inserindo {$contador_exec_query_i} cargos",'debug');
        		$query_insert = "";
        		$contador_exec_query_i = 0;

        		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_inc).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_inc = array();

        	}

        	if(!empty($query_atualizar)) {
        		$this->query($query_atualizar);
        		$this->log("Cargos: Atualizando {$contador_exec_query_a} cargos",'debug');
        		$query_atualizar = "";
        		$contador_exec_query_a = 0;

        		$query_atualiza_status = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo IN (".implode(",",$codigo_atualizar_atu).");";
				$this->query($query_atualiza_status);

				$codigo_atualizar_atu = array();

        	}
	        
        	//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);
	        
		}//fim dados tabela vazio
		else {
			//atualiza todos os dados que ficaram para traz
        	$query = "UPDATE RHHealth.dbo.int_cliente_cargos SET codigo_status_transferencia = 8 WHERE codigo_status_transferencia = 3 AND codigo_int_upload_cliente = {$codigo_int_upload_cliente};";
        	$this->query($query);
		}

		$this->log("FINALIZANDO OS Cargos",'debug');

	    return $retorno;

	}//fim set_cargo

	private function temDiferencaCargo($cargo_stage, $cargo) {

		$diferente = false;

		if($cargo_stage['Cargo']['descricao'] != $cargo['Cargo']['descricao']) {
			$diferente = true;
		}

		if($cargo_stage['Cargo']['codigo_externo_cargo'] != $cargo['Cargo']['codigo_externo_cargo']) {
			$diferente = true;
		}

		if($cargo_stage['Cargo']['ativo'] != $cargo['Cargo']['ativo']) {
			$diferente = true;
		}

		if($cargo_stage['Cargo']['codigo_cbo'] != $cargo['Cargo']['codigo_cbo']) {
			$diferente = true;
		}
		
        return $diferente;

    }//FINAL FUNCTION temDiferencaCargo

	public function log_erros_int_cliente_cargos($codigo, $erro)
	{

		$int_cliente_cargo['IntClienteCargos']['codigo'] = $codigo;
		$int_cliente_cargo['IntClienteCargos']['codigo_status_transferencia'] = 6; // estrutura falhou
		$int_cliente_cargo['IntClienteCargos']['observacao'] = json_encode($erro);

		$this->atualizar($int_cliente_cargo);

	}//fim log_erros_int_cliente_cargos

	/**
	 * [getDadosIncluirAtualizar metodo para buscar os dados carregados do upload e atualizar a base principal do cliente]
	 * @param  [int] $codigo_int_upload_cliente [codigo do upload para processamento]
	 * @return [array]                            [retornando os dados dos Cargos que devem ser atualizados/incluidos]
	 */
	public function getDadosIncluirAtualizar($codigo_int_upload_cliente)
	{

		$query = "
			with cteIntClienteCargos AS (
				select 
					cc.codigo as cc_codigo,
					cc.codigo_int_upload_cliente,
					cc.codigo_usuario_inclusao,
					cc.codigo_empresa,
					cli.codigo as codigo_cliente_cc,
					ge.codigo_cliente as codigo_cliente_ge,
					cc.codigo as codigo_int_cliente_cargo,
					CAST(cc.descricao AS varchar(256)) as cargo_descricao,
					cc.cbo as cargo_cbo,
					cc.codigo_cliente as cargo_codigo_cliente,
					cli.codigo_documento,
					cc.cnpj as cargo_cnpj,
					CAST(cc.codigo_externo_cargo AS varchar(256)) as codigo_externo_cargo,		
					cc.ativo as cargo_ativo
				from int_cliente_cargos cc
					INNER JOIN cliente cli on cc.cnpj = cli.codigo_documento
					inner join grupos_economicos_clientes gec  on cli.codigo = gec.codigo_cliente
					inner join grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo
				where cc.codigo_status_transferencia = 3
					AND cc.codigo_int_upload_cliente = {$codigo_int_upload_cliente}
			)
			,
			cteCargoAtualizar AS (
				select 
					cc.*
					,c.ativo as s_ativo
					,(CASE WHEN c.ativo = cc.cargo_ativo THEN c.ativo else cc.cargo_ativo end) as cargo_atv
					,c.codigo as c_codigo
					,'1' as cargo_atualizar
					,'0' as cargo_incluir
				from cteIntClienteCargos cc
					left join cargos c on cc.codigo_cliente_ge = c.codigo_cliente
						and cc.cargo_descricao = c.descricao
				where (c.ativo <> cc.cargo_ativo OR c.codigo_cbo <> cc.cargo_cbo OR c.codigo_externo_cargo <> cc.codigo_externo_cargo)
			)
			,
			cteCargoIncluir AS (
				select 
					cc.*
					,c.ativo as s_ativo
					,(CASE WHEN c.ativo = cc.cargo_ativo THEN c.ativo else cc.cargo_ativo end) as cargo_atv
					,c.codigo as c_codigo
					,'0' as cargo_atualizar
					,'1' as cargo_incluir
				from cteIntClienteCargos cc
					left join cargos c on cc.codigo_cliente_ge = c.codigo_cliente
						and cc.cargo_descricao = c.descricao
				where c.codigo is null
			)
			select * from cteCargoAtualizar
			union all
			select * from cteCargoIncluir
		";

		$dados = $this->query($query);

		return $dados;

	}//fim getDadosIncluirAtualizar
}
