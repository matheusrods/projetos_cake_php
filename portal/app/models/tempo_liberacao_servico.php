<?php
class TempoLiberacaoServico extends AppModel
{
    public $name = 'TempoLiberacaoServico';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'tempo_liberacao_servico';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array();

    public function scheduleMailTempoLiberacao($email, $codigo_fornecedor) {
    
		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
		$this->Scheduler  = new SchedulerComponent();

		//seta os dados para o email
		$this->StringView->reset();

        $this->StringView->set('codigo_fornecedor', $codigo_fornecedor);
		$content = $this->StringView->renderMail('emails_tempo_liberacao');
		
		$this->Scheduler->schedule($content, array(
			'from' => 'portal@rhhealth.com.br',
			'to' => $email,
			'subject' => 'Lembrete - Preenchimento do cadastro de prestador de serviço RH Health'
		),
            null,
            null
	    );
		
	}//FINAL FUNCTION scheduleMailFaturamento
}
