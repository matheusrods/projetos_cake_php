<?php

class PesquisasConfiguracoesController extends AppController {

    public $name = 'PesquisasConfiguracoes';
    public $uses = array('PesquisaConfiguracao', 'Ficha', 'LogFaturamentoTeleconsult', 'Seguradora');
    public $components = array('mailer.Scheduler');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('pesquisar_ficha', 'pesquisar_ficha_renovacao', 'agendaEmail'));
    }

    function index() {
        $this->pageTitle = 'Configurações Pesquisa Automática de Profissionais';
        $configuracoes = $this->PesquisaConfiguracao->find('all');
        $this->set(compact('configuracoes'));
    }

    function atualiza($codigo) {
        $this->pageTitle = 'Editar Pesquisa Automática de Profissionais';
        if (!empty($this->data)) {
            if ($this->PesquisaConfiguracao->atualiza($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $status = & ClassRegistry::init('Status');
        $this->data = $this->PesquisaConfiguracao->find('first', array('conditions' => array($this->PesquisaConfiguracao->name . '.codigo' => $codigo)));
        $descricao_produto = ($this->data['PesquisaConfiguracao']['codigo_produto'] == 1 ? 'Standard' : ($this->data['PesquisaConfiguracao']['codigo_produto'] == 2 ? 'Plus' : '') );
        $lista_status = $status->find('list');
        $this->set(compact('descricao_produto', 'lista_status'));
    }

    function _helperStatusFicha($statusDadoValidado) {
        if (is_null($statusDadoValidado)) {
            return '-';
        }

        if ($statusDadoValidado === false) {
            return 'Pendente';
        } else if ($statusDadoValidado === true) {
            return 'Concluído';
        } else
            return $statusDadoValidado;
    }

    function pesquisar_ficha_renovacao() {
        $dataAtual = date('Y-m-d');
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);

        $fichaPesquisaModel = & ClassRegistry::init('FichaPesquisa');

        $fichas = $fichaPesquisaModel->obterFichasParaRenovacao($dataAtual);
        $fichaModel = & ClassRegistry::init('Ficha');
        
        $gravar = true;
        $qtdeOk = 0;
        $qtdeFichas = count($fichas);
        $this->FichaRetorno = & ClassRegistry::init('FichaRetorno');
        foreach($fichas as $k => $ficha) {
            $arrStatusFicha = $this->PesquisaConfiguracao->validar($ficha, $gravar);
            if ($arrStatusFicha === false) {
                continue;
            }
            $validacoesNok = array_filter($arrStatusFicha, create_function('$validacao', 'return !is_null($validacao) && $validacao === false;'));
            if (count($validacoesNok) == 0) {
                $fichaValidada = $this->PesquisaConfiguracao->atualizaStatusFichaAdequadoAoRisco($ficha);
                if ($fichaValidada) {
                    $fichaModel->liberaFicha($fichaValidada, null);
                    $this->insereCt($fichaValidada);
                    $qtdeOk++;
                }
            }
        }
        $retorno = new stdClass();
        $retorno->success = true;
        $retorno->total = $qtdeFichas;
        $retorno->validadas = $qtdeOk;
        $retorno->message = "Pesquisa realizada com sucesso";

        echo json_encode($retorno);
        exit(0);
    }

    function pesquisar_ficha($codigo_ficha, $visualiza = 1) {
        $this->layout = 'ajax';
        ClassRegistry::init('FichaVeiculo');

        $gravar = !$visualiza;
        
        if ($visualiza == 0) {
            $categoria_carreteiro = 1;
            $arrStatusFicha = $this->PesquisaConfiguracao->validar($codigo_ficha, $gravar, $categoria_carreteiro);
            
            $msg = null;
            if ($arrStatusFicha === false) {
                $success = false;
            } else {
                $validacoesNok = array_filter($arrStatusFicha, create_function('$validacao', 'return !is_null($validacao) && $validacao === false;'));                
                
                $success = true;
                $ok = (count($validacoesNok) == 0);
                if ($ok) {
                    $msg = 'Pesquisa realizada: Status OK.';
                } else {
                    $msg = 'Pesquisa realizada: Status Pendente.';
                }
                if ($ok) {
                    $ficha = $this->PesquisaConfiguracao->atualizaStatusFichaAdequadoAoRisco($codigo_ficha);
                    if ($ficha)
                        $this->agendaEmail($codigo_ficha);
                }
            }
            $retorno = new stdClass();
            $retorno->success = $success;
            $retorno->message = $msg;

            echo json_encode($retorno);
            exit(0);
        } else {
            $arrStatusFicha = $this->PesquisaConfiguracao->validar($codigo_ficha, $gravar);
            $this->set(array_map(array($this, '_helperStatusFicha'), $arrStatusFicha));
        }
    }


    function insereCt($ficha){
        $fichaCt =& ClassRegistry::init('FichaCt');
        $fichaCt->insere($ficha);
    }

    function agendaEmail($codigo_ficha) {
        $this->FichaRetorno = & ClassRegistry::init('FichaRetorno');
        $listaEmail = $this->FichaRetorno->selecaoEmails($codigo_ficha);

        $retornoEmail = !empty($listaEmail);

        ClassRegistry::init('FichaVeiculo');
        $this->Ficha->bindLazy();
        $ficha = $this->Ficha->find('first', array('conditions' =>
            array('Ficha.codigo' => $codigo_ficha)
                )
        );

        if (!empty($listaEmail)) {
            $log_faturamento = $this->LogFaturamentoTeleconsult->find('first', array('conditions' => array('codigo_ficha' => $codigo_ficha)));
            $veiculo = array_filter($ficha['VeiculoLog'], create_function('$validacao', 'return !is_null($validacao["FichaVeiculo"]["tipo"]) && $validacao["FichaVeiculo"]["tipo"] == 0;'));
            if ($veiculo) {
                $veiculo = $veiculo['0']['placa'];
            } else {
                $veiculo = '-';
            }

            $carreta = array_filter($ficha['VeiculoLog'], create_function('$validacao', 'return !is_null($validacao["FichaVeiculo"]["tipo"]) && $validacao["FichaVeiculo"]["tipo"] == 1;'));

            if (count($carreta) > 0) {
                $carreta = array_shift(array_values($carreta));
                $carreta = $carreta['placa'];
            } else {
                $carreta = '-';
            }

            if ($ficha['Cliente']['codigo_seguradora'] == Seguradora::ROYAL) {
                $tel_retorno = '(11) 2124-2311';
                $from = 'operacional@informe.srv.br';
            } else{
                $tel_retorno = '(11) 3443-2325';
                $from = 'retorno.perfil@buonny.com.br';
            }

            if (!empty($ficha)) {
                $corpo_mail_bruto =
                        '<img src="http://www.rhhealth.com.br/assets/img/logo-rhhealth.png"><br><br>'.'Retorno de Pesquisas - CARRETEIRO <br><br>' .
                        "Cliente: {$ficha['Cliente']['razao_social']} <br><br>" .
                        "Produto: {$ficha['Produto']['descricao']} <br><br>" .
                        "Nome: {$ficha['ProfissionalLog']['nome']} <br><br>" .
                        "Veiculo: {$veiculo} <br><br>" .
                        "Carreta: {$carreta} <br><br>" .
                        '<b>Status: 1 (UM) PERFIL ADEQUADO AO RISCO</b><br><br>' .
                        "Consulta Número: {$log_faturamento['LogFaturamentoTeleconsult']['numero_liberacao']} <br><br>" .
                        'Validade: O EMBARQUE <br><br>' .
                        'Atenção: É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à<br>' .
                        'contratante e ao funcionário infrator, responsabilidade civil e criminal. <br><br>' .
                        'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a ' .
                        'Gerenciadora de Riscos<br>qualquer responsabilidade sobre esta decisão. <br><br>' .
                        '<center>SETOR DE PESQUISAS <br>' .
                        'Todos os STATUS podem sofrer alterações. <br><br>' .
                        'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER. <br>' .
                        "Em caso de dúvida fone: $tel_retorno </center>";
            }

            $hora_email = null;
            $configuracao = $this->PesquisaConfiguracao->find('first', array('conditions' => array('codigo_produto' => $ficha['Ficha']['codigo_produto'])));
            if ($configuracao) {
                $hora_email = Date('Y-m-d H:i:s', strtotime('+'.$configuracao['PesquisaConfiguracao']['quantidade_minutos_espera_envio_email'].' minute', mktime()));
            }

            foreach ($listaEmail as $email) {
                $email = $email['FichaRetorno']['descricao'];
                $options = array(
                    'from' => $from,
                    'cc' => 'retorno.perfil@buonny.com.br',
                    'sent' => null,
                    'to' => $email,
                    'subject' => 'Confirmação de Carreteiro - BUONNY - ' . Date('d/m/Y'),
                    'liberar_envio_em' => $hora_email
                );

                $this->Scheduler->schedule($corpo_mail_bruto, $options, 'Ficha', $codigo_ficha);
            }
        }
        $this->Ficha->liberaFicha($ficha, $listaEmail);
    }

}

?>
