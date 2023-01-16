<?php
class ViagensFinalizadasDataMaiorQueAtualShell extends Shell {

		function main() {
			echo "==================================================\n";
			echo "* Incluir \n";
			echo "* \n";
			echo "* \n";
			echo "==================================================\n\n";

			echo "=> viagens_finalizadas_data_maior_que_atual: realiza a busca de totas a viagens finalizadas ou iniciadas maior que a data atual \n\n";
		}

		function viagens_finalizadas_data_maior_que_atual(){
			$this->TViagViagem = ClassRegistry::init('TViagViagem');
			$this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');

			$this->TViagViagem->bindModel(array('belongsTo' => array(
				'Embarcador' => array('className' => 'TPjurPessoaJuridica', 'foreignKey' => false,'conditions' => array('Embarcador.pjur_pess_oras_codigo = TViagViagem.viag_emba_pjur_pess_oras_codigo'),  'type' => 'INNER'),
				'Transportador' => array('className' => 'TPjurPessoaJuridica', 'foreignKey' => false,'conditions' => array('Transportador.pjur_pess_oras_codigo  = TViagViagem.viag_tran_pess_oras_codigo'), 'type' => 'INNER'),
			)));

			$conditions['OR'] = array( 
				array("TViagViagem.viag_data_fim > " => date('Y-m-d'.' 23:59:59')),
				array("TViagViagem.viag_data_inicio >" => date('Y-m-d'.' 23:59:59')),
			);
			$fields = array(
				'TViagViagem.viag_codigo_sm',
				'TViagViagem.viag_data_inicio',
				'TViagViagem.viag_data_fim',
				'Embarcador.pjur_razao_social',
				'Transportador.pjur_razao_social',
			);
			$dados = $this->TViagViagem->find('all', compact('conditions', 'fields'));
			App::import('Component', array('StringView', 'Mailer.Scheduler'));
			$this->StringView 	= new StringViewComponent();
			$this->Scheduler  	= new SchedulerComponent();
						
			$this->StringView->reset();
			$this->StringView->set(compact('dados'));
			$content = $this->StringView->renderMail('email_viagens_finalizadas_futuro', 'default');
			$options = array(
				'from' 		=> 'portal@rhhealth.com.br',
				'sent' 		=> null,
				'to'   		=> 'tid@ithealth.com.br;',
				'subject' 	=> 'Alerta de Viagens',
			);
			$this->Scheduler->schedule($content, $options);


		}

					
	
}