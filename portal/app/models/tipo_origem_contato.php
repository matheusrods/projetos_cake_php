<?php

class TipoOrigemContato extends AppModel {

	var $name = 'TipoOrigemContato';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'tipo_origem_contato';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
        
        public function obtemDescricao($codigo) {
            $status = $this->findByCodigo($codigo);
            if (!$status) {
                return null;
            }
            return $status[$this->name]['descricao'];
        }

        public function listar_ativos() {
            $conditions = array('ativo'=>'S');
            return $this->find('list',compact('conditions'));
        }

        public function valida_existencia($codigo) {
            $conditions = array('codigo'=>$codigo);
            $conta = $this->find('count',compact('conditions'));
            return ($conta>0);
        }

}

?>