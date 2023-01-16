<?php
App::import('Component', 'Auth');
App::import('Component', 'StringView');

class VerificaAtestadoSemCidShell extends Shell {
	var $uses = array(
		'Atestado',
		'Alerta',
		'AlertaTipo'
	);

	function main() {
		echo "verifica_atestado_sem_cid [run] \n";
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep 'verifica_atestado_sem_cid {$tipo}'");
		
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function run() {

		$options['joins'] = array(
			array(
				'table' => 'medicos',
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => array('Medico.codigo = Atestado.codigo_medico')
			),
			array(
				'table' => 'conselho_profissional',
				'alias' => 'ConselhoProfissional',
				'type' => 'INNER',
				'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional')
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario')
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('Cliente.codigo = ClienteFuncionario.codigo_cliente')
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
			),

			array(
				'table' => 'endereco_cidade',
				'alias' => 'EnderecoCidade',
				'type' => 'LEFT',
				'conditions' => array('EnderecoCidade.codigo = Atestado.codigo_cidade')
			),
			array(
				'table' => 'endereco_estado',
				'alias' => 'EnderecoEstado',
				'type' => 'LEFT',
				'conditions' => array('EnderecoEstado.codigo = Atestado.codigo_estado')
			),				
		);
		
		$options['fields'] = array(
			'Atestado.codigo',
			'Atestado.data_inclusao',
			'Atestado.data_afastamento_periodo',
			'Atestado.hora_afastamento',
			'Atestado.data_retorno_periodo',
			'Atestado.hora_retorno',
			'Atestado.endereco',
			'Atestado.numero',
			'Atestado.complemento',
			'Atestado.bairro',
			'Atestado.cep',
			'Medico.nome',
			'Medico.numero_conselho',
			'Medico.conselho_uf',
			'ConselhoProfissional.descricao',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Funcionario.nome',
			'Funcionario.cpf',
			'EnderecoCidade.descricao',
			'EnderecoEstado.descricao'
		);
		
		$options['conditions'] = array('Atestado.codigo not in (select codigo from atestados_cid)');
		$listas_atestados_sem_CID = $this->Atestado->find('all', $options);
		
		$separa_por_cliente = array();
		foreach($listas_atestados_sem_CID as $atestado) {
			$separa_por_cliente[$atestado['Cliente']['codigo']][] = $atestado; 
		}
		
		$this->StringView 	= new StringViewComponent();
		if(count($separa_por_cliente)) {
			
			foreach($separa_por_cliente as $codigo_cliente => $atestados) {
				
				$this->StringView->reset();
					
				$this->StringView->set('atestados_sem_CID', $atestados);
				$content = $this->StringView->renderMail('atestados_sem_CID');
				
				$alerta = array(
					'Alerta' => array(
						'codigo_cliente'     => $codigo_cliente,
						'descricao'          => "Atestados sem CID",
						'assunto'            => "Atestados sem CID",
						'descricao_email'    => $content,
						'codigo_alerta_tipo' => AlertaTipo::ATESTADOS_SEM_CID,
						'model'              => 'Atestado',
						'foreign_key'        => NULL,
						'email_agendados'    => false,
						'sms_agendados'      => false
					),
				);
				
				$this->Alerta->incluir($alerta);
			}
		}
		
	}
}
?>