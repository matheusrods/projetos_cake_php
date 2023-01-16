<?php
class TipoHistorico extends AppModel {
	var $name = 'TipoHistorico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipo_historico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';
        
        /**
         * Metodo generico para listar dados.
         * 
         * @param string $type
         * @param array $conditions
         * @return array|int|mixed $result Fetch result
         */
        public function listar($type, $conditions = '') {
            $result = $this->find($type, $conditions);
            return $result;
        }
        
        /**
         * Método generico para listar dados no formato chave -> valor
         * 
         * @return array $result
         */
        public function listarNomeHistoricos() {
            $result = $this->listar('list');
            return $result;
        }
}