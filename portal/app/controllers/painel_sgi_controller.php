<?php
	class PainelSgiController extends AppController {
		var $name='PainelSgi';
		var $uses = array('ModuloSgi','MensagemDeAcesso');

		public function beforeFilter() {
		    parent::beforeFilter();
		    $this->BAuth->allow(array('*'));
		}
		
		private function mensagem_de_acesso(){			
			$mensagem = array();
			$dados = $this->MensagemDeAcesso->listarMensagensNoPeriodo();
			if($dados){
				$range = count($dados) - 1;				
				$msg   = rand(0, $range);
				$mensagem = $dados[$msg];				
			}else{
				$mensagem['MensagemDeAcesso']['mensagem'] = '';
			}

			return $mensagem;
		}

		function modulo_admin(){
			$this->pageTitle = 'Módulo Admin';
			$this->Session->write('modulo_selecionado', Modulo::ADMIN);			
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}
		
		function modulo_logistico(){
			$this->pageTitle = 'Módulo Logístico';
			$this->Session->write('modulo_selecionado', ModuloSgi::LOGISTICO);			
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}
		
		function modulo_temperatura(){
			$this->pageTitle = 'Módulo Temperatura';
			$this->Session->write('modulo_selecionado', ModuloSgi::TEMPERATURA);
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}
		
		function modulo_jornada(){
			$this->pageTitle = 'Módulo Jornada';
			$this->Session->write('modulo_selecionado', ModuloSgi::JORNADA);
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}
		
		function modulo_gerencial(){
			$this->pageTitle = 'Módulo Gerêncial';
			$this->Session->write('modulo_selecionado', ModuloSgi::GERENCIAL);
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}

		function modulo_transyseg(){
			$this->pageTitle = 'Módulo Transyseg';
			$this->Session->write('modulo_selecionado', ModuloSgi::TRANSYSEG);
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}

		function sem_modulo(){
			$this->pageTitle = '';
			$this->Session->write('modulo_selecionado', ModuloSgi::SEM_MODULO);
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
			$this->set('mensagem', $this->mensagem_de_acesso());
		}
	}
?>