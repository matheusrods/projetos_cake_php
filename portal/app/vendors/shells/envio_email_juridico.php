<?php
App::import('Component', 'Mailer.Scheduler');
App::import('Component', 'StringView');

class EnvioEmailJuridicoShell extends Shell {
    var $uses = array(
        'Produto',
        'Cliente',
        'ClienteLog',
        'ClienteProdutoLog',
        'ClienteEndereco',
        'ClienteEnderecoLog',
        'ClienteProdutoServico2Log',
        'TipoContato',
    );

    private $listaEmail = array(
        'adm.contratos@buonny.com.br',
        'nataly.arandas@buonny.com.br'
    );
    
    function main() {
        echo "Funcoes: \n";
        echo "=> enviar_email_cliente \n";
        echo "=> enviar_email_cliente_produto \n";
        echo "=> enviar_email_cliente_produto_servico2 \n";
    }

    function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"envio_email_juridico \" | wc -l");
        return ($retorno > 3);
    }

    function enviar_email(){
        if($this->is_alive())
            return false;
        
        $this->Scheduler = new SchedulerComponent();

        $this->enviar_email_cliente();
        $this->enviar_email_cliente_produto();
        $this->enviar_email_cliente_produto_servico2();
    }

    function enviar_email_cliente(){
        $this->layout = 'email';
        $this->ClienteLog->bindUsuarioInclusao();
        $this->ClienteLog->bindUsuarioAlteracao();
        $this->ClienteLog->bindCorretora();
        $this->ClienteLog->bindSeguradora();
        $this->ClienteLog->bindEnderecoRegiao();
        $this->ClienteLog->bindGestor();
        $this->ClienteLog->bindSubtipo();
        $this->ClienteLog->bindCorporacao();
        $logs = $this->ClienteLog->listarParaEnvioEmailJuridico();
        
        foreach($logs as $log){
            $this->StringView = new StringViewComponent();
            $codigo_cliente = $log['ClienteLog']['codigo_cliente'];
            $nome_cliente = $log['ClienteLog']['razao_social'];

            $endereco_comercial = $this->ClienteEnderecoLog->listarEnderecoLogByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL);
            
            $arr_endereco_comercial = array(
                'logradouro' => $endereco_comercial[0]['ClienteEndereco']['logradouro'],
                'cep' => $endereco_comercial[0]['ClienteEndereco']['cep'],
                'cidade' => $endereco_comercial[0]['ClienteEndereco']['cidade'],
                'bairro' => $endereco_comercial[0]['ClienteEndereco']['bairro'],
                'estado' => $endereco_comercial[0]['ClienteEndereco']['estado_abreviacao'],
            );
            if ($endereco_comercial[0]['ClienteEnderecoLog']['numero']) {
                $arr_endereco_comercial['logradouro'] .= ', ' . $endereco_comercial[0]['ClienteEnderecoLog']['numero'];
            }

            if(empty($log['ClienteLog']['data_alteracao'])){
                $titulo = 'Cadastro de novo cliente';
                $tipoEmail = 'cadastro_cliente';
            }else{
                $titulo = 'Alteração de dados do cliente';
                $log_anterior = $this->carregarClienteLogAnterior($log);
                $mensagens = $this->verificarAlteracoesCliente($log,$log_anterior,$endereco_comercial);
                $tipoEmail = 'alteracao_cliente';
            }

            $subject = implode(': ', array($titulo, $codigo_cliente . ' - ' . $nome_cliente));

            $this->StringView->set(compact('log', 'subject', 'mensagens', 'arr_endereco_comercial','tipoEmail'));

            $content = $this->StringView->renderMail('emails_cliente', 'default');

            if($this->enviaEmail($subject,$content)){
                $log['ClienteLog']['enviado_juridico'] = true;
                $this->ClienteLog->atualizar($log);
                echo " - OK";
            }
            echo "\n";
        }
    }

    function enviar_email_cliente_produto(){
        $this->layout = 'email';
        $this->ClienteProdutoLog->bindUsuarioInclusao();
        $this->ClienteProdutoLog->bindUsuarioAlteracao();
        $this->ClienteProdutoLog->bindProduto();
        $this->ClienteProdutoLog->bindCliente();
        $this->ClienteProdutoLog->bindMotivoBloqueio();
        $logs = $this->ClienteProdutoLog->listarParaEnvioEmailJuridico();
        foreach($logs as $log){
            $this->StringView = new StringViewComponent();
            $produto = $log['Produto']['descricao'];
            $codigo_cliente = $log['Cliente']['codigo'];
            $nome_cliente = $log['Cliente']['razao_social'];

            $endereco_comercial = array_shift($this->ClienteEndereco->listaEnderecoByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL));
            
            $arr_endereco_comercial = array(
                'logradouro' => $endereco_comercial['ClienteEndereco']['logradouro'],
                'cep' => $endereco_comercial['ClienteEndereco']['cep'],
                'cidade' => $endereco_comercial['ClienteEndereco']['cidade'],
                'bairro' => $endereco_comercial['ClienteEndereco']['bairro'],
                'estado' => $endereco_comercial['ClienteEndereco']['estado_abreviacao'],
            );
            if ($endereco_comercial['ClienteEndereco']['numero']) {
                $arr_endereco_comercial['logradouro'] .= ', ' . $endereco_comercial['ClienteEndereco']['numero'];
            }

            $titulo = 'Alteração de produto';
            $log_anterior = $this->carregarClienteProdutoLogAnterior($log);
            $mensagens = $this->verificarAlteracoesClienteProduto($log,$log_anterior);
            $tipoEmail = 'alteracao_cliente_produto';

            $subject = implode(': ', array($titulo, $produto.' - '.$codigo_cliente . ' - ' . $nome_cliente));

            $this->StringView->set(compact('log', 'subject', 'mensagens', 'arr_endereco_comercial','tipoEmail'));

            $content = $this->StringView->renderMail('emails_cliente_produto', 'default');
            
            if(empty($mensagens) || $this->enviaEmail($subject,$content)){
                $log['ClienteProdutoLog']['enviado_juridico'] = true;
                $this->ClienteProdutoLog->atualizar($log);
                echo " - OK";
            }
            echo "\n";
        }
    }

    function enviar_email_cliente_produto_servico2(){
        $this->layout = 'email';
        $this->ClienteProdutoServico2Log->bindUsuarioInclusao();
        $this->ClienteProdutoServico2Log->bindUsuarioAlteracao();
        $this->ClienteProdutoServico2Log->bindClienteProduto();
        $this->ClienteProdutoServico2Log->bindServico();
        $logs = $this->ClienteProdutoServico2Log->listarParaEnvioEmailJuridico();

        foreach($logs as $log){
            $this->StringView = new StringViewComponent();

            if($log['ClienteProdutoServico2Log']['acao_sistema'] == 2){
                $cliente_produto_log = $this->carregaClienteProdutoLog($log);
                $log['Produto'] = $cliente_produto_log['ProdutoExclusao'];
                $log['Cliente'] = $cliente_produto_log['ClienteExclusao'];
            }

            $produto = $log['Produto']['descricao'];
            $servico = $log['Servico']['descricao'];
            $codigo_cliente = $log['Cliente']['codigo'];
            $nome_cliente = $log['Cliente']['razao_social'];

            $endereco_comercial = array_shift($this->ClienteEndereco->listaEnderecoByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL));
            
            $arr_endereco_comercial = array(
                'logradouro' => $endereco_comercial['ClienteEndereco']['logradouro'],
                'cep' => $endereco_comercial['ClienteEndereco']['cep'],
                'cidade' => $endereco_comercial['ClienteEndereco']['cidade'],
                'bairro' => $endereco_comercial['ClienteEndereco']['bairro'],
                'estado' => $endereco_comercial['ClienteEndereco']['estado_abreviacao'],
            );
            if ($endereco_comercial['ClienteEndereco']['numero']) {
                $arr_endereco_comercial['logradouro'] .= ', ' . $endereco_comercial['ClienteEndereco']['numero'];
            }

            if($log['ClienteProdutoServico2Log']['acao_sistema'] == 0){
                $titulo = 'Cadastro de serviço';
                $tipoEmail = 'cadastro_cliente_produto_servico';
            }elseif($log['ClienteProdutoServico2Log']['acao_sistema'] == 1){
                $titulo = 'Alteração de serviço';
                $log_anterior = $this->carregarClienteProdutoServico2LogAnterior($log);
                $mensagens = $this->verificarAlteracoesClienteProdutoServico2($log,$log_anterior);
                $tipoEmail = 'alteracao_cliente_produto_servico';
            }else{
                $titulo = 'Exclusão de serviço';
                $tipoEmail = 'exclusao_cliente_produto_servico';
            }

            $subject = implode(': ', array($titulo, $produto.' - '.$servico.' - '.$codigo_cliente . ' - ' . $nome_cliente));

            $this->StringView->set(compact('log', 'subject', 'mensagens', 'arr_endereco_comercial','tipoEmail'));

            $content = $this->StringView->renderMail('emails_cliente_produto_servico', 'default');
            
            if($this->enviaEmail($subject,$content)){
                $log['ClienteProdutoServico2Log']['enviado_juridico'] = true;
                $this->ClienteProdutoServico2Log->atualizar($log);
                echo " - OK";
            }
            echo "\n";
        }
    }

    private function carregarClienteLogAnterior($cliente_log){
        $this->ClienteLog->bindCorretora();
        $this->ClienteLog->bindSeguradora();
        $this->ClienteLog->bindEnderecoRegiao();
        $this->ClienteLog->bindGestor();
        $this->ClienteLog->bindSubtipo();
        $this->ClienteLog->bindCorporacao();
        return $this->ClienteLog->find('first',array(
            'conditions' => array(
                'ClienteLog.codigo < ?' => array($cliente_log['ClienteLog']['codigo']),
                'ClienteLog.codigo_cliente' => $cliente_log['ClienteLog']['codigo_cliente'],
            ),
            'order' => array('ClienteLog.data_inclusao DESC'),
        ));
    }

    private function carregarClienteProdutoLogAnterior($cliente_produto_log){
        $this->ClienteProdutoLog->bindMotivoBloqueio();
        return $this->ClienteProdutoLog->find('first',array(
            'conditions' => array(
                'ClienteProdutoLog.codigo < ?' => array($cliente_produto_log['ClienteProdutoLog']['codigo']),
                'ClienteProdutoLog.codigo_cliente_produto' => $cliente_produto_log['ClienteProdutoLog']['codigo_cliente_produto'],
            ),
            'order' => array('ClienteProdutoLog.data_inclusao DESC'),
        ));
    }

    private function carregarClienteProdutoServico2LogAnterior($cliente_produto_servico2_log){
        return $this->ClienteProdutoServico2Log->find('first',array(
            'conditions' => array(
                'ClienteProdutoServico2Log.codigo < ?' => array($cliente_produto_servico2_log['ClienteProdutoServico2Log']['codigo']),
                'ClienteProdutoServico2Log.codigo_cliente_produto_servico2' => $cliente_produto_servico2_log['ClienteProdutoServico2Log']['codigo_cliente_produto_servico2'],
            ),
            'order' => array('ClienteProdutoServico2Log.data_inclusao DESC'),
        ));
    }

    private function verificarAlteracoesCliente($log_atual,$log_anterior,$endereco_comercial){
        $alteracoes = array();

        if($log_atual['ClienteLog']['razao_social'] != $log_anterior['ClienteLog']['razao_social'])
            $alteracoes[] = 'Foi realizada alteração da razão social do cliente. De '.$log_anterior['ClienteLog']['razao_social'].' para '.$log_atual['ClienteLog']['razao_social'];

        if($log_atual['ClienteLog']['nome_fantasia'] != $log_anterior['ClienteLog']['nome_fantasia'])
            $alteracoes[] = 'Foi realizada alteração do nome fantasia do cliente. De '.$log_anterior['ClienteLog']['nome_fantasia'].' para '.$log_atual['ClienteLog']['nome_fantasia'];

        if($log_atual['ClienteLog']['inscricao_estadual'] != $log_anterior['ClienteLog']['inscricao_estadual'])
            $alteracoes[] = 'Foi realizada alteração da inscrição estadual do cliente. De '.$log_anterior['ClienteLog']['inscricao_estadual'].' para '.$log_atual['ClienteLog']['inscricao_estadual'];

        if($log_atual['ClienteLog']['ccm'] != $log_anterior['ClienteLog']['ccm'])
            $alteracoes[] = 'Foi realizada alteração da inscrição municipal do cliente. De '.$log_anterior['ClienteLog']['ccm'].' para '.$log_atual['ClienteLog']['ccm'];

        if($log_atual['ClienteLog']['codigo_endereco_regiao'] != $log_anterior['ClienteLog']['codigo_endereco_regiao'])
            $alteracoes[] = 'Foi realizada alteração da filial do cliente. De '.$log_anterior['EnderecoRegiao']['descricao'].' para '.$log_atual['EnderecoRegiao']['descricao'];

        if($log_atual['ClienteLog']['codigo_gestor'] != $log_anterior['ClienteLog']['codigo_gestor'])
            $alteracoes[] = 'Foi realizada alteração do gestor do cliente. De '.$log_anterior['Gestor']['nome'].' para '.$log_atual['Gestor']['nome'];

        if($log_atual['ClienteLog']['codigo_corretora'] != $log_anterior['ClienteLog']['codigo_corretora'])
            $alteracoes[] = 'Foi realizada alteração da corretora do cliente. De '.$log_anterior['Corretora']['nome'].' para '.$log_atual['Corretora']['nome'];

        if($log_atual['ClienteLog']['codigo_seguradora'] != $log_anterior['ClienteLog']['codigo_seguradora'])
            $alteracoes[] = 'Foi realizada alteração da seguradora do cliente. De '.$log_anterior['Seguradora']['nome'].' para '.$log_atual['Seguradora']['nome'];

        if($log_atual['ClienteLog']['codigo_cliente_sub_tipo'] != $log_anterior['ClienteLog']['codigo_cliente_sub_tipo'])
            $alteracoes[] = 'Foi realizada alteração do subtipo do cliente. De '.$log_anterior['ClienteSubTipo']['descricao'].' para '.$log_atual['ClienteSubTipo']['descricao'];

        if($log_atual['ClienteLog']['codigo_corporacao'] != $log_anterior['ClienteLog']['codigo_corporacao'])
            $alteracoes[] = 'Foi realizada alteração da corporação do cliente. De '.$log_anterior['Corporacao']['descricao'].' para '.$log_atual['Corporacao']['descricao'];

        if($log_atual['ClienteLog']['regiao_tipo_faturamento'] != $log_anterior['ClienteLog']['regiao_tipo_faturamento'])
            $alteracoes[] = 'Foi realizada alteração do tipo de faturamento do cliente. De '.($log_anterior['ClienteLog']['regiao_tipo_faturamento'] ? 'Parcial para Total' : 'Total para Parcial');

        if($log_atual['ClienteLog']['ativo'] != $log_anterior['ClienteLog']['ativo']){
            if($log_atual['ClienteLog']['ativo'])
                $alteracoes[] = 'O Cliente foi reativado.';
            else
                $alteracoes[] = 'O Cliente foi inativado.';
        }

        if(count($endereco_comercial) == 2){
            $endereco_atual = $endereco_comercial[0];
            $endereco_anterior = $endereco_comercial[1];
            $alterado = false;
            
            if(
            $endereco_atual['ClienteEndereco']['logradouro'] != $endereco_anterior['ClienteEndereco']['logradouro'] ||
            $endereco_atual['ClienteEnderecoLog']['numero'] != $endereco_anterior['ClienteEnderecoLog']['numero'] ||
            $endereco_atual['ClienteEndereco']['cep'] != $endereco_anterior['ClienteEndereco']['cep'] ||
            $endereco_atual['ClienteEndereco']['cidade'] != $endereco_anterior['ClienteEndereco']['cidade'] ||
            $endereco_atual['ClienteEndereco']['bairro'] != $endereco_anterior['ClienteEndereco']['bairro'] ||
            $endereco_atual['ClienteEndereco']['estado_abreviacao'] != $endereco_anterior['ClienteEndereco']['estado_abreviacao'])
                $alterado = true;

            if($alterado){
                $mensagem = 'Foi realizada alteração do endereço comercial do cliente<BR>';
                $mensagem .= 'De: '.$endereco_anterior['ClienteEndereco']['logradouro'].', '.$endereco_anterior['ClienteEnderecoLog']['numero'].' - '.$endereco_anterior['ClienteEndereco']['bairro'].' - '.$endereco_anterior['ClienteEndereco']['cidade'].', '.$endereco_anterior['ClienteEndereco']['estado_abreviacao'].'<BR>';
                $mensagem .= 'Para: '.$endereco_atual['ClienteEndereco']['logradouro'].', '.$endereco_atual['ClienteEnderecoLog']['numero'].' - '.$endereco_atual['ClienteEndereco']['bairro'].' - '.$endereco_atual['ClienteEndereco']['cidade'].', '.$endereco_atual['ClienteEndereco']['estado_abreviacao'].'';
                $alteracoes[] = $mensagem;
            }
        }

        return $alteracoes;
    }

    private function verificarAlteracoesClienteProduto($log_atual,$log_anterior){
        $alteracoes = array();

        if($log_atual['ClienteProdutoLog']['valor_taxa_bancaria'] != $log_anterior['ClienteProdutoLog']['valor_taxa_bancaria'])
            $alteracoes[] = 'Foi realizada alteração do valor da taxa bancária. De R$ '.number_format($log_anterior['ClienteProdutoLog']['valor_taxa_bancaria'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoLog']['valor_taxa_bancaria'],2,',','.');

        if($log_atual['ClienteProdutoLog']['valor_taxa_corretora'] != $log_anterior['ClienteProdutoLog']['valor_taxa_corretora'])
            $alteracoes[] = 'Foi realizada alteração do valor da taxa corretora. De R$ '.number_format($log_anterior['ClienteProdutoLog']['valor_taxa_corretora'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoLog']['valor_taxa_corretora'],2,',','.');

        if($log_atual['ClienteProdutoLog']['valor_premio_minimo'] != $log_anterior['ClienteProdutoLog']['valor_premio_minimo'])
            $alteracoes[] = 'Foi realizada alteração do valor do prêmio mínimo. De R$ '.number_format($log_anterior['ClienteProdutoLog']['valor_premio_minimo'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoLog']['valor_premio_minimo'],2,',','.');

        if($log_atual['ClienteProdutoLog']['qtd_premio_minimo'] != $log_anterior['ClienteProdutoLog']['qtd_premio_minimo'])
            $alteracoes[] = 'Foi realizada alteração da quantidade do prêmio mínimo. De R$ '.number_format($log_anterior['ClienteProdutoLog']['qtd_premio_minimo'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoLog']['qtd_premio_minimo'],2,',','.');

        if($log_atual['ClienteProdutoLog']['codigo_motivo_bloqueio'] != $log_anterior['ClienteProdutoLog']['codigo_motivo_bloqueio'])
            $alteracoes[] = 'Foi realizada alteração no status do produto. De '.$log_anterior['MotivoBloqueio']['descricao'].' para '.$log_atual['MotivoBloqueio']['descricao'];

        if(substr($log_atual['ClienteProdutoLog']['data_faturamento'],0,10) != substr($log_anterior['ClienteProdutoLog']['data_faturamento'],0,10))
            $alteracoes[] = 'Foi realizada alteração na data do faturamento. De '.substr($log_anterior['ClienteProdutoLog']['data_faturamento'],0,10).' para '.substr($log_atual['ClienteProdutoLog']['data_faturamento'],0,10);

        if($log_atual['ClienteProdutoLog']['pendencia_comercial'] != $log_anterior['ClienteProdutoLog']['pendencia_comercial']){
            if($log_atual['ClienteProdutoLog']['pendencia_comercial']){
                $alteracoes[] = 'Foi adicionada pendência comercial';
            }else{
                $alteracoes[] = 'Foi retirada pendência comercial';
            }
        }

        if($log_atual['ClienteProdutoLog']['pendencia_financeira'] != $log_anterior['ClienteProdutoLog']['pendencia_financeira']){
            if($log_atual['ClienteProdutoLog']['pendencia_financeira']){
                $alteracoes[] = 'Foi adicionada pendência financeira';
            }else{
                $alteracoes[] = 'Foi retirada pendência financeira';
            }
        }

        if($log_atual['ClienteProdutoLog']['pendencia_juridica'] != $log_anterior['ClienteProdutoLog']['pendencia_juridica']){
            if($log_atual['ClienteProdutoLog']['pendencia_juridica']){
                $alteracoes[] = 'Foi adicionada pendência jurídica';
            }else{
                $alteracoes[] = 'Foi retirada pendência jurídica';
            }
        }

        return $alteracoes;
    }

    private function verificarAlteracoesClienteProdutoServico2($log_atual,$log_anterior){
        $alteracoes = array();

        if($log_atual['ClienteProdutoServico2Log']['valor'] != $log_anterior['ClienteProdutoServico2Log']['valor'])
            $alteracoes[] = 'Foi realizada alteração do valor. De R$ '.number_format($log_anterior['ClienteProdutoServico2Log']['valor'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoServico2Log']['valor'],2,',','.');
        
        if($log_atual['ClienteProdutoServico2Log']['valor_maximo'] != $log_anterior['ClienteProdutoServico2Log']['valor_maximo'])
            $alteracoes[] = 'Foi realizada alteração do valor máximo. De R$ '.number_format($log_anterior['ClienteProdutoServico2Log']['valor_maximo'],2,',','.').' para R$ '.number_format($log_atual['ClienteProdutoServico2Log']['valor_maximo'],2,',','.');

        return $alteracoes;
    }

    private function enviaEmail($subject,$content){
        echo "Enviando e-mail: ".$subject;
        $qtd_email = count($this->listaEmail);
        $qtd_enviado = 0;
        $options = array(
            'from' => 'portal@rhhealth.com.br',
            //'cc' => 'retorno.perfil@buonnny.com.br',
            'sent' => null,
            'subject' => $subject,
        );

        foreach($this->listaEmail as $email){
            $options['to'] = $email;
            
            if($this->Scheduler->schedule($content,$options))
                $qtd_enviado++;
        }

        return ($qtd_email == $qtd_enviado);
    }
    
    private function carregaClienteProdutoLog($log){
        return $this->ClienteProdutoLog->find('first',array(
            'joins' => array(
                array(
                    'table' => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
                    'alias' => 'ProdutoExclusao',
                    'type' => 'INNER',
                    'conditions' => array('ClienteProdutoLog.codigo_produto = ProdutoExclusao.codigo')
                ),
                array(
                    'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                    'alias' => 'ClienteExclusao',
                    'type' => 'INNER',
                    'conditions' => array('ClienteProdutoLog.codigo_cliente = ClienteExclusao.codigo')
                ),
            ),
            'conditions' => array(
                'ClienteProdutoLog.codigo_cliente_produto' => $log['ClienteProdutoServico2Log']['codigo_cliente_produto']
            ),
            'order' => array('ClienteProdutoLog.data_inclusao DESC'),
            'fields' => array(
                'ClienteProdutoLog.*',
                'ProdutoExclusao.codigo',
                'ProdutoExclusao.descricao',
                'ClienteExclusao.codigo',
                'ClienteExclusao.razao_social',
                'ClienteExclusao.codigo_documento',
            ),
        ));
    }
}