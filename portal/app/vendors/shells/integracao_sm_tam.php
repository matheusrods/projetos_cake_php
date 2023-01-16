<?php
class IntegracaoSmTAMShell extends Shell {

	var $uses = array(
		'SmIntegracao','Cliente'
	);

	public function main() {
		echo "integracao_sm [integrar_sm_tam]\n";
	}

	public function integrar_sm_tam(){
        $dados_cliente = $this->Cliente->carregarPorDocumento( '02012862000917', 'codigo' );
        $this->codigo_cliente = $dados_cliente['Cliente']['codigo'];        
		if (!$this->im_running()) {
			$path = DS.'home'.DS.'tam'.DS.'tam'.DS;        
	        $this->SmIntegracao->diretorioEnviado    = $path.'enviado';
	        $this->SmIntegracao->diretorioProcessado = $path.'processado';
	        $this->SmIntegracao->diretorioRetorno    = $path.'retorno';
	        $this->incluirViagem();
		}
	}
    private function im_running() {
        $cmd = `ps aux | grep 'integrar_sm_tam'`; 
        // 1 execução é a execução atual
        return substr_count($cmd, 'cake.php -working') > 1;
    }

    public function incluirViagem(){
        $LogAplicacao = ClassRegistry::init('LogAplicacao');
        $LogAplicacao->sistema = 'FTP';
        $LogAplicacao->codigo_cliente = $this->codigo_cliente;
        $arquivos = $this->SmIntegracao->listarArquivos('xml');
        foreach($arquivos as $key => $value){
            $this->SmIntegracao->extension_file = '.xml';
            $this->SmIntegracao->rename_file    = false;
            $LogAplicacao->incluirLog('Abrindo arquivo '.$value, LogAplicacao::INFO, null, 'FTP');
            if( file_exists($value) ){
                $this->validationError = array();
                $arquivo      = $this->SmIntegracao->lerArquivo($value);
                $mensagem     = null;
                $pedido       = null;
                $nome_arquivo = end(explode(DS, $this->SmIntegracao->arquivo));
                $sm           = 'Erro de leitura do arquivo';
                $status       = SmIntegracao::ERRO;
                if($arquivo){
                    $LogAplicacao->incluirLog('Converter XML do arquivo '.$nome_arquivo, LogAplicacao::INFO, null, 'FTP');
                    $sm = $this->converterXml($arquivo,$nome_arquivo);
                }
                $arquivoProcessado = $this->SmIntegracao->arquivoProcessado;
                $LogAplicacao->incluirLog('Arquivo processado '.$nome_arquivo, LogAplicacao::INFO, null, 'FTP');
                $this->SmIntegracao->transferirArquivoProcessado($arquivoProcessado,false,"");
                $this->SmIntegracao->extension_file = '.ret';
                $LogAplicacao->incluirLog('Criar dados de retorno para arquivo '.$nome_arquivo, LogAplicacao::INFO, null, 'FTP');
                $this->SmIntegracao->criarArquivoDeRetorno($arquivoProcessado,(isset($sm->enc_value) ? $sm->enc_value : $sm));
            }
        }
    }

    public function converterXml($xml,$nome_arquivo){
        $LogAplicacao = ClassRegistry::init('LogAplicacao');
        $LogAplicacao->sistema = 'FTP';
        $LogAplicacao->codigo_cliente = $this->codigo_cliente;
        $retorno = false;
        try {
            if( empty($xml) )
                throw new Exception("XML em branco!");
            $xml = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $xml);
            $xml_std_class = json_decode(json_encode(simplexml_load_string($xml)));
            if( !$xml_std_class ){
                throw new Exception("Erro na leitura do XML!");
            }
            $xml_std_class->sistema_origem = 'FTP';
            App::import('Component');
            App::import('Component', 'SmSoap');
            $this->SmSoap = new SmSoapComponent();
            $LogAplicacao->incluirLog('Iniciar inclusão da SM '.$nome_arquivo, LogAplicacao::INFO, null, 'FTP');
            $retorno = $this->SmSoap->incluirSm($xml_std_class);
            $LogAplicacao->incluirLog('Retorno '.$nome_arquivo." \n".strip_tags($retorno->enc_value), LogAplicacao::INFO, null, 'FTP');
        } catch (Exception $ex) {
            $retorno = $ex->getMessage();
            $LogAplicacao->incluirLog('Retorno '.$nome_arquivo." \n".$ex->getMessage(), LogAplicacao::INFO, null, 'FTP');
        }
        return $retorno;
    }
}
?>
