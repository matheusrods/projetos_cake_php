<?php
class TipoRetorno extends AppModel {
	var $name = 'TipoRetorno';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RhHealth';
	var $useTable = 'tipo_retorno';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';
	
	const TIPO_RETORNO_TELEFONE = 1;
	const TIPO_RETORNO_EMAIL = 2;
	const TIPO_RETORNO_CELULAR_MOTORISTA = 5;
	const TIPO_RETORNO_RADIO = 6;
	const TIPO_RETORNO_CELULAR = 7;
	const TIPO_RETORNO_3G = 8;
	const TIPO_RETORNO_SMS = 9;
	const TIPO_RETORNO_RAMAL = 10;
	const TIPO_RETORNO_MENSALIDADE = 11;
	
	function listar(){
		$order = array('descricao');
		return $this->find('list',compact('order'));
	}

	function listar_para_proposta(){
		$order = array('descricao');

		$conditions = Array(
			'codigo' => Array(
				self::TIPO_RETORNO_TELEFONE, self::TIPO_RETORNO_EMAIL, self::TIPO_RETORNO_CELULAR
			)
		);

		return $this->find('list',compact('conditions','order'));
	}


	public function converteFiltroEmCondition($dados) {
		$conditions = array();

		if (!empty($dados['TipoRetorno']['codigo'])) {
			$conditions["TipoRetorno.codigo"] = $dados['TipoRetorno']["codigo"];
		}
		if (!empty($dados['TipoRetorno']['descricao'])) {
			$conditions["TipoRetorno.descricao LIKE"] = "%".$dados['TipoRetorno']["descricao"]."%";
		}
        if (!empty($dados['TipoRetorno']['profissional'])) {
            $conditions["TipoRetorno.usuario_interno"] = $dados['TipoRetorno']["profissional"];
        }
        if (!empty($dados['TipoRetorno']['proprietario'])) {
            $conditions["TipoRetorno.usuario_interno"] = $dados['TipoRetorno']["proprietario"];
        }
        if (!empty($dados['TipoRetorno']['usuario_interno'])) {
            if($dados['TipoRetorno']["usuario_interno"] == true) {
            	$conditions["TipoRetorno.usuario_interno"] = 1;
            } 
            if($dados['TipoRetorno']["usuario_interno"] == false) {
            	$conditions["TipoRetorno.usuario_interno"] = 0;
            }
        }
		return $conditions; 
	}

}
?>