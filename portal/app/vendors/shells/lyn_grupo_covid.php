<?php
/** 
 * Cron - Classe de para verficar o usuario que foi passado para o grupo laranja, e tem uma data para o fim do afastamento está cron é responsavel para 
 * voltar o usuario para o grupo que estava
 * 
 * @author Willians Paulo Pedroso <willians.pedroso@ithealth.com.br>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app lyn_grupo_covid buscar
 */


class LynGrupoCovidShell extends Shell {
	var $uses = array('UsuarioGrupoCovid');
	var $arquivo;

	function main() {
		echo "*************************************************************************************************************************************\n";
		echo "* LYN COVID - VERIFICACAO SE O USUARIO PODE VOLTAR PARA O GRUPO ANTERIOR QUE ESTAVA \n";
		echo "*************************************************************************************************************************************\n";
	}

	function buscar(){
		echo "\n";
		echo "=> Lyn - Busca usuarios que devem voltar para o grupo pois acabou o afastamento\n";
	
		//$codigo_cliente = $this->args[0];
		if (!$this->im_running()) {
			echo "Inicia Busca"."\n";
			$this->UsuarioGrupoCovid->get_usuario_fim_afastamento();
			echo "Busca Concluído"."\n";
		} else {
			echo "Já existe uma busca em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'lyn_grupo_covid'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"lyn_grupo_covid"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>
