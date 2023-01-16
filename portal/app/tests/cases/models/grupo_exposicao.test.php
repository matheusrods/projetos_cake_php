<?php
App::import('Model', 'GrupoExposicao');
class GrupoExposicaoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.grupo_exposicao',
		'app.cliente_setor',
		'app.ordem_servico',
		'app.ordem_servico_item',
		'app.grupo_economico_cliente',
		'app.grupo_exposicao_risco',
		'app.grupo_exp_risco_fonte_gera',
		'app.grupo_economico',
		'app.grupo_exposicao_risco_epi',
		'app.grupo_exposicao_risco_epc',
		'app.grupo_homogeneo',
		'app.grupo_hom_detalhe',
		'app.setor',
		'app.cargo',
		'app.cliente',
		'app.configuracao',
		'app.atribuicao_grupo_expo',
		'app.atribuicao',
		'app.grupo_exp_risco_atrib_det',
		'app.risco_atributo_detalhe',
		'app.usuario',
		'app.usuarios_dados',
		'app.cliente_setor_log',
		'app.grupo_exposicao_log',
		'app.grupo_exposicao_risco_log',
		'app.grupo_exp_risco_fonte_gera_log',
		'app.grupo_exposicao_risco_epi_log',
		'app.grupo_exposicao_risco_epc_log',
		'app.aplicacao_exame',
		'app.exame',
		'app.alerta_hierarquia_pendente',
	);

	public function startTest() {
		$this->GrupoExposicao = & ClassRegistry::init('GrupoExposicao');
		$this->ClienteSetor = & ClassRegistry::init('ClienteSetor');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltroEmCondition() {

		$dados = array(
			'codigo' => 1,
			'codigo_cliente' => 2,
			'codigo_setor' => 3,
			'codigo_cargo' => 4,
			'codigo_grupo_homogeneo' => 5
		);

		$retorno = $this->GrupoExposicao->converteFiltroEmCondition($dados);

		$comparacao = array(
			'GrupoExposicao.codigo' => 1,
			'ClienteSetor.codigo_setor' => 3,
			'GrupoExposicao.codigo_cargo' => 4,
			'GrupoExposicao.codigo_grupo_homogeneo' => 5
		);

		$this->assertEqual($retorno, $comparacao);
	}

	public function testIncluirAtualizar() {
		$dados_1 = array(
			'GrupoExposicao' => array(
				'codigo_cargo' => 381,
				'data_inicio_vigencia' => '2018-01-01 00:00:00',
				'codigo_cliente_setor' => '',
				'descricao_tipo_setor_cargo' => 1,
			),
			'ClienteSetor' => array(
				'codigo_cliente_alocacao' => 10011,
				'codigo_setor' => 346,
			),
			'GrupoExposicaoRisco' => array(
				0 => array(
	                'codigo_grupo_risco' => 1,
	                'codigo_risco' => 4,
	                'codigo_efeito_critico' => 6,
	                'codigo_risco_atributo' => 1,
	                'tempo_exposicao' => 1,
	                'minutos_tempo_exposicao' => 456,
	                'jornada_tempo_exposicao' => 456,
	                'intensidade' => 5,
	                'resultante' => 9,
	                'dano' => '',
	                'codigo_tipo_medicao' => 1,
	                'codigo_tecnica_medicao' => 4,
	                'valor_maximo' => 654,
	                'valor_medido' => 654
            	),
			)
		);

		$this->assertTrue($this->GrupoExposicao->incluir($dados_1));

		$joins = array(
            array(
              	'table' => 'grupos_exposicao_risco',
              	'alias' => 'GrupoExposicaoRisco',
              	'type' => 'LEFT',
              	'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
            ),
		);

		$fields = array(
			'GrupoExposicao.codigo','GrupoExposicao.codigo_cargo','GrupoExposicao.codigo_cliente_setor','GrupoExposicaoRisco.codigo_risco'
		);

		$dados_incluidos = $this->GrupoExposicao->find('all',array('conditions' => array('GrupoExposicao.codigo' => $this->GrupoExposicao->id),'joins' => $joins,'fields' => $fields));
		
		$esperado_incluido = array(
			'0' => array(
	            'GrupoExposicao' => array(
	                'codigo' => 15772,
	                'codigo_cargo' => 381,
	                'codigo_cliente_setor' => 10008,
	            ),
	            'GrupoExposicaoRisco' => array(
	                'codigo_risco' => 4,
	            ),
        	),
		);

		$this->assertEqual($dados_incluidos,$esperado_incluido);

		$dados_2 = array(
			'GrupoExposicao' => array(
				'codigo' => $this->GrupoExposicao->id,
				'descricao_tipo_setor_cargo' => 1,
				'data_inicio_vigencia' => '2018-10-10 00:00:00',
			),
			'ClienteSetor' => array(
				'codigo_cliente_alocacao' => 2395,
				'codigo' => $this->ClienteSetor->id,
			),
			'GrupoExposicaoRisco' => array(
				0 => array(
	                'codigo_grupo_risco' => 1,
	                'codigo_risco' => 5,
	                'codigo_efeito_critico' => 6,
	                'codigo_risco_atributo' => 1,
	                'tempo_exposicao' => 1,
	                'minutos_tempo_exposicao' => 456,
	                'jornada_tempo_exposicao' => 456,
	                'intensidade' => 5,
	                'resultante' => 9,
	                'dano' => '',
	                'codigo_tipo_medicao' => 1,
	                'codigo_tecnica_medicao' => 4,
	                'valor_maximo' => 654,
	                'valor_medido' => 654
            	),
            ),
		);

		$this->assertTrue($this->GrupoExposicao->atualizar($dados_2));

		$dados_atualizados = $this->GrupoExposicao->find('all',array('conditions' => array('GrupoExposicao.codigo' => $this->GrupoExposicao->id),'joins' => $joins,'fields' => $fields));
		
		$esperado_atualizado = array(
    		'0' => array(
	            'GrupoExposicao' => array(
	                'codigo' => 15772,
	                'codigo_cargo' => 381,
	                'codigo_cliente_setor' => 10008,
	            ),
	            'GrupoExposicaoRisco' => array(
	                'codigo_risco' => 4,
	            ),
        	),
    		'1' => array(
	            'GrupoExposicao' => array(
	                'codigo' => 15772,
	                'codigo_cargo' => 381,
	                'codigo_cliente_setor' => 10008,
	            ),
	            'GrupoExposicaoRisco' => array(
	                'codigo_risco' => 5,
	            ),
        	),
		);

		$this->assertEqual($dados_atualizados,$esperado_atualizado);
	}

	public function testRetorna_dados_grupo_exposicao_ghe() {

		$dados = array(
			array(
				'GrupoExposicao' => array(
					'codigo' => 5552,
					'codigo_cargo' => 3153,
					'descricao_atividade' => 'wwdasdasdsaasddas',
					'data_documento' => '02/09/2016 00:00:00',
					'observacao' => 'GFGF', 
					'codigo_cliente_setor' => 1078,
					'codigo_grupo_homogeneo' => 34
				),
				'ClienteSetor' => array(
					'codigo' => 1078,
					'codigo_cliente' => 1165,
					'codigo_cliente_alocacao' => '',
					'codigo_setor' => 2127,
					'data_inclusao' => '01/09/2016 11:18:57',
					'codigo_usuario_inclusao' => 66984,
					'codigo_empresa' => 1,
					'pe_direito' => '',
					'cobertura' => '',
					'iluminacao' => '',
					'ventilacao' => '',
					'piso' => '',
					'estrutura' => ''
				)
			)
		);

		$retorno = $this->GrupoExposicao->retorna_dados_grupo_exposicao_ghe(1078, 34);

		$this->assertEqual($retorno, $dados);
	}

	public function testExluir() {
		$this->assertTrue($this->GrupoExposicao->excluir(5552));
	}

	public function testRetornaDescricaoGrupoHomogeneo() {

		$dados = array(
			array(
				'GrupoHomogeneo' => array(
					'codigo' => 34,
					'descricao' => 'Nova Exposição',
					'codigo_cliente' => 2298
				),
				'GrupoHomDetalhe' => array(
					'codigo' => 125,
					'codigo_grupo_homogeneo' => 34,
					'codigo_setor' => 2127,
					'codigo_cargo' => 3153
				),
				'Setor' => array(
					'codigo' => 2127,
					'descricao' => 'RH'
				),
				'Cargo' => array(
					'codigo' => '',
					'descricao' => ''
				),
				'GrupoExposicao' => array(
					'codigo' => 5552,
					'codigo_grupo_homogeneo' => 34,
					'codigo_cliente_setor' => 1078,
					'descricao_atividade' => 'wwdasdasdsaasddas'
				),
			),
		);

		$retorno = $this->GrupoExposicao->retornaDescricaoGrupoHomogeneo(34);

		$this->assertEqual($retorno, $dados);
	}

	public function testeDadosModalPcmsoPendente(){
		$dados = $this->GrupoExposicao->dados_modal_pcmso_pendente(2395,358,381);

		$esperado = array(
		    'codigo_unidade' => '2395',
		    'nome_fantasia' => 'PALACIO TANGARA',
		    'codigo_setor' => '358',
		    'setor' => 'Banquets',
		    'codigo_cargo' => '381',
		    'cargo' => 'teste_pendencia',
		    'exames' => array(
		        '84' => 'PESQUISA DE FUNGOS',
		    ),
		);

		$this->assertEqual($dados,$esperado);
	}

	public function endTest() {
		unset($this->GrupoExposicao);
		ClassRegistry::flush();
	}
}
?>