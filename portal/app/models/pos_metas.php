<?php
class PosMetas extends AppModel {
	var $name = 'PosMetas';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_metas';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pos_meta'));

    public function por_cliente($codigo_cliente) {

        $GrupoEconomico = & ClassRegistry::init('GrupoEconomico');
        $Setor = & ClassRegistry::init('Setor');

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

        $dados = $Setor->obterLista($codigo_cliente);

        return $dados;

    }//FINAL FUNCTION por_cliente

}
