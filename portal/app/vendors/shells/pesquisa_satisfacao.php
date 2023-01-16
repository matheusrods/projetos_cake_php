<?php
class PesquisaSatisfacaoShell extends Shell {
    var $uses =  array('PesquisaSatisfacao','MonitoraCron');    
    public function main(){
        echo "Pesquisa de Satisfacao: Carrega os TOP 100 clientes (por Produto) Faturados no mes.";
    }
    public function run( ){
        if (!$this->im_running('pesquisa_satisfacao')) {
            $options  = array('codigo_usuario_inclusao'=> 2 );//IMPORTACAO
            $this->PesquisaSatisfacao->carregar_cliente_pesquisa_satisfacao( $options );
            $this->MonitoraCron->execucao('pesquisa_satisfacao');            
        }
    }
    private function im_running($tipo) {
        $cmd = shell_exec("ps aux | grep '{$tipo}'");
        return substr_count($cmd, 'cake.php -working') > 1;
    }
}
?>