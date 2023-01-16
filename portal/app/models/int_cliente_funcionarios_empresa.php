<?php
class IntClienteFuncionariosEmpresa extends AppModel
{
	public $name          = 'IntClienteFuncionariosEmpresa';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_cliente_funcionarios_empresa';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Funcionários x Empresa";
	public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_funcionarios_empresa'));
	public $fillable      = array(
		'codigo_empresa',
		'codigo_cliente_funcionarios_empresa',
		'cpf',
		'data_admissao',
		'data_demissao',
		'matricula',
		'status_matricula',
		'numero_registro',
		'turno',
		'categoria_colaborador',
		'teletrabalho',
		'matricula_chefia_imediata',
		'numero_registro_chefia_imediata',
		'codigo_bu',
		'chave_tipo_afastamento',
		'descricao_tipo_afastamento',
		'cnpj',
		'codigo_externo_centro_resultado',
		'cnpj_alocado',
		'codigo_externo_setor',
		'codigo_externo_cargo',
		'cnpj_chefia_imediata',
		'data_inicio_cargo',
		'hora_inicio_afastamento',
		'hora_fim_afastamento',
		'data_inicio_afastamento',
		'data_fim_afastamento',
		'ativo',
	);

}
