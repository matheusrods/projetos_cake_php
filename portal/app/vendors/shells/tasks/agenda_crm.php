<?php
class AgendaCrmTask extends Shell {
	
	var $uses =  array('VtigerContactsubdetails');
	
	public function __construct() {
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
  		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();		
	}
	

    public function cadastraAniversariantes() {

        $month = date( 'm' );
        $day   = date( 'd' );        

        $data = $this->VtigerContactsubdetails->buscaAniversarianteDoDia( $month, $day );

        if( count( $data ) > 0 ){
            foreach( $data as $value) {
                $this->scheduleMail( $value['VtigerContactdetails'] );
            }
        }
            
    }
	
    private function scheduleMail( $dados ) {
        $this->stringView->reset();
        $this->stringView->set('dados', $dados);
        $content = $this->stringView->renderMail('emails_aniversario_crm');
        $this->scheduler->schedule($content, array(
            'from' => 'portal@rhhealth.com.br',
            'cc' => null,
            'to' => $dados['email'],
            'subject' => 'Cartão de Aniversário',            
        ));
    }
	
	private function marcaEnvio($retorno, $links) {
	    $this->RetornoNf->marcaEnvio($retorno, $links);
	}
}
?>
 