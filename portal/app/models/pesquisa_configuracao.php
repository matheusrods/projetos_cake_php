<?php

class PesquisaConfiguracao extends AppModel {

    var $name = 'PesquisaConfiguracao';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'pesquisas_configuracoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_pesquisa_configuracao'));
    var $httpSocket = null;
    var $belongsTo = array(
        'Status' => array(
            'class' => 'Status',
            'foreignKey' => 'codigo_status_anterior'
        )
    );

    function beforeValidate() {
        if (isset($this->data[$this->name]['valor_serasa'])) {
            $valor = $this->data[$this->name]['valor_serasa'];
            if (strpos($valor, '.') > 0 && strpos($valor, ',') > 0) {
                $valor = str_replace('.', '', $valor);
            }
            $this->data[$this->name]['valor_serasa'] = str_replace(',', '.', $valor);
        }
    }

    function atualiza($data) {
        if (empty($data['PesquisaConfiguracao']['codigo']) || $data['PesquisaConfiguracao']['historico_quantidade_meses'] == 0) {
            return false;
        }

        return $this->save($data);
    }

    function getHttpSocket() {
        if ($this->httpSocket == null) {
            App::import('Core', 'HttpSocket');
            $this->setHttpSocket(new HttpSocket());
        }
        return $this->httpSocket;
    }

    function setHttpSocket($sk) {
        $this->httpSocket = $sk;
    }


    function validaPesquisadorAutomaticoCheque($codigoProduto, $qtdeChequesInformada) {
        $qtdeCheques = $this->find('first', array(
            'conditions' => array(
                'codigo_produto' => $codigoProduto
            ),
            'fields' => array(
                'quantidade_cheque'
            )
                ));

        if (empty($qtdeCheques))
            return false;

        return ($qtdeChequesInformada <= $qtdeCheques['PesquisaConfiguracao']['quantidade_cheque']);
    }

    function validaMontanteSerasaProfissional($codigoFicha) {
        $this->Ficha =& ClassRegistry::init('Ficha');
        $this->ProfissionalSerasa =& ClassRegistry::init('ProfissionalSerasa');

        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($codigoFicha);

        $resposta = $this->find('first', array(
            'conditions' => array('codigo_produto' => '2'),
            'fields' => array('valor_serasa'))
        );

        $valorConfigurado = $resposta['PesquisaConfiguracao']['valor_serasa'];
        $valorProfissional = $this->ProfissionalSerasa->valorSerasa($codigo_profissional);

        if ($valorProfissional === false) {
            return false;
        }

        return ($valorProfissional <= $valorConfigurado);
    }

    function validaQuantidadeChequesSerasaProfissionalScorecard($codigoFicha, $codigo_profissional = null, $quantidadeChequesConfigurada = null) {
        $this->FichaScorecard =& ClassRegistry::init('FichaScorecard');
        $this->ProfissionalSerasa =& ClassRegistry::init('ProfissionalSerasa');
        App::import('Model', 'Produto');

        $this->ProfissionalSerasa->httpSocket =& $this->httpSocket;
        $usuario = & ClassRegistry::init('Usuario');
        
        $usuarioPesquisadorAutomatico = $usuario->findByApelido('pesquisador_automatico');

        if(is_null($quantidadeChequesConfigurada)){
            $resposta = $this->find('first', array(
                    'conditions' => array(
                        'codigo_produto' => Produto::SCORECARD
                        ), 
                    'fields' => array('quantidade_cheque')
                ) 
            );               
            $quantidadeChequesConfigurada = $resposta['PesquisaConfiguracao']['quantidade_cheque'];
        }

        if(is_null($codigo_profissional)){
            $codigo_profissional = $this->FichaScorecard->buscaCodigoProfissional($codigoFicha);
        }

        $quantidadeChequesProfissional = $this->ProfissionalSerasa->quantidadeChequesSemFundo(
                    $codigo_profissional, $usuarioPesquisadorAutomatico['Usuario']['apelido']
                );
        
        if ($quantidadeChequesProfissional == false) {
           $quantidadeChequesProfissional = 0;
        }
         

        return ($quantidadeChequesProfissional <= $quantidadeChequesConfigurada);
    } 

    function validaMontanteSerasaProfissionalScorecard($codigoFicha, $codigo_profissional = null, $valorConfigurado = null) {
        $this->FichaScorecard =& ClassRegistry::init('FichaScorecard');
        $this->ProfissionalSerasa =& ClassRegistry::init('ProfissionalSerasa');
        App::import('Model', 'Produto');

        if(is_null($valorConfigurado)){
            $resposta = $this->find('first', array(
                'conditions' => array('codigo_produto' => Produto::SCORECARD),
                'fields' => array('valor_serasa'))
            );
            $valorConfigurado = $resposta['PesquisaConfiguracao']['valor_serasa'];
        }
        if(is_null($codigo_profissional)){
            $codigo_profissional = $this->FichaScorecard->buscaCodigoProfissional($codigoFicha);
        }
        
        $valorProfissional = $this->ProfissionalSerasa->valorSerasa($codigo_profissional);
        
        if ($valorProfissional == false) {
            $valorProfissional=0;
        }
        return ($valorProfissional <= $valorConfigurado);
    }

    function validaStatusUltimaPesquisaProfissional($codigo_profissional, $codigo_ficha = NULL){
        $this->FichaScorecard = ClassRegistry::init('FichaScorecard');
        $penultima_ficha = $this->FichaScorecard->carregaFichaAnteriorProfissional($codigo_profissional, FALSE, $codigo_ficha);        
        $codigo_parametro_score = $penultima_ficha['FichaScorecard']['codigo_parametro_score'];
        if ($codigo_parametro_score==ParametroScore::INSUFICIENTE || $codigo_parametro_score==ParametroScore::DIVERGENTE)
            return false;
        return true;
    }

    function validaMontanteSerasaProprietario($codigoFicha) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->ProprietarioSerasa =& ClassRegistry::init('ProprietarioSerasa');

        $codigo_proprietario = $this->Ficha->buscaCodigoProprietario($codigoFicha);
        $resposta = $this->find('first', array(
            'conditions' => array('codigo_produto' => '2'),
            'fields' => array('valor_serasa'))
        );
        $valorConfigurado = $resposta['PesquisaConfiguracao']['valor_serasa'];
        $valorProprietario = $this->ProprietarioSerasa->valorSerasa($codigo_proprietario);

        if ($valorProprietario === false) {
            return false;
        }
        return ($valorProprietario <= $valorConfigurado);
    }

    function validaQuantidadeChequesSerasaProfissional($codigoFicha) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->ProfissionalSerasa =& ClassRegistry::init('ProfissionalSerasa');
        $this->ProfissionalSerasa->httpSocket =& $this->httpSocket;
        $usuario = & ClassRegistry::init('Usuario');
        
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($codigoFicha);
        $usuarioPesquisadorAutomatico = $usuario->findByApelido('pesquisador_automatico');

        $resposta = $this->find('first', array('conditions' => array('codigo_produto' => '2'), 'fields' => array('quantidade_cheque')) );

        $quantidadeChequesConfigurada = $resposta['PesquisaConfiguracao']['quantidade_cheque'];
        $quantidadeChequesProfissional = $this->ProfissionalSerasa->quantidadeChequesSemFundo($codigo_profissional, $usuarioPesquisadorAutomatico['Usuario']['apelido'], 'profissional');

        if ($quantidadeChequesProfissional === false) {
            return false;
        }

        return ($quantidadeChequesProfissional <= $quantidadeChequesConfigurada);
    }

    function validaQuantidadeChequesSerasaProprietario($codigoFicha) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->ProprietarioSerasa =& ClassRegistry::init('ProprietarioSerasa');
        $this->ProprietarioSerasa->httpSocket =& $this->httpSocket;
        $usuario = & ClassRegistry::init('Usuario');
        
        $codigo_proprietario = $this->Ficha->buscaCodigoProprietario($codigoFicha);
        $usuarioPesquisadorAutomatico = $usuario->findByApelido('pesquisador_automatico');

        $resposta = $this->find('first', array('conditions' => array('codigo_produto' => '2'), 'fields' => array('quantidade_cheque')) );

        $quantidadeChequesConfigurada = $resposta['PesquisaConfiguracao']['quantidade_cheque'];
        $quantidadeChequesProprietario = $this->ProprietarioSerasa->quantidadeChequesSemFundo($codigo_proprietario, $usuarioPesquisadorAutomatico['Usuario']['apelido'], 'proprietario');

        if ($quantidadeChequesProprietario === false) {
            return false;
        }

        return ($quantidadeChequesProprietario <= $quantidadeChequesConfigurada);
    }

    function validaQuantidadeChequesTelechequeProprietario($codigoFicha) {
        $this->Ficha = & ClassRegistry::init('Ficha');

        $codigo_proprietario = $this->Ficha->buscaCodigoProprietario($codigoFicha);

        $resposta = $this->find('first', array(
            'conditions' => array('codigo_produto' => '1'),
            'fields' => array('quantidade_cheque')
                )
        );

        $usuario = & ClassRegistry::init('Usuario');
        $proprietarioModel = & ClassRegistry::init('Proprietario');

        $usuarioPesquisadorAutomatico = $usuario->findByApelido('pesquisador_automatico');
        $proprietario = $proprietarioModel->findByCodigo($codigo_proprietario);

        $responseTelecheque = $this->getHttpSocket()->post(URL_INFORMACOES . '/bcb/telecheque/consulta-informacoes/resumo/1', array(
            'codigoUsuario' => $usuarioPesquisadorAutomatico['Usuario']['codigo'],
            'codigo' => $codigo_proprietario,
            'codigoDocumento' => $proprietario['Proprietario']['codigo_documento'],
            'consultaProfissional' => 0
                ));

        $quantidadeTelechequeConfigurada = $resposta['PesquisaConfiguracao']['quantidade_cheque'];

        $quantidadeChequesTelecheque = $this->Ficha->buscaQuantidadeTelechequeProprietario($responseTelecheque, $codigo_proprietario);

        if ($quantidadeChequesTelecheque === false) {
            return false;
        }

        return ($quantidadeChequesTelecheque <= $quantidadeTelechequeConfigurada);
    }

    function validaQuantidadeChequesTelechequeProfissional($codigoFicha) {
        $this->Ficha = & ClassRegistry::init('Ficha');

        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($codigoFicha);

        $resposta = $this->find('first', array(
            'conditions' => array('codigo_produto' => '1'),
            'fields' => array('quantidade_cheque')
                )
        );

        $quantidadeTelechequeConfigurada = $resposta['PesquisaConfiguracao']['quantidade_cheque'];

        $usuario = & ClassRegistry::init('Usuario');
        $profissionalModel = & ClassRegistry::init('Profissional');

        $usuarioPesquisadorAutomatico = $usuario->findByApelido('pesquisador_automatico');
        $profissional = $profissionalModel->findByCodigo($codigo_profissional);

        $responseTelecheque = $this->getHttpSocket()->post(URL_INFORMACOES . '/bcb/telecheque/consulta-informacoes/resumo/1', array(
            'codigoUsuario' => $usuarioPesquisadorAutomatico['Usuario']['codigo'],
            'codigo' => $codigo_profissional,
            'codigoDocumento' => $profissional['Profissional']['codigo_documento'],
            'consultaProfissional' => 1
                ));

        $quantidadeChequesTelecheque = $this->Ficha->buscaQuantidadeTelechequeProfissional($responseTelecheque, $codigo_profissional);

        if ($quantidadeChequesTelecheque === false) {
            return false;
        }

        return ($quantidadeChequesTelecheque <= $quantidadeTelechequeConfigurada);
    }

    function validaStatusUltimaFicha($codigo_produto, $codigo_ficha, $codigo_profissional) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->ProfissionalLog = & ClassRegistry::init('ProfissionalLog');

        $statusEsperado = $this->find('first', array(
            'conditions' => array(
                'codigo_produto' => $codigo_produto
            ),
            'fields' => array(
                'codigo_status_anterior'
            )
                ));
        
        $statusEsperado = $statusEsperado['PesquisaConfiguracao']['codigo_status_anterior'];

        if ($statusEsperado == 0) {
            return true;
        }

        $ultimaFicha = $this->ProfissionalLog->buscaStatusPenultimaFicha($codigo_produto, $codigo_ficha, $codigo_profissional);

        unset($this->Ficha);
        unset($this->ProfissionalLog);

        if (empty($ultimaFicha)) {
            return false;
        }

        if ($ultimaFicha == 0) {
            return false;
        }

        if ($statusEsperado == $ultimaFicha) {
            return true;
        } else {
            return false;
        }
        return true;//new
    }



    function validaStatusUltimaFichaProprietario($codigo_produto, $codigo_ficha, $documento_proprietario) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->ProfissionalLog = & ClassRegistry::init('ProfissionalLog');

        $status_divergente = 2;
        $status_insuficiente = 3;

        $ultimaFicha = $this->ProfissionalLog->buscaStatusPenultimaFichaPorDocumento($codigo_produto, $codigo_ficha, $documento_proprietario);

        unset($this->Ficha);
        unset($this->ProprietarioLog);

        if (empty($ultimaFicha)) {
            return true;
        }

        if(($ultimaFicha != $status_divergente) and ($ultimaFicha != $status_insuficiente)){
           return true;
        } else {
           return false;
        }
        return true;//new
    }

    function validaProfissionalNegativado($codigo_profissional) {

        if (empty($codigo_profissional))
            return FALSE;

        $this->ProfissionalNegativacao = & ClassRegistry::init('ProfissionalNegativacao');

        $validado = $this->ProfissionalNegativacao->find('count', array('conditions' => array('codigo_profissional' => $codigo_profissional)));

        return $validado == 0;
    }

    function validaCNHVencida($codigo_profissional) {
        if (empty($codigo_profissional))
            return FALSE;

        $this->Profissional = & ClassRegistry::init('Profissional');

        $resultado = $this->Profissional->find('first', array('fields' => 'cnh_vencimento',
            'conditions' => array('codigo' => $codigo_profissional)));

        if (empty($resultado['Profissional']['cnh_vencimento']))
            return TRUE;

        $data_vencimento = strtotime('+30 day', Comum::dateToTimestamp($resultado['Profissional']['cnh_vencimento']));
        $data_atual = time();

        return ($data_vencimento >= $data_atual);
    }

    function verificaVeiculoOcorrencia($codigo_veiculo) {
        if (empty($codigo_veiculo))
            return 0;

        $this->VeiculoOcorrencia = & ClassRegistry::init('VeiculoOcorrencia');

        if (is_array($codigo_veiculo)) {
            foreach ($codigo_veiculo as $codigo) {
                $result = $this->verificaVeiculoOcorrencia($codigo);
                if (!$result)
                    return 0;
            }
            return 1;
        } else {
            $condicoes = array('codigo_veiculo' => $codigo_veiculo);

            $resultado = $this->VeiculoOcorrencia->find('first', array('conditions' => $condicoes,
                'fields' => 'codigo_ocorrencia',
                'order' => 'data_inclusao DESC'));

            if (empty($resultado) || $resultado['VeiculoOcorrencia']['codigo_ocorrencia'] == 9)
                return 1;
            else
                return 0;
        }
    }

    function validaVeiculoComOcorrencias($codigo_ficha) {
        if (empty($codigo_ficha))
            return FALSE;

        $this->Ficha = & ClassRegistry::init('Ficha');

        $codigos_veiculo = $this->Ficha->buscaCodigoVeiculo($codigo_ficha);

        if (is_array($codigos_veiculo)) {
            $arrStatusVeiculo = array();

            foreach ($codigos_veiculo as $codigo) {
                $arrStatusVeiculo[] = $this->verificaVeiculoOcorrencia($codigo);
            }

            $veiculosStatus = array_filter($arrStatusVeiculo, create_function('$status', 'return $status === 0;'));

            if (count($veiculosStatus))
                return false;
            else
                return true;
        } else {
            $resultado = $this->verificaVeiculoOcorrencia($codigos_veiculo);

            return $resultado;
        }
    }

    function validaHistoricoProfissional($codigo_profissional, $codigo_produto) {
        App::import('Model', 'Produto');
        $this->LogFaturamentoTeleconsult = & ClassRegistry::init('LogFaturamentoTeleconsult');

        $configurado = $this->find('first', array('conditions' => array('codigo_produto' => $codigo_produto),
            'fields' => array('historico_quantidade_viagem', 'historico_quantidade_meses')
                ));

        $quantidade_configurada = $configurado['PesquisaConfiguracao']['historico_quantidade_viagem'];
        $quantidade_meses = $configurado['PesquisaConfiguracao']['historico_quantidade_meses'];

        $quantidade_meses_formatada_para_busca = "-" . $quantidade_meses . " month";

        $data_corte = date("Y-m-d 00:00:00", strtotime($quantidade_meses_formatada_para_busca, strTotime(date('Y-m-d'))));
        $usuario_que_consulta_monitora = 1;
        $condicoes = array(
            'LogFaturamentoTeleconsult.codigo_tipo_operacao' => array(1),
            'LogFaturamentoTeleconsult.codigo_profissional'  => $codigo_profissional,
            'LogFaturamentoTeleconsult.data_inclusao > ?'    => $data_corte,            
            'not' => array('LogFaturamentoTeleconsult.codigo_usuario_inclusao' => $usuario_que_consulta_monitora));
        if($codigo_produto != Produto::SCORECARD)
            array_push($condicoes, array('LogFaturamentoTeleconsult.codigo_produto' => $codigo_produto));            

        $encontrado = $this->LogFaturamentoTeleconsult->find('count', array('conditions' => $condicoes));        
        return ($quantidade_configurada <= $encontrado);
    }
	
    function validaHistoricoProfissionalRenovacaoAuto($codigo_profissional, $codigo_produto, $codigo_ficha){
        $this->LogFaturamentoTeleconsult = & ClassRegistry::init('LogFaturamentoTeleconsult');
        $configurado = $this->find('first', array(
            'conditions' => array(
                'codigo_produto' => $codigo_produto),
                'fields' => array(
                     'historico_quantidade_viagem_ren_atu'
                    ,'historico_quantidade_meses_ren_atu'
                )
            )
        );

        $quantidade_configurada_ren_atu		  = $configurado['PesquisaConfiguracao']['historico_quantidade_viagem_ren_atu'];
        $quantidade_meses_ren_atu			  = $configurado['PesquisaConfiguracao']['historico_quantidade_meses_ren_atu'];
        $qtde_meses_format_para_busca_ren_atu = "-" . $quantidade_meses_ren_atu . " month";
        $data_corte_ren_atu                   = date("Y-m-d 00:00:00", strtotime($qtde_meses_format_para_busca_ren_atu, strTotime(date('Y-m-d'))));

        $condicoes_ren_atu = array(
            'LogFaturamentoTeleconsult.codigo_tipo_operacao' => array(11,21,75),
            'LogFaturamentoTeleconsult.codigo_profissional'  => $codigo_profissional,            
            'LogFaturamentoTeleconsult.data_inclusao > ?'    => $data_corte_ren_atu
        );
        if($codigo_produto==Produto::SCORECARD)
            array_push($condicoes_ren_atu, '(LogFaturamentoTeleconsult.codigo_ficha_scorecard <> '.$codigo_ficha.' or LogFaturamentoTeleconsult.codigo_ficha_scorecard IS NULL)');
        else{
            array_push($condicoes_ren_atu, array('LogFaturamentoTeleconsult.codigo_ficha <>' => $codigo_ficha));
            array_push($condicoes_ren_atu, array('LogFaturamentoTeleconsult.codigo_produto'  => $codigo_produto));
        }
        $encontrado_ren_atu = $this->LogFaturamentoTeleconsult->find('count', array('conditions' => $condicoes_ren_atu));
        
        return ($quantidade_configurada_ren_atu <= $encontrado_ren_atu);
    }

    function tempo($msg) {
        if (false)
            echo $msg.' '.date('H:i:s')."\n";
    }

    function validar($codigo_ficha, $gravar = false, $profissional_tipo = null) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->Proprietario = & ClassRegistry::init('Proprietario');
        $this->ProfissionalTipo = & ClassRegistry::init('ProfissionalTipo');
        $this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');
        $this->FichaPesquisaQR = & ClassRegistry::init('FichaPesquisaQR');
        $this->Produto = & ClassRegistry::init('Produto');

        /* o Cake está se perdendo e não esta enchergando a model */
        ClassRegistry::init('FichaVeiculo');

        $condicoes = array('Ficha.codigo' => $codigo_ficha);
        
        $this->tempo('dados_ficha');
        $dados_ficha = $this->Ficha->find('first', array('fields' => array('codigo_produto', 'codigo_cliente', 'codigo_profissional_tipo'),'conditions' => $condicoes));
        $this->tempo('dados_ficha');
        
        $this->tempo('dados_ficha_pesquisa');
        $dados_ficha_pesquisa = $this->FichaPesquisa->find('first', array('fields' => 'FichaPesquisa.codigo','conditions' => array('codigo_ficha' => $codigo_ficha)));
        $this->tempo('dados_ficha_pesquisa');
        
        $codigo_cliente = $dados_ficha['Ficha']['codigo_cliente'];
        $codigo_produto = $dados_ficha['Ficha']['codigo_produto'];
        
        $this->tempo('nome_produto');
        $nome_produto = $this->Produto->find('first', array('fields' => 'descricao', 'conditions' => array('codigo' => $codigo_produto)));
        $this->tempo('nome_produto');
        
        $nome_produto = $nome_produto['Produto']['descricao'];
        $codigo_categoria = $dados_ficha['Ficha']['codigo_profissional_tipo'];

        if ($profissional_tipo && $codigo_categoria != $profissional_tipo) {
            return false;
        }
        
        $this->tempo('categoria');
        $categoria = $this->ProfissionalTipo->findByCodigo($codigo_categoria);
        $this->tempo('categoria');
        
        $nome_categoria = $categoria['ProfissionalTipo']['descricao'];
        $codigo_ficha_pesquisa = $dados_ficha_pesquisa['FichaPesquisa']['codigo'];
        
        $this->tempo('configuracao');
        $configuracao = $this->find('first', array('conditions' => array('codigo_produto' => $codigo_produto)));
        $this->tempo('configuracao');
        
        $this->tempo('codigo_profissional');
        $codigo_profissional = $this->Ficha->buscaCodigoProfissional($codigo_ficha);
        $this->tempo('codigo_profissional');
        
        $this->tempo('codigo_proprietario');
        $codigo_proprietario = $this->Ficha->buscaCodigoProprietario($codigo_ficha);
        $this->tempo('codigo_proprietario');

        $this->tempo('codigo_veiculo');
        $codigo_veiculo = $this->Ficha->buscaCodigoVeiculo($codigo_ficha);
        $this->tempo('codigo_veiculo');
        
        $this->tempo('proprietario_pessoa_fisica');
        $proprietario_pessoa_fisica = $this->Proprietario->verficarPessoaFisicaJuridica($codigo_proprietario);
        $this->tempo('proprietario_pessoa_fisica');
        
        $status_profissional_cheque             = null;
        $status_proprietario_montante           = null;
        $status_profissional_montante           = null;
        $status_historico_profissional          = null;
        $status_proprietario_cheque             = null;
        $status_ficha_anterior                  = null;
        $status_ficha_anterior_proprietario     = null;
        $status_profissional_negativado         = null;
        $status_cnh_vencida                     = null;
        $status_veiculo_ocorrencias             = null;
        $status_carreta_ocorrencias             = null;
        $status_historico_profissional_ren_atu  = null;

        /* Retirada a verificação no SERASA e TELECHEQUE: Lei proibe a verificação dessas informações
        if ($codigo_produto == 1) {
            if ($configuracao['PesquisaConfiguracao']['quantidade_cheque'] <> 0) {
                $this->tempo('status_profissional_cheque_telecheque');
                $status_profissional_cheque = $this->validaQuantidadeChequesTelechequeProfissional($codigo_ficha);
                $this->tempo('status_profissional_cheque_telecheque');
                
                if ($proprietario_pessoa_fisica) {
                    $this->tempo('status_proprietario_cheque');
                    $status_proprietario_cheque = $this->validaQuantidadeChequesTelechequeProprietario($codigo_ficha);
                    $this->tempo('status_proprietario_cheque');
                }

                if ($gravar) {
                    if ($status_proprietario_cheque && $status_profissional_cheque) {
                        $this->tempo('grava_aprovacao_cheque');
                        $this->FichaPesquisaQR->gravaAprovacaoCheque($codigo_ficha_pesquisa);
                        $this->tempo('grava_aprovacao_cheque');
                    }
                }
            }
        } else {
            if ($configuracao['PesquisaConfiguracao']['quantidade_cheque'] <> 0) {
                $this->tempo('status_profissional_cheque_serasa');
                $status_profissional_cheque = $this->validaQuantidadeChequesSerasaProfissional($codigo_ficha);
                $this->tempo('status_profissional_cheque_serasa');
                if ($proprietario_pessoa_fisica) {
                    $this->tempo('status_proprietario_cheque');
                    $status_proprietario_cheque = $this->validaQuantidadeChequesSerasaProprietario($codigo_ficha);
                    $this->tempo('status_proprietario_cheque');
                }

                if ($gravar) {
                    if ($status_proprietario_cheque && $status_profissional_cheque) {
                        $this->tempo('grava_aprovacao_cheque');
                        $this->FichaPesquisaQR->gravaAprovacaoCheque($codigo_ficha_pesquisa);
                        $this->tempo('grava_aprovacao_cheque');
                    }
                }
            }

            if ($configuracao['PesquisaConfiguracao']['valor_serasa'] > 0) {
                $this->tempo('status_profissional_montante');
                $status_profissional_montante = $this->validaMontanteSerasaProfissional($codigo_ficha);
                $this->tempo('status_profissional_montante');
                if ($proprietario_pessoa_fisica) {
                    $this->tempo('status_proprietario_montante');
                    $status_proprietario_montante = $this->validaMontanteSerasaProprietario($codigo_ficha);
                    $this->tempo('status_proprietario_montante');
                }

                if ($gravar) {                    
                    if ($status_proprietario_montante && $status_profissional_montante) {
                        $this->tempo('grava_aprovacao_cheque');
                        $this->FichaPesquisaQR->gravaAprovacaoCheque($codigo_ficha_pesquisa);
                        $this->tempo('grava_aprovacao_cheque');
                    }
                }
            }
        }*/

        if ($configuracao['PesquisaConfiguracao']['codigo_status_anterior'] == 1) {
            $this->tempo('status_ficha_anterior');
            $status_ficha_anterior = $this->validaStatusUltimaFicha($codigo_produto, $codigo_ficha, $codigo_profissional);
            $this->tempo('status_ficha_anterior');
        }

        if ($configuracao['PesquisaConfiguracao']['codigo_status_anterior_proprietario'] == 1) {
            if($proprietario_pessoa_fisica){
                $this->tempo('documento_proprietario');
                $documento_proprietario = $this->Proprietario->buscaDocumento($codigo_proprietario);
                $this->tempo('documento_proprietario');
                $this->tempo('status_ficha_anterior_proprietario');
                $status_ficha_anterior_proprietario = $this->validaStatusUltimaFichaProprietario($codigo_produto, $codigo_ficha, $documento_proprietario);
                $this->tempo('status_ficha_anterior_proprietario');
            }
        }

        if ($configuracao['PesquisaConfiguracao']['verificar_profissional_negativado'] == 1) {
            $this->tempo('status_profissional_negativado');
            $status_profissional_negativado = $this->validaProfissionalNegativado($codigo_profissional);
            $this->tempo('status_profissional_negativado');

            if ($gravar) {
                if ($status_profissional_negativado) {
                    $this->tempo('grava_aprovacao_profissional');
                    $this->FichaPesquisaQR->gravaAprovacaoProfissional($codigo_ficha_pesquisa);
                    $this->tempo('grava_aprovacao_profissional');
                }
            }
        }

        // Não verifica CNH para carreteiro (Fabiane Simões)
        if ($codigo_categoria <> 1 &&$configuracao['PesquisaConfiguracao']['verificar_validade_cnh'] == 1) {
            $this->tempo('status_cnh_vencida');
            $status_cnh_vencida = $this->validaCNHVencida($codigo_profissional);
            $this->tempo('status_cnh_vencida');
            if ($gravar) {
                if ($status_cnh_vencida) {
                    $this->tempo('grava_aprovacao_cnh');
                    $this->FichaPesquisaQR->gravaAprovacaoCnh($codigo_ficha_pesquisa);
                    $this->tempo('grava_aprovacao_cnh');
                }
            }
        }

        if ($configuracao['PesquisaConfiguracao']['verificar_veiculo_ocorrencia'] == 1) {
            $this->tempo('status_veiculo_ocorrencias');
            $status_veiculo_ocorrencias = $this->validaVeiculoComOcorrencias($codigo_ficha);
            $this->tempo('status_veiculo_ocorrencias');
            if ($gravar) {
                if ($status_veiculo_ocorrencias) {
                    $this->tempo('grava_aprovacao_ocorrencia_veiculo');
                    $this->FichaPesquisaQR->gravaAprovacaoOcorrenciaVeiculo($codigo_ficha_pesquisa);
                    $this->tempo('grava_aprovacao_ocorrencia_veiculo');
                }
            }
        }

        if ($configuracao['PesquisaConfiguracao']['historico_quantidade_viagem'] > 0) {
            if ($codigo_categoria == 1) { // carreteiro
                
                $rule_1 = $this->validaHistoricoProfissional($codigo_profissional, $codigo_produto);
                $rule_2 = $this->validaHistoricoProfissionalRenovacaoAuto($codigo_profissional, $codigo_produto, $codigo_ficha);

                if($rule_1) // do not aproved in first rule of professional
                {
                    $this->tempo('status_historico_profissional_ren_atu');
                    $status_historico_profissional_ren_atu = true;
                    $this->tempo('status_historico_profissional_ren_atu');

                    $this->tempo('status_historico_profissional');
                    $status_historico_profissional = true;
                    $this->tempo('status_historico_profissional');
                }
                else
                {
                    if($rule_2) // approved 2 rules based in second rule
                    {
                        $this->tempo('status_historico_profissional_ren_atu');
                        $status_historico_profissional_ren_atu = true;
                        $this->tempo('status_historico_profissional_ren_atu');

                        $this->tempo('status_historico_profissional');
                        $status_historico_profissional = true;
                        $this->tempo('status_historico_profissional');
                    }
                    else // reproved 2 rules based in second rule
                    {
                        $this->tempo('status_historico_profissional_ren_atu');
                        $status_historico_profissional_ren_atu = false;
                        $this->tempo('status_historico_profissional_ren_atu');

                        $this->tempo('status_historico_profissional');
                        $status_historico_profissional = false;
                        $this->tempo('status_historico_profissional');
                    }
                }
                
            }
        }

        $arrStatusFicha = compact('status_ficha_anterior', 'status_profissional_negativado', 'status_cnh_vencida',
        'status_veiculo_ocorrencias', 'status_profissional_cheque', 'status_proprietario_cheque',
        'status_historico_profissional_ren_atu','status_proprietario_montante', 'status_profissional_montante',
        'status_historico_profissional','nome_categoria', 'nome_produto', 'status_ficha_anterior_proprietario');

        unset($this->Ficha);
        unset($this->Proprietario);
        unset($this->ProfissionalTipo);
        unset($this->FichaPesquisa);
        unset($this->FichaPesquisaQR);
        unset($this->Produto);
        return $arrStatusFicha;
    }

    function atualizaStatusFichaAdequadoAoRisco($codigo_ficha) {
        $this->Ficha = & ClassRegistry::init('Ficha');
        $this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');
        $this->Usuario = & ClassRegistry::init('Usuario');
        $this->LogAtendimento = & ClassRegistry::init('LogAtendimento');
        $novo_status = 1;
        $usuarioPesquisadorAutomatico = $this->Usuario->findByApelido('pesquisador_automatico');

        try {
            $ficha = $this->Ficha->read(null, $codigo_ficha);
            if ($ficha['Ficha']['codigo_status'] != $novo_status) {
                $this->query('begin transaction');
                $this->Ficha->set(array(
                    'codigo_status' => $novo_status,
                    'codigo_usuario_alteracao_manual' => $usuarioPesquisadorAutomatico['Usuario']['codigo'],
                    'codigo_usuario_alteracao' => $usuarioPesquisadorAutomatico['Usuario']['codigo'],
                    'data_alteracao' => date('Y-m-d H:i:s')
                ));
                if (!$this->Ficha->save())
                    throw new Exception();
                if (!$this->FichaPesquisa->finaliza($codigo_ficha))
                    throw new Exception();
                $ficha = $this->Ficha->read();
                if (!$this->LogAtendimento->gravaLogAtendimentoAprovacaoProfissional($ficha))
                    throw new Exception();
                $this->commit();
            }
            return $ficha;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function validaVeiculoComOcorrenciasScorecard($codigo_ficha) {
        if (empty($codigo_ficha))
            return FALSE;

        $this->FichaScorecard = & ClassRegistry::init('FichaScorecard');

        $codigos_veiculo = $this->FichaScorecard->buscaCodigoVeiculo($codigo_ficha);

        if (is_array($codigos_veiculo)) {
            $arrStatusVeiculo = array();

            foreach ($codigos_veiculo as $codigo) {
                $arrStatusVeiculo[] = $this->verificaVeiculoOcorrencia($codigo);
            }

            $veiculosStatus = array_filter($arrStatusVeiculo, create_function('$status', 'return $status === 0;'));

            if (count($veiculosStatus))
                return false;
            else
                return true;
        } else {
            $resultado = $this->verificaVeiculoOcorrencia($codigos_veiculo);

            return $resultado;
        }
    }

}