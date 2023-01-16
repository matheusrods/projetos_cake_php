<?php
class TipoRelacionamento extends AppModel {
	var $name = 'TipoRelacionamento';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'tipo_relacionamento';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';
        
        /**
         * Lista todos os TipoRelacionamentos em formato chave => valor
         * 
         * @return array $key=>$value
         */
        public function listar() {
            $result = $this->find('list');
            return $result;
        }
}
?>