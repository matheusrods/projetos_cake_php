<?php

class TipoFrota extends AppModel {

    var $name = 'TipoFrota';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'tipo_frota';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';

    const FIXO 		= 1;
    const TERCEIRO 	= 2;

    function converteTipoGuardian($tvco_tipo){
    	App::import('Model','TTvcoTipoVinculoContratual');

    	if(is_array($tvco_tipo)){
    		foreach ($tvco_tipo as $tipo) {
    			if($tipo == TTvcoTipoVinculoContratual::FROTA) return self::FIXO;
    		}

    	} else {
    		if($tvco_tipo == TTvcoTipoVinculoContratual::FROTA) return self::FIXO;
    		
    	}

    	return self::TERCEIRO;
    }
}
