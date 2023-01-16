<?php
ini_set('mssql.timeout', '600');
class ConsolidarEstatisticaShell extends Shell {

    var $uses = array(
    	'EstatisticaSm',
    	'EstatisticaSmGeralHora',
    	'EstatisticaSmGeralDia',
    	'EstatisticaSmOperadorHora',
    	'EstatisticaSmOperadorDia',
    	'EstatisticaSmOperacaoHora',
    	'EstatisticaSmOperacaoDia',
    	'EstatisticaSmClienteHora',
    	'EstatisticaSmClienteDia',
    );
	
	function main() {
	    $metodos = get_class_methods($this);
	    
	    $this->out('Estatisticas:', 2);
	    foreach ($metodos as $metodo) {
	        if (preg_match('/^consolidar.*/', $metodo)) {
	            $this->out('    ' . $metodo);
	        }
	    }
	    $this->out('', 2);
	    
	}

	function carregarTabela() {
		$this->EstatisticaSm->carregarTabela();
	}

	function carregarEstatisticaSm() {
		$file_control = $this->limit_runner();
		$this->EstatisticaSm->carregarTabela();
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmGeralDia ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmGeralHora ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmOperacaoDia ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmOperacaoHora ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmOperadorDia ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmOperadorHora ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmClienteDia ');
		Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' consolidar_estatistica consolidarSmClienteHora ');
		unlink($file_control);
	}

	private function limit_runner() {
		$complete_path_file = DS . 'tmp' . DS . $this->command.'.tmp';
		if (file_exists($complete_path_file)) {
			$file_date = filemtime($complete_path_file);
			$diff = mktime() - $file_date;
			$diff = sprintf('%d', floor($diff)/60);
			if ($diff > 30) {
				unlink($complete_path_file);
			} else {
				die;				
			}
		}
		$file_control = DS . 'tmp' . DS . $this->command . '.tmp';
		touch($file_control);
		return $file_control;
	}

	function consolidarSmGeralDia() {
		$file_control = $this->limit_runner();
    	$this->EstatisticaSmGeralDia->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmGeralHora() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmGeralHora->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmOperadorDia() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmOperadorDia->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmOperadorHora() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmOperadorHora->carregarConsolidado();
	    unlink($file_control);
	}

	function consolidarSmOperacaoDia() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmOperacaoDia->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmOperacaoHora() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmOperacaoHora->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmClienteDia() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmClienteDia->carregarConsolidado();
	    unlink($file_control);
	}
	
	function consolidarSmClienteHora() {
		$file_control = $this->limit_runner();
	    $this->EstatisticaSmClienteHora->carregarConsolidado();
	    unlink($file_control);
	}
}
?>
