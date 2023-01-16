<?php
class MonitoraCronShell extends Shell {
	var $uses = array(
		'MonitoraCron',
		'Alerta',
	);

	function main() {
		echo "\n\n";		
		echo "===========================================================\n\n";		
		echo " CRON Verifica os crons agendados que nao foram executados\n\n";		
		echo "===========================================================\n\n";
	}

	function run() {
		if (!$this->im_running('monitora_cron'))
        	$this->verifica_cron_executado();
    }
    

	private function im_running($tipo) {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep '{$tipo}'");
			// 1 execução é a execução atual
			return substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;			
		}
	}

	function verifica_cron_executado(){
		$crons = $this->MonitoraCron->find('all',array('conditions' => array('ativo' => 1)));		
		foreach ($crons as $cron) {
			$erro = false;
			if(!empty($cron['MonitoraCron']['dia_processamento'])){
				//Caso for mensal
				if($cron['MonitoraCron']['dia_processamento'] == date('d')){
					if(!$cron['MonitoraCron']['data_ultima_execucao'] >= date('Ymd 00:00:00')){
						$erro = true;
					}
				}

			}else{
				//Cron diario
				if(!$cron['MonitoraCron']['data_ultima_execucao'] >= date('Ymd 00:00:00')){
					$erro = true;
				}
			}
			
			if($erro){
				$dados_erro = $this->monta_dados_alerta($cron);
				$this->Alerta->incluir($dados_erro);
			}

		}
	}


	function monta_dados_alerta($dados_erro){
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
        $this->StringView   = new StringViewComponent();
        $this->Scheduler    = new SchedulerComponent();
		$this->StringView->set(compact('dados_erro'));
        
        $content = $this->StringView->renderMail('email_cron_nao_executado', 'default_novo');
		
        if(!empty($dados_erro['MonitoraCron']['data_ultima_execucao'])){
			$execucao = "Ultima execucao do cron as {$dados_erro['MonitoraCron']['data_ultima_execucao']}";
		}else{
			$execucao  = "Cron nunca foi executado.";
		}
		$alerta = array(
			'Alerta' => array(
			    'codigo_cliente' => NULL,
			    'descricao' => "FALHA NA EXECUCAO DO CRON {$dados_erro['MonitoraCron']['descricao']} - {$execucao}",
			    'descricao_email' => $content,
			    'codigo_alerta_tipo' => 51,
			    'model' =>  'MonitoraCron',
			    'foreign_key' => $dados_erro['MonitoraCron']['codigo'],
			),
		);
		return $alerta;
	}


}