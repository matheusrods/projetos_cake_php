<?php
/**
 * Classe de controle dos shells criados.
 * 
 *
 * @copyright     Copyright 2016, , Buonny Projetos e Serviços de Riscos Securitários Inc. (http://cakefoundation.org)
 * @link          http://www.buonny.com.br/ Buonny Gerenciamento de riscos 
 * @package       libs
 * @subpackage    cake.cake.console.libs
 */

/**
 * Realiza a inclusão de 
 *
 */
App::import('Model', 'THbeaHeartBeat');
class AppShell extends Shell {
	
	/**
	* Frequencia responsavel, para saber se o mesmo encontra-se atualizado.
	* Quantidade em minutos
	* @var Interger
	* @access public
	*/
	public $frequencia = THbeaHeartBeat::PADRAO_FREQUENCIA_CRON;
	public $erros = array();
	CONST ERRO_FALTAL = 1;
	CONST ERRO_WARNING = 2;

	function __construct(&$dispatch) {
		$class_name = get_class($dispatch);
		if (strpos($class_name,"TestCase")<=0) {
			set_error_handler(array($this, 'catch_erros_local'));
			$this->THbeaHeartBeat = ClassRegistry::init('THbeaHeartBeat');
			if(!empty($dispatch->shell)) {
				$nome = !empty($dispatch->shellName) ? $dispatch->shellName : "";
				$nome_arquivo = !empty($dispatch->shell) ? $dispatch->shell : "";
				$local = !empty($dispatch->shellPaths) ? $dispatch->shellPaths : "";

				$this->THbeaHeartBeat->atualizarAgoraComControle($nome_arquivo, $this->frequencia, THbeaHeartBeat::INICIO);
			}
		}
		parent::__construct(&$dispatch); 
	}
	
	public function catch_erros_local($tipo = null, $message, $filename = null, $line = null, $super_globals = null, $mask = null) {
		//E_STRICT e E_DEPRECATED são tipos de erros que quase sempre aparecem, verificam se a função será removida em novas versoes do PHP
		if($tipo != E_STRICT && $tipo != E_DEPRECATED && $tipo !=E_WARNING ) {
			$this->erros[] = array(
				'tipo' =>$tipo,
				'message' =>$message,
				'filename' =>$filename,
				'line' =>$line,
				'data_hora' =>date('d/m/Y H:i:s'),
			);
		}
        return $tipo;
	} 

	public function registra_erro($mensagem = NULL, $linha = NULL) {
		$this->THbeaHeartBeat = ClassRegistry::init('THbeaHeartBeat');
		if(!empty($this->Dispatch->shell)) {
			$nome_arquivo = !empty($this->Dispatch->shell) ? $this->Dispatch->shell : "";
			$local = !empty($this->Dispatch->shellPath) ? $this->Dispatch->shellPath : "";
			$this->erros[] = array(
					'tipo' =>3,
					'message' =>(!empty($mensagem) ? $mensagem : "Não foi possivel executar"),
					'filename' =>!empty($local) ? $local : "Não foi possivel identificar",
					'line' =>$linha,
					'data_hora' => date('d/m/Y H:i:s'),
			);
		}
	}

	private function registra_erro_na_tabela() {
		if(!empty($this->erros)) {
			$nome_arquivo = !empty($this->Dispatch->shell) ? $this->Dispatch->shell : "";
			$lista_erros = $this->erros;
			$this->THbeaHeartBeat = ClassRegistry::init('THbeaHeartBeat');
			$erros_fatais = array(E_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_RECOVERABLE_ERROR);
			$tipo_fatal = FALSE;
			$tipo_warning = FALSE;
			foreach ($lista_erros as $key => $lista_erro) {
				if(in_array($lista_erro['tipo'], $erros_fatais)) {
					$tipo_fatal = TRUE;
				}
				if(!in_array($lista_erro['tipo'], $erros_fatais)) {
					$tipo_warning = TRUE;
				}
			}
			$tipo = ($tipo_fatal) ? self::ERRO_FALTAL : ($tipo_warning) ? self::ERRO_WARNING : NULL;
			$this->log(var_export($lista_erros,true),$nome_arquivo);
			$this->THbeaHeartBeat->atualiza_erro_shell($nome_arquivo, $tipo);
		}

	}

	function __destruct() {
		$class_name = get_class($this->Dispatch);
		if (strpos($class_name,"TestCase")<=0) {
			$this->THbeaHeartBeat = ClassRegistry::init('THbeaHeartBeat');
			if(!empty($this->Dispatch->shell)) {
				$nome_arquivo = !empty($this->Dispatch->shell) ? $this->Dispatch->shell : "";
				$this->THbeaHeartBeat->atualizarAgoraComControle($nome_arquivo, $this->frequencia, THbeaHeartBeat::FIM);
			}
			$this->registra_erro_na_tabela();
		}
		unset($this->frequencia);
		unset($this->erros);
	}

}
