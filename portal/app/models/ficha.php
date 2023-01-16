<?php
class Ficha extends AppModel {
    public $name = 'Ficha';
    public $tableSchema = 'informacoes';
    public $databaseTable = 'dbTeleconsult';
    public $useTable = 'ficha';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $validate = array(
        'codigo_cliente' => 'notEmpty',
        'codigo_produto' => 'notEmpty',
        'codigo_profissional_log' => 'notEmpty',
        'codigo_profissional_tipo' => 'notEmpty',
        'codigo_status' => 'notEmpty',
        'data_validade' => 'notEmpty',
    );

    public $uses = Array('Cliente','ClienteProduto', 'Profissional', 'Proprietario', 'FichaRetorno', 'Proprietario', 'Veiculo', 'FichaQuestaoResposta', 'Status', 'ProfissionalLog', 'LogAtendimento', 'LogFaturamentoTeleconsult', 'ClienteProdutoServico', 'FichaPesquisa', 'FichaPesquisaArtCriminal', 'FichaPesquisaQR', 'EnderecoCidade', 'FichaProfContatoLog', 'FichaProfEnderecoLog', 'FichaPropContatoLog', 'FichaPropEnderecoLog', 'TipoOperacao', 'Usuario');

    const ATIVO = 1;
    const INATIVO = 0;
    const EXCLUIDO = 2;
    const EDITADO = 3;

    const ATUALIZACAO_FICHA = 2;
    const SOLICITACAO_CLIENTE = 3;
    const RENOVACAO_AUTOMATICA = 4;
    const PREMIO_MINIMO = 5;
    const TAXA_BANCARIA = 6;
    const TAXA_CORRETORA = 7;

    public function bindLazyProprietario() {
        $this->bindModel(array(
            'belongsTo' => array(
                'ProprietarioLog' => array(
                    'class' => 'ProprietarioLog',
                    'foreignKey' => 'codigo_proprietario_log'
            ))));
    }

