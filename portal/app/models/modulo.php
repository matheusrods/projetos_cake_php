<?php
class Modulo extends AppModel {
	var $name = 'Modulo';
	var $useTable = false;

	const ADMIN = 1;
	const FINANCEIRO = 2;
	const CREDENCIAMENTO = 3;
	const COMERCIAL = 4;
	const JURIDICO = 5;
	const BUONNYCREDIT = 6;
	const TELECONSULT = 7;
	const RH = 8;
	const SEM_MODULO = 9;
	const SAUDE = 10;
	const SEGURANCA = 11;
	const ADMINISTRATIVO = 13;
	const MAPEAMENTORISCO = 14;
	const GESTAOCONTRATOS = 15;
	const AGENDA = 16;
	const ESOCIAL = 17;
	const COVID = 18;
	const GESTAODOCUMENTOS = 21;
	const CONTASMEDICAS = 19;
    const PLANO_DE_ACAO = 20;
    const WALK_TALK = 22;
	const OBSERVADOR_EHS = 23;

	function modulos(){
		return array(
			array(
				'nome' => 'Sistema',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_admin')
			), 
			array(
				'nome' => 'Administrativo',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_administrativo')
			),				
			array(
				'nome' => 'Financeiro',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_financeiro')
			), 
			array(
				'nome' => 'Gestão de Contratos',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_gestao_contrato')
			), 
			array(
				'nome' => 'Credenciamento',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_credenciamento')
			), 
			array(
				'nome' => 'Comercial',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_comercial')
			), 
			array(
				'nome' => 'Agenda',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_agenda')
			), 
			array(
				'nome' => 'Jurídico',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_juridico')
			), 
			array(
				'nome' => 'BuonnyCredit',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_buonnycredit')
			),
			array(
				'nome' => 'Teleconsult',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_teleconsult')
			), 
			array(
				'nome' => 'RH',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_rh')
			), 
			array(
				'nome' => '',
				'url' => array('controller' => 'Painel', 'action' => 'sem_modulo')
			),
			array(
				'nome' => 'Saúde',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_saude')
			),
			array(
				'nome' => 'Segurança',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_seguranca')
			), 
			array(
				'nome' => 'Mapeamento Risco',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_mapeamento_risco')
			),
			array(
				'nome' => 'E-Social',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_e_social')
			),
			array(
				'nome' => 'Covid',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_covid')
			),
			array(
				'nome' => 'Gestão Documentos',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_gestao_documentos')
			),
			array(
				'nome' => 'Contas Médicas',
				'url' => array('controller' => 'Painel', 'action' => 'modulo_contas_medicas')
			),
            array(
                'nome' => 'Walk & Talk',
                'url' => array('controller' => 'Painel', 'action' => 'modulo_walk_talk')
            ),
			array(
                'nome' => 'Observador EHS',
                'url' => array('controller' => 'Painel', 'action' => 'modulo_observador_ehs')
            ),
            array(
                'nome' => 'Plano de Ação',
                'url' => array('controller' => 'Painel', 'action' => 'modulo_plano_de_acao')
            ),
		);
	}
	function modulosToOptions(){
		return array(
			self::ADMIN        => 'Sistema',
			self::ADMINISTRATIVO    => 'Administrativo',
			self::FINANCEIRO   => 'Financeiro',
			self::CREDENCIAMENTO    => 'Credenciamento',
			self::GESTAOCONTRATOS    => 'Gestão de Contratos',
			self::COMERCIAL    => 'Comercial',
			self::AGENDA    => 'aAgenda',
			self::JURIDICO     => 'Jurídico',
			self::BUONNYCREDIT => 'BuonnyCredit',
			self::TELECONSULT  => 'Teleconsult',
			self::RH           => 'RH',		
			self::SEM_MODULO   => 'Sem Módulo',
			self::SAUDE    	   => 'Saude',
			self::SEGURANCA    => 'Segurança',
			self::MAPEAMENTORISCO => 'Mapeamento Risco',
			self::ESOCIAL => 'E-Social',
			self::COVID => 'Covid',
			self::GESTAODOCUMENTOS => 'Gestão Documentos',
			self::CONTASMEDICAS => 'Contas Médicas',
			self::WALK_TALK => 'Walk & Talk',
			self::OBSERVADOR_EHS => 'Observador EHS',
            self::PLANO_DE_ACAO => 'Plano de Ação'
		);
	}
}
?>
