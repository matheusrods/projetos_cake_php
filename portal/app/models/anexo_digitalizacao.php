<?php

class AnexoDigitalizacao extends AppModel {

	var $name = 'AnexoDigitalizacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'anexo_digitalizacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	/**
	 * [converteFiltroEmCondition description]
	 * 
	 * metodo para montar o where realizando o filtro da tela
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmCondition($data) 
	{

        $conditions = array();

        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {

			$GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
			
			$GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');

			$codigo_cliente = $filtros['codigo_cliente'];
			
			//verifica se é multicliente para passar o array, senão ele irá pesquisar a matriz do cliente pesquisado
			if(isset($_SESSION['Auth']['Usuario']['multicliente'])) {
				$codigo_matriz = $codigo_cliente;
			}
			else {
				$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			}
			
			$codigos_unidades = $GrupoEconomicoCliente->lista($codigo_matriz);
			
			$conditions[] = array('FuncionarioSetorCargo.codigo_cliente_alocacao IN (
			select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente IN('.implode(",",array_keys($codigos_unidades)).')))');
		}

        if (!empty($data['codigo_cliente'])){
            $conditions['AnexoDigitalizacao.codigo_cliente_matriz'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_unidade'])){
            $conditions['AnexoDigitalizacao.codigo_cliente_alocacao'] = $data['codigo_unidade'];
        }  

        if (!empty($data['codigo_tipo_digitalizacao'])){
            $conditions['AnexoDigitalizacao.codigo_tipo_digitalizacao'] = $data['codigo_tipo_digitalizacao'];
        }

        //seta automaticamente
		if(!isset($data["tipo_periodo"])) {
			$data["tipo_periodo"] = 'I';
		}

        if(!empty($data["data_inicio"])) {
			
			$data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
			
			switch ($data["tipo_periodo"]) {
				case 'I'://data de inclusao
					$conditions['AnexoDigitalizacao.data_inclusao >= '] = $data_inicio;	
					break;
				case 'V'://data de validade
					$conditions['AnexoDigitalizacao.validade >= '] = $data_inicio;	
					break;
			}//switch
		}//fim if

		if(!empty($data["data_fim"])) {
			switch ($data["tipo_periodo"]) {
				case 'I'://data de inclusao
					$conditions['AnexoDigitalizacao.data_inclusao <= '] = $data_fim;	
					break;
				case 'V'://data de validade
					$conditions['AnexoDigitalizacao.validade <= '] = $data_fim;	
					break;
			}//switch
		}

        return $conditions;
    }

    public function converteFiltroEmConditionTerceiros($data) 
	{

        $conditions = array();

        if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {

			$GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
			
			$GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');

			$codigo_cliente = $filtros['codigo_cliente'];
			
			//verifica se é multicliente para passar o array, senão ele irá pesquisar a matriz do cliente pesquisado
			if(isset($_SESSION['Auth']['Usuario']['multicliente'])) {
				$codigo_matriz = $codigo_cliente;
			}
			else {
				$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
			}
			
			$codigos_unidades = $GrupoEconomicoCliente->lista($codigo_matriz);
			
			$conditions[] = array('FuncionarioSetorCargo.codigo_cliente_alocacao IN (
			select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente IN('.implode(",",array_keys($codigos_unidades)).')))');
		}

        if (!empty($data['codigo_cliente'])){
            $conditions['AnexoDigitalizacao.codigo_cliente_matriz'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_unidade'])){
            $conditions['AnexoDigitalizacao.codigo_cliente_alocacao'] = $data['codigo_unidade'];
        }  

        if (!empty($data['codigo_tipo_digitalizacao'])){
            $conditions['AnexoDigitalizacao.codigo_tipo_digitalizacao'] = $data['codigo_tipo_digitalizacao'];
        }

        //seta automaticamente
		if(!isset($data["tipo_periodo"])) {
			$data["tipo_periodo"] = 'I';
		}

        if(!empty($data["data_inicio"])) {
			
			$data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
			$data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
			
			switch ($data["tipo_periodo"]) {
				case 'I'://data de inclusao
					$conditions['AnexoDigitalizacao.data_inclusao >= '] = $data_inicio;	
					break;
				case 'V'://data de validade
					$conditions['AnexoDigitalizacao.validade >= '] = $data_inicio;	
					break;
			}//switch
		}//fim if

		if(!empty($data["data_fim"])) {
			switch ($data["tipo_periodo"]) {
				case 'I'://data de inclusao
					$conditions['AnexoDigitalizacao.data_inclusao <= '] = $data_fim;	
					break;
				case 'V'://data de validade
					$conditions['AnexoDigitalizacao.validade <= '] = $data_fim;	
					break;
			}//switch
		}

        return $conditions;
    }	
}//fim class AnexoDigitalizacao
?>