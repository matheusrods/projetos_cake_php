<?php
App::import('Component', 'StringView');
App::import('Core', 'Controller');
App::import('Component', 'Email');
class AlertasConsultaShell extends Shell {
	var $uses = array(
		'ControleAlerta',
		'TIpcpInformacaoPcp',
	);

	function main() {
		echo "==================================================\n\n";
		echo "=> verificaConsulta => Verifica query rodando a mais de 30 minutos. \n\n";
	}

	function run() {
		if (!$this->im_running('alertas_consulta'))
        	$this->verificaConsulta();        	
    }
    

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}
	
    public function verificaConsulta(){
    	$query = "SELECT * 
				FROM pg_stat_activity WHERE query_start < now() - INTERVAL '30 minutes' 
					AND NOT (current_query LIKE '<IDLE>' 
				 	OR current_query LIKE 'VACUUM%' 
					OR current_query LIKE 'autovacuum%')";

    	$consulta = $this->TIpcpInformacaoPcp->query($query);

    	if(!empty($consulta)){
			if(!$this->ControleAlerta->consulta_execucao(ControleAlerta::CONSULTA_EM_EXECUCAO)){
				$this->ControleAlerta->incluir_consulta_execucao();
				$this->enviaEmail($consulta,ControleAlerta::CONSULTA_EM_EXECUCAO,ControleAlerta::INICIO_ALERTA);
			}
		}else{
    		if($this->ControleAlerta->alertaFinalizado(ControleAlerta::CONSULTA_EM_EXECUCAO)){
    			$this->enviaEmail($consulta,ControleAlerta::CONSULTA_EM_EXECUCAO,ControleAlerta::FIM_ALERTA);
    		}
		}
     	
    }

 	public function enviaEmail($consulta,$codigo_alerta,$codigo_notificacao) {
        App::import('Component', 'Email');
        App::import('Core', 'Controller');
    	$controller =& new Controller();
		$this->StringView = new StringViewComponent();
        $this->Email =& new EmailComponent();

        $this->StringView->reset();

        $descricao_alerta = $this->ControleAlerta->lista_alerta_por_codigo($codigo_alerta);
		$this->StringView->set(compact('consulta','descricao_alerta'));

		if($codigo_notificacao == ControleAlerta::INICIO_ALERTA){
			$mensagem = $this->StringView->renderMail('emails_alertas_consulta', 'default');
		}elseif($codigo_notificacao == ControleAlerta::FIM_ALERTA){
			$mensagem = $this->StringView->renderMail('emails_alertas_consulta_finalizado', 'default');
		}

        $this->Email->startup($controller);
        $this->Email->sendAs = 'html';
		$this->Email->from = 'portal@rhhealth.com.br>';
	    $this->Email->to = array('tid@ithealth.com.br');
		$this->Email->subject = 'Alertas Consulta';
		$this->Email->template 	= null;
		$this->Email->layout 	= null;
		$this->Email->smtpOptions = array(
			'port'=>'25',
			'timeout'=>'30',
			'host' => 'webmail.buonny.com.br',
		);
		$this->Email->delivery = 'smtp';
		$this->Email->send($mensagem);
    }
}