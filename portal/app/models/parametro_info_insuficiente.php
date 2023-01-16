<?php

class ParametroInfoInsuficiente extends AppModel {

    var $name = 'ParametroInfoInsuficiente';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'parametros_informacoes_insuficientes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';

    var $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Descrição não informada',
            'required' => true,
            'allowEmpty' => false,
        ),
    );    

    function excluirOuInativar($codigo_parametro) {
    	if(!$this->excluir($codigo_parametro)) {
    		return($this->inativar($codigo_parametro));
    	}
    	return true;
    }

    function inativar($codigo_parametro) {
    	$parametro = $this->carregar($codigo_parametro);
    	$parametro['ParametroInfoInsuficiente']['ativo'] = false;
    	if(!$this->atualizar($parametro)) {
    		return false;
    	}
    	return true;
    }
}