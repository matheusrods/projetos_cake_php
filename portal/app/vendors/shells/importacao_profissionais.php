<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class ImportacaoProfissionaisShell extends Shell {
    var $uses = array(
        'Ficha',
        'Cliente',
        'Usuario',
        'Profissional',
        'RenovacaoAutomatica',
    	'FichaRetorno',
    );

    function main() {
        echo "Funcoes: \n";
        echo "=> importar \n";
    }

    function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"importacao_profissionais \" | wc -l");
        return ($retorno > 3);
    }

    function run(){
        if($this->is_alive())
            return false;

        $this->importar();
    }

    function importar(){
        $path = dirname(ROOT).DS.'arquivos'.DS;
        $arquivos = glob($path.'impProf*.csv');
        if(count($arquivos) > 0){
            $this->StringView   = new StringViewComponent();
            $this->Scheduler    = new SchedulerComponent();
            foreach($arquivos as $key => $value){
                $nome_arquivo = end(explode("/",end(explode("\\",$value))));
                $dados_nome_arquivo = explode('|', urldecode($nome_arquivo));
                $usuario = $this->Usuario->carregar($dados_nome_arquivo[1]);
                $saida = $path.'importacao_profissionais'.DS.$nome_arquivo;
                $str_saida = "";
                if(file_exists($value)){
                    $arquivo = fopen($value, "r");
                    if ($arquivo) {
                        $i=0;
                        $qtd_profissionais = 0;
                        $qtd_add_para_renovar = 0;
                        while (!feof($arquivo)) {
                            $linha = trim( fgets($arquivo, 4096) );
                            $str_saida .= $linha.';';
                            if( $i > 0 && $linha!="" && $linha!=";" && str_replace(';', '', $linha) != ''){
                                $qtd_profissionais++;
                                $dados = explode(';', $linha );
                                if(empty($dados[3]) || empty($dados[10])){
                                    if(empty($dados[3]))
                                        $str_saida .= "Preencha o cnpj da transportadora;";
                                    if(empty($dados[10]))
                                        $str_saida .= "Preencha o cpf do profissional;";
                                    $str_saida .= "\n";
                                    continue;
                                }
                                $transportadora = $this->Cliente->carregarPorDocumento(str_pad($dados[3],14,'0',STR_PAD_LEFT));
                                $profissional = $this->Profissional->buscaPorCPF(str_pad($dados[10],11,'0',STR_PAD_LEFT));

                                if(!$transportadora || !$profissional){
                                    if(!$transportadora)
                                        $str_saida .= "Transportadora nao encontrada;";
                                    if(!$profissional)
                                        $str_saida .= "Profissional nao encontrado;";
                                    $str_saida .= "\n";
                                    continue;
                                }
                                $this->Ficha->bindFichaRetorno();
                                $ficha = $this->Ficha->carregaUltimaFichaProfissional($transportadora['Cliente']['codigo'],$profissional['Profissional']['codigo'],FALSE,array(1,2));

                                if(!$ficha){
                                    $str_saida .= "Nao encontrou ficha para este cliente;\n";
                                    continue;
                                }elseif(AppModel::dateTimeToDbDateTime2($ficha['Ficha']['data_inclusao']) >= date("Y-m-d H:i:s", strtotime("-30 days"))){
                                    $str_saida .= "Profissional renovado a menos de 30 dias;Renovado em ".$ficha['Ficha']['data_inclusao'].";Data Vencimento: ".$ficha['Ficha']['data_validade'].";\n";
                                    continue;
                                }

                                try{
                                    $this->RenovacaoAutomatica->query("BEGIN TRANSACTION");
                                    $renovacao = $this->RenovacaoAutomatica->find('all',array(
                                        'conditions' => array(
                                            'codigo_profissional' => $profissional['Profissional']['codigo'],
                                            'codigo_cliente' => $transportadora['Cliente']['codigo'],
                                            'renovar' => true,
                                            'processado' => false,
                                            'codigo_produto' => $ficha['Ficha']['codigo_produto']
                                        ),
                                    ));
                                    if(!$renovacao){
                                        $nova_ficha = array(
                                            'codigo_cliente' => $transportadora['Cliente']['codigo'],
                                            'codigo_profissional' => $profissional['Profissional']['codigo'],
                                            'codigo_profissional_tipo' => $ficha['Ficha']['codigo_profissional_tipo'],
                                            'data_atualizacao_ficha' => ($ficha['Ficha']['data_alteracao'] ? $ficha['Ficha']['data_alteracao'] : $ficha['Ficha']['data_inclusao']),
                                            'data_validade_ficha' => $ficha['Ficha']['data_validade'],
                                            'contato' => $ficha['FichaRetorno']['descricao'],
                                            'representante' => $ficha['FichaRetorno']['nome'],
                                            'codigo_produto' => $ficha['Ficha']['codigo_produto'],
                                            'renovar' => true,
                                            'processado' => false,
                                            'codigo_usuario_inclusao' => $usuario['Usuario']['codigo'],
                                        );

                                        if(!$this->RenovacaoAutomatica->incluir($nova_ficha)){
                                            $str_saida .= "Erro ao renovar a ficha;";
                                            throw new Exception();
                                        }
                                        $str_saida .= "Ficha Renovada;";
                                        $qtd_add_para_renovar++;
                                    }else{
                                        $str_saida .= "Ficha ja esta em renovacao;";
                                        throw new Exception();
                                    }

                                    $this->RenovacaoAutomatica->commit();
                                }catch(Exception $ex){
                                    $this->RenovacaoAutomatica->rollback();
                                }
                            }
                            $str_saida .= "\n";
                            ++$i;
                        }
                        fclose($arquivo);
                        unlink($value);
                    }
                }
                file_put_contents($saida,$str_saida);
                $this->StringView->set(compact('qtd_profissionais','qtd_add_para_renovar','dados_nome_arquivo','usuario'));
                $content = $this->StringView->renderMail('email_renovacao_automatica_importacao_planilha', 'default');
                $options = array(
                    'from'      => 'portal@rhhealth.com.br',
                    'sent'      => null,
                    'to'        => 'camarinho@buonny.com.br;janaina.silva@buonny.com.br;agregado.suporte@buonny.com.br;elcio.gallo@buonny.com.br',
                    'subject'   => 'Renovação Automática Importação Planilha',
                );
                $this->Scheduler->schedule($content, $options);
                if (!empty($usuario['Usuario']['email'])) {
                    $options = array(
                        'from'      => 'portal@rhhealth.com.br',
                        'sent'      => null,
                        'to'        => $usuario['Usuario']['email'],
                        'subject'   => 'Renovação Automática Importação Planilha',
                    );
                    $this->Scheduler->schedule($content, $options);
                }
            }
        }
    }
}