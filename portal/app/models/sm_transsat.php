<?php
App::import('Model', 'SmIntegracao');
class SmTranssat extends SmIntegracao {
    var $name                = 'SmTranssat';    
    var $cliente_portal      = null;
    var $cliente_monitora    = null;
    
    public function __construct(){
        parent::__construct();
    	$ClientEmpresa = ClassRegistry::init('ClientEmpresa');

        $this->cliente_portal = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) ? '28505'  : '28505';

        if(!isset($this->cliente_monitora)) {
        	$clientes_monitora = $ClientEmpresa->porCodigoCliente($this->cliente_portal);
			if (count($clientes_monitora) > 0) {
				$this->cliente_monitora = $clientes_monitora[0]['ClientEmpresa']['codigo'];
			}
		}
    }

	public function incluirSMTranssat() {
        $MWebsm = ClassRegistry::init('MWebsm');

        $arquivos = $this->listarArquivos('txt');

        foreach($arquivos as $key => $value){

            $arquivo = $this->lerArquivo($value);


            if($arquivo){
                $log = $MWebsm->processarArquivo($this->arquivoProcessado,$this->cliente_monitora,2);

                $mensagem = '';

				foreach($log as $key => $status) {

					switch ($status) {
						case 1: $status = 'STATUS_OK';
							break;
						case 2: $status = 'STATUS_PLACA_NAO_CADASTRADA';
							break;
						case 3: $status = 'STATUS_TECNOLOGIA_NAO_CADASTRADA';
							break;
						case 4: $status = 'STATUS_CIDADE_ORIGEM_NAO_CADASTRADA';
							break;
						case 5: $status = 'STATUS_CIDADE_DESTINO_NAO_CADASTRADA';
							break;
						case 6: $status = 'STATUS_JA_IMPORTADO';
							break;
						default:
							break;
					}

					$mensagem .= $key . " - " . $status . "\n";
				}

				$this->organizarProcessamento($this->arquivoProcessado,$mensagem, SmIntegracao::SUCESSO);

            } else {              
                $this->organizarProcessamento($this->arquivoProcessado,'Erro de leitura do arquivo', SmIntegracao::ERRO);
            }
        }
    }
}