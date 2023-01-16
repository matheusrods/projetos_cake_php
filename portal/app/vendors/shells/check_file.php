<?php

class CheckFileShell extends Shell {
	var $uses = array('LogIntegracao');

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ FILE CHECK\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "=> lg_check: verifica se os arquivos do dia foram integrados pela LG\n";
		echo "=> gpa_check: verifica se os arquivos do dia foram integrados pela GPA\n";
	}

	function lg_check() {
    	$path = DS.'home'.DS.'lg'.DS.'sm'.DS.'processado';
    	$arquivos = $this->check($path);
    	$arquivos = array_merge($arquivos,$this->check($path,1));
    	$this->enviar_email($arquivos,'Integração LG');
    }

    function gpa_check() {
    	$path = DS.'home'.DS.'paodeacucar'.DS.'gpa'.DS.'processado';
    	$arquivos = $this->check($path);
    	$arquivos = array_merge($arquivos,$this->check($path,1));
    	$this->enviar_email($arquivos,'Integração GPA');
    }

    private function check($diretorio,$dias = 0){
    	$exec 		= "find {$diretorio} -mtime -{$dias}";
    	$lista		= shell_exec($exec);
    	$lista 		= explode("\n",$lista);
        $retorno 	= array();

        foreach ($lista as $key => $value) {
        	$data_arquivo = date("Ymd H:i:00", filemtime($value));
        	$conditions = array('arquivo' => $arquivo, 'data_inclusao >=' => $data_arquivo);
        	if (!$this->LogIntegracao->find('count', array('conditions' => $conditions))) $retorno[] = $arquivo;
        }

        return $retorno;
    }

    private function enviar_email($arquivos,$subject){

		if($arquivos){
			App::import('Component', array('Mailer.Scheduler'));
			$this->Scheduler  = new SchedulerComponent();

			$options = array(
				'from' 		=> 'portal@buonny.com.br',
				'sent' 		=> null,
				'to'   		=> 'tid@ithealth.com.br',
				'subject' 	=> "{$subject} - Arquivos não integrados",
			);

			$content = 	"<h3>Arquivos ainda não integrados</h3>";
			$content .= implode('<br />',$arquivos);

			$this->Scheduler->schedule($content, $options);
		}

	}

}
?>
