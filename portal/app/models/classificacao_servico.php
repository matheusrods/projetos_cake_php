<?php

class ClassificacaoServico extends AppModel {

	public $name = 'ClassificacaoServico';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'classificacao_servico';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	public $displayField = 'descricao';

	const CONSULTAS = 1;
	const EXAMES = 2;
	const PLANOSDESAUDE = 3;

	public function obtemClassificacaoServicos()
	{
		return $this->find('list', array('conditions' => array('ClassificacaoServico.codigo <>' => ClassificacaoServico::PLANOSDESAUDE)));
	}

}