<?php
class IntegracaoSmSantaCruzShell extends Shell {

	var $uses = array(
		'SmIntegracao',
	);

	public function main() {
		echo "integracao_sm [integrar_sm_santa_cruz]\n";
	}

	public function integrar_sm_santa_cruz(){
		if (!$this->im_running()) {
			$path = DS.'home'.DS.'santacruz'.DS.'santacruz'.DS;        
	        $this->SmIntegracao->diretorioEnviado    = $path.'enviada';
	        $this->SmIntegracao->diretorioProcessado = $path.'processado';
	        $this->SmIntegracao->diretorioRetorno    = $path.'retorno';
	        $this->incluirViagem();
		}
	}

	private function im_running() {
		$cmd = `ps aux | grep 'integrar_sm_santa_cruz'`;
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

    public function incluirViagem(){
        $LogAplicacao = ClassRegistry::init('LogAplicacao');
        $LogAplicacao->sistema = 'SmSantaCruz_FTP';
        $LogAplicacao->codigo_cliente = '33013';

        $arquivos = $this->SmIntegracao->listarArquivos('xml');
        foreach($arquivos as $key => $value){
            $this->SmIntegracao->extension_file = '.xml';
            $this->SmIntegracao->rename_file    = false;
            $LogAplicacao->incluirLog('Abrindo arquivo '.$value, LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
            if( file_exists($value) ){
                $this->validationError = array();
                $arquivo      = $this->SmIntegracao->lerArquivo($value);
                $mensagem     = null;
                $pedido       = null;
                $nome_arquivo = end(explode(DS, $this->SmIntegracao->arquivo));
                $sm           = 'Erro de leitura do arquivo';
                $status       = SmIntegracao::ERRO;
                if($arquivo){
                    $LogAplicacao->incluirLog('Converter XML do arquivo '.$nome_arquivo, LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
                    $sm = $this->converterXml($arquivo,$nome_arquivo);
                }
                $arquivoProcessado = $this->SmIntegracao->arquivoProcessado;
                $LogAplicacao->incluirLog('Arquivo processado '.$nome_arquivo, LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
                $this->SmIntegracao->transferirArquivoProcessado($arquivoProcessado,false,"");
                $this->SmIntegracao->extension_file = '.ret';
                $LogAplicacao->incluirLog('Criar dados de retorno para arquivo '.$nome_arquivo, LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
                $this->SmIntegracao->criarArquivoDeRetorno($arquivoProcessado,(isset($sm->enc_value) ? $sm->enc_value : $sm));
            }
        }
    }

    public function converterXml($xml,$nome_arquivo){
        $LogAplicacao = ClassRegistry::init('LogAplicacao');
        $LogAplicacao->sistema = 'SmSantaCruz_FTP';
        $LogAplicacao->codigo_cliente = '33013';
        
        $retorno = false;
        try {
            if( empty($xml) )
                throw new Exception("XML em branco!");

            $xml = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $xml);
            $xml_std_class = json_decode(json_encode(simplexml_load_string($xml)));
            if( !$xml_std_class ){
                throw new Exception("Erro na leitura do XML!");
            }
            $xml_std_class->sistema_origem = 'SmSantaCruz_FTP';

            App::import('Component');
            App::import('Component', 'SmSoap');
            $this->SmSoap = new SmSoapComponent();
            $LogAplicacao->incluirLog('Iniciar inclusão da SM '.$nome_arquivo, LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
            $retorno = $this->SmSoap->incluirSm($xml_std_class);
            $LogAplicacao->incluirLog('Retorno '.$nome_arquivo." \n".strip_tags($retorno->enc_value), LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
        } catch (Exception $ex) {
            $retorno = $ex->getMessage();
            $LogAplicacao->incluirLog('Retorno '.$nome_arquivo." \n".$ex->getMessage(), LogAplicacao::INFO, null, 'SmSantaCruz_FTP');
        }
        return $retorno;
    }

}
?>
