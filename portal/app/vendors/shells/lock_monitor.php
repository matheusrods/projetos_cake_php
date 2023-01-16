<?php
class LockMonitorShell extends Shell {
	var $uses = array("TEstaEstatus");

	function main($seconds = 10) {
		$query = '
			SELECT 
			    waiting.locktype           AS waiting_locktype,
			    waiting.relation::regclass AS waiting_table,
			    waiting_stm.current_query  AS waiting_query,
			    waiting.mode               AS waiting_mode,
			    waiting.pid                AS waiting_pid,
			    waiting_stm.client_addr    AS waiting_client_addr,
			    waiting_stm.query_start    AS waiting_query_start,
			    other.locktype             AS other_locktype,
			    other.relation::regclass   AS other_table,
			    other_stm.current_query    AS other_query,
			    other.mode                 AS other_mode,
			    other.pid                  AS other_pid,
			    other_stm.client_addr      AS other_client_addr,
			    other_stm.query_start      AS other_query_start,
			    other.granted              AS other_granted
			FROM
			    pg_catalog.pg_locks AS waiting
			JOIN
			    pg_catalog.pg_stat_activity AS waiting_stm
			    ON (
			        waiting_stm.procpid = waiting.pid
			    )
			JOIN
			    pg_catalog.pg_locks AS other
			    ON (
			        (
			            waiting."database" = other."database"
			        AND waiting.relation  = other.relation
			        )
			        OR waiting.transactionid = other.transactionid
			    )
			JOIN
			    pg_catalog.pg_stat_activity AS other_stm
			    ON (
			        other_stm.procpid = other.pid
			    )
			WHERE
			    NOT waiting.granted
			AND
			    waiting.pid <> other.pid
			ORDER BY waiting_stm.query_start';
		echo "Verificando locks \n";
		$results = $this->TEstaEstatus->query($query);
		if (count($results) > 30) {
			echo "Aguardando {$seconds} segundos para nova verificação \n";
			sleep($seconds);
			echo "Confirmando locks \n";
			$results = $this->TEstaEstatus->query($query);
			if (count($results) > 10) {
				echo "Banco com lock, enviando email \n";
				$mensagem = "";
				foreach($results as $registro => $result) {
					if ($registro == 0) {
						$linha = "";
						foreach ($result[0] AS $titulo => $coluna) {
							$linha .= $titulo.";";
						}
						$mensagem .= $linha."\n";
					}
					$linha = "";
					foreach ($result[0] AS $coluna) {
						$linha .= $coluna.";";
					}
					$mensagem .= $linha."\n";
				}
				$this->enviarEmail($mensagem);
			} else {
				echo "Nenhum problema detectado \n";
			}
		} else {
			echo "Nenhum problema detectado \n";
		}
	}

	function enviarEmail($mensagem) {
		App::import('Core', 'Controller');
        App::import('Component', 'Email');
        $controller =& new Controller();
        $this->Email =& new EmailComponent();
        $this->Email->startup($controller);

		$this->Email->sendAs = 'text';
	    $this->Email->to = array('bruno.oliveira@buonny.com.br','nelson.ota@buonny.com.br');
		$this->Email->from = 'Buonny <monitor@buonny.com.br>';
		$this->Email->subject = 'Locks no Postgres';
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
?>
