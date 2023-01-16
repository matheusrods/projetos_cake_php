<?php

App::import('Component', array('Upload'));

class AnexoNotaFiscalServico extends AppModel {
	
	public $name		   	= 'AnexoNotaFiscalServico';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'anexo_nota_fiscal_servico';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');

	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição',
				'required' => true
			 )
		),
		'codigo_nota_fiscal_servico' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o código da nota fiscal',
				'required' => true
			 )
		),
		'caminho_arquivo' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o caminho do arquivo',
				'required' => true
			 )
		),
		'codigo_usuario_inclusao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o código do usuário',
				'on'		=> 'create',
				'required' => false
			 )
		),
		'data_inclusao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a data de inclusão',
				'on'		=> 'create',
				'required' => false
			 )
		),		
	);



	/**
	 * Filtra e valida dados de payload
	 *
	 * @param array $data
	 * @param boolean $edit_mode
	 * @return array
	 */
	function converteFiltroEmConditions($data, $edit_mode = false) {
		
		$conditions = array();

		// apenas se criando - edit_mode:false

		if ($edit_mode == false){
			
			if(!isset($data['ativo'])){
				$conditions['AnexoNotaFiscalServico.ativo'] = 1;
			}

			if (isset($data['data_inclusao']) && !empty($data['data_inclusao'])) {
				$conditions['AnexoNotaFiscalServico.data_inclusao'] = $data['data_inclusao'];
			} else {
				$conditions['AnexoNotaFiscalServico.data_inclusao'] = Comum::now();
			}
	
			if (isset($data['codigo_usuario_inclusao']) && !empty($data['codigo_usuario_inclusao'])) {
				$conditions['AnexoNotaFiscalServico.codigo_usuario_inclusao'] = $data['codigo_usuario_inclusao'];
			} else {
				$conditions['AnexoNotaFiscalServico.codigo_usuario_inclusao'] = Comum::codigoUsuarioAutenticado();
			}

			if (isset($data['codigo_nota_fiscal_servico']) && !empty($data['codigo_nota_fiscal_servico'])) {
				$conditions['AnexoNotaFiscalServico.codigo_nota_fiscal_servico'] = $data['codigo_nota_fiscal_servico'];
			}

		}

		// apenas se alterando - edit_mode:true
		if ($edit_mode == true)
		{

			$conditions['AnexoNotaFiscalServico.data_alteracao'] = Comum::now();
			$conditions['AnexoNotaFiscalServico.codigo_usuario_alteracao'] = Comum::codigoUsuarioAutenticado();
		}
		

		// em qualquer modo 

		if (isset($data['ativo']) && !empty($data['ativo'])) {
			$conditions['AnexoNotaFiscalServico.ativo'] = $data['ativo'];
		}

		if (isset($data['caminho_arquivo']) && !empty($data['caminho_arquivo'])) {
			$conditions['AnexoNotaFiscalServico.caminho_arquivo'] = $data['caminho_arquivo'];
		}

		if (isset($data['caminho_arquivo']) && !empty($data['caminho_arquivo'])) {
			$conditions['AnexoNotaFiscalServico.descricao'] = $data['descricao'];
		} else {
			$conditions['AnexoNotaFiscalServico.descricao'] = '';
		}

		return $conditions;
	}	

}