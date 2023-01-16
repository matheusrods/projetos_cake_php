<?php
App::import('Model', 'Importar');
class ImportarTestCase extends CakeTestCase
{
	var $fixtures = array(
		'app.cargo',
		'app.cliente',
		'app.setor',
		'app.cliente_funcionario',
		'app.funcionario',
		'app.pedido_exame',
		'app.multi_empresa',
		'app.ficha_clinica',
		'app.medico',
		'app.conselho_profissional',
		'app.ficha_assistencial',
		'app.atestado',
		'app.ficha_assistencial_resposta',
		'app.ficha_assistencial_questao',
		'app.ficha_assistencial_gq',
		'app.ficha_assistencial_farmaco',
		'app.fornecedor',
		'app.item_pedido_exame',
		'app.fornecedor_medico',
		'app.ficha_clinica_resposta',
		'app.ficha_clinica_questao',
		'app.ficha_clinica_grupo_questao',
		'app.ficha_clinica_farmaco',
		'app.funcionario_setor_cargo',
		'app.grupo_homogeneo',
		'app.cliente_setor',
		'app.setor_caracteristica',
		'app.setor_caracteristica_atributo',
		'app.grupo_exposicao',
		'app.grupo_exposicao_risco',
		'app.risco',
		'app.exposicao_ocupacional',
		'app.exposicao_ocup_atributo',
		'app.risco_atributo',
		'app.risco_atributo_detalhe',
		'app.tecnica_medicao',
		'app.fonte_geradora',
		'app.grupo_exp_risco_fonte_gera',
		'app.epc',
		'app.grupo_exposicao_risco_epc',
		'app.epi',
		'app.grupo_exposicao_risco_epi',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.cliente_setor_cargo',
		'app.Cliente_setor_log',
		'app.ordem_servico_item',
		'app.ordem_servico',
		'app.grupo_exposicao_log',
		'app.configuracao',
		'app.grupo_exposicao_risco_log',
		'app.setor_log',
		'app.cargo_log',
		'app.exame',
		'app.aplicacao_exame',
		'app.aplicacao_exame_log',
		'app.cliente_implantacao',
		'app.lista_de_preco',
		'app.lista_de_preco_produto',
		'app.lista_de_preco_produto_servico',
		'app.gpra',
		'app.prevencao_risco_ambiental',
		'app.fornecedor_endereco',
		'app.fornecedor_contato',
		'app.cliente_endereco',
		'app.gpra_log',
		'app.ppra_versoes',
		'app.ordem_servico_versoes',
		'app.ordem_servico_item_versoes',
		'app.cliente_setor_versoes',
		'app.grupo_exposicao_versoes',
		'app.grupo_exposicao_risco_versoes',
		'app.grupo_exposicao_risco_epc_versoes',
		'app.grupo_exposicao_risco_epi_versoes',
		'app.grupo_exp_risco_atrib_det',
		'app.usuario',
		'app.usuarios_dados',
		'app.grupo_exp_risco_atrib_det_vers',
		'app.grupo_exp_risco_fonte_gera_versoes',
		'app.gpra_versoes',
		'app.prevencao_risco_ambiental_versoes',
		'app.pcmso_versoes',
		'app.aplicacao_exame_versoes',
		'app.cliente_produto_servico_2',
		'app.alerta',
		'app.cliente_contato',
		'app.alerta_hierarquia_pendente',
		'app.grupo_exp_risco_atrib_det_log',
		'app.grupo_exposicao_risco_epi_log',
		'app.grupo_exposicao_risco_epc_log',
		'app.grupo_exp_risco_fonte_gera_log',
	);

	public function startTest()
	{
		$this->Importar				  = ClassRegistry::init('Importar');
		$this->GrupoEconomicoCliente  = ClassRegistry::init('GrupoEconomicoCliente');
		$this->ClienteSetorCargo	  = ClassRegistry::init('ClienteSetorCargo');
		$this->Setor				  = ClassRegistry::init('Setor');
		$this->Cargo				  = ClassRegistry::init('Cargo');
		$this->GrupoExposicaoRisco	  = ClassRegistry::init('GrupoExposicaoRisco');
		$this->AplicacaoExame		  = ClassRegistry::init('AplicacaoExame');
		$this->GrupoExpRiscoAtribDet  = ClassRegistry::init('GrupoExpRiscoAtribDet');
		$this->GrupoExposicaoRiscoEpi = ClassRegistry::init('GrupoExposicaoRiscoEpi');
		$this->GrupoExposicaoRiscoEpc = ClassRegistry::init('GrupoExposicaoRiscoEpc');
		$this->GrupoExpRiscoFonteGera = ClassRegistry::init('GrupoExpRiscoFonteGera');

		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;

		// verificando e criando diretório para as planilhas de teste

		if (!is_dir('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra')) {
			mkdir('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra');
		}
		if (!file_exists('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra\planilha_teste_automatico_importacao_ppra.csv')) {
			fopen('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra\planilha_teste_automatico_importacao_ppra.csv', 'w');
		}

		if (!is_dir('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso')) {
			mkdir('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso');
		}
		if (!file_exists('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso\planilha_teste_automatico_importacao_pcmso.csv')) {
			fopen('C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso\planilha_teste_automatico_importacao_pcmso.csv', 'w');
		}


		// REGRA DE VERSOES: nas importações de PPRA e PCMSO, os dados da nova versão serão aqueles encontrados na primeira linha de determinada unidade.
		// se esta primeira linha, por algum motivo, vier a dar erro, todas as linhas referentes à essa unidade serão descartadas.
		// no começo da leitura da primeira linha ( de cada unidade ), será verificado se foi atualizada a versao de determinada unidade, para seguir com a importação.
		// ERRO Ocorreu um erro ao incluir Ordem de Servico. - Para um PPRA/PCMSO sem ordem_servico, será necessário que o cliente possua um cliente_contato com retorno = 1 ( cliente_implantacao.php , metodo enviar_ordem_servico ).
		// ERRO Ocorreu um erro na conclusao da versao da unidade - As verificacoes de atualizacao da ordem_servico retornam possiveis erros no $this->log pelo exception ( importar.php - metodo valida_versao ).
	}

