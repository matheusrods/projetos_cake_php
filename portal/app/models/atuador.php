<?php
class Atuador extends AppModel {

    var $name = 'Atuador';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'Atuador';
    var $primaryKey = 'Codigo'	;
    var $displayField = 'descricao';

    public function buscaAtudadoresPorPlaca($placa){
    	$atuadores = false;
    	$placa 	= $this->trata_placa($placa);
        $this->bindModel(array('hasOne' => array(
            'AtuadorCarro' => array('foreignKey' => 'CodAtenua')
        )));
        $groups 	= array('Atuador.Codigo');
        $conditions	= array('AtuadorCarro.Placa' => $placa);
        $recursive = 1;
        $atuadores 	= $this->find('list',compact('conditions','group', 'recursive'));
        return (($atuadores)?$atuadores:array());
    }

    public function lista(){
    	return $this->find('list');
    }

    public function listaPorPlaca($placa){

    }

    function trata_placa($placa){
    	$placa = str_replace('-', '', $placa);
    	return substr($placa, 0, 3).'-'.substr($placa, 3);
    }
}
?>