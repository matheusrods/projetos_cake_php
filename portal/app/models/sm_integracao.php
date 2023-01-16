<?php
App::import('Model', 'LogIntegracao');
class SmIntegracao extends AppModel {
    var $name                = 'SmGpa';
    var $useTable            = false;
    var $diretorioEnviado    = null;
    var $diretorioProcessado = null;
    var $diretorioRetorno    = null;
    var $arquivoProcessado   = null;
    var $cliente_portal      = null;
    var $cliente_monitora    = null;
    var $cliente_guardian    = null;
    var $sistema_monitora    = null;
    var $rename_file         = true;
    var $extension_file      = '.dat';
    var $return_file         = 'API';
    var $LogIntegracao       = null;
    var $conteudo            = null;
    var $arquivo             = null;
    var $validationError     = array();
    const SUCESSO            = 0;
    const ERRO               = 1;

    public function __construct(){
        $this->LogIntegracao =& ClassRegistry::init('LogIntegracao');
    }

    public function listarArquivos($extension = 'dat'){
        return glob($this->diretorioEnviado.DS.'*.'.$extension);
    }

    public function lerArquivo($arquivo){
        $this->arquivo = $arquivo;
        $novo_nome = preg_replace("/\\.[^.\\s]{2,4}$/", "", $arquivo);
        $novo_nome .= '.proc';
        $this->arquivoProcessado = $novo_nome;
        rename($arquivo, $novo_nome);
        $this->conteudo = file_get_contents($novo_nome);
        return $this->conteudo;
    }

    public function transferirArquivoProcessado($arquivo, $voltar_status_original = false, $origem_organizacao=""){
        $novo_nome  = end(explode(DS, $arquivo));
        $novo_nome  = array_shift(explode('.', $novo_nome));
        $novo_nome .= $this->extension_file;
        $this->arquivoProcessado = null;
        if ($voltar_status_original) {
            //$this->log($origem_organizacao." ".$this->diretorioEnviado.DS.$novo_nome, 'SmIntegracao');
            return rename($arquivo, $this->diretorioEnviado.DS.$novo_nome);
        } else {
            //$this->log($origem_organizacao." ".$this->diretorioProcessado.DS.$novo_nome, 'SmIntegracao');
            return rename($arquivo, $this->diretorioProcessado.DS.$novo_nome);
        }
    }

    public function criarArquivoDeRetorno($arquivo,$mensagem){
        $novo_nome  = end(explode(DS, $arquivo));
        $novo_nome  = array_shift(explode('.', $novo_nome));
        $novo_nome .= $this->extension_file;
        if( $this->rename_file ){
            $novo_nome = $this->return_file . substr($novo_nome, 3);
        }
        return file_put_contents($this->diretorioRetorno.DS.$novo_nome, $mensagem);
    }

    public function excluirArquivoProcessado($arquivo){
        return unlink($arquivo);
    }

    public function organizarProcessamento($arquivo, $mensagem, $voltar_status_original = false, $origem_organizacao = ""){
        if( $this->return_file && !$voltar_status_original )
            $this->criarArquivoDeRetorno($arquivo,$mensagem);
        return $this->transferirArquivoProcessado($arquivo, $voltar_status_original, $origem_organizacao);
    }

    public function cadastrarLog($data, $codigo_cliente = NULL){
        $log_integracao = array('LogIntegracao' => array(
            'arquivo'        => end(explode(DS, $this->arquivo)),
            'conteudo'       => $this->conteudo,
            'retorno'        => $data['mensagem'],
            'sistema_origem' => $this->name,
            'status'         => $data['status'],
            'codigo_cliente' => ( $codigo_cliente ? $codigo_cliente : $this->cliente_portal),
            'descricao'      => $data['descricao'],
            'tipo_operacao'  => isset($data['operacao']) ? $data['operacao'] : NULL,
            'numero_pedido'  => isset($data['pedido']) ? $data['pedido'] : NULL,
            'placa_cavalo'   => (isset($data['placa_cavalo'])?str_replace('-', '', $data['placa_cavalo']):null),
            'placa_carreta'  => (isset($data['placa_carreta'])?str_replace('-', '', $data['placa_carreta']):null),
            'loadplan'       => (isset($data['load_planner'])?$data['load_planner']:null),
            'cpf_motorista'  => (isset($data['motorista_cpf'])?$data['motorista_cpf']:null),
        ));
        $this->LogIntegracao->incluir($log_integracao);
    }
}