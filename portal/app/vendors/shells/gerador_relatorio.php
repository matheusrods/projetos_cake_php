<?php
class GeradorRelatorioShell extends Shell {
	var $uses = array(
		'Usuario',
		'TarefaDesenvolvimento'
	);
	var $arquivo;

	function main() {
		echo "==================================================\n";
		echo "* Gerador de Relatorios \n";
		echo "* \n";
		echo "* Cria relatorios para gestores e supervisores \n";
		echo "==================================================\n\n";
		echo "=> relatorio \n\n";
	}


	function relatorio() {
		App::import('Component', array('Mailer.Scheduler'));
		$this->Scheduler  = new SchedulerComponent();
		$user = $this->getUser();
		try{			
			$this->relatorio_redmine( $user );
			return "Relatorio de Tarefas Enviado!\n\n";			
		} catch( Exception $ex ){
			echo $ex->getMessage();
		}
	}

	private function emailHead(){
		$head  = "<html>\n";
		$head .= "<head>\n";
		$head .= "<title>Relatorio de tarefase em produção</title>\n";
		$head .= "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
		$head .= "</head>\n";
		$head .= "<body lang=\"pt-BR\" dir=\"ltr\">\n";
		$head .= "<h3><i>Relatório de Tarefas Publicadas na Produção.</i></h3>\n";
		$head .= "<h4> Data: ".AppModel::dbDateToDate(date('d-m-Y H:i:s'))."</h4>\n";
		return $head;
	}

	private function emailFoot(){
		$foot  = "</body>\n";
		$foot .= "</html>\n";		
		return $foot;
	}

	public function relatorio_redmine( $user ){
		App::import('Component', array('Mailer.Scheduler'));
		$this->Scheduler  = new SchedulerComponent();
		try{
			$service_url    = URL_PORTAL.'/issues/deploying';
			$curl           = curl_init($service_url);
			$curl_post_data = array( 'user' => $user, 'project' => 41 );
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data );
			$curl_response  = curl_exec( $curl );
			curl_close($curl);
			$tarefas        = json_decode($curl_response);
			$email          = '';
			if( $tarefas ){
				$email  .= $this->emailHead( $user );
				foreach ($tarefas as $key => $tarefa) {
					$email  .= $this->emailBodyRedMine($tarefa);
				}
				$email .= $this->emailFoot();
			}
			if($email){
				$options        = array(
				'from'          => 'portal@rhhealth.com.br',
				'to'            => 'tid@ithealth.com.br',
				'subject'       => 'Relatorio de Tarefas em: '.date('Y-m-d H:i:s'),
				'sent'          => null,
				);
				$this->Scheduler->schedule($email, $options);
			}
		} catch( Exception $ex ){
			echo $ex->getMessage();
		}
	}

	private function emailBodyRedMine($data){
		$retorno  = "<div align=\"justify\" style=\"width: 80%; font: 10pt Verdana, Arial;\">\n";
		$retorno .= "<strong><p><font color=\"red\">Nome:&nbsp;</font><i>{$data->PUser->login}</i></p></strong>\n";
		$retorno .= "<strong><p><font color=\"red\">Título:&nbsp;</font>{$data->PIssue->subject}</p></strong>\n";
		$retorno .= "<p>{$data->PIssue->description}</p>\n";
		$retorno .= "<strong><p>____________________________________________________</p></strong>\n";
		$retorno .= "</div>\n";
		return $retorno;
	}

	private function getUser(){
		$file = '/tmp/log_atualizador_rhhealth.csv';		
		$user = NULL;
		if( file_exists( $file ) ){
			$content = file_get_contents($file);
			$content = explode("\n", $content);
			foreach ($content as $key => $row) {
				$row   = explode(";", $row);
				if( isset($row[0]) && !empty($row[0]) && isset($row[1]) && isset($row[2]) ){
					if( ( date("d") == trim($row[1]))  ){
						if( date("M") == trim($row[2]) ) {
							$user = $row[0];
						}
					}
				}				
			}
		}
		return $user;
	}
}
?>