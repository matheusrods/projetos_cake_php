<?php
App::import('Model', 'Atestado');
class AtestadoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.atestado',
		'app.atestado_cid',
		'app.atestado_cid_log',
		'app.atestado_log',
		'app.cargo',
		'app.cid',
		'app.cid_cnae',
		'app.cliente',
		'app.cliente_endereco',
		'app.cliente_funcionario',
		'app.cnae',
		'app.cnae_secao',
		'app.conselho_profissional',
		'app.esocial',
		'app.ficha_clinica',
		'app.ficha_clinica_farmaco',
		'app.ficha_clinica_grupo_questao',
		'app.ficha_clinica_questao',
		'app.ficha_clinica_resposta',
		'app.fornecedor',
		'app.fornecedor_medico',
		'app.funcionario',
		'app.funcionario_endereco',
		'app.funcionario_setor_cargo',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.item_pedido_exame',
		'app.medico',
		'app.motivo_afastamento',
		'app.multi_empresa',
		'app.pedido_exame',
		'app.setor',
		'app.tipo_contato',
		'app.tipo_local_atendimento',
		'app.uvw_endereco',
	);

	public function startTest() {
		$this->Atestado =& ClassRegistry::init('Atestado');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConsultas() {
		$this->analitico();
		$this->sintetico();
	}

	public function analitico() {
		$data = array(
			'codigo_unidade' => 20,
			'codigo_setor' => 699
		);
		$conditions = $this->Atestado->converteFiltrosEmConditions($data);
		$results = $this->Atestado->analitico('all', compact('conditions'));
		$expected = array( 
			array( 
				array( 
					'atestado_afastamento_em_dias' => NULL, 
					'atestado_afastamento_em_horas' => '48', 
					'atestado_cep' => ' ', 
					'atestado_codigo' => 10,
					'atestado_afastamento_periodo' => '2017-05-08 00:00:00', 
					'atestado_data_inclusao' => '2017-05-24 08:36:43', 
					'atestado_data_retorno_periodo' => '2017-05-10 00:00:00', 
					'atestado_endereco' => ' ', 
					'atestado_hora_afastamento' => '00:00:00', 
					'atestado_hora_retorno' => '00:00:00', 
					'atestado_restricao' => ' ', 
					'cargo_codigo' => 883,
					'cargo_descricao' => 'OP DE MONITORAMENTO JUNIOR', 
					'cid_codigo_cid10' => NULL, 
					'cid_descricao' => NULL, 
					'unidade_cnae' => '8020000', 
					'cliente_funcionario_matricula' => '2652', 
					'cliente_razao_social' => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA.', 
					'cnae_unidade_descricao' => 'Atividades de monitoramento de sistemas de segurança', 
					'dia_semana' => 2, 
					'dias_afastado' => 3, 
					'distancia_funcionario' => NULL, 
					'distancia_unidade' => NULL, 
					'esocial_descricao' => 'Acidente/Doença não relacionada ao trabalho', 
					'funcionario_cpf' => '16500461894', 
					'funcionario_endereco' => ' ', 
					'funcionario_end_complemento' => ' ', 
					'funcionario_endereco_numero' => ' ', 
					'funcionario_nome' => 'ANDREA BELTRAME', 
					'funcionario_rg' => '236598569', 
					'medico_conselho_uf' => 'RS', 
					'medico_nome' => ' DR RODRIGO CAVALHEIRO', 
					'medico_numero_conselho' => '42092', 
					'minutos_afastado' => 0, 
					'motivo_afastamento_descricao' => 'Internação', 
					'nexo' => 'N', 
					'setor_codigo' => 699,
					'setor_descricao' => 'OPER - BSAT - CHECKLIST', 
					'tipo_local_atend_descricao' => NULL, 
					'unidade_codigo' => 20,
					'unidade_codigo_documento' => '06326025000166', 
					'unidade_endereco' => ' ', 
					'unidade_endereco_complemento' => ' ', 
					'unidade_endereco_numero' => 191, 
					'unidade_nome_fantasia' => '191 - BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA.', 
				), 
				
			), 
		);
		$this->assertEqual($results, $expected);
	}

	function sintetico() {
		$data = array(
			'codigo_unidade' => 20,
			'codigo_setor' => 699
		);
		$conditions = $this->Atestado->converteFiltrosEmConditions($data);
		$results = $this->Atestado->sintetico(Atestado::AGRP_UNIDADE, $conditions);
		$expected = array(
    		array(
            	array(
                    'codigo' => 20,
                    'descricao' => '191 - BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA.',
                    'quantidade' => 1,
                )
	        )
		);
		$this->assertEqual($results, $expected);

		$results = $this->Atestado->sintetico(Atestado::AGRP_SETOR, $conditions);
		$expected = array(
    		array(
            	array(
                    'codigo' => 699,
                    'descricao' => 'OPER - BSAT - CHECKLIST',
                    'quantidade' => 1,
                )
	        )
		);
		$this->assertEqual($results, $expected);
	}

	public function endTest() {
		unset($this->Atestado);
		ClassRegistry::flush();
	}
}