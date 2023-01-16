
<?php
class MailerComponent extends Object {
        var $name = 'Mailer';

        var $ambiente = null;

        var $smtpServerOptions = array(
                #'port'=>'25',
                #'timeout'=>'30',
                #'host' => 'webmail.buonny.com.br',
              'port'=>'587',
               'timeout'=>'30',
               'host' => 'smtp.sendgrid.net',
               'username' => '----', #usuario
               'password' => '----', #senha

        );

        var $defaultOptions = array(
                'from' => 'Buonny <retorno.perfil@rhhealth.com.br>',
                'to' => 'Desenvolvimento <grupotid@rhhealth.com.br>',
                'subject_prefix' => '[RHHealth] ',
                'subject' => '',
                'template' => null,
                'layout' => null,
        );

        public function __construct(){
                App::import('Component', 'Email');
                $this->Email = new EmailComponent();
                $this->Email->xMailer = 'Buonny Mailer';
                $this->ambiente = new Ambiente();
        }

        public function startup(&$controller){
                $this->Email->Controller =& $controller;
        }

        public function send($content, $options = array()){

                if( !empty($options['attachments']) ){
                        if($this->isJson($options['attachments'])) {
                                $attachments = json_decode($options['attachments']);
                                unset($options['attachments']);
                                foreach ($attachments as $key => $attachment) {
                                        $options['attachments'][$key] = $attachment;
                                }
                        }
                 }

                $this->configuraOpcoesProximoEnvio($options);
                $this->configuraOpcoesServidorEnvio();

                if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
                        $content.= "\n\n O email seria enviado para o(s) email(s): ".$options['to'];
                }

                $email_enviado = $this->Email->send($content);

                if( $email_enviado && !empty($options['attachments']) ) {
                        if(is_array($options['attachments'])) {
                                foreach ($options['attachments'] as $key => $attachment) {
                                        $this->removeArquivoAnexo( $attachment );
                                }
                        } else {
                                $this->removeArquivoAnexo( $options['attachments'] );
                        }
                }
                return $email_enviado;
        }

        private function configuraOpcoesProximoEnvio($options){
                $options = array_merge($this->defaultOptions, $options);

                $this->Email->reset();

                $this->Email->sendAs = 'html';
                if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
                    $this->Email->to = explode(';', $options['to']);
                } else {
                    $this->Email->to = array('tid@ithealth.com.br');
                    $options['subject_prefix'] = '[teste] ';
                }
                $this->Email->from = $options['from'];
                if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
                        $this->Email->cc = !is_array($options['cc']) ? explode(';', $options['cc']) : $options['cc'];
                        $this->Email->cc = (!isset($options['cc']) ? array() : (is_array($options['cc']) ? $options['cc'] : array($options['cc'])) );
                } else {
                        $this->Email->cc = $this->Email->to;
                }
                $this->Email->subject = $options['subject_prefix'] . $options['subject'];
                if( !empty($options['attachments']) ){
                        $this->Email->attachments = $options['attachments'];
                }
                $this->Email->template  = $options['template'];
                $this->Email->layout    = $options['layout'];
        }

        private function configuraOpcoesServidorEnvio(){
                if($this->ambiente->getServidor() == Ambiente::SUITE_TESTE) {
                        $this->Email->delivery = 'debug';
                } else {
                        $this->Email->smtpOptions = $this->smtpServerOptions;
                        $this->Email->delivery = 'smtp';
                }
        }

        /**
         * Remvove o arquivo anexado
         */
        private function removeArquivoAnexo( $attachments )
        {

                //verificacao para não deletar os arquivos da pasta durante 7 dias do arquivo
                if(stripos($attachments,'email_arquivo_cliente') == true) {
                        return true;
                }

                //apaga o arquivo anexado
                unlink( $attachments );

        } //fim removeArquivoAnexo

        private function isJson($json) {
                json_decode($json);
                return (json_last_error() == JSON_ERROR_NONE);
        }

}
?>
