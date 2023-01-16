<?php
class PainelController extends AppController
{
	var $name = 'Painel';
	var $uses = array('Modulo', 'MensagemDeAcesso', 'TipoPerfil', 'Usuario', 'ModuloLateral');
	function beforeFilter()
	{
		parent::beforeFilter();
	}

	private function mensagem_de_acesso($modulo = null)
	{
		return null;

		$mensagem = array();
		$perfil = $this->TipoPerfil->verificaTipoPerfil($this->authUsuario);
		$dados = $this->MensagemDeAcesso->listarMensagensNoPeriodo($modulo, $perfil);

		if ($dados) {
			$range = count($dados) - 1;
			$msg   = rand(0, $range);
			$mensagem = $dados[$msg];
		} else {
			$mensagem['MensagemDeAcesso']['mensagem'] = '';
		}
		return $mensagem;
	}

	function modulo_admin()
	{
		$this->pageTitle = 'Módulo Admin';
		$this->Session->write('modulo_selecionado', Modulo::ADMIN);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::ADMIN));

		if (!empty($this->authUsuario['Usuario']['codigo_empresa'])) {
			$this->redirect(array('controller' => 'painel', 'action' => 'modulo_administrativo'));
		}
	}

	function modulo_administrativo()
	{
		$this->pageTitle = 'Módulo Administrativo';
		$this->Session->write('modulo_selecionado', Modulo::ADMINISTRATIVO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::ADMINISTRATIVO));
	}

	function modulo_financeiro()
	{
		$this->pageTitle = 'Módulo Financeiro';
		$this->Session->write('modulo_selecionado', Modulo::FINANCEIRO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		//			$this->set('mensagem', $this->mensagem_de_acesso(Modulo::FINANCEIRO));
	}

	function modulo_gestao_contratos()
	{
		$this->pageTitle = 'Módulo Gestao de Contratos';
		$this->Session->write('modulo_selecionado', Modulo::GESTAOCONTRATOS);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::GESTAOCONTRATOS));
	}

	function modulo_agenda()
	{
		$this->pageTitle = 'Módulo Agenda';
		$this->Session->write('modulo_selecionado', Modulo::AGENDA);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::AGENDA));
	}

	function modulo_credenciamento()
	{
		$this->pageTitle = 'Módulo Credenciamento';
		$this->Session->write('modulo_selecionado', Modulo::CREDENCIAMENTO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::CREDENCIAMENTO));

		// verifica se é perfil: credenciado
		if ($this->authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) {
			$this->redirect('/propostas_credenciamento/minha_proposta');
		}
	}

	function modulo_comercial()
	{
		$this->pageTitle = 'Módulo Comercial';
		$this->Session->write('modulo_selecionado', Modulo::COMERCIAL);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::COMERCIAL));
	}

	function modulo_juridico()
	{
		$this->pageTitle = 'Módulo Jurídico';
		$this->Session->write('modulo_selecionado', Modulo::JURIDICO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::JURIDICO));
	}

	function modulo_buonnycredit()
	{
		$this->pageTitle = 'Módulo BuonnyCredit';
		$this->Session->write('modulo_selecionado', Modulo::BUONNYCREDIT);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::BUONNYCREDIT));
	}

	function modulo_teleconsult()
	{
		$this->pageTitle = 'Módulo Teleconsult';
		$this->Session->write('modulo_selecionado', Modulo::TELECONSULT);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::TELECONSULT));
	}

	function modulo_rh()
	{
		$this->pageTitle = 'Módulo RH';
		$this->Session->write('modulo_selecionado', Modulo::RH);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::RH));
	}

	function sem_modulo()
	{
		$this->pageTitle = '';
		$this->Session->write('modulo_selecionado', Modulo::SEM_MODULO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::SEM_MODULO));
	}

	function modulo_mapeamento_risco()
	{

		$this->pageTitle = 'Módulo Mapeamento de Risco';
		$this->Session->write('modulo_selecionado', Modulo::MAPEAMENTORISCO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::MAPEAMENTORISCO));

		// verifica se é perfil: credenciado
		if ($this->authUsuario['Usuario']['codigo_uperfil'] == Uperfil::FUNCIONARIO) {
			return $this->redirect(array('controller' => 'dados_saude', 'action' => 'dashboard'));
		}
	}

	function modulo_saude()
	{
		$this->pageTitle = 'Módulo Saúde';
		$this->Session->write('modulo_selecionado', Modulo::SAUDE);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::SAUDE));
	}

	function modulo_seguranca()
	{
		$this->pageTitle = 'Módulo Segurança';
		$this->Session->write('modulo_selecionado', Modulo::SEGURANCA);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::SEGURANCA));
	}

	function modulo_e_social()
	{
		$this->pageTitle = 'Módulo eSocial';
		$this->Session->write('modulo_selecionado', Modulo::ESOCIAL);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::ESOCIAL));
	}

	function modulo_covid()
	{
		$this->pageTitle = 'Módulo Covid';
		$this->Session->write('modulo_selecionado', Modulo::COVID);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::COVID));
	}

	function modulo_gestao_documentos()
	{
		$this->pageTitle = 'Módulo Gestão Documentos';
		$this->Session->write('modulo_selecionado', Modulo::GESTAODOCUMENTOS);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::GESTAODOCUMENTOS));
	}

	function modulo_contas_medicas()
	{
		$this->pageTitle = 'Módulo Contas Médicas';
		$this->Session->write('modulo_selecionado', Modulo::CONTASMEDICAS);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::CONTASMEDICAS));
	}

	function modulo_painel_administrativo()
	{
		$this->pageTitle = 'Módulo Painel Administrativo';
		$this->Session->write('modulo_selecionado', Modulo::PAINEL_ADMINISTRATIVO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::PAINEL_ADMINISTRATIVO));
	}

	function modulo_walk_talk()
	{
		$this->pageTitle = 'Módulo Walk & Talk';
		$this->Session->write('modulo_selecionado', Modulo::WALK_TALK);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::WALK_TALK));
	}

	function modulo_observador_ehs()
	{
		$this->pageTitle = 'Módulo Observador EHS';
		$this->Session->write('modulo_selecionado', Modulo::OBSERVADOR_EHS);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::OBSERVADOR_EHS));
	}

	function modulo_plano_de_acao()
	{
		$this->pageTitle = 'Módulo Plano de Ação';
		$this->Session->write('modulo_selecionado', Modulo::PLANO_DE_ACAO);
		$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		$this->set('mensagem', $this->mensagem_de_acesso(Modulo::PLANO_DE_ACAO));
	}

	function dashboard_exames_agendados()
	{

		$this->pageTitle = '';

		$reportId = '1f99d27e-a5d4-4b66-8f93-22dc8422dd61';

		$dadosUsuario = $this->BAuth->user();


		$this->BiComponent = new BiComponent($reportId, $dadosUsuario);

		$this->set('biHtml', $this->BiComponent->render());
	}

	function dashboard_absenteismo_sintetico()
	{

		$this->pageTitle = '';

		$reportId = '9c9afe77-b54b-4b0b-91cb-57946504aaa7';

		$dadosUsuario = $this->BAuth->user();


		$this->BiComponent = new BiComponent($reportId, $dadosUsuario);

		$this->set('biHtml', $this->BiComponent->render());
	}

	function dashboard_riscos_engenharia()		
	{

		$this->pageTitle = '';

		$reportId = '1ad25c94-6b01-43a5-8245-6308d2b8322d';

		$dadosUsuario = $this->BAuth->user();


		$this->BiComponent = new BiComponent($reportId, $dadosUsuario,false,true);

		$this->set('biHtml', $this->BiComponent->render());
	}
}