	public function testImportaPpraHierarquia()
	{
		$grupo_eco_cliente_teste = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('codigo_cliente' => 10110), 'fields' => array('codigo', 'codigo_cliente', 'bloqueado'), 'recursive' => -1));

		//***************************************************************************************************************************************************************\\
		$grupo_eco_cliente_teste['GrupoEconomicoCliente']['bloqueado'] = 0;
		$this->GrupoEconomicoCliente->atualizaBloqueio($grupo_eco_cliente_teste);
		//***************************************************************************************************************************************************************\\

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_ppra.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra\planilha_teste_automatico_importacao_ppra.csv';
		$array_header = 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Nome do Funcionário;CPF do Funcionário;Tipo do PPRA(1:Individual, 2:Individual por Funcionário, 3:Por Grupo Homogêneo);Nome do Grupo Homogêneo;Data da Vistoria;Pé Direito do Setor (3 Metros,Menor que 3 Metros,Maior que 3 Metros,Outros);Iluminação do Setor (Natural,Natural + Artificial (Florescentes),Natural + Artificial (Incandecentes),Natural + Artificial (Led),Natural + Artificial (Croica),Artificial (Florescentes),Artificial (Incandecentes),Artificial (Led),Artificial (Croica),Outros);Cobertura do Setor (Laje,Laje + Forro,Telhas Metálicas,Telhas Fibrocimento,Outros);Estrutura do Setor (Alvenaria,Concreto,Metálico,Madeira,Fechamento Lateral,Outros);Ventilação do Setor (Natural,Natural + Ventiladores,Natural + Ar Condicionado Local,Natural + Ar condicionado Central,Ar Condicionado Central,Outros);Piso do Setor (Industrial com revestimento, Industrial sem revestimento,Carpete de Madeira,Cerâmico,Outros);Observação;Descrição das Atividades;Medidas de Controle;CPF Funcionário (Entrevistado);Nome Funcionário (Entrevistado Terceiro);Data Início Vigência Grupo Exposição;Risco (Descrição);Fonte Geradora (Descrição);Efeito Crítico (Não Aplica,Leve,Moderado,Sério,Severo);Meio de Propagação (Ar,Contato,Ar / Contato);Tipo do Tempo de Exposicao (P: PERMANENTE,I: INTERMITENTE,O: OCASIONAL);Minutos;Jornada;Descanso;Intensidade (B: BAIXA,M: MÉDIA,A: ALTA,MA: MUITO ALTA);Exposição Resultante(I:IRRELEVANTE,A:DE ATENÇÃO,C:CRÍTICA,IN:INCERTA);Potencial de Dano (L: LEVE,B: BAIXO,M: MÉDIO,A: ALTO,I: IMINENTE);Grau de Risco (AC: ACEITÁVEL,M; MODERADO,A: ALTO,MA: MUITO ALTO);Tipo de Medição (1: Quantitativo, 2: Qualitativo);Dosimetria;Avaliação Instantanea;Técnica de Medição (º C,kgf/cm²,dB(A),dB(C),m/s,mSvMHz ou GHz);Valor Máximo;Valor Medido;Descanso no Local;Descanso TBN;Descanso TBS;Descanso TBG;Carga Solar;Trabalho TBN;Trabalho TBS;Trabalho TBG;EPI (Descrição);EPC (Descrição);CNPJ Fornecedor;Data Inicio Vigência Versão;Vigência Contrato (3, 6, 9, 12 Meses);Número do Conselho;Conselho Profissional(CREA/MTE);Conselho Estado(UF)
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		// caso 1 desbloqueado - sem erros, unidade existente, setor existente, cargo existente, hierarquia existente.
		// não criar nada.
		$dados_1 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_1);

		$retorno_1 = $this->Importar->importar_ppra($parametros);

		$this->assertTrue(empty($retorno_1['Erro']));

		// caso 2 desbloqueado - sem erros, unidade existente, setor existente, cargo existente , hierarquia inexistente.
		// cria hierarquia.
		$dados_2 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_2);

		$caso_2_hierarquia_antes = $this->ClienteSetorCargo->find('count');

		$retorno_2 = $this->Importar->importar_ppra($parametros);

		$caso_2_hierarquia_depois = $this->ClienteSetorCargo->find('count');

		$this->assertTrue(empty($retorno_2['Erro']));
		$this->assertEqual($caso_2_hierarquia_antes, $caso_2_hierarquia_depois - 1);

		// caso 3 desbloqueado - sem erros, unidade existente, setor inexistente, cargo existente, hierarquia inexistente.
		// cria setor, cria hierarquia.
		$dados_3 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor4A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_3);

		$caso_3_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_3_setor_antes = $this->Setor->find('count');

		$retorno_3 = $this->Importar->importar_ppra($parametros);

		$caso_3_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_3_setor_depois = $this->Setor->find('count');

		$this->assertTrue(empty($retorno_3['Erro']));
		$this->assertEqual($caso_3_hierarquia_antes, $caso_3_hierarquia_depois - 1);
		$this->assertEqual($caso_3_setor_antes, $caso_3_setor_depois - 1);

		// caso 4 desbloqueado - sem erros, unidade existente, setor existente, cargo inexistente, hierarquia inexistente.
		// cria cargo, cria hierarquia.
		$dados_4 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo4A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_4);

		$caso_4_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_4_cargo_antes = $this->Cargo->find('count');

		$retorno_4 = $this->Importar->importar_ppra($parametros);

		$caso_4_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_4_cargo_depois = $this->Cargo->find('count');

		$this->assertTrue(empty($retorno_4['Erro']));
		$this->assertEqual($caso_4_hierarquia_antes, $caso_4_hierarquia_depois - 1);
		$this->assertEqual($caso_4_cargo_antes, $caso_4_cargo_depois - 1);

		// caso 5 desbloqueado - sem erros, unidade existente, setor inexistente, cargo inexistente, hierarquia inexistente.
		// cria setor, cria cargo, cria hierarquia.
		$dados_5 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor5A;TesteCargo5A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_5);

		$caso_5_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_5_setor_antes = $this->Setor->find('count');
		$caso_5_cargo_antes = $this->Cargo->find('count');

		$retorno_5 = $this->Importar->importar_ppra($parametros);

		$caso_5_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_5_setor_depois = $this->Setor->find('count');
		$caso_5_cargo_depois = $this->Cargo->find('count');

		$this->assertTrue(empty($retorno_5['Erro']));
		$this->assertEqual($caso_5_hierarquia_antes, $caso_5_hierarquia_depois - 1);
		$this->assertEqual($caso_5_setor_antes, $caso_5_setor_depois - 1);
		$this->assertEqual($caso_5_cargo_antes, $caso_5_cargo_depois - 1);

		//***************************************************************************************************************************************************************\\
		$grupo_eco_cliente_teste['GrupoEconomicoCliente']['bloqueado'] = 1;
		$this->GrupoEconomicoCliente->atualizaBloqueio($grupo_eco_cliente_teste);
		//A criação de hierarquia depende da verificação de setor/cargo, logo se um dos dois não estiver presente e não for incluído, a chamada de hierarquia não é exexutada.
		//***************************************************************************************************************************************************************\\

		// caso 1 bloqueado - sem erros.
		$dados_6 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_6);
		$retorno_6 = $this->Importar->importar_ppra($parametros);
		$this->assertTrue(empty($retorno_6['Erro']));

		// caso 2 bloqueado - erro na criação da hierarquia.
		$dados_7 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3A;TesteCargo3A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_7);
		$retorno_7 = $this->Importar->importar_ppra($parametros);
		$this->assertEqual($retorno_7['Erro']['1']['erros']['ClienteSetorCargo']['codigo'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir a nova hierarquia.');

		// caso 3 bloqueado - erro na criação do setor.
		$dados_8 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor6A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_8);
		$retorno_8 = $this->Importar->importar_ppra($parametros);
		$this->assertEqual($retorno_8['Erro']['1']['erros']['Setor']['codigo_setor'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo setor.');

		// caso 4 bloqueado - erro na criação do cargo.
		$dados_9 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo6A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_9);
		$retorno_9 = $this->Importar->importar_ppra($parametros);
		$this->assertEqual($retorno_9['Erro']['1']['erros']['Cargo']['codigo_cargo'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo cargo.');

		// caso 5 bloqueado - erro na criação do setor e erro na criação do cargo.
		$dados_10 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor7A;TesteCargo7A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_10);
		$retorno_10 = $this->Importar->importar_ppra($parametros);
		$this->assertEqual($retorno_10['Erro']['1']['erros'], array('Setor' => array('codigo_setor' => 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo setor.'), 'Cargo' => array('codigo_cargo' => 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo cargo.')));
	}

	public function testImportaPcmsoHierarquia()
	{
		$grupo_eco_cliente_teste = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('codigo_cliente' => 10110), 'fields' => array('codigo', 'codigo_cliente', 'bloqueado'), 'recursive' => -1));

		//***************************************************************************************************************************************************************\\
		$grupo_eco_cliente_teste['GrupoEconomicoCliente']['bloqueado'] = 0;
		$this->GrupoEconomicoCliente->atualizaBloqueio($grupo_eco_cliente_teste);
		//***************************************************************************************************************************************************************\\

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_pcmso.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso\planilha_teste_automatico_importacao_pcmso.csv';
		$array_header = 'Código Externo Unidade;Nome do Setor;Nome do Cargo;Exame;Periodicidade  - Frequência (em Meses);Periodicidade - Após admissão;Aplicável em (A: Admissional, P: Periódico, D: Demissional, R: Retorno ao Trabalho, M: Mudança de Riscos Ocupacionais) - A|P|D|R|M;A partir de qual idade?;Solicitar este exame em quanto tempo?;A partir de qual idade? (2);Solicitar este exame em quanto tempo? (2);A partir de qual idade? (3);Solicitar este exame em quanto tempo? (3);A partir de qual idade? (4);Solicitar este exame em quanto tempo? (4);Objetivo do Exame(O: Ocupacional, Q: Qualidade de Vida);Tipos de Exames (CE: Convocação Exames, PP: PPP, AS: ASO, PC: PCMSO, RA: Relatório Anual) -  CE|PP|AS|PC|RA;CNPJ Fornecedor
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		// caso 1 desbloqueado - 
		// sem erros, unidade existente, setor existente, cargo existente, hierarquia existente.
		$dados_1 = 'Unidade operacional de teste;TesteSetor1B;TesteCargo1B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';

		file_put_contents($caminho, $array_header . $dados_1);

		$retorno_1 = $this->Importar->importar_pcmso($parametros);

		$this->assertTrue(empty($retorno_1['Erro']));

		// caso 2 desbloqueado - sem erros, unidade existente, setor existente, cargo existente , cria hierarquia.
		$dados_2 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_2);

		$caso_2_hierarquia_antes = $this->ClienteSetorCargo->find('count');

		$retorno_2 = $this->Importar->importar_pcmso($parametros);

		$caso_2_hierarquia_depois = $this->ClienteSetorCargo->find('count');

		$this->assertTrue(empty($retorno_2['Erro']));
		$this->assertEqual($caso_2_hierarquia_antes, $caso_2_hierarquia_depois - 1);

		// caso 3 desbloqueado - sem erros, unidade existente, cria setor, cargo existente, cria hierarquia.
		$dados_3 = 'Unidade operacional de teste;TesteSetor4B;TesteCargo2B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_3);

		$caso_3_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_3_setor_antes = $this->Setor->find('count');

		$retorno_3 = $this->Importar->importar_pcmso($parametros);

		$caso_3_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_3_setor_depois = $this->Setor->find('count');

		$this->assertTrue(empty($retorno_3['Erro']));
		$this->assertEqual($caso_3_hierarquia_antes, $caso_3_hierarquia_depois - 1);
		$this->assertEqual($caso_3_setor_antes, $caso_3_setor_depois - 1);

		// caso 4 desbloqueado - sem erros, unidade existente, setor existente, cria cargo, cria hierarquia.
		$dados_4 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo4B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_4);

		$caso_4_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_4_cargo_antes = $this->Cargo->find('count');

		$retorno_4 = $this->Importar->importar_pcmso($parametros);

		$caso_4_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_4_cargo_depois = $this->Cargo->find('count');

		$this->assertTrue(empty($retorno_4['Erro']));
		$this->assertEqual($caso_4_hierarquia_antes, $caso_4_hierarquia_depois - 1);
		$this->assertEqual($caso_4_cargo_antes, $caso_4_cargo_depois - 1);

		// caso 5 desbloqueado - sem erros, unidade existente, cria setor, cria cargo, cria hierarquia.
		$dados_5 = 'Unidade operacional de teste;TesteSetor5B;TesteCargo5B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_5);

		$caso_5_hierarquia_antes = $this->ClienteSetorCargo->find('count');
		$caso_5_setor_antes = $this->Setor->find('count');
		$caso_5_cargo_antes = $this->Cargo->find('count');

		$retorno_5 = $this->Importar->importar_pcmso($parametros);

		$caso_5_hierarquia_depois = $this->ClienteSetorCargo->find('count');
		$caso_5_setor_depois = $this->Setor->find('count');
		$caso_5_cargo_depois = $this->Cargo->find('count');

		$this->assertTrue(empty($retorno_5['Erro']));
		$this->assertEqual($caso_5_hierarquia_antes, $caso_5_hierarquia_depois - 1);
		$this->assertEqual($caso_5_setor_antes, $caso_5_setor_depois - 1);
		$this->assertEqual($caso_5_cargo_antes, $caso_5_cargo_depois - 1);

		//***************************************************************************************************************************************************************\\
		$grupo_eco_cliente_teste['GrupoEconomicoCliente']['bloqueado'] = 1;
		$this->GrupoEconomicoCliente->atualizaBloqueio($grupo_eco_cliente_teste);
		//A criação de hierarquia depende da verificação de setor/cargo, logo se um dos dois não estiver presente e não for incluído, a chamada de hierarquia não é exexutada.
		//***************************************************************************************************************************************************************\\

		// caso 1 bloqueado - sem erros.
		$dados_6 = 'Unidade operacional de teste;TesteSetor1B;TesteCargo1B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_6);
		$retorno_6 = $this->Importar->importar_pcmso($parametros);
		$this->assertTrue(empty($retorno_6['Erro']));

		// caso 2 bloqueado - erro na criação da hierarquia.
		$dados_7 = 'Unidade operacional de teste;TesteSetor3B;TesteCargo3B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_7);
		$retorno_7 = $this->Importar->importar_pcmso($parametros);
		$this->assertEqual($retorno_7['Erro']['1']['erros']['ClienteSetorCargo']['codigo'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir a nova hierarquia.');

		// caso 3 bloqueado - erro na criação do setor.
		$dados_8 = 'Unidade operacional de teste;TesteSetor6B;TesteCargo2B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_8);
		$retorno_8 = $this->Importar->importar_pcmso($parametros);
		$this->assertEqual($retorno_8['Erro']['1']['erros']['Setor']['codigo_setor'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo setor.');

		// caso 4 bloqueado - erro na criação do cargo.
		$dados_9 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo6B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_9);
		$retorno_9 = $this->Importar->importar_pcmso($parametros);
		$this->assertEqual($retorno_9['Erro']['1']['erros']['Cargo']['codigo_cargo'], 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo cargo.');

		// caso 5 bloqueado - erro na criação do setor e erro na criação do cargo.
		$dados_10 = 'Unidade operacional de teste;TesteSetor7B;TesteCargo7B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_10);
		$retorno_10 = $this->Importar->importar_pcmso($parametros);
		$this->assertEqual($retorno_10['Erro']['1']['erros'], array('Setor' => array('codigo_setor' => 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo setor.'), 'Cargo' => array('codigo_cargo' => 'A unidade encontra-se bloqueada, logo nao foi possivel incluir o novo cargo.')));
	}

	public function testImportaPpraFoto()
	{

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_ppra.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra\planilha_teste_automatico_importacao_ppra.csv';
		$array_header = 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Nome do Funcionário;CPF do Funcionário;Tipo do PPRA(1:Individual, 2:Individual por Funcionário, 3:Por Grupo Homogêneo);Nome do Grupo Homogêneo;Data da Vistoria;Pé Direito do Setor (3 Metros,Menor que 3 Metros,Maior que 3 Metros,Outros);Iluminação do Setor (Natural,Natural + Artificial (Florescentes),Natural + Artificial (Incandecentes),Natural + Artificial (Led),Natural + Artificial (Croica),Artificial (Florescentes),Artificial (Incandecentes),Artificial (Led),Artificial (Croica),Outros);Cobertura do Setor (Laje,Laje + Forro,Telhas Metálicas,Telhas Fibrocimento,Outros);Estrutura do Setor (Alvenaria,Concreto,Metálico,Madeira,Fechamento Lateral,Outros);Ventilação do Setor (Natural,Natural + Ventiladores,Natural + Ar Condicionado Local,Natural + Ar condicionado Central,Ar Condicionado Central,Outros);Piso do Setor (Industrial com revestimento, Industrial sem revestimento,Carpete de Madeira,Cerâmico,Outros);Observação;Descrição das Atividades;Medidas de Controle;CPF Funcionário (Entrevistado);Nome Funcionário (Entrevistado Terceiro);Data Início Vigência Grupo Exposição;Risco (Descrição);Fonte Geradora (Descrição);Efeito Crítico (Não Aplica,Leve,Moderado,Sério,Severo);Meio de Propagação (Ar,Contato,Ar / Contato);Tipo do Tempo de Exposicao (P: PERMANENTE,I: INTERMITENTE,O: OCASIONAL);Minutos;Jornada;Descanso;Intensidade (B: BAIXA,M: MÉDIA,A: ALTA,MA: MUITO ALTA);Exposição Resultante(I:IRRELEVANTE,A:DE ATENÇÃO,C:CRÍTICA,IN:INCERTA);Potencial de Dano (L: LEVE,B: BAIXO,M: MÉDIO,A: ALTO,I: IMINENTE);Grau de Risco (AC: ACEITÁVEL,M; MODERADO,A: ALTO,MA: MUITO ALTO);Tipo de Medição (1: Quantitativo, 2: Qualitativo);Dosimetria;Avaliação Instantanea;Técnica de Medição (º C,kgf/cm²,dB(A),dB(C),m/s,mSvMHz ou GHz);Valor Máximo;Valor Medido;Descanso no Local;Descanso TBN;Descanso TBS;Descanso TBG;Carga Solar;Trabalho TBN;Trabalho TBS;Trabalho TBG;EPI (Descrição);EPC (Descrição);CNPJ Fornecedor;Data Inicio Vigência Versão;Vigência Contrato (3, 6, 9, 12 Meses);Número do Conselho;Conselho Profissional(CREA/MTE);Conselho Estado(UF)
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		/************************** TESTES DE PLANILHA - ERRO *****************************************/

		// caso 0A - importa planilha com risco vazio - erro
		$dados_0A = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0A);

		$retorno_0A = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0A['Erro']['1']['erros']['Erro']['Risco']['codigo_risco'], 'Risco nao enviado!');

		// caso 0B - importa planilha com 2 riscos vazios para grupo_exposicao iguais - erro
		$dados_0B = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0B);

		$retorno_0B = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0B['Erro']['1']['erros']['Erro']['Risco']['codigo_risco'], 'Risco nao enviado!');
		$this->assertEqual($retorno_0B['Erro']['2']['erros']['Erro']['Risco']['codigo_risco'], 'Risco nao enviado!');

		// caso 0C - importa planilha com 2 riscos vazios para grupo_exposicao diferentes - erro
		$dados_0C = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0C);

		$retorno_0C = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0C['Erro']['1']['erros']['Erro']['Risco']['codigo_risco'], 'Risco nao enviado!');
		$this->assertEqual($retorno_0C['Erro']['2']['erros']['Erro']['Risco']['codigo_risco'], 'Risco nao enviado!');

		/************************** TESTES DE PLANILHA - 1 REGISTRO *****************************************/

		// caso 1 - importa risco inexistente no grupo_exposição que atualmente não possui risco
		// 		  - inclui o risco.
		$dados_1 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_1);

		$caso_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$retorno_1 = $this->Importar->importar_ppra($parametros);

		$caso_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$this->assertEqual($caso_1_gru_exp_risco_antes, $caso_1_gru_exp_risco_depois - 1);

		// caso 2 - importa risco existente no grupo_exposição que atualmente possui somente esse risco
		//		  - atualiza o risco.
		$dados_2 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_2);

		$caso_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$retorno_2 = $this->Importar->importar_ppra($parametros);

		$caso_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$this->assertEqual($caso_2_gru_exp_risco_antes, $caso_2_gru_exp_risco_depois);

		// caso 3 - importa risco inexistente no grupo_exposição que atualmente possui outro risco
		//		  - inclui o risco e deleta o que já existia.
		$dados_3 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_3);

		$caso_3_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$retorno_3 = $this->Importar->importar_ppra($parametros);

		$caso_3_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$this->assertEqual($caso_3_gru_exp_risco_antes, $caso_3_gru_exp_risco_depois);

		/************************** TESTES DE PLANILHA - 2 REGISTROS, MESMO GRUPO_EXPOSICAO *****************************************/

		// caso 4 - importa 2 riscos inexistentes no grupo_exposição que atualmente não possui risco
		// 	 	  - inclui os 2 riscos.
		$dados_4 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_4);

		$caso_4_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10002)));

		$retorno_4 = $this->Importar->importar_ppra($parametros);

		$caso_4_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10002)));

		$this->assertEqual($caso_4_gru_exp_risco_antes, $caso_4_gru_exp_risco_depois - 2);

		// caso 5 - importa 2 riscos existentes no grupo_exposição que atualmente possui somente esses riscos
		//		  - atualiza os riscos.
		$dados_5 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2A;TesteCargo2A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_5);

		$caso_5_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10002)));

		$retorno_5 = $this->Importar->importar_ppra($parametros);

		$caso_5_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10002)));

		$this->assertEqual($caso_5_gru_exp_risco_antes, $caso_5_gru_exp_risco_depois);

		// caso 6 - importa 1 risco existente e 1 risco inexistente no grupo_exposição que atualmente possui somente esse risco
		// 		  - atualiza o risco existente, inclui o inexistente.
		$dados_6 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3A;TesteCargo3A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3A;TesteCargo3A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_6);

		$caso_6_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10003)));

		$retorno_6 = $this->Importar->importar_ppra($parametros);

		$caso_6_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10003)));

		$this->assertEqual($caso_6_gru_exp_risco_antes, $caso_6_gru_exp_risco_depois - 1);

		// caso 7 - importa 1 risco existente e 1 risco inexistente no grupo_exposição que atualmente possui outros riscos
		//		  - atualiza o risco existente, inclui o inexistente e deleta os outros.
		$dados_7 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1B;TesteCargo1B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1B;TesteCargo1B;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_7);

		$caso_7_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10004)));

		$retorno_7 = $this->Importar->importar_ppra($parametros);

		$caso_7_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10004)));

		$this->assertEqual($caso_7_gru_exp_risco_antes, $caso_7_gru_exp_risco_depois);

		/************************** TESTES DE PLANILHA - 2 REGISTROS, GRUPO_EXPOSICAO DIFERENTES *****************************************/

		// caso 8 - importa riscos que não existem em ambos grupo_exposicao, sem riscos adicionais
		//		  - inclui ambos.
		$dados_8 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2B;TesteCargo2B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3B;TesteCargo3B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_8);

		$caso_8_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_8_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$retorno_8 = $this->Importar->importar_ppra($parametros);

		$caso_8_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_8_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$this->assertEqual($caso_8_1_gru_exp_risco_antes, $caso_8_1_gru_exp_risco_depois - 1);
		$this->assertEqual($caso_8_2_gru_exp_risco_antes, $caso_8_2_gru_exp_risco_depois - 1);

		// caso 9 - importa riscos que existem em ambos grupo_exposicao, sem riscos adicionais
		//		  - atualiza ambos.
		$dados_9 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2B;TesteCargo2B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3B;TesteCargo3B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_9);

		$caso_9_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_9_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$retorno_9 = $this->Importar->importar_ppra($parametros);

		$caso_9_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_9_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$this->assertEqual($caso_9_1_gru_exp_risco_antes, $caso_9_1_gru_exp_risco_depois);
		$this->assertEqual($caso_9_2_gru_exp_risco_antes, $caso_9_2_gru_exp_risco_depois);

		// caso 10 - importa riscos que não existem em ambos grupo_exposicao, riscos adicionais
		//		   - inclui ambos, deleta os adicionais.
		$dados_10 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2B;TesteCargo2B;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3B;TesteCargo3B;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_10);

		$caso_10_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_10_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$retorno_10 = $this->Importar->importar_ppra($parametros);

		$caso_10_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_10_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$this->assertEqual($caso_10_1_gru_exp_risco_antes, $caso_10_1_gru_exp_risco_depois);
		$this->assertEqual($caso_10_2_gru_exp_risco_antes, $caso_10_2_gru_exp_risco_depois);

		// caso 11 - importa riscos que existem em ambos grupo_exposicao, riscos adicionais
		//		   - atualiza ambos, deleta os adicionais.
		$dados_11 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3A;TesteCargo3A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1B;TesteCargo1B;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_11);

		$caso_11_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10003)));
		$caso_11_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10004)));

		$retorno_11 = $this->Importar->importar_ppra($parametros);

		$caso_11_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10003)));
		$caso_11_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10004)));

		$this->assertEqual($caso_11_1_gru_exp_risco_antes, $caso_11_1_gru_exp_risco_depois + 1);
		$this->assertEqual($caso_11_2_gru_exp_risco_antes, $caso_11_2_gru_exp_risco_depois + 1);

		// caso 12 - importa risco que existe em um e não existe no outro grupo_exposicao, sem riscos adicionais
		//		   - atualiza o grupo_exposicao_risco com risco que já existe e inclui o risco no grupo_exposicao que não possui o risco.
		$dados_12 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteImportacao;TesteImportacao;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_12);

		$caso_12_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10000)));
		$caso_12_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$retorno_12 = $this->Importar->importar_ppra($parametros);

		$caso_12_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10000)));
		$caso_12_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10001)));

		$this->assertEqual($caso_12_1_gru_exp_risco_antes, $caso_12_1_gru_exp_risco_depois - 1);
		$this->assertEqual($caso_12_2_gru_exp_risco_antes, $caso_12_2_gru_exp_risco_depois);

		// caso 13 - importa risco que existe em um e não existe no outro grupo_exposicao, com riscos adicionais
		//		   - atualiza o grupo_exposicao_risco com risco que já existe e inclui o risco no grupo_exposicao que não possui o risco; deleta o risco adicional.
		$dados_13 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor2B;TesteCargo2B;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor3B;TesteCargo3B;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;Leve;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_13);

		$caso_13_1_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_13_2_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$retorno_13 = $this->Importar->importar_ppra($parametros);

		$caso_13_1_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10005)));
		$caso_13_2_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo_grupo_exposicao' => 10006)));

		$this->assertEqual($caso_13_1_gru_exp_risco_antes, $caso_13_1_gru_exp_risco_depois);
		$this->assertEqual($caso_13_2_gru_exp_risco_antes, $caso_13_2_gru_exp_risco_depois);

		// caso 14 - TESTE DE EXCLUSAO DE RISCO ATRELADO
		$dados_14 = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteExclusao;TesteExclusao;;;1;;;;;;;;;;;;;;01/01/2018;Risco Grave de Morte.;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_14);

		$caso_14_gru_exp_risco_antes = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo' => 21339)));
		$caso_14_gru_exp_risco_atrib_det_antes = $this->GrupoExpRiscoAtribDet->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_epi_antes = $this->GrupoExposicaoRiscoEpi->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_epc_antes = $this->GrupoExposicaoRiscoEpc->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_fonte_gera_antes = $this->GrupoExpRiscoFonteGera->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));

		$retorno_14 = $this->Importar->importar_ppra($parametros);

		$caso_14_gru_exp_risco_depois = $this->GrupoExposicaoRisco->find('count', array('conditions' => array('codigo' => 21339)));
		$caso_14_gru_exp_risco_atrib_det_depois = $this->GrupoExpRiscoAtribDet->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_epi_depois = $this->GrupoExposicaoRiscoEpi->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_epc_depois = $this->GrupoExposicaoRiscoEpc->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));
		$caso_14_gru_exp_risco_fonte_gera_depois = $this->GrupoExpRiscoFonteGera->find('count', array('conditions' => array('codigo_grupos_exposicao_risco' => 21339)));

		$this->assertEqual($caso_14_gru_exp_risco_antes, $caso_14_gru_exp_risco_depois + 1);
		$this->assertEqual($caso_14_gru_exp_risco_atrib_det_antes, $caso_14_gru_exp_risco_atrib_det_depois + 1);
		$this->assertEqual($caso_14_gru_exp_risco_epi_antes, $caso_14_gru_exp_risco_epi_depois + 1);
		$this->assertEqual($caso_14_gru_exp_risco_epc_antes, $caso_14_gru_exp_risco_epc_depois + 1);
		$this->assertEqual($caso_14_gru_exp_risco_fonte_gera_antes, $caso_14_gru_exp_risco_fonte_gera_depois + 1);
	}

	public function testImportaPcmsoFoto()
	{

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_pcmso.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso\planilha_teste_automatico_importacao_pcmso.csv';
		$array_header = 'Código Externo Unidade;Nome do Setor;Nome do Cargo;Exame;Periodicidade  - Frequência (em Meses);Periodicidade - Após admissão;Aplicável em (A: Admissional, P: Periódico, D: Demissional, R: Retorno ao Trabalho, M: Mudança de Riscos Ocupacionais) - A|P|D|R|M;A partir de qual idade?;Solicitar este exame em quanto tempo?;A partir de qual idade? (2);Solicitar este exame em quanto tempo? (2);A partir de qual idade? (3);Solicitar este exame em quanto tempo? (3);A partir de qual idade? (4);Solicitar este exame em quanto tempo? (4);Objetivo do Exame(O: Ocupacional, Q: Qualidade de Vida);Tipos de Exames (CE: Convocação Exames, PP: PPP, AS: ASO, PC: PCMSO, RA: Relatório Anual) -  CE|PP|AS|PC|RA;CNPJ Fornecedor
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		/************************** TESTES DE PLANILHA - ERRO *****************************************/

		// caso 0A - importa planilha com exame vazio - erro
		$dados_0A = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_0A);

		$retorno_0A = $this->Importar->importar_pcmso($parametros);

		$this->assertEqual($retorno_0A['Erro']['1']['erros']['AplicacaoExame']['codigo_exame'], 'Exame nao enviado!');

		// caso 0B - importa planilha com 2 exames vazios para aplicacao_exame iguais - erro
		$dados_0B = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_0B);

		$retorno_0B = $this->Importar->importar_pcmso($parametros);

		$this->assertEqual($retorno_0B['Erro']['1']['erros']['AplicacaoExame']['codigo_exame'], 'Exame nao enviado!');
		$this->assertEqual($retorno_0B['Erro']['2']['erros']['AplicacaoExame']['codigo_exame'], 'Exame nao enviado!');

		// caso 0C - importa planilha com 2 exames vazios para aplicacao_exame diferentes - erro
		$dados_0C = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor1B;TesteCargo1B;;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_0C);

		$retorno_0C = $this->Importar->importar_pcmso($parametros);

		$this->assertEqual($retorno_0C['Erro']['1']['erros']['AplicacaoExame']['codigo_exame'], 'Exame nao enviado!');
		$this->assertEqual($retorno_0C['Erro']['2']['erros']['AplicacaoExame']['codigo_exame'], 'Exame nao enviado!');

		/************************** TESTES DE PLANILHA - 1 REGISTRO *****************************************/

		// caso 1 - importa exame inexistente na aplicacao_exame que atualmente não possui exame
		// 		  - inclui o exame (inclui aso).
		$dados_1 = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_1);

		$caso_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$retorno_1 = $this->Importar->importar_pcmso($parametros);

		$caso_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_1_apli_exame_antes, $caso_1_apli_exame_depois - 2);

		// caso 2 - importa exame existente na aplicação_exame que atualmente possui somente esse exame
		//		  - atualiza o exame.
		$dados_2 = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_2);

		$caso_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$retorno_2 = $this->Importar->importar_pcmso($parametros);

		$caso_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_2_apli_exame_antes, $caso_2_apli_exame_depois);

		// caso 3 - importa exame inexistente na aplicação_exame que atualmente possui outro exame
		//		  - inclui o exame e deleta o que já existia (aso não é excluído).
		$dados_3 = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_3);

		$caso_3_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$retorno_3 = $this->Importar->importar_pcmso($parametros);

		$caso_3_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_3_apli_exame_antes, $caso_3_apli_exame_depois);

		/************************** TESTES DE PLANILHA - 2 REGISTROS, MESMO GRUPO_EXPOSICAO *****************************************/

		// caso 4 - importa 2 exames inexistentes na aplicação_exame que atualmente não possui exame
		// 	 	  - inclui os 2 exames (inclui aso).
		$dados_4 = 'Unidade operacional de teste;TesteSetor2A;TesteCargo2A;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor2A;TesteCargo2A;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_4);

		$caso_4_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1002, 'codigo_cargo' => 1002, 'codigo_cliente_alocacao' => 10110)));

		$retorno_4 = $this->Importar->importar_pcmso($parametros);

		$caso_4_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1002, 'codigo_cargo' => 1002, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_4_apli_exame_antes, $caso_4_apli_exame_depois - 3);

		// caso 5 - importa 2 exames existentes na aplicação_exame que atualmente possui somente esses exames
		//		  - atualiza os exames.
		$dados_5 = 'Unidade operacional de teste;TesteSetor2A;TesteCargo2A;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor2A;TesteCargo2A;AUDIOMETRIA;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_5);

		$caso_5_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1002, 'codigo_cargo' => 1002, 'codigo_cliente_alocacao' => 10110)));

		$retorno_5 = $this->Importar->importar_pcmso($parametros);

		$caso_5_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1002, 'codigo_cargo' => 1002, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_5_apli_exame_antes, $caso_5_apli_exame_depois);

		// caso 6 - importa 1 exame existente e 1 exame inexistente na aplicação_exame que atualmente possui somente esse exame
		// 		  - atualiza o exame existente, inclui o inexistente.
		$dados_6 = 'Unidade operacional de teste;TesteSetor3A;TesteCargo3A;*ASO - EXAME CLÃNICO;1;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3A;TesteCargo3A;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_6);

		$caso_6_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1003, 'codigo_cargo' => 1003, 'codigo_cliente_alocacao' => 10110)));

		$retorno_6 = $this->Importar->importar_pcmso($parametros);

		$caso_6_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1003, 'codigo_cargo' => 1003, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_6_apli_exame_antes, $caso_6_apli_exame_depois - 1);

		// caso 7 - importa 1 exame existente e 1 exame inexistente na aplicação_exame que atualmente possui outros exames
		//		  - atualiza o exame existente, inclui o inexistente e deleta os outros.
		$dados_7 = 'Unidade operacional de teste;TesteSetor1B;TesteCargo1B;*ASO - EXAME CLÃNICO;1;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor1B;TesteCargo1B;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_7);

		$caso_7_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1004, 'codigo_cargo' => 1004, 'codigo_cliente_alocacao' => 10110)));

		$retorno_7 = $this->Importar->importar_pcmso($parametros);

		$caso_7_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1004, 'codigo_cargo' => 1004, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_7_apli_exame_antes, $caso_7_apli_exame_depois);

		/************************** TESTES DE PLANILHA - 2 REGISTROS, GRUPO_EXPOSICAO DIFERENTES *****************************************/

		// caso 8 - importa exame que não existem em ambos aplicação_exame, sem exame adicionais
		//		  - inclui exame (inclui aso).
		$dados_8 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3B;TesteCargo3B;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_8);

		$caso_8_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_8_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$retorno_8 = $this->Importar->importar_pcmso($parametros);

		$caso_8_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_8_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_8_1_apli_exame_antes, $caso_8_1_apli_exame_depois - 2);
		$this->assertEqual($caso_8_2_apli_exame_antes, $caso_8_2_apli_exame_depois - 2);

		// caso 9 - importa exame que existem em ambos aplicação_exame, sem exames adicionais
		//		  - atualiza ambos.
		$dados_9 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3B;TesteCargo3B;ACIDO URICO;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_9);

		$caso_9_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_9_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$retorno_9 = $this->Importar->importar_pcmso($parametros);

		$caso_9_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_9_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_9_1_apli_exame_antes, $caso_9_1_apli_exame_depois);
		$this->assertEqual($caso_9_2_apli_exame_antes, $caso_9_2_apli_exame_depois);

		// caso 10 - importa exame que não existem em ambos aplicação_exame, exames adicionais
		//		   - inclui ambos, deleta os adicionais.
		$dados_10 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3B;TesteCargo3B;AUDIOMETRIA;;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_10);

		$caso_10_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_10_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$retorno_10 = $this->Importar->importar_pcmso($parametros);

		$caso_10_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_10_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_10_1_apli_exame_antes, $caso_10_1_apli_exame_depois);
		$this->assertEqual($caso_10_2_apli_exame_antes, $caso_10_2_apli_exame_depois);

		// caso 11 - importa exame que existem em ambas aplicação_exame, exames adicionais
		//		   - atualiza ambos, deleta os adicionais.
		$dados_11 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;AUDIOMETRIA;1;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3B;TesteCargo3B;AUDIOMETRIA;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_11);

		$caso_11_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_11_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$retorno_11 = $this->Importar->importar_pcmso($parametros);

		$caso_11_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_11_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_11_1_apli_exame_antes, $caso_11_1_apli_exame_depois);
		$this->assertEqual($caso_11_2_apli_exame_antes, $caso_11_2_apli_exame_depois);

		// caso 12 - importa exame que existe em uma aplicação_exame e um exame que não existe na outra aplicação_exame, sem riscos adicionais
		//		   - atualiza a aplicação_exame com exame que já existe e inclui o exame na aplicação_exame que não possui o exame (inclui aso).
		$dados_12 = 'Unidade operacional de teste;TesteImportacao;TesteImportacao;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor1A;TesteCargo1A;AUDIOMETRIA;1;;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_12);

		$caso_12_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1000, 'codigo_cargo' => 1000, 'codigo_cliente_alocacao' => 10110)));
		$caso_12_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$retorno_12 = $this->Importar->importar_pcmso($parametros);

		$caso_12_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1000, 'codigo_cargo' => 1000, 'codigo_cliente_alocacao' => 10110)));
		$caso_12_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1001, 'codigo_cargo' => 1001, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_12_1_apli_exame_antes, $caso_12_1_apli_exame_depois - 2);
		$this->assertEqual($caso_12_2_apli_exame_antes, $caso_12_2_apli_exame_depois);

		// caso 13 - importa risco que existe em um e não existe no outro aplicação_exame, com riscos adicionais
		//		   - atualiza o aplicação_exame_risco com risco que já existe e inclui o risco no aplicação_exame que não possui o risco; deleta o risco adicional.
		$dados_13 = 'Unidade operacional de teste;TesteSetor2B;TesteCargo2B;ACIDO URICO;;;;;;;;;;;;O;;00326844000127;
		Unidade operacional de teste;TesteSetor3B;TesteCargo3B;AUDIOMETRIA;1;1;;;;;;;;;;O;;00326844000127;'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		file_put_contents($caminho, $array_header . $dados_13);

		$caso_13_1_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_13_2_apli_exame_antes = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$retorno_13 = $this->Importar->importar_pcmso($parametros);

		$caso_13_1_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1005, 'codigo_cargo' => 1005, 'codigo_cliente_alocacao' => 10110)));
		$caso_13_2_apli_exame_depois = $this->AplicacaoExame->find('count', array('conditions' => array('codigo_setor' => 1006, 'codigo_cargo' => 1006, 'codigo_cliente_alocacao' => 10110)));

		$this->assertEqual($caso_13_1_apli_exame_antes, $caso_13_1_apli_exame_depois);
		$this->assertEqual($caso_13_2_apli_exame_antes, $caso_13_2_apli_exame_depois);
	}

	public function testImportaPpraVersionamento()
	{

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_ppra.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_ppra\planilha_teste_automatico_importacao_ppra.csv';
		$array_header = 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Nome do Funcionário;CPF do Funcionário;Tipo do PPRA(1:Individual, 2:Individual por Funcionário, 3:Por Grupo Homogêneo);Nome do Grupo Homogêneo;Data da Vistoria;Pé Direito do Setor (3 Metros,Menor que 3 Metros,Maior que 3 Metros,Outros);Iluminação do Setor (Natural,Natural + Artificial (Florescentes),Natural + Artificial (Incandecentes),Natural + Artificial (Led),Natural + Artificial (Croica),Artificial (Florescentes),Artificial (Incandecentes),Artificial (Led),Artificial (Croica),Outros);Cobertura do Setor (Laje,Laje + Forro,Telhas Metálicas,Telhas Fibrocimento,Outros);Estrutura do Setor (Alvenaria,Concreto,Metálico,Madeira,Fechamento Lateral,Outros);Ventilação do Setor (Natural,Natural + Ventiladores,Natural + Ar Condicionado Local,Natural + Ar condicionado Central,Ar Condicionado Central,Outros);Piso do Setor (Industrial com revestimento, Industrial sem revestimento,Carpete de Madeira,Cerâmico,Outros);Observação;Descrição das Atividades;Medidas de Controle;CPF Funcionário (Entrevistado);Nome Funcionário (Entrevistado Terceiro);Data Início Vigência Grupo Exposição;Risco (Descrição);Fonte Geradora (Descrição);Efeito Crítico (Não Aplica,Leve,Moderado,Sério,Severo);Meio de Propagação (Ar,Contato,Ar / Contato);Tipo do Tempo de Exposicao (P: PERMANENTE,I: INTERMITENTE,O: OCASIONAL);Minutos;Jornada;Descanso;Intensidade (B: BAIXA,M: MÉDIA,A: ALTA,MA: MUITO ALTA);Exposição Resultante(I:IRRELEVANTE,A:DE ATENÇÃO,C:CRÍTICA,IN:INCERTA);Potencial de Dano (L: LEVE,B: BAIXO,M: MÉDIO,A: ALTO,I: IMINENTE);Grau de Risco (AC: ACEITÁVEL,M; MODERADO,A: ALTO,MA: MUITO ALTO);Tipo de Medição (1: Quantitativo, 2: Qualitativo);Dosimetria;Avaliação Instantanea;Técnica de Medição (º C,kgf/cm²,dB(A),dB(C),m/s,mSvMHz ou GHz);Valor Máximo;Valor Medido;Descanso no Local;Descanso TBN;Descanso TBS;Descanso TBG;Carga Solar;Trabalho TBN;Trabalho TBS;Trabalho TBG;EPI (Descrição);EPC (Descrição);CNPJ Fornecedor;Data Inicio Vigência Versão;Vigência Contrato (3, 6, 9, 12 Meses);Número do Conselho;Conselho Profissional(CREA/MTE);Conselho Estado(UF)
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		/************************** TESTES DE PLANILHA - ERRO *****************************************/

		// caso 0A - importa planilha com CNPJ vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0A = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;;02/02/2018;6;123321;MTE;SP
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0A);

		$retorno_0A = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0A['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - CNPJ do Fornecedor nao encontrado.');
		$this->assertTrue(isset($retorno_0A['Sucesso']['2']));
		$this->assertEqual($retorno_0A['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');

		// caso 0B - importa planilha com Data Vigencia vazia - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0B = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;;6;123321;MTE;SP
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0B);

		$retorno_0B = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0B['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - Data inicio vigencia invalida.');
		$this->assertTrue(isset($retorno_0B['Sucesso']['2']));
		$this->assertEqual($retorno_0B['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');

		// caso 0C - importa planilha com Vigencia vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0C = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;;123321;MTE;SP
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0C);

		$retorno_0C = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0C['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - Duracao da vigencia invalida.');
		$this->assertTrue(isset($retorno_0C['Sucesso']['2']));
		$this->assertEqual($retorno_0C['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');

		// caso 0DA - importa planilha com Profissional Responsavel vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0DA = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;;MTE;SP
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0DA);

		$retorno_0DA = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0DA['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - Numero do conselho do profissional nao encontrado.');
		$this->assertTrue(isset($retorno_0DA['Sucesso']['2']));
		$this->assertEqual($retorno_0DA['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');

		// caso 0DB - importa planilha com Conselho Profissional vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0DB = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;;SP
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0DB);

		$retorno_0DB = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0DB['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - Numero do conselho do profissional nao encontrado.');
		$this->assertTrue(isset($retorno_0DB['Sucesso']['2']));
		$this->assertEqual($retorno_0DB['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');

		// caso 0DC - importa planilha com Conselho Estado vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_0DC = 'UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;
 		EMPRESA TREINAMENTO;EMPRESA TREINAMENTO;TREINAMENTO;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP
 		UNIDADE OPERACIONAL DE TESTE;Unidade operacional de teste;Unidade operacional de teste;TesteSetor1A;TesteCargo1A;;;1;;;;;;;;;;;;;;01/01/2018;Calor;;;;;;;;;;;;;;;;;;;;;;;;;;;;00326844000127;02/02/2018;6;123321;MTE;SP';
		file_put_contents($caminho, $array_header . $dados_0DC);

		$retorno_0DC = $this->Importar->importar_ppra($parametros);

		$this->assertEqual($retorno_0DC['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - Numero do conselho do profissional nao encontrado.');
		$this->assertTrue(isset($retorno_0DC['Sucesso']['2']));
		$this->assertEqual($retorno_0DC['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');
	}

	public function testImportaPcmsoVersionamento()
	{

		$parametros = array(
			'Importar' => array(
				'codigo_cliente' => 10011,
				'arquivo' => array(
					'name' => 'planilha_teste_automatico_importacao_pcmso.csv',
					'tmp_name' => ''
				)
			)
		);

		$caminho = 'C:\home\sistemas\rhhealth\portal\app\tmp\importacao_pcmso\planilha_teste_automatico_importacao_pcmso.csv';
		$array_header = 'Código Externo Unidade;Nome do Setor;Nome do Cargo;Exame;Periodicidade  - Frequência (em Meses);Periodicidade - Após admissão;Aplicável em (A: Admissional, P: Periódico, D: Demissional, R: Retorno ao Trabalho, M: Mudança de Riscos Ocupacionais) - A|P|D|R|M;A partir de qual idade?;Solicitar este exame em quanto tempo?;A partir de qual idade? (2);Solicitar este exame em quanto tempo? (2);A partir de qual idade? (3);Solicitar este exame em quanto tempo? (3);A partir de qual idade? (4);Solicitar este exame em quanto tempo? (4);Objetivo do Exame(O: Ocupacional, Q: Qualidade de Vida);Tipos de Exames (CE: Convocação Exames, PP: PPP, AS: ASO, PC: PCMSO, RA: Relatório Anual) -  CE|PP|AS|PC|RA;CNPJ Fornecedor
			'; // ENTER FOI PROPOSITAL PARA PULAR LINHA NO CSV!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		/************************** TESTES DE PLANILHA - ERRO *****************************************/

		// caso erro - importa planilha com CNPJ vazio - erro principal no primeiro registro e erro secundário nos demais da mesma unidade
		$dados_erro = 'Unidade operacional de teste;TesteSetor1A;TesteCargo1A;*ASO - EXAME CLÍNICO;1;;;;;;;;;;;O;;;
 		TREINAMENTO;TesteSetor1A;TesteCargo1A;*ASO - EXAME CLÍNICO;1;;;;;;;;;;;O;;00326844000127;
 		Unidade operacional de teste;TesteSetor1A;TesteCargo1A;*ASO - EXAME CLÍNICO;1;;;;;;;;;;;O;;00326844000127;';
		file_put_contents($caminho, $array_header . $dados_erro);

		$retorno_erro = $this->Importar->importar_pcmso($parametros);

		$this->assertEqual($retorno_erro['Erro']['1']['erros']['OrdemServico']['10110'], 'Ocorreu um erro ao abrir a nova versão - CNPJ do Fornecedor nao encontrado.');
		$this->assertTrue(isset($retorno_erro['Sucesso']['2']));
		$this->assertEqual($retorno_erro['Erro']['3']['erros']['OrdemServico']['10110'], 'Os registros dessa unidade foram invalidados, ja que nao foi possivel abrir uma nova versao.');
	}

	public function endTest()
	{
		ClassRegistry::flush();
	}
}