    public function unbindProprietario() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'ProprietarioLog'
            )
        ));
    }
    
    public function bindLazyStatus() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Status' => array(
                    'class' => 'Status',
                    'foreignKey' => 'codigo_status'
            ))));
    }

    public function unbindStatus() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Status'
            )
        ));
    }

    public function bindLazyUsuarioSolicitacao() {
        $this->bindModel(array(
            'belongsTo' => array(
                'UsuarioSolicitacao' => array(
                    'className' => 'Usuario',
                    'foreignKey' => 'codigo_usuario_solicitacao'
            ))));
    }

    public function unbindUsuarioSolicitacao() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'StatusSolicitacao'
            )
        ));
    }

    public function unbindAll() {
        $tiposDeJoins = array(
            'hasMany',
            'hasOne',
            'belongsTo',
            'hasAndBelongsToMany'
        );

        foreach ($tiposDeJoins as $join) {
            $models = array_keys($this->$join);
            $this->unbindModel(array($join => $models));
        }
    }

    public function bindLazyProfissional() {
        $this->bindModel(array(
            'belongsTo' => array(
                'ProfissionalLog' => array(
                    'class' => 'ProfissionalLog',
                    'foreignKey' => 'codigo_profissional_log'
            ))));
    }

    public function unbindProfissional() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'ProfissionalLog'
            )
        ));
    }

    public function bindLazyVeiculo() {
        $this->bindModel(array(
            'hasAndBelongsToMany' => array(
                'VeiculoLog' => array(
                    'class' => 'Veiculo',
                    'foreignKey' => 'codigo_ficha',
                    'joinTable' => 'ficha_veiculo',
                    'associationForeignKey' => 'codigo_veiculo_log',
            ))));
    }

    public function unbindVeiculo() {
        $this->unbindModel(array(
            'hasAndBelongsToMany' => array(
                'VeiculoLog'
            )
        ));
    }

    public function bindLazyCliente() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Cliente' => array(
                    'class' => 'Cliente',
                    'foreignKey' => 'codigo_cliente'
            ))));
    }

    public function bindLazyProduto() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Produto' => array(
                    'class' => 'Produto',
                    'foreignKey' => 'codigo_produto'
            ))));
    }

    public function bindLazyFichaPesquisa() {
        $this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');

        $this->FichaPesquisa->bindModel(array(
            'belongsTo' => array(
                'Ficha' => array(
                    'class' => 'Ficha',
                    'foreignKey' => 'codigo'
            ))));

        $this->bindModel(array(
            'hasMany' => array(
                'FichaPesquisa' => array(
                    'class' => 'FichaPesquisa',
                    'foreignKey' => 'codigo_ficha'
            ))));
    }

    public function unbindFichaPesquisa() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'FichaPesquisa'
            )
        ));
    }

    public function bindLazy() {
        $this->bindLazyVeiculo();
        $this->bindLazyCliente();
        $this->bindLazyproduto();
        $this->bindLazyProfissional();
    }

    public function bindFichaRetorno(){
        $this->bindModel(array(
            'hasOne' => array(
                'FichaRetorno' => array(
                    'className' => 'FichaRetorno',
                    'foreignKey' => 'codigo_ficha'
            ))));
    }

    public function buscaCodigoProprietario($codigo_ficha) {
        $this->bindLazyProprietario();
        $ficha = $this->find('first', array('fields' => array('ProprietarioLog.codigo_proprietario'), 'conditions' => array('Ficha.codigo' => $codigo_ficha)));
        $this->unbindProprietario();
        return $ficha['ProprietarioLog']['codigo_proprietario'];
    }

    public function buscaCodigoCliente($codigo_ficha) {
        $ficha = $this->find('first', array('fields' => array('Ficha.codigo_cliente'), 'conditions' => array('Ficha.codigo' => $codigo_ficha)));
        return $ficha['Ficha']['codigo_cliente'];
    }

    public function buscaQuantidadeTelechequeProprietario($responseTelecheque, $codigo_proprietario) {
        if (empty($codigo_proprietario))
            return false;

        $responseTelecheque = json_decode($responseTelecheque);

        if (!in_array($responseTelecheque, array('finalizado', 'erro'))) {
            return false;
        }
        if ($responseTelecheque == 'erro') {
            return false;
        }
        $this->bindLazyProprietario();

        $this->ProprietarioTelecheque = & ClassRegistry::init('ProprietarioTelecheque');

        $condicoes = array('ProprietarioTelecheque.codigo_proprietario' => $codigo_proprietario);

        $resultado = $this->ProprietarioTelecheque->find('first', array('conditions' => $condicoes,
            'order' => 'data_inclusao DESC',
            'fields' => array('quantidade_ocorrencias')));

        $this->unbindProprietario();

        return $resultado['ProprietarioTelecheque']['quantidade_ocorrencias'];
    }

    public function buscaCodigoProfissional($codigo_ficha) {
        $this->bindLazyProfissional();
        $ficha = $this->find('first', array('fields' => array('ProfissionalLog.codigo_profissional'), 'conditions' => array('Ficha.codigo' => $codigo_ficha)));
        $this->unbindProfissional();
        return $ficha['ProfissionalLog']['codigo_profissional'];
    }

    public function buscaQuantidadeTelechequeProfissional($responseTelecheque, $codigo_profissional) {
        if (empty($codigo_profissional))
            return false;

        $responseTelecheque = json_decode($responseTelecheque);

        if (!in_array($responseTelecheque, array('finalizado', 'erro'))) {
            return false;
        }
        if ($responseTelecheque == 'erro') {
            return false;
        }
        $this->bindLazyProfissional();

        $this->ProfissionalTelecheque = & ClassRegistry::init('ProfissionalTelecheque');

        $condicoes = array('ProfissionalTelecheque.codigo_profissional' => $codigo_profissional);

        $resultado = $this->ProfissionalTelecheque->find('first', array('conditions' => $condicoes,
            'order' => 'data_inclusao DESC',
            'fields' => array('quantidade_ocorrencias')));

        $this->unbindProfissional();

        return $resultado['ProfissionalTelecheque']['quantidade_ocorrencias'];
    }

    public function buscaLiberacao($codigo_ficha) {
        $fichaLiberacaoModel = & ClassRegistry::init('FichaLiberacao');
        $fichaLiberacao = $fichaLiberacaoModel->findByCodigoFicha($codigo_ficha);
        return $fichaLiberacao;
    }

    public function buscaNumeroLiberacao($ficha) {
        $logFaturamentoTeleconsultModel = & ClassRegistry::init('LogFaturamentoTeleconsult');
        $logFaturamento = $logFaturamentoTeleconsultModel->findByCodigoFicha($ficha['Ficha']['codigo']);
        return $logFaturamento['LogFaturamentoTeleconsult']['numero_liberacao'];
    }

    public function liberaFicha($ficha, $listaEmails = null) {
        $fichaLiberacaoModel = & ClassRegistry::init('FichaLiberacao');
        $fichaLiberacaoItemModel = & ClassRegistry::init('FichaLiberacaoItem');
        $fichaLiberacao = $this->buscaLiberacao($ficha['Ficha']['codigo']);

        $codigo_status_liberacao = !empty($listaEmails) ? 6 : 4;

        if (empty($fichaLiberacao)) {
            $numeroLiberacao = $this->buscaNumeroLiberacao($ficha);

            $email_cliente = !empty($listaEmails) ?
                    strtoupper(implode(', ', array_map(create_function('$retorno', 'return $retorno["FichaRetorno"]["descricao"];'), $listaEmails))) : null;
            $fichaLiberacao = array(
                'FichaLiberacao' => array(
                    'codigo_ficha' => $ficha['Ficha']['codigo'],
                    'codigo_status_profissional' => 1, //@coisadotadeu
                    'codigo_status_liberacao' => $codigo_status_liberacao,
                    'codigo_usuario_inclusao' => $ficha['Ficha']['codigo_usuario_alteracao'], // @FIXME?
                    'email_cliente' => $email_cliente,
                    'numero_liberacao' => $numeroLiberacao
                )
            );
        } else {
            $fichaLiberacao['FichaLiberacao']['codigo_status_liberacao'] = $codigo_status_liberacao;
        }

        $fichaLiberacaoModel->save($fichaLiberacao);
        $fichaLiberacao = $this->buscaLiberacao($ficha['Ficha']['codigo']);

        if (!empty($listaEmails)) {
            $dados = array(
                'FichaLiberacaoItem' => array(
                    'codigo_ficha_liberacao' => $fichaLiberacao['FichaLiberacao']['codigo'],
                    'descricao' => 'OK',
                    'codigo_usuario_inclusao' => $fichaLiberacao['FichaLiberacao']['codigo_usuario_inclusao']
                )
            );
            $fichaLiberacaoItemModel->inserir($dados);
        }
    }

    function buscaCodigoVeiculo($codigo_ficha) {
        if (empty($codigo_ficha))
            return false;

        $this->bindLazyVeiculo();
        
        $condicoes = array('Ficha.codigo' => $codigo_ficha);

        $arr_veiculo_log = $this->find('first', array('conditions' => $condicoes));

        $codigos_veiculo = array();

        if (count($arr_veiculo_log['VeiculoLog']) > 0) {

            foreach ($arr_veiculo_log['VeiculoLog'] as $veiculoLog) {
                $codigos_veiculo[] = $veiculoLog['codigo_veiculo'];
            }
        }

        $this->unbindVeiculo();

        return $codigos_veiculo;
    }

    public function buscarDataEncerramentoFicha($codigo) {
        $this->bindLazyFichaPesquisa();
        $data = $this->find('first', array('conditions' => array('Ficha.codigo' => $codigo)));
        $this->unbindFichaPesquisa();

        return $data['FichaPesquisa'][0]['data_alteracao'];
    }
    
    function converteFiltroEmCondition($data) {
        $conditions = array();
        if (isset($data['codigo']) && (!empty($data['codigo']) || $data['codigo'] === 0))  {
            $conditions['Ficha.codigo'] = $data['codigo'];
        }
        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = preg_replace('/\D/', '', $data['codigo_cliente']);
        }
        if (isset($data['razao_social']) && !empty($data['razao_social'])) {
            $conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        }
        if (isset($data['codigo_documento']) && !empty($data['codigo_documento'])) {
            $conditions['ProfissionalLog.codigo_documento'] = preg_replace('/\D/', '', $data['codigo_documento']);
        }
        if (isset($data['codigo_produto']) && !empty($data['codigo_produto'])) {
            $conditions['Produto.codigo'] = $data['codigo_produto'];
        }
        if (isset($data['data_inclusao_inicio']) && !empty($data['data_inclusao_inicio'])) {
            $conditions['Ficha.data_inclusao >'] = AppModel::dateToDbDate($data['data_inclusao_inicio']) . ' 00:00:00.0';
        }
        if (isset($data['data_inclusao_fim']) && !empty($data['data_inclusao_fim'])) {
            $conditions['Ficha.data_inclusao <'] = AppModel::dateToDbDate($data['data_inclusao_fim']) . ' 23:59:59.997';
        }
        if (isset($data['data_validade_inicio']) && !empty($data['data_validade_inicio'])) {
            $conditions['Ficha.data_validade >'] = AppModel::dateToDbDate($data['data_validade_inicio']) . ' 00:00:00.0';
        }
        if (isset($data['data_validade_fim']) && !empty($data['data_validade_fim'])) {
            $conditions['Ficha.data_validade <'] = AppModel::dateToDbDate($data['data_validade_fim']) . ' 23:59:59.997';
        }
        if (isset($data['produto_de']) && !empty($data['produto_de'])) {
            $conditions['Ficha.codigo_produto'] = $data['produto_de'];
        }
        // if (isset($data['produto_para']) && !empty($data['produto_para'])) {
        //     $conditions['Ficha.codigo_produto'] = $data['produto_para'];
        // }
        return $conditions;
    }

    public function obterListaFicha($conditions) {
        $pode_reabrir = 'exists
                        (
                            select TOP 1
                                data_alteracao
                            from 
                                dbteleconsult.informacoes.ficha_pesquisa
                            where 
                                codigo_ficha = Ficha.codigo
                                and codigo_status_profissional in (2, 3)
                                and datediff(HOUR, data_alteracao, getdate()) <= 48
                        )';

        $conditions[] = $pode_reabrir;
        $conditions[] = array('Ficha.codigo_status !=' => 8);

        $options = array(
            'fields' => array(
                'Ficha.codigo AS [codigo_ficha]',
                'UPPER(ProfissionalLog.nome) AS [nome]',
                'RHHealth.publico.ufn_formata_cpf(ProfissionalLog.codigo_documento) AS [cpf]',
                'CONVERT(VARCHAR(10),Ficha.data_inclusao,103) + " " + CONVERT(VARCHAR(10),Ficha.data_inclusao,108) AS [data_cadastro]',
                'Produto.descricao AS [produto]',
                'ProfissionalTipo.descricao AS [tipo_profissional]',
                'Usuario.apelido AS [usuario]'
            ),
            'joins' => array(
                array(
                    'table' => 'dbbuonny.portal.usuario',
                    'alias' => 'Usuario',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_usuario_inclusao = Usuario.codigo'
                ),
                array(
                    'table' => 'dbbuonny.publico.profissional_log',
                    'alias' => 'ProfissionalLog',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_profissional_log = ProfissionalLog.codigo'
                ),
                array(
                    'table' => 'dbbuonny.vendas.produto',
                    'alias' => 'Produto',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_produto = Produto.codigo'
                ),
                array(
                    'table' => 'dbbuonny.publico.profissional_tipo',
                    'alias' => 'ProfissionalTipo',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_profissional_tipo = ProfissionalTipo.codigo'
                ),
            ),
            'conditions' => $conditions,
                //'limit' => 50
        );

        return $this->find('all', $options);
    }

    public function alterarStatusManualmente($ficha, $novo_status=null) {
        $this->ProfissionalLog =& ClassRegistry::init('ProfissionalLog');
        $this->Profissional =& ClassRegistry::init('Profissional');

        $profissionalLogDaFicha = $ficha['Ficha']['codigo_profissional_log'];
        $codigo_cliente = $ficha['Ficha']['codigo_cliente'];
        $codigo_produto = $ficha['Ficha']['codigo_produto'];
        
        $profissionalDaFicha = $this->ProfissionalLog->find('first', array(
            'conditions' => array(
                'ProfissionalLog.codigo' => $profissionalLogDaFicha
            )
        ));

        $codigo_profissional = $profissionalDaFicha['ProfissionalLog']['codigo_profissional'];

        $fichaEmAnalise = $this->Profissional->possuiFichaEmAnalise($codigo_profissional, $codigo_cliente, $codigo_produto, true);

        if ($fichaEmAnalise) {
            if ($fichaEmAnalise == $ficha['Ficha']['codigo']) {
                $this->invalidate('codigo_status', 'Profissional está em pesquisa');
                return false;
            }
        }

        $ficha['Ficha']['codigo_status'] = $novo_status;
        return $this->save($ficha);
    }

    public function alterarUsuarioSolicitacao($ficha, $novo_codigo_usuario_solicitacao) {
        $ficha['Ficha']['codigo_usuario_solicitacao'] = $novo_codigo_usuario_solicitacao;
        return $this->save($ficha);
    }

    public function alterarNomeProfissional($ficha, $novo_nome) {
        $ficha['ProfissionalLog']['nome'] = $novo_nome;
        return $this->ProfissionalLog->save($ficha);
    }

    /**
     * Duplica uma Ficha
     * 
     * @param int $codigo
     * 
     * @return int|boolean
     */
    public function duplicar($codigo, $params=null) {
        try {
            if (empty($codigo)) {
                throw new Exception();
            }

            $model_data = $this->find('first', array(
                'conditions' => array(
                    "{$this->name}.codigo" => $codigo
                    )));
            
            $model_data[$this->name] = array_merge($model_data[$this->name], (array) $params);

            $result = $this->incluir($model_data);
            
            if ($result) {
                return $this->id;
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            return false;
        }
    }


    public function iniciarModelsParaReabrirFicha() {

        $this->FichaPesquisa =& ClassRegistry::init('FichaPesquisa');
        $this->Profissional =& ClassRegistry::init('Profissional');
        $this->ProfissionalLog =& ClassRegistry::init('ProfissionalLog');
        $this->ProprietarioLog =& ClassRegistry::init('ProprietarioLog');
        $this->FichaPesquisaArtCriminal =& ClassRegistry::init('FichaPesquisaArtCriminal');
        $this->FichaPesquisaQR =& ClassRegistry::init('FichaPesquisaQR');
        $this->FichaVeiculo =& ClassRegistry::init('FichaVeiculo');
        $this->FichaRetorno =& ClassRegistry::init('FichaRetorno');
        $this->FichaCt =& ClassRegistry::init('FichaCt');
        $this->FichaLiberacao =& ClassRegistry::init('FichaLiberacao');

        $this->FichaProfContatoLog =& ClassRegistry::init('FichaProfContatoLog');
        $this->FichaProfEnderecoLog =& ClassRegistry::init('FichaProfEnderecoLog');
        $this->FichaPropContatoLog =& ClassRegistry::init('FichaPropContatoLog');
        $this->FichaPropEnderecoLog =& ClassRegistry::init('FichaPropEnderecoLog');
        $this->ClienteProduto =& ClassRegistry::init('ClienteProduto');
        $this->ClienteProdutoServico =& ClassRegistry::init('ClienteProdutoServico');

        $this->LogFaturamentoTeleconsult =& ClassRegistry::init('LogFaturamentoTeleconsult');
        $this->LogAtendimento =& ClassRegistry::init('LogAtendimento');
        
        $this->PesquisaConfiguracao =& ClassRegistry::init('PesquisaConfiguracao');
        
        $this->Status =& ClassRegistry::init('Status');
    }
    
    /**
     * Reabri uma ficha duplicando todas suas dependencias.
     * 
     * @param int $codigo_ficha
     * 
     * @return array Em caso de erro, retorna um array contendo 'error', com a string do erro.
     * @return int Em caso de sucesso, retorna apenas o codigo da nova ficha.
     */
    public function reabrirFicha($codigo_ficha, $alterar_produto = false, $produto_para = false) {
        $com_cobranca = $alterar_produto;
        
        $this->iniciarModelsParaReabrirFicha();
        
        $dados_ficha = $this->findByCodigo($codigo_ficha);

        $codigo_profissional_log = $dados_ficha['Ficha']['codigo_profissional_log'];
        $codigo_proprietario_log = $dados_ficha['Ficha']['codigo_proprietario_log'];

        $codigo_cliente = $dados_ficha['Ficha']['codigo_cliente'];
        $codigo_produto = $dados_ficha['Ficha']['codigo_produto'];
        $dados_profissional = $this->ProfissionalLog->obterProfissionalPeloCodigoProfissionalLog($codigo_profissional_log);
        $codigo_profissional = $dados_profissional['Profissional']['codigo'];
        $data_validade_ficha = $dados_ficha['Ficha']['data_validade'];

        $ultimaFichaPesquisa = $this->FichaPesquisa->obterUltimaFichaPesquisa($codigo_ficha);
        $codigo_ficha_pesquisa = $ultimaFichaPesquisa['FichaPesquisa']['codigo'];
        
        $this->query('begin tran');
        try {
            
            $fichaEstaNoTempoPermitidoParaReabertura = $this->validarPeriodoReaberturaDeFicha($ultimaFichaPesquisa['FichaPesquisa']['data_alteracao']);
            if (!$fichaEstaNoTempoPermitidoParaReabertura && !$alterar_produto) {
                throw new Exception('Não foi possível reabrir a ficha, pois já excedeu o prazo de 48h.');
            }

            $novo_codigo_profissional_log = $this->ProfissionalLog->duplicar($codigo_profissional_log);
            $novo_codigo_proprietario_log = $this->ProprietarioLog->duplicar($codigo_proprietario_log);

            $possuiFichaEmAnalise = $this->Profissional->possuiFichaEmAnalise($codigo_profissional, $codigo_cliente, $codigo_produto);
            if ($possuiFichaEmAnalise && !$alterar_produto) {
                throw new Exception('Não foi possível reabrir a ficha, pois o profissional já encontra-se em análise.');
            }

            $ultimaFichaPossuiPerfilAdequadoAoRisco = $this->Profissional->ultimaFichaPossuiPerfilAdequadoAoRisco($codigo_profissional, $codigo_cliente, $codigo_produto);
            if($ultimaFichaPossuiPerfilAdequadoAoRisco && !$alterar_produto) {
                throw new Exception('Não foi possível reabrir a ficha, pois o profissional já está adequado ao risco.');
            }

            if (!$novo_codigo_profissional_log) {
                throw new Exception('Não foi possível reabrir a ficha. (#1)');
            }

            if (!$novo_codigo_proprietario_log && !$alterar_produto) {
                throw new Exception('Não foi possível reabrir a ficha. (#2)');
            }

            $cliente_produto = $this->ClienteProduto->getClienteProdutoByCodigoClienteEProduto($codigo_cliente, $codigo_produto);
            $cliente_produto_servico = $this->ClienteProdutoServico->getByCodigoClienteProdutoEServico($cliente_produto['ClienteProduto']['codigo'], 1);
            $meses_validade_ficha = $cliente_produto_servico['ClienteProdutoServico']['validade'];

            $codigo_ficha_nova  = $this->duplicar($codigo_ficha, array(
                'codigo_profissional_log' => $novo_codigo_profissional_log,
                'codigo_proprietario_log' => $novo_codigo_proprietario_log,
                'codigo_status' => Status::EM_PESQUISA,
                'data_alteracao' => null,
                'data_validade' => date('Y-m-d H:i:s', strtotime("+$meses_validade_ficha month")),
                'codigo_usuario_alteracao' => null
            ));

            if (false === $codigo_ficha_nova) {
                throw new Exception('Não foi possível reabrir a ficha. (#3)');
            }

            if (!$this->FichaVeiculo->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#4)');
            }
            if (!$this->FichaProfContatoLog->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#5)');
            }
            if (!$this->FichaProfEnderecoLog->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#6)');
            }
            if (!$this->FichaPropContatoLog->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#7)');
            }
            if (!$this->FichaPropEnderecoLog->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#8)');
            }
            if (!$this->FichaRetorno->duplicar($codigo_ficha, $codigo_ficha_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#9)');
            }
            if (!$this->LogAtendimento->gravaLogAtendimentoDuplicarFicha($dados_ficha, $com_cobranca)) {
                throw new Exception('Não foi possível reabrir a ficha. (#11)');
            }

            $log_faturamento = $this->LogFaturamentoTeleconsult->find('first', array(
                'conditions' => array(
                    'codigo_ficha' => $codigo_ficha,
                )
            ));
            $codigo_log_faturamento = $log_faturamento['LogFaturamentoTeleconsult']['codigo'];

            $novo_codigo_faturamento = $this->LogFaturamentoTeleconsult->duplicar($codigo_log_faturamento, array(
                'codigo_ficha' => $codigo_ficha_nova
            ), $com_cobranca);

            $codigo_liberacao_nova = $this->FichaLiberacao->duplicar($codigo_ficha, array(
                'codigo_ficha' => $codigo_ficha_nova,
                'numero_liberacao' => $novo_codigo_faturamento
            ));

            if ($codigo_liberacao_nova === false) {
                throw new Exception('Não foi possível reabrir a ficha. (#12)');
            }

            $configuracoesNovaFichaPesquisa = array(
                'codigo_ficha' => $codigo_ficha_nova,
                'codigo_tipo_pesquisa' => 1,
                'data_alteracao' => null,
                'codigo_usuario_alteracao' => null,
                'codigo_usuario_atual' => null
            );

            if ($alterar_produto) {
                $configuracoesNovaFichaPesquisa['codigo_status_profissional'] = 8;
            }

            $codigo_ficha_pesquisa_nova = $this->FichaPesquisa->duplicar($codigo_ficha, $configuracoesNovaFichaPesquisa);

            if (!$codigo_ficha_pesquisa_nova) {
                throw new Exception('Não foi possível reabrir a ficha. (#13)');
            }

            if (!$this->FichaPesquisaArtCriminal->duplicar($codigo_ficha_pesquisa, $codigo_ficha_pesquisa_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#14)');
            }

            if (!$this->FichaPesquisaQR->duplicar($codigo_ficha_pesquisa, $codigo_ficha_pesquisa_nova)) {
                throw new Exception('Não foi possível reabrir a ficha. (#15)');
            }

            $this->query('commit');
        } catch(Exception $e) {
            $this->query('rollback');
            
            return array(
                'error' => $e->getMessage()
            );
        }
        return $codigo_ficha_nova;
    }


    public function validarPeriodoReaberturaDeFicha($data_alteracao) {
        $timestamp_data_alteracao = Comum::dateToTimestamp($data_alteracao);
        $data_limite = strtotime('-2 day', time());
        return $timestamp_data_alteracao >= $data_limite;
    }

    public function profissionalTipoDoCliente($codigo_documento, $codigo_cliente = FALSE) {
        if (!empty($codigo_documento)) {
            $this->bindLazyProfissional();
            $order = array('Ficha.codigo DESC');
            $conditions = array( 'ProfissionalLog.codigo_documento' => $codigo_documento );
            if( $codigo_cliente )
                $conditions['Ficha.codigo_cliente'] = $codigo_cliente;
            $ficha = $this->find('first', compact('conditions', 'order'));
            if ($ficha) {
                return $ficha['Ficha']['codigo_profissional_tipo'];
            }
        }
    }

    public function obterUltimaFichaProfissional($codigo_cliente, $codigo_profissional, $codigo_produto = null) {
        $this->bindLazyProfissional();
        $this->bindLazyCliente();
        
        $options = array(
            'conditions' => array(
                'ProfissionalLog.codigo_profissional' => $codigo_profissional,
                'Ficha.ativo NOT' => array(2,3)
            ),
            'order' => 'Ficha.data_inclusao DESC'
        );

        if ($codigo_produto) {
            $options['conditions']['Ficha.codigo_produto'] = $codigo_produto;
        }
        
        if (!empty($codigo_cliente)) {
            $options['conditions']['Cliente.codigo'] = $codigo_cliente;
            $options['conditions']['Ficha.codigo_profissional_tipo <>'] = 1;
        } else {
            $options['conditions']['Ficha.codigo_profissional_tipo'] = 1;
        }
        
        $ultimaFicha = $this->find('first', $options);

        $this->unbindProfissional();
        return $ultimaFicha;
    }
    
    public function obterDataValidadeDaUltimaFichaDoProfissional($codigo_cliente, $codigo_produto, $codigo_profissional) {
        $ultimaFicha = $this->obterUltimaFichaProfissional($codigo_cliente, $codigo_profissional, $codigo_produto);
        return $ultimaFicha[$this->name]['data_validade'];
    }

    public function obterFichasParaAlterarProduto($type, $options = array()) {
        $this->Behaviors->attach('LinkModel');
        $this->unlinkAll();
        $this->linkModel('FichaPesquisa', null, 'Ficha.codigo = FichaPesquisa.codigo_ficha', 'INNER');
        $this->linkModel('ProfissionalLog', null, 'Ficha.codigo_profissional_log = ProfissionalLog.codigo', 'LEFT');
        $this->linkModel('ProfissionalTipo', null, 'Ficha.codigo_profissional_tipo = ProfissionalTipo.codigo', 'LEFT');

        $this->bindModel(array('belongsTo' => array('ProfissionalTipo' => array('class' => 'ProfissionalTipo','foreignKey' => 'codigo_profissional_tipo'))));

        $subquery = array();

        if ($type != 'count') {
            $options['fields'] = array('max(FichaPesquisa.codigo) as ficha_pesquisa_codigo','ProfissionalTipo.descricao','ProfissionalLog.nome','ProfissionalLog.codigo_documento','Ficha.codigo_profissional_tipo','Ficha.codigo','Ficha.codigo_cliente','Ficha.codigo_produto','Ficha.data_validade','Ficha.data_inclusao');//'ProfissionalLog.codigo', 'ProfissionalLog.nome',,'ProfissionalTipo.descricao','Produto.descricao'
            $options['group'] = array('Ficha.codigo','Ficha.codigo_cliente','Ficha.codigo_produto','ProfissionalTipo.descricao','ProfissionalLog.nome','ProfissionalLog.codigo_documento','Ficha.codigo_profissional_tipo','Ficha.data_validade','Ficha.data_inclusao');//'ProfissionalLog.codigo','ProfissionalLog.nome',,'ProfissionalTipo.descricao','Produto.descricao'
        } else {
            $options['fields'] = array('COUNT(distinct Ficha.codigo) as [count]');
        }

        // Sobreescreve filtros que vem do converteFiltrosEmConditions
        if (isset($options['conditions']['Cliente.codigo'])) {
            $options['conditions']['Ficha.codigo_cliente'] = $options['conditions']['Cliente.codigo'];
            unset($options['conditions']['Cliente.codigo']);
        }
        
        // Regras para obter fichas validas para alteracao de produto
        $conditions = array(
            'Ficha.ativo' => 1,
            'Ficha.codigo_profissional_tipo <>' => 1,
            'Ficha.data_validade >=' => date('Ymd'),
            'FichaPesquisa.codigo_status_profissional <>' => 8, // somente profissionais que não estão me pesquisa
        );

        $options['recursive'] = 0;
        $options['conditions'] = isset($options['conditions']) ? $options['conditions'] : array();

        if (!isset($options['conditions']['Ficha.codigo_cliente'])) {
            return false;
        }

        $options['conditions'] = array_merge($options['conditions'], $conditions);
        $options['order'] = 'Ficha.codigo DESC';
        
        //debug($this->find($type, $options));

        return $this->find($type, $options);


    }

    protected function subselectCodigoDocumentoEmPesquisa($codigo_cliente,$codigo_produto) {
        $dbo = $this->getDataSource();
        
        if($codigo_produto == 1)
            $codigo_produto = 2;
        else
            $codigo_produto = 1;
        
        $subquery = $dbo->buildStatement(
            array(
                'fields' => array('InnerProfissionalLog.codigo_documento'),
                'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
                'alias' => 'InnerFicha',
                'joins' => array(
                    array(
                        'table' => "{$this->FichaPesquisa->databaseTable}.{$this->FichaPesquisa->tableSchema}.{$this->FichaPesquisa->useTable}",
                        'alias' => 'InnerFichaPesquisa',
                        'type' => 'left',
                        'conditions' => 'InnerFicha.codigo = InnerFichaPesquisa.codigo_ficha and InnerFichaPesquisa.codigo_tipo_pesquisa in (1,4,5,6)'
                    ),
                    array(
                        'table' => "{$this->ProfissionalLog->databaseTable}.{$this->ProfissionalLog->tableSchema}.{$this->ProfissionalLog->useTable}",
                        'alias' => 'InnerProfissionalLog',
                        'type' => 'left',
                        'conditions' => 'InnerFicha.codigo_profissional_log = InnerProfissionalLog.codigo'
                    ),
                ),
                'conditions' => array(
                    'InnerFicha.codigo_cliente' => $codigo_cliente,
                    'InnerFicha.codigo_produto' => $codigo_produto,
                ),
                'group' => null,
                'order' => null,
                'limit' => null,
            ), $this
        );

        return 'ProfissionalLog.codigo_documento NOT IN (' . $subquery . ') ';
    }

    public function migrarProdutoCliente($data) {
        
        $this->ClienteProduto = & ClassRegistry::init('ClienteProduto');
        $this->ClienteProdutoServico = & ClassRegistry::init('ClienteProdutoServico');
        $this->FichaPesquisa = & ClassRegistry::init('FichaPesquisa');
        $this->ProfissionalTipo = & ClassRegistry::init('ProfissionalTipo');
        $this->LogFaturamentoTeleconsult = & ClassRegistry::init('LogFaturamentoTeleconsult');

        try {

            if (!isset($data['Cliente']['codigo']) || empty($data['Cliente']['codigo'])
                || !isset($data['Cliente']['Produto']) || empty($data['Cliente']['Produto'])
                || !isset($data['Cliente']['Servico']) || empty($data['Cliente']['Servico'])) {
                throw new Exception();
            }

            $dados['ClienteProduto'] = $data['Cliente']['Produto'];
            $dados['ClienteProduto']['codigo_cliente'] = $data['Cliente']['codigo'];
            $dados['ClienteProduto']['codigo_motivo_bloqueio'] = 1;

            $this->ClienteProduto->incluir($dados); // Incluir Novo Produto Para o Cliente

            if (!isset($dados['ClienteProduto']['codigo_produto_antigo']) || empty($dados['ClienteProduto']['codigo_produto_antigo'])) {
                throw new Exception();
            }

            // ATUALIZAR MOTIVO BLOQUEIO = 3 (SOLICITACAO_CLIENTE)
            $cliente_produto_antigo = $this->ClienteProduto->getClienteProdutoByCodigoClienteEProduto($dados['ClienteProduto']['codigo_cliente'],$dados['ClienteProduto']['codigo_produto_antigo']);

            $cliente_produto_antigo['ClienteProduto']['codigo_motivo_bloqueio'] = Ficha::SOLICITACAO_CLIENTE;
            unset($cliente_produto_antigo['ClienteProduto']['codigo_produto']);
            unset($cliente_produto_antigo['Produto']);
            unset($cliente_produto_antigo['MotivoBloqueio']);

            $testresult = $this->ClienteProduto->atualizar($cliente_produto_antigo,true);

            // INCLUIR NOVOS SERVICOS
            $cliente_produto = $this->ClienteProduto->getClienteProdutoByCodigoClienteEProduto($dados['ClienteProduto']['codigo_cliente'],$dados['ClienteProduto']['codigo_produto']);

            //LISTAR TODOS OS TIPOS DE PROFISSIONAIS
            $profissionaltipo = $this->ProfissionalTipo->find('all');
 
            // LER CAMPOS TEMPO PESQUISA E VALIDADE A SEREM COPIADOS NO CLIENTE PRODUTO SERVIÇO
            $dadosprodutosservicos = $this->ClienteProdutoServico->produtosEServicos($data['Cliente']['codigo']);

            foreach ($dadosprodutosservicos as $key) {
                $tempopesquisa[$key['Servico']['codigo']] = $key['ClienteProdutoServico']['tempo_pesquisa'];
                $validade[$key['Servico']['codigo']] = $key['ClienteProdutoServico']['validade'];
            }

            foreach ($data['Cliente']['Servico'] as $key) {
                $dados['ClienteProdutoServico']['codigo_cliente_produto'] = $cliente_produto['ClienteProduto']['codigo'];
                $dados['ClienteProdutoServico']['codigo_servico'] = $key['codigo_servico'];
                $dados['ClienteProdutoServico']['valor'] = $key['valor'];
                if(isset($tempopesquisa[$key['codigo_servico']]) && isset($validade[$key['codigo_servico']])) {
                    $dados['ClienteProdutoServico']['tempo_pesquisa'] = $tempopesquisa[$key['codigo_servico']]; //COPIAR DO SERVICO JA EXISTENTE
                    $dados['ClienteProdutoServico']['validade'] = $validade[$key['codigo_servico']]; //COPIAR DO SERVICO JA EXISTENTE
                }
                $dados['ClienteProdutoServico']['codigo_cliente_pagador'] = $data['Cliente']['codigo'];

                $logfaturamentovalores[$key['codigo_servico']] = $key['valor'];

                if ($key['codigo_servico'] == Ficha::RENOVACAO_AUTOMATICA || $key['codigo_servico'] == Ficha::PREMIO_MINIMO || $key['codigo_servico'] == Ficha::TAXA_BANCARIA || $key['codigo_servico'] == Ficha::TAXA_CORRETORA ) {
                    $dados['ClienteProdutoServico']['codigo_profissional_tipo'] = null;
                    $this->ClienteProdutoServico->incluir($dados);
                } else {
                    foreach ($profissionaltipo as $tipo) {
                        $dados['ClienteProdutoServico']['codigo_profissional_tipo'] = $tipo['ProfissionalTipo']['codigo'];
                        $this->ClienteProdutoServico->incluir($dados);
                    }
                }
            }

            if (!isset($data['Cliente']['Ficha']) || empty($data['Cliente']['Ficha'])) {
                return true;
            } else {
                foreach ($data['Cliente']['Ficha'] as $key) {
                    $dados['Ficha']['codigo'] = $key['codigo'];
                    $params = array(
                        'codigo_produto' => $dados['ClienteProduto']['codigo_produto'],
                        );

                    $testduplicaficha = $this->duplicar($dados['Ficha']['codigo'],$params);

                    $paramspesquisa = array(
                        'codigo_ficha' => $testduplicaficha,
                        );

                    $this->FichaPesquisa->duplicar($dados['Ficha']['codigo'],$paramspesquisa);
                    
                    // DUPLICACAO LOG FATURAMENTO
                    $logfaturamento = $this->LogFaturamentoTeleconsult->find('first',array('conditions'=>array('codigo_ficha' => $dados['Ficha']['codigo'])));
                    
                    if (isset($logfaturamento)) {

                        $valor = isset($logfaturamentovalores[Ficha::ATUALIZACAO_FICHA])?$logfaturamentovalores[Ficha::ATUALIZACAO_FICHA]:0;
                        $valor_premio_minimo = isset($logfaturamentovalores[Ficha::PREMIO_MINIMO])?$logfaturamentovalores[Ficha::PREMIO_MINIMO]:0;
                        $valor_taxa_bancaria = isset($logfaturamentovalores[Ficha::TAXA_BANCARIA])?$logfaturamentovalores[Ficha::TAXA_BANCARIA]:0;

                        $this->LogFaturamentoTeleconsult->duplicar($logfaturamento['LogFaturamentoTeleconsult']['codigo'],
                            array(
                                'codigo_ficha' => $testduplicaficha,
                                'valor' => $valor,
                                'valor_premio_minimo' => $valor_premio_minimo,
                                'valor_taxa_bancaria' => $valor_taxa_bancaria,
                                'codigo_produto' => $dados['ClienteProduto']['codigo_produto']
                                ), true);
                    }

                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }

    }

    public function converteFiltroEmConditions($filtros = null) {
        $conditions = array();

        if (isset($filtros['Ficha']['codigo_cliente']) && !empty($filtros['Ficha']['codigo_cliente']))
            $conditions['Ficha.codigo_cliente'] = $filtros['Ficha']['codigo_cliente'];       

        return $conditions;
    }

    public function paginate($filtros, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {        
        $conditions = $filtros;

        if( isset($extra['extra']['fichas_pendentes']) ){

            $joins = $extra['extra']['joins'];
            $group = $fields;

            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));
        }else{
            $group = array('ProfissionalTipo.descricao','ProfissionalLog.nome','ProfissionalLog.codigo_documento','Ficha.codigo_profissional_tipo','Ficha.codigo','Ficha.codigo_cliente','Ficha.codigo_produto','Ficha.data_validade','Ficha.data_inclusao');
            
            $fields = array_merge($group, array('max(FichaPesquisa.codigo) as ficha_pesquisa_codigo'));
            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group'));
        }

    }

    function paginateCount($conditions = null, $recursive = 0, $extra = array()) {        
        if( isset($extra['extra']['fichas_pendentes']) ){
            $joins = $extra['extra']['joins'];
            return $this->find('count', compact('conditions', 'recursive', 'joins'));            
        }else{
            return $this->find('count', compact('conditions', 'recursive'));
        }        
    }

    public  function listas_fichas_pendentes($filtros){

        $FichaPesquisa      = ClassRegistry::init("FichaPesquisa");
        $Cliente            = ClassRegistry::init("Cliente");
        $Seguradora         = ClassRegistry::init("Seguradora");
        $Produto            = ClassRegistry::init("Produto");
        $FichaVeiculo       = ClassRegistry::init("FichaVeiculo");
        $VeiculoLogCavalo   = ClassRegistry::init("VeiculoLog");
        $ProfissionalLog    = ClassRegistry::init("ProfissionalLog");
        $ProfissionalTipo   = ClassRegistry::init("ProfissionalTipo");
        $Usuario            = ClassRegistry::init("Usuario");
        $ProfissionalLogRg  = ClassRegistry::init("EnderecoEstado");
        $UsuarioAtual       = ClassRegistry::init("Usuario");
        $Status             = ClassRegistry::init("Status");    

        $conditions = array();
        
        if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])){
            $conditions['Cliente.codigo']   = $filtros['codigo_cliente']; 
        } 

        if(isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])){
           $conditions['Seguradora.codigo']  = $filtros['codigo_seguradora']; 
        } 


        if(isset($filtros['produto_codigo']) && !empty($filtros['produto_codigo'])){
           $conditions['Produto.codigo']  = $filtros['produto_codigo']; 
        }


        if(isset($filtros['codigo_ficha']) && !empty($filtros['codigo_ficha'])){
           $conditions['Ficha.codigo'] = $filtros['codigo_ficha'];            
        }

        if(isset($filtros['codigo_documento']) && !empty($filtros['codigo_documento'])){
            $conditions['ProfissionalLog.codigo_documento']  = str_replace(array('.','-'), '', $filtros['codigo_documento']);            
        } 

        $conditions['ProfissionalTipo.codigo'] = array(1,2);

        if(isset($filtros['codigo_tipo_profissional']) && $filtros['codigo_tipo_profissional'] != null ){
            //$conditions = array_merge($conditions,array('ProfissionalTipo.codigo' => $filtros['codigo_tipo_profissional']));
            //$conditions['FichaPesquisa.codigo_tipo_pesquisa'] = 1;
            $conditions['ProfissionalTipo.codigo'] = $filtros['codigo_tipo_profissional'];
        } 

        //$conditions['FichaPesquisa.codigo_tipo_pesquisa'] = 1;
        
        
        
        $fields= array(
            'Cliente.codigo                   AS codigo_cliente', 
            'Cliente.razao_social             AS razao_social',
            'Seguradora.codigo                AS codigo_seguradora' ,
            'Seguradora.nome                  AS nome_seguradora' ,
            'Ficha.codigo                     AS codigo_ficha',  
            'codigo_tipo_pesquisa             AS codigo_tipo_pesquisa',
            'ProfissionalTipo.descricao       AS profissional_descricao',
            'ProfissionalTipo.codigo          AS codigo_tipo_profissional', 
            'ProfissionalLog.nome             AS profissional_nome',
            'ProfissionalLog.codigo_documento AS codigo_documento',
            'Produto.codigo                   AS produto_codigo',
            'Produto.descricao                AS produto_descricao',
            '(SELECT CAST(FichaPesquisa.tempo_restante - datediff(MINUTE,FichaPesquisa.data_inclusao,
             GETDATE()) AS INT))              AS tempo_restante',
            'CONVERT(VARCHAR(20), FichaPesquisa.data_inclusao, 20) AS  data_inclusao',
            'CONVERT(VARCHAR(20), Ficha.data_validade, 20) AS data_validade',
            'FichaPesquisa.observacao         AS observacao' ,
            'FichaPesquisa.codigo             AS codigo_pesquisa' 
        );
        
        $extra = array(
            'joins'  => array(
                array(
                    'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_cliente= Cliente.codigo'
                ),
                array(
                    'table' => "{$Seguradora->databaseTable}.{$Seguradora->tableSchema}.{$Seguradora->useTable}",
                    'alias' => 'Seguradora',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo_seguradora = Seguradora.codigo'
                ),

                array(
                    'table' => "{$FichaPesquisa->databaseTable}.{$FichaPesquisa->tableSchema}.{$FichaPesquisa->useTable}",
                    'alias' => 'FichaPesquisa',
                    'type' => 'INNER',
                    'conditions' => 'FichaPesquisa.codigo_ficha = Ficha.codigo'
                ),
                array(
                    'table' =>"{$ProfissionalLog->databaseTable}.{$ProfissionalLog->tableSchema}.{$ProfissionalLog->useTable}",
                    'alias' => 'ProfissionalLog',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_profissional_log = ProfissionalLog.codigo'
                ),
                array(
                    'table' => "{$ProfissionalLogRg->databaseTable}.{$ProfissionalLogRg->tableSchema}.{$ProfissionalLogRg->useTable}",
                    'alias' => 'ProfissionalLogRg',
                    'type' => 'INNER',
                    'conditions' => 'ProfissionalLog.codigo_estado_rg = ProfissionalLogRg.codigo'
                ), 
              
                array(
                    'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                    'alias' => 'Produto',
                    'type' => 'INNER',
                    'conditions' => 'Ficha.codigo_produto = Produto.codigo'
                ),            
                array(
                    'table' => "{$ProfissionalTipo->databaseTable}.{$ProfissionalTipo->tableSchema}.{$ProfissionalTipo->useTable}",
                    'alias' => 'ProfissionalTipo',
                    'type' => 'INNER',
                    'conditions' => 'ProfissionalTipo.codigo = Ficha.codigo_profissional_tipo'
                ),
            ),
            'fichas_pendentes' => true,
        );

        $limit     = 50;
        $order     = array('codigo_ficha DESC');                     
        
        return compact('conditions','fields','extra','limit','order');       
    } 

   function listar_conditions($filtros){
       if(isset($filtros['codigo_tipo_profissional'])){
           $conditions[] = array('ProfissionalTipo.codigo' => $filtros['codigo_tipo_profissional']);
       }
    
       $return = $this->Ficha->find('all',compact('conditions'));
            return $return;
   }

   public function carregarProfissionalPorCodigoFicha($codigo_ficha){
        $this->bindModel(array(
            'belongsTo' => array(
                'ProfissionalLog' => array( 
                    'foreignKey'  => 'codigo_profissional_log',                     
                    'type'        => 'inner',
                ),
                'Profissional'    => array(
                    'foreignKey'  => false,
                    'conditions'  => 'ProfissionalLog.codigo_documento = Profissional.codigo_documento',
                    'type'        => 'inner',
                ),                
            ),
        ));

        $conditions = array('Ficha.codigo'=>$codigo_ficha);
        $fields     = array('Profissional.*');
        
        return $this->find('first',compact('conditions','fields'));
    }
    
    public function findClienteProdutoServico($dataFicha) {
        ClassRegistry::init('Servico');
        $clienteProdutoServico = ClassRegistry::init('ClienteProdutoServico');
        $clienteProdutoServico->bindLazy();
        return $clienteProdutoServico->find('first', array(
            'fields' => array(
                'ClienteProdutoServico.codigo_cliente_pagador',
                'ClienteProdutoServico.validade',
                'ClienteProdutoServico.tempo_pesquisa'
            ),
            'conditions' => array(
                'ClienteProduto.codigo_cliente' => $dataFicha['codigo_cliente'],
                'ClienteProdutoServico.codigo_profissional_tipo' => @$dataFicha['codigo_profissional_tipo'],
                'ClienteProdutoServico.codigo_servico' => Servico::CADASTRO_DE_FICHA,
                'ClienteProduto.codigo_produto' => $dataFicha['codigo_produto']
            )
        ));
    }
  
    public function dataUltimaAtualizacaoTelefoneNaFicha($cpf = null){
        $ProfissionalLog    = ClassRegistry::init("ProfissionalLog");
        $dataUltimaAtualizacao = $this->find('all',array(
                'fields' => 'MAX(Ficha.data_inclusao) as dataAtualizacao_Telefone',
                'joins' => array(
                    array(
                        'table' => "{$ProfissionalLog->databaseTable}.{$ProfissionalLog->tableSchema}.{$ProfissionalLog->useTable}",
                        "alias" => "ProfissionalLog",
                        "type" => "INNER",
                        "conditions" => array(
                            "ProfissionalLog.codigo = Ficha.codigo_profissional_log",
                        )
                    ),
                ),
                'conditions' => array('ProfissionalLog.codigo_documento'=>$cpf)
            )
        );
        if(empty($dataUltimaAtualizacao[0][0]['dataAtualizacao_Telefone'])){
           $pegarData = '';
        } else {
           $pegarData = date('d/m/Y', strtotime($dataUltimaAtualizacao[0][0]['dataAtualizacao_Telefone']));
        }
        return $pegarData;
    }


    public function carregaUltimaFichaProfissional($codigo_cliente, $codigo_profissional, $vencida = FALSE, $codigo_produto = FALSE ) {
        $this->bindModel(array('belongsTo' => array(
            'ProfissionalLog' => array('foreignKey' => 'codigo_profissional_log')))
        );
        $conditions = array(
            'Ficha.codigo_cliente' => $codigo_cliente,
            'ProfissionalLog.codigo_profissional' => $codigo_profissional,
            'NOT' => array('Ficha.ativo' => array(2,3)),
        );
        if ($vencida)
            $conditions['Ficha.data_validade <'] = date('Ymd');

        $order  = array('Ficha.codigo DESC');
        $fields = 'CASE WHEN Ficha.data_validade < GETDATE() THEN 1 ELSE 0 END AS vencida, *';

        if($codigo_produto != FALSE){
            $conditions["Ficha.codigo_produto"] = $codigo_produto;
            $fields = null;
        }

        $ultimaFicha = $this->find('first', compact('conditions', 'order', 'fields'));
        return $ultimaFicha;
    }
 
    private function preparaArrayVeiculos($veiculos, $ficha_veiculo) {
        try {
            $veiculos_retorno = Array();
            foreach ($veiculos as $key => $veiculo) {
                $dados_veiculo = Array('Veiculo'=>$veiculo);
                $dados_veiculo['FichaVeiculo'] = $ficha_veiculo[$key];
                $veiculos_retorno[] = $dados_veiculo;
            }
            return $veiculos_retorno;
        } catch(Exception $ex) {
            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    public function validarDadosGravacaoFicha($dados) {
        try {
            $this->Profissional = ClassRegistry::init('Profissional');
            $this->ClienteProduto = ClassRegistry::init('ClienteProduto');

            //validações de placas
            if (!empty($dados['Veiculo']['0']['placa']) || !empty($dados['Veiculo']['1']['placa'])) {
                $dados['Veiculo']['0']['placa'] = (isset($dados['Veiculo']['0']['placa']) ? $dados['Veiculo']['0']['placa'] : '');
                $dados['Veiculo']['1']['placa'] = (isset($dados['Veiculo']['1']['placa']) ? $dados['Veiculo']['1']['placa'] : '');
                if (preg_replace('/\W/', '', $dados['Veiculo']['0']['placa']) == preg_replace('/\W/', '', $dados['Veiculo']['1']['placa'])) {
                    throw new Exception('A placa do veiculo não pode ser a mesma da carreta.');
                }

                $dados['Veiculo']['0']['renavam'] = (isset($dados['Veiculo']['0']['renavam']) ? $dados['Veiculo']['0']['renavam'] : '');
                $dados['Veiculo']['1']['renavam'] = (isset($dados['Veiculo']['1']['renavam']) ? $dados['Veiculo']['1']['renavam'] : '');
                if (preg_replace('/\D/', '', $dados['Veiculo']['0']['renavam']) == preg_replace('/\D/', '', $dados['Veiculo']['1']['renavam'])) {
                    throw new Exception('O renavam do veiculo não pode ser o mesmo da carreta.');
                }
            }
            if (!$this->Profissional->validarDadosFicha($dados,true)) {
                $msg_erro = implode("\n",$this->Profissional->validationErrors);
                $this->invalidate('',$msg_erro);
                return false;
            }

            $qtdCliente = $this->ClienteProduto->produtoClienteAtivo($dados['Ficha']['codigo_cliente'],$dados['Ficha']['codigo_produto']);
            if ($qtdCliente<=0) throw new Exception('Cliente sem o produto especificado.');

            return true;
        } catch(Exception $ex) {

            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    private function preparaDadosGravacaoFicha(&$dados, &$clienteProdutoServico) {
        try {
            $this->Profissional = ClassRegistry::init('Profissional');
            $this->ClienteProduto = ClassRegistry::init('ClienteProduto');

            $dados['Profissional']['codigo_documento'] = preg_replace('/\D/', '', $dados['Profissional']['codigo_documento']);
            $dados['Profissional']['cnh'] = preg_replace('/\D/', '', $dados['Profissional']['cnh']);

            // Verifica se o profissional existe na base
            $conditions = array('ProfissionalLog.codigo_documento'=>$dados['Profissional']['codigo_documento']);
            $this->bindLazyProfissional();
            $profissional = $this->find('count',compact('conditions'));
            $profissionalExisteNoBanco = (empty($profissional) ? false : true) ;
            $dados['Ficha']['profissional_existe_no_banco'] = $profissionalExisteNoBanco;

            // Verifica se o produto / cliente / serviço está ativo
            $clienteProdutoServico = $this->ClienteProdutoServico->obterParametrosDoServico($dados['Ficha']['codigo_cliente'],$dados['Ficha']['codigo_produto'],($profissionalExisteNoBanco ? 2 : 1),$dados['Ficha']['codigo_profissional_tipo']);
            if (!$clienteProdutoServico) throw new Exception("Cliente inativo ou sem o serviço solicitado.");

            $dados['Ficha']['codigo_status'] = Status::EM_PESQUISA;
            $dados['Ficha']['ativo'] = self::ATIVO;
            
            //$dados['Ficha']['data_validade'] = $this->rawQuery("dateadd(m, {$clienteProdutoServico['ClienteProdutoServico']['validade']}, getdate())");
            $validade = $clienteProdutoServico['ClienteProdutoServico']['validade'];
            $dataValidade=date('d/m/Y',strtotime('+'.$validade.' month'));
            $dados['Ficha']['data_validade'] = $dataValidade;

            if ($dados['Ficha']['codigo_profissional_tipo'] != 1 && isset($this->data['Ficha']['observacao_outros'])) {
                $dados['Ficha']['observacao'] = $this->data['Ficha']['observacao_outros'];
            }

            return true;
        } catch(Exception $ex) {

            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    public function incluirFicha($dados) {
        try {
            //$msg_erro = &$this->msg_erro;
            if (!empty($dados)) {
                ClassRegistry::init('Status');
                $this->ClienteProdutoServico = ClassRegistry::init('ClienteProdutoServico');
                $this->ProfissionalLog = ClassRegistry::init('ProfissionalLog');
                $this->Profissional = ClassRegistry::init('Profissional');
                $this->Proprietario = ClassRegistry::init('Proprietario');
                $this->FichaPesquisa = ClassRegistry::init('FichaPesquisa');
                $this->Veiculo = ClassRegistry::init('Veiculo');
                $this->FichaQuestaoResposta = ClassRegistry::init('FichaQuestaoResposta');
                $this->FichaProfContatoLog = ClassRegistry::init('FichaProfContatoLog');
                $this->FichaProfEnderecoLog = ClassRegistry::init('FichaProfEnderecoLog');
                $this->FichaPropContatoLog = ClassRegistry::init('FichaPropContatoLog');
                $this->FichaPropEnderecoLog = ClassRegistry::init('FichaPropEnderecoLog');
                $this->FichaRetorno = ClassRegistry::init('FichaRetorno');
                $this->LogAtendimento = ClassRegistry::init('LogAtendimento');
                $this->LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');
                $this->FichaForense = ClassRegistry::init('FichaForense');
                $this->FichaVeiculo = ClassRegistry::init('FichaVeiculo');

                ClassRegistry::init('TipoOperacao');

                if (!$this->validarDadosGravacaoFicha($dados)) return false;

                $clienteProdutoServico = array();
                if (!$this->preparaDadosGravacaoFicha($dados,$clienteProdutoServico)) return false;

                $this->query('begin transaction');
                
                // Verifica se o profissional já está em pesquisa
                if ($ret = $this->ProfissionalLog->profissionalEmPesquisa($dados['Profissional']['codigo_documento'], $dados['Ficha']['codigo_profissional_tipo'], $dados['Ficha']['codigo_produto'], $dados['Ficha']['codigo_cliente'])) {
                    $this->rollback();
                    throw new Exception('Esta ficha não pôde ser salva porque o profissional está em pesquisa.');
                }
                
                // Grava dados do profissional (se não existir, inclui um novo)
                $funcao = (empty($dados['Profissional']['codigo']) ? 'incluir' : 'atualizar');
                $dados_retorno_profissional = $this->Profissional->$funcao($dados,true);
                if (!($dados_retorno_profissional)) {
                    $this->rollback();
                    $msg_erro = (!empty($this->Profissional->validationErrors) ? Comum::implodeRecursivo("\n",$this->Profissional->validationErrors) : 'Erro ao salvar profissional');
                    $this->invalidate('',$msg_erro);
                    return false;
                }
                if (empty($dados['Profissional']['codigo'])) $dados['Profissional']['codigo'] = $this->Profissional->id;
                $dados['Ficha']['codigo_profissional_log'] = $this->Profissional->ProfissionalLog->id;

                // Grava dados do proprietário se informado
                if (!empty($dados['Proprietario'])) {
                    if (!$this->Proprietario->validarDados($dados,true)) {
                        $this->rollback();
                        $msg_erro = Comum::implodeRecursivo("\n",$this->Proprietario->validationErrors);
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }
                    $dados['ProprietarioEndereco'] = reset($dados['ProprietarioEndereco']);

                    $dados_retorno_proprietario = $this->Proprietario->salvarProprietarioScorecard($dados);
                    if (!$dados_retorno_proprietario) {
                        $this->rollback();
                        $msg_erro = (!empty($this->Proprietario->validationErrors) ? Comum::implodeRecursivo("\n",$this->Proprietario->validationErrors) : 'Erro ao salvar Proprietario');
                        $this->invalidate('',$msg_erro);
                        return false;
                    }
                    $dados['Ficha']['codigo_proprietario_log'] = $this->Proprietario->ProprietarioLog->id;
                }

                // Valida os dados da ficha
                if (!$this->validates()) {
                    $this->rollback();
                    throw new Exception(var_export($this->validationErrors,true));
                    return false;
                }

                // Salva a Ficha
                if (!$this->save($dados)) {
                    $this->rollback();
                    $msg_erro = (!empty($this->validationErrors) ? Comum::implodeRecursivo("\n",$this->validationErrors) : '');
                    if ($msg_erro=='') $this->invalidate('','Erro ao salvar ficha');
                    return false;
                }

                $codigo_ficha = $this->id;

                // Se já existir uma ficha de pesquisa para o profissional, carrega os dados da ficha para gravação da nova ficha pesquisa
                $codigo_ultima_ficha = $this->FichaPesquisa->codigoUltimaFichaPesquisa($dados['Profissional']['codigo_documento']);
                if (!empty($codigo_ultima_ficha)) {
                    $ficha_pesquisa = Array();
                    $ficha_pesquisa['FichaPesquisa']['codigo_ficha'] = $codigo_ficha;
                    $ficha_pesquisa['FichaPesquisa']['codigo_tipo_pesquisa'] = 1;
                    $ficha_pesquisa['FichaPesquisa']['codigo_status_profissional'] = Status::EM_PESQUISA;
                    $ficha_pesquisa['FichaPesquisa']['tempo_restante'] = $clienteProdutoServico['ClienteProdutoServico']['tempo_pesquisa'];

                    if (!$this->FichaPesquisa->duplicarFichaCompleta($codigo_ultima_ficha, $ficha_pesquisa)) {
                        $this->rollback();
                        $msg_erro = (!empty($this->FichaPesquisa->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaPesquisa->validationErrors) : 'Erro ao salvar Ficha de Pesquisa');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }

                    
                } else {
                    if (!$this->FichaPesquisa->salvarDaFicha($codigo_ficha, $clienteProdutoServico['ClienteProdutoServico']['tempo_pesquisa'])) {
                        $this->rollback();
                        $msg_erro = (!empty($this->FichaPesquisa->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaPesquisa->validationErrors) : 'Erro ao salvar Ficha de Pesquisa');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }
                }

                // Valida e grava os veículos
                $veiculos = $this->preparaArrayVeiculos($dados['Veiculo'],$dados['FichaVeiculo']);
                if (!$this->Veiculo->validarDadosScorecard($veiculos)) {
                    $this->rollback();
                    $msg_erro = Comum::implodeRecursivo("\n",$this->Veiculo->validationErrors);
                    $this->invalidate('',$msg_erro);
                    return false;                    
                }
                $codigos_veiculo = Array();
                foreach ($veiculos as $key => $veiculo) {
                    $dados_veiculo_gravado = $this->Veiculo->salvarVeiculoScorecard($veiculo);
                    if (!$dados_veiculo_gravado) {
                        $this->rollback();
                        $msg_erro = (!empty($this->Veiculo->validationErrors) ? Comum::implodeRecursivo("\n",$this->Veiculo->validationErrors) : 'Erro ao salvar Veiculo');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }
                    if (empty($veiculo['Veiculo']['codigo'])) $veiculo['Veiculo']['codigo'] = $this->Veiculo->id;
                    //$this->log(var_export($veiculo,true),'ws_teleconsult');
                    $codigos_veiculo[] = $veiculo['Veiculo']['codigo'];

                    $dados_veiculo = Array(
                        'FichaVeiculo' => Array(
                            'codigo_ficha' => $codigo_ficha,
                            'codigo_veiculo_log' => $dados_veiculo_gravado['VeiculoLog'],
                            'tipo' => $key,
                            'codigo_tecnologia' => $veiculo['FichaVeiculo']['codigo_tecnologia']
                        )
                    );

                    if (!$this->FichaVeiculo->incluir($dados_veiculo)) {
                        $this->rollback();
                        $msg_erro = (!empty($this->FichaVeiculo->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaVeiculo->validationErrors) : 'Erro ao salvar Ficha de Veiculo');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }                    
                }

                // Salva as respostas às questões da Ficha Complementar
                if (!$this->FichaQuestaoResposta->salvarTodosFicha($dados['FichaQuestaoResposta'],$codigo_ficha)) {
                    $this->rollback();
                    throw new Exception(var_export($this->FichaQuestaoResposta->validationErrors,true));
                    $msg_erro = (!empty($this->FichaQuestaoResposta->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaQuestaoResposta->validationErrors) : 'Erro ao salvar Resposta');
                    $this->invalidate('',$msg_erro);
                    return false;                        
                }

                // Grava o Log de Contatos do Profissional
                if (isset($dados_retorno_profissional['ProfissionalContatoLog'])) {
                    if (!$this->FichaProfContatoLog->salvarTodosFicha($dados_retorno_profissional['ProfissionalContatoLog'],$codigo_ficha)) {
                        $this->rollback();
                        $msg_erro = (!empty($this->FichaProfContatoLog->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaProfContatoLog->validationErrors) : 'Erro ao salvar Log de Ficha de Contato de Profissional');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }
                }
                
                // Grava o Log de Endereço do Profissional
                if (isset($dados_retorno_profissional['ProfissionalEnderecoLog'])) {
                    if (!$this->FichaProfEnderecoLog->salvarDaFicha($dados_retorno_profissional['ProfissionalEnderecoLog'],$codigo_ficha)) {
                        $this->rollback();
                        $msg_erro = (!empty($this->FichaProfEnderecoLog->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaProfEnderecoLog->validationErrors) : 'Erro ao salvar Log de Ficha de Endereço de Profissional');
                        $this->invalidate('',$msg_erro);
                        return false;                        
                    }

                }

                if (isset($dados['Proprietario']) && is_array($dados['Proprietario']) && count($dados['Proprietario'])>0) {
                    // Grava o Log de Contatos do Proprietário
                    if (isset($dados_retorno_proprietario['ProprietarioContatoLog']) && count($dados_retorno_proprietario['ProprietarioContatoLog'])>0) {
                        if (!$this->FichaPropContatoLog->salvarTodosFicha($dados_retorno_proprietario['ProprietarioContatoLog'],$codigo_ficha)) {
                            $this->rollback();
                            $msg_erro = (!empty($this->FichaPropContatoLog->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaPropContatoLog->validationErrors) : 'Erro ao salvar Log de Ficha de Contato de Proprietario');
                            $this->invalidate('',$msg_erro);
                            return false;                        
                        }                        
                    }

                    // Grava o Log de Endereço do Proprietário
                    if (isset($dados_retorno_proprietario['ProprietarioEnderecoLog'])  && count($dados_retorno_proprietario['ProprietarioEnderecoLog'])>0) {
                        if (!$this->FichaPropEnderecoLog->salvarDaFicha($dados_retorno_proprietario['ProprietarioEnderecoLog'],$codigo_ficha)) {
                            $this->rollback();
                            $msg_erro = (!empty($this->FichaPropEnderecoLog->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaPropEnderecoLog->validationErrors) : 'Erro ao salvar Log de Ficha de Endereço de Proprietario');
                            $this->invalidate('',$msg_erro);
                            return false;                        
                        }

                    }

                }

                // Inclui os dados do usuário logado na Ficha Retorno
                $ret = $this->FichaRetorno->salvarRetornoUsuario($_SESSION['Auth']['Usuario']['codigo'], $codigo_ficha);
                if (!$ret) {
                    $this->rollback();
                    $msg_erro = (!empty($this->FichaRetorno->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaRetorno->validationErrors) : 'Erro ao salvar Ficha Retorno');
                    $this->invalidate('',$msg_erro);
                    return false;                        
                }

                // Recupera o tipo da Operação para os logs de atendimento e faturamento
                $tipo_operacao = $this->LogFaturamentoTeleconsult->obterTipoOperacaoLogFaturamento($dados['Ficha']['codigo_cliente'],$dados['Profissional']['codigo'],$dados['Ficha']['codigo_produto']);
                 if (!$tipo_operacao) {
                    $this->rollback();
                    $msg_erro = (!empty($this->LogFaturamentoTeleconsult->validationErrors) ? Comum::implodeRecursivo("\n",$this->LogFaturamentoTeleconsult->validationErrors) : 'Erro ao salvar Log de Atendimento');
                    $this->invalidate('',$msg_erro);
                    return false; 
                }

                $codigo_tipo_operacao = ($dados['Ficha']['profissional_existe_no_banco'] ? $this->LogFaturamentoTeleconsult->obterTipoOperacaoLogFaturamento() : TipoOperacao::TIPO_OPERACAO_CADASTRO);
                if (!$this->LogAtendimento->gravaLogAtendimentoFichaInformacoes($dados,$codigo_tipo_operacao)) {
                    $this->rollback();
                    $msg_erro = (!empty($this->LogAtendimento->validationErrors) ? Comum::implodeRecursivo("\n",$this->LogAtendimento->validationErrors) : 'Erro ao salvar Log de Atendimento');
                    $this->invalidate('',$msg_erro);
                    return false;                    
                }

                $ret_log_faturamento = $this->LogFaturamentoTeleconsult->gerarFaturamentoFicha($dados,$clienteProdutoServico, $codigo_ficha,$dados['Profissional']['codigo'], $codigos_veiculo,$dados['Ficha']['profissional_existe_no_banco']);
                if (!$ret_log_faturamento) {
                    $this->rollback();
                    $msg_erro = (!empty($this->LogFaturamentoTeleconsult->validationErrors) ? Comum::implodeRecursivo("\n",$this->LogFaturamentoTeleconsult->validationErrors) : 'Erro ao salvar Log de Faturamento');
                    file_put_contents(APP . '/tmp/erro_log_faturamento.txt', $codigo_tipo_operacao." - ".$dados['Ficha']['codigo_cliente']. " - " . $dados['Profissional']['codigo'] . "\n", FILE_APPEND);
                    $this->invalidate('',$msg_erro);
                    return false;                    
                }

                if (!$this->FichaForense->incluir($codigo_ficha)) {
                    $this->rollback();
                    $msg_erro = (!empty($this->FichaForense->validationErrors) ? Comum::implodeRecursivo("\n",$this->FichaForense->validationErrors) : 'Erro ao salvar Ficha Forense');
                    $this->invalidate('',$msg_erro);
                    return false;                    

                }
                
                $this->commit();

                return true;
            }
        } catch(Exception $ex) {

            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    /**
     * Busca o último log de faturamento, caso o mesmo seja
     * <= 1 mês o código do log de faturamento será o 67 (atualização sem cobrança).
     * senão será o 22
     * @return integer código do tipo de operação do log de faturamento
     */
    public function obterTipoOperacaoLogFaturamento($codigo_cliente, $codigo_profissional, $codigo_produto) { 
        try {
            $this->LogFaturamento = ClassRegistry::init('LogFaturamentoTeleconsult');
            
            ClassRegistry::init('TipoOperacao');

            $codigo_cliente = preg_replace('/\D/', '', $codigo_cliente);
            $codigo_profissional = preg_replace('/\D/', '', $codigo_profissional);

            $fields = Array(
                "case when datediff(d, data_inclusao, getdate()) > 32 then ".TipoOperacao::TIPO_OPERACAO_ATUALIZACAO . "
                else ".TipoOperacao::ATUALIZACAO_SEM_COBRANCA."
                end as codigo_tipo_operacao"
            );

            $conditions = Array(
                'codigo_profissional' => $codigo_profissional,
                'codigo_cliente' => $codigo_cliente,
                'codigo_produto' => $codigo_produto,
                'codigo_tipo_operacao' => Array(TipoOperacao::TIPO_OPERACAO_CADASTRO,TipoOperacao::TIPO_OPERACAO_ATUALIZACAO,TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA)
            );

            $order = Array('data_inclusao desc');

            $retorno = $this->LogFaturamento->find('first',compact('fields','conditions','order'));
            if (empty($retorno)) {
                return TipoOperacao::TIPO_OPERACAO_ATUALIZACAO;
            } else {
                return $retorno[0]['codigo_tipo_operacao'];
            }
            //return true;
        } catch(Exception $ex) {

            $msg = (!empty($ex) ? $ex->getmessage() : '');
            $this->invalidate('',$msg);
            return false;
        }
    }

    public function sla_servicos_periodo($mes, $ano){
        $FichaPesquisa         = ClassRegistry::init('FichaPesquisa');
        $Usuario               = ClassRegistry::init('Usuario');
        //$LogFaturamento        = ClassRegistry::init('LogFaturamento');
        $TipoOperacao          = ClassRegistry::init('TipoOperacao');
        $Servico               = ClassRegistry::init('Servico');
        $ClienteProduto        = ClassRegistry::init('ClienteProduto');
        $ClienteProdutoServico = ClassRegistry::init('ClienteProdutoServico');

        $ultimo_dia = date('t', strtotime($ano.'-'.$mes.'-01'));

        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $dbo = $this->getDataSource();

        $sql = "WITH ficha AS 
                (   SELECT f.codigo, MAX(fp.codigo) AS codigo_ficha_pesquisa 
                    FROM dbTeleconsult.informacoes.ficha f WITH(NOLOCK) 
                    INNER JOIN dbTeleconsult.informacoes.ficha_pesquisa fp WITH(NOLOCK) ON fp.codigo_ficha = f.codigo 
                    WHERE f.data_inclusao BETWEEN '{$ano}{$mes}01 00:00:00' AND '{$ano}{$mes}{$ultimo_dia} 23:59:59' AND f.codigo_status IN (1,2,3,10) 
                    GROUP BY f.codigo),
                base  AS 
                (   SELECT s.descricao,
                        f.data_inclusao,
                        fp.data_alteracao,
                        datediff(MINUTE, f.data_inclusao, fp.data_alteracao) AS tempo,
                        cps.tempo_pesquisa,
                        u.apelido 
                    FROM ficha 
                    INNER JOIN dbTeleconsult.informacoes.ficha_pesquisa fp WITH(NOLOCK) ON ficha.codigo_ficha_pesquisa = fp.codigo 
                    INNER JOIN dbTeleconsult.informacoes.ficha f WITH(NOLOCK) ON f.codigo = fp.codigo_ficha 
                    INNER JOIN dbBuonny.portal.usuario u WITH(NOLOCK) ON u.codigo = fp.codigo_usuario_alteracao 
                    INNER JOIN dbTeleconsult.informacoes.log_faturamento lf WITH(NOLOCK, INDEX(ix_log_faturamento__codigo_ficha)) ON lf.codigo_ficha = f.codigo 
                    INNER JOIN dbTeleconsult.informacoes.tipo_operacao tio WITH(NOLOCK) ON tio.codigo = lf.codigo_tipo_operacao 
                    INNER JOIN dbBuonny.vendas.servico s WITH(NOLOCK) ON s.codigo = tio.codigo_servico 
                    INNER JOIN dbBuonny.vendas.cliente_produto cp WITH(NOLOCK) ON cp.codigo_produto = lf.codigo_produto AND cp.codigo_cliente = lf.codigo_cliente_pagador 
                    INNER JOIN dbBuonny.vendas.cliente_produto_servico cps WITH(NOLOCK) ON cps.codigo_cliente_produto = cp.codigo AND cps.codigo_servico = s.codigo AND cps.codigo_profissional_tipo = lf.codigo_profissional_tipo ) 
            SELECT descricao,
                MONTH(data_inclusao) AS mes,
                SUM( CASE WHEN tempo <= tempo_pesquisa THEN 1 ELSE 0 END) AS dentro_sla,
                SUM( CASE WHEN tempo > tempo_pesquisa THEN 1 ELSE 0 END) AS fora_sla 
            FROM base 
            GROUP BY descricao, MONTH(data_inclusao)";
        $lista = $dbo->fetchAll($sql);

        return $lista;
    }

     function valor_carga_por_codigo($codigo){
        switch ($codigo) {
            case 1:
                $valor = 'De R$ 0,01 a R$ 100.000,00';
                break;
            case 2:
                $valor = 'De R$ 100.000,01 a R$ 200.000,00';
                break;
            case 3:
                $valor = 'De R$ 200.000,01 a R$ 300.000,00';
                break;
            case 4:
                $valor = 'De R$ 300.000,01 a R$ 400.000,00';
                break;
            case 5:
                $valor = 'De R$ 400.000,01 a R$ 500.000,00';
                break;
            case 6:
                $valor = 'De R$ 500.000,01 a R$ 800.000,00';
                break;
            case 7:
                $valor = 'De R$ 800.000,01 a R$ 1.000.000,00';
                break;
            case 8:
                $valor = 'De R$ 1.000.000,01 a R$ 3.000.000,00';
                break;
            case 9:
                $valor = 'De R$ 3.000.000,01 a R$ 9.999.999,00';
                break;                              
            default:
                $valor = '';
                break;
        }
        return $valor;
    }
}