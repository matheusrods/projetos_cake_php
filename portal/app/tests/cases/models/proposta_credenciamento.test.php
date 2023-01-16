<?php
App::import('Model', 'PropostaCredenciamento');
class PropostaCredenciamentoTestCase extends CakeTestCase {

	var $fixtures = array(
		'app.proposta_credenciamento',
		'app.proposta_cred_endereco',
		'app.servico',
		'app.proposta_cred_exame',
		'app.proposta_cred_produto',
		'app.proposta_cred_medico',
		'app.medico',
		'app.horario',
		'app.status_proposta_cred',
		'app.documento',
		);

	function startTest() {
		$this->PropostaCredenciamento = & ClassRegistry::init('PropostaCredenciamento');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
	}

	function testInclusaoCNPJInvalido() {
		$dados = array(
			'PropostaCredenciamento' => array(
				'motivo_recusa' => NULL,
				'codigo_status_proposta_credenciamento' => 9,
			    'codigo' => 511,
			    'tipo_atendimento' => 1,
			    'acesso_portal' => 1,
			    'exames_local_unico' => 1,
			    'ativo' => 1,
			    'codigo_conselho_profissional' => 1,
			    'data_inclusao' => '25/04/2016 11:04:33',
			    'razao_social' => 'DR CONSULTA CLINICA MEDICA LTDA',
			    'nome_fantasia' => 'DR CONSULTA',
			    'codigo_documento' => '123',
			    'responsavel_tecnico_nome' => 'Dr. Flavio oliveira',
			    'responsavel_tecnico_numero_conselho' => '15.663 6',
			    'responsavel_administrativo' => 'Sr. Osnir111 alvez',
			    'favorecido' => 'dr consulta',
			    'agencia' => '0002',
			    'numero_conta' => '5258326-3',
			    'telefone' => '(11)6659-866',
			    'fax' => ' ',
			    'celular' => ' ',
			    'email' => 'daniloborgespereira@gmail.com',
			    'numero_banco' => '065 - Banco Bracce S.A.',
			    'tipo_conta' => '1',
			    'responsavel_tecnico_conselho_uf' => 'BA',
	    	),
		);

		$this->assertFalse($this->PropostaCredenciamento->incluir($dados));
		$invalidFields = $this->PropostaCredenciamento->invalidFields();
		$this->assertEqual($invalidFields, array(
			'codigo_documento' => 'CNPJ inválido, verifique!'
		));
	}

	function endTest() {
		unset($this->PropostaCredenciamento);
		ClassRegistry::flush();
	}
}
?>