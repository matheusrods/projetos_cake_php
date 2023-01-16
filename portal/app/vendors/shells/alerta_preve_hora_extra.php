<?php
class AlertaPreveHoraExtraShell extends Shell {   
    
    function run(){
         echo 'INICIO: '.date("H:i:s") . "\n";
        if (!$this->im_running('alerta_preve_hora_extra')) {
            $this->previsaoHoraExtra();
        } else {
            echo "Já em execução";
        }
    }

    private function im_running($tipo) {
        $cmd = shell_exec("ps aux | grep '{$tipo}'");
        // 1 execução é a execução atual
        return substr_count($cmd, 'cake.php -working') > 1;
    }

    function previsaoHoraExtra(){
        $this->UsuarioExpediente    = ClassRegistry::init('UsuarioExpediente');
        $this->Alerta               = ClassRegistry::init('Alerta');
        $this->PontoEletronico      = ClassRegistry::init('PontoEletronico');
        $this->AutorizacaoHoraExtra = ClassRegistry::init('AutorizacaoHoraExtra');        
        $usuarios = $this->PontoEletronico->verificaPossivelHorasExtrasNaoAutorizadas( );
        if( $usuarios ){
            foreach ($usuarios as $key => $dados ) {
                $dados_usuario = array(
                    'Usuario' => array(
                        'codigo' => $dados['Usuario']['codigo'],
                        'escala' => $dados['Usuario']['escala'] 
                    ),                    
                    'PontoEletronico' => array( 
                        'codigo_tipo_ponto_eletronico' => 2,
                        'data_ponto' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
                    )
                );
                if (!$this->PontoEletronico->validaHorarioPontoEletronico($dados_usuario)){
                    if (!$this->AutorizacaoHoraExtra->permissaoHoraExtra( $dados_usuario['Usuario']['codigo'] ) ) {
                        App::import('Component', array('StringView', 'Mailer.Scheduler'));
                        $this->StringView = new StringViewComponent();          
                        $hora_saida = date('h:i',strtotime( $dados_usuario['PontoEletronico']['data_ponto']) ); 
                        $config_horario_trabalho = $this->PontoEletronico->obtemHoraConfigurada( $dados_usuario );
                        $this->StringView->set(compact('config_horario_trabalho'));
                        $content = $this->StringView->renderMail('email_preve_hora_extra','default');
                        $alerta = array(
                            'Alerta' => array(
                                'descricao' => "Alerta de provavel Hora Extra nao autorizada",
                                'descricao_email' => $content,
                                'assunto' => "Alerta de provavel Hora Extra nao autorizada",
                                'codigo_alerta_tipo' => 53,
                                'email_agendados' => NULL,
                                'model' =>  'Usuario',
                                'foreign_key' => $dados['Usuario']['codigo']
                            ),
                        );
                        $this->Alerta->incluir($alerta);
                    }                
                }
            }
        }
    }    	
}
?>