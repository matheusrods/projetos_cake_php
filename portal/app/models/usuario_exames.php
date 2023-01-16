<?php
class UsuarioExames extends AppModel {

	public $name		   	= 'UsuarioExames';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'usuario_exames';
	public $primaryKey	   	= 'codigo';
	//public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_anexos_atestados'));

	public function getExamesCovid($codigo_usuario, $codigo_cliente_funcionario)
	{
		$fields = array(
			'UsuarioExames.codigo as codigo', 
			'UsuarioExames.codigo_usuario as codigo_usuario', 
			//UsuarioExames.codigo_exames, 
			'UsuarioExames.data_realizacao as data_realizacao',
			'Exames.descricao as exame',
			'UsuarioExamesImagens.imagem as arquivo_exame'
		);

		$joins = array(
			array(
				'table' => 'exames',
				'alias'	=> 'Exames',
				'type'  => 'INNER',
				'conditions' => array("Exames.codigo = UsuarioExames.codigo_exames AND Exames.descricao like '%covid%'")
			),			
			array(
				'table' => 'usuario_exames_imagens',
				'alias'	=> 'UsuarioExamesImagens',
				'type'  => 'INNER',
				'conditions' => array('UsuarioExamesImagens.codigo_usuario_exames = UsuarioExames.codigo')
			),				
			array(
				'table' => 'atestados',
				'alias'	=> 'Atestados',
				'type'  => 'LEFT',
				'conditions' => array("
					Atestados.codigo = UsuarioExames.codigo_atestado 
					AND Atestados.ativo = 1 
				")
			),
			array(
				'table' => 'anexos_atestados',
				'alias'	=> 'AnexosAtestados',
				'type'  => 'LEFT',
				'conditions' => array("AnexosAtestados.codigo_atestado = Atestados.codigo")
			)
			
		);

		$conditions = array(
			'UsuarioExames.codigo_usuario' => $codigo_usuario,
			'UsuarioExames.resultado_exame IS NULL'
		);
	
		$dados = $this->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));

		// debug($dados);exit;

		return $dados;

	}

}