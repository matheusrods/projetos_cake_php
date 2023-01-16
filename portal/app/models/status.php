<?php

class Status extends AppModel {

	var $name = 'Status';
	var $tableSchema = 'informacoes';
	var $databaseTable = 'dbTeleconsult';
	var $useTable = 'status';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
        
        const RECOMENDADO = 1;
        const NAO_RECOMENDADO = 2;
        const INSUFICIENCIA_DADOS = 3;
        const PENDENTE = 4;
        const NOVA_PESQUISA = 5;
        const LIBERACAO_OK = 6;
        const TENTATIVAS_ENCERRADAS = 7;
        const EM_PESQUISA = 8;

        public function obtemDescricao($codigo) {
            $status = $this->findByCodigo($codigo);
            if (!$status) {
                return null;
            }
            return $status[$this->name]['descricao'];
        }

}

?>