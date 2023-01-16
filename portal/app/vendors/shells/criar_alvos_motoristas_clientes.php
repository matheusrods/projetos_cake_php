<?php
class CriarAlvosMotoristasClientesShell extends Shell {
    
    public function main() {
        if (!$this->im_running('criar_alvos_motoristas')) {
            $this->runMotoristas();
            $this->runClientes();
        }
    }
       
    public function runMotoristas() {
        $this->TMotoMotorista = ClassRegistry::init('TMotoMotorista');
        $motoristas_pendentes = $this->TMotoMotorista->retornaPendentesEndereco('01/06/2015');
        //debug(count($motoristas_pendentes));
        /*
        $motoristas_pendentes = Array(
            Array(
                'TMotoMotorista' => Array('moto_pfis_pess_oras_codigo'=>1523042),
                'TPfisPessoaFisica' => Array('pfis_cpf' => '84860199073')
            )
        );
        */
        foreach ($motoristas_pendentes as $key => $motorista) {
            $this->TMotoMotorista->validationErrors = null;
            $ret = $this->TMotoMotorista->incluirReferenciaMotorista($motorista['TPfisPessoaFisica']['pfis_cpf']);

            if ($ret) {
                echo "{$key}: Incluido alvo para o Motorista: ".$motorista['TPfisPessoaFisica']['pfis_cpf']."\n";

            } else {
                echo "{$key}: Erro ao incluir o alvo para o Motorista: ".$motorista['TPfisPessoaFisica']['pfis_cpf'].": ";
                $erro = current($this->TMotoMotorista->validationErrors);
                echo "{$erro}\n";

            }

        }
    }

    public function runClientes() {
        $this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
        $clientes_pendentes = $this->TPjurPessoaJuridica->retornaPendentesEndereco('01/06/2015');
        //debug($clientes_pendentes); die;
        /*
        $clientes_pendentes = Array(
            Array(
                'TPjurPessoaJuridica' => Array(
                    'pjur_pess_oras_codigo' => 4,
                    'pjur_cnpj' => '06326025000166',
                ),
                'TPessPessoa' => Array (
                    'pess_tipo' => 'tra',
                    'pess_nome' => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS',
                )
            )        
        );
        */
        foreach ($clientes_pendentes as $key => $cliente) {
            $this->TPjurPessoaJuridica->validationErrors = null;
            $ret = $this->TPjurPessoaJuridica->incluirReferenciaCliente($cliente['TPjurPessoaJuridica']['pjur_cnpj']);

            if ($ret) {
                echo "{$key}: Incluido alvo para o Cliente: ".$cliente['TPessPessoa']['pess_nome']." - ".$cliente['TPjurPessoaJuridica']['pjur_cnpj']."\n";

            } else {
                echo "{$key}: Erro ao incluir o alvo para o Cliente: ".$cliente['TPessPessoa']['pess_nome']." - ".$cliente['TPjurPessoaJuridica']['pjur_cnpj'].": ";
                $erro = current($this->TPjurPessoaJuridica->validationErrors);
                echo "{$erro}\n";
            }
        }
    }


    private function im_running($tipo) {
        if (PHP_OS!='WINNT') {
            $cmd = shell_exec("ps aux | grep '{$tipo}'");
            // 1 execução é a execução atual
            return substr_count($cmd, 'cake.php -working') > 1;
        } else {
            $cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
            $ret = substr_count($cmd, 'cake\console\cake') > 1;         
        }
    }
    
}
?>