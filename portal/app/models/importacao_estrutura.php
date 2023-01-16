<?php
App::import('model', 'StatusImportacao');
class ImportacaoEstrutura extends AppModel {
    var $name = 'ImportacaoEstrutura';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'importacao_estrutura';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function incluir($path, $arquivo, $codigo_cliente,$shell = null) {
        if (file_exists($path.$arquivo)) {
            $this->bindModel(array(
                'belongsTo' => array(
                    'GrupoEconomico' => array('foreignKey' => 'codigo_grupo_economico')
                ),
                'hasMany' => array(
                    'RegistroImportacao' => array('foreignKey' => 'codigo_importacao_estrutura')
                )
            ));
            $grupo_economico = $this->GrupoEconomico->findByCodigoCliente($codigo_cliente);
            if ($grupo_economico) {
                try {
                    $importacao_estrutura = array(
                        'codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo'],
                        'nome_arquivo' => $arquivo,
                        'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
                    );

                    if($shell){
                        $importacao_estrutura['codigo_usuario_inclusao'] = 1;
                        $importacao_estrutura['codigo_empresa'] = 1;
                    }

                    $this->create();
                    $this->query('begin transaction');

                    if (!parent::incluir($importacao_estrutura)) throw new Exception("Erro ao importar arquivo", 1);
                    $columns = $this->RegistroImportacao->schema();
                    $columns = array_keys($columns);

                    $handle = fopen($path.$arquivo, "r");
                    if ($handle) {
                        $registro = 0;
                        while (!feof($handle)) {
                            $registro++;
                            $linha = fgetcsv($handle,0,';','"');
                            //ignorando linhas em branco
                            if (count($linha) === 1 && ($linha[0] === null || trim($linha[0]) === "" )) {
                                continue;
                            }
                            $linha = array_map("trim", $linha);
                            $dados = array_map("utf8_encode", $linha);
                            // debug(count($dados));die();
                            if ($registro > 1) {
                                if (count($dados) == 63) {
                                    $registro = $this->carregaColunas($dados, $this->id);
                                    if (!$this->RegistroImportacao->incluir($registro)) throw new Exception("Erro ao incluir registro do arquivo", 1);
                                } else {
                                    throw new Exception("Arquivo inválido, não possui as 63 colunas do modelo", 1);
                                }
                            }
                        }
                    }
                    $this->commit();
                    fclose($handle);
                    return true;
                } catch (Exception $ex) {
                    $this->invalidate('codigo', $ex->getMessage());
                    $this->rollback();
                }
            }
        }
    }//FINAL FUNCTION incluir

    function carregaColunas($dados, $codigo_importacao_estrutura) {
        $registro = array(
            'codigo_importacao_estrutura' => $codigo_importacao_estrutura,
            'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
        );
        
        foreach ($dados as $key => $value) $dados[$key] = trim(str_replace('"', '', str_replace("=", "", str_replace("'","",$dados[$key]))));
        $registro['codigo_alocacao'] = $dados[0];
        $registro['nome_alocacao'] = $dados[1];
        $registro['nome_setor'] = $dados[2];
        $registro['nome_cargo'] = $dados[3];
        $registro['codigo_matricula'] = $dados[4];
        $registro['matricula_funcionario'] = $dados[5];
        $registro['nome_funcionario'] = $dados[6];
        $registro['data_nascimento'] = $dados[7];
        $registro['sexo'] = $dados[8];
        $registro['situacao_cadastral'] = $dados[9];
        $registro['data_admissao'] = $dados[10];
        $registro['data_demissao'] = $dados[11];
        $registro['data_inicio_cargo'] = $dados[12];
        $registro['estado_civil'] = $dados[13];
        $registro['pis_pasep'] = $dados[14];
        $registro['rg'] = $dados[15];
        $registro['estado_rg'] = $dados[16];
        $registro['cpf'] = Comum::soNumero($dados[17]);
        $registro['ctps'] = $dados[18];
        $registro['serie_ctps'] = $dados[19];
        $registro['uf_ctps'] = $dados[20];
        $registro['endereco_funcionario'] = $dados[21];
        $registro['numero_funcionario'] = Comum::soNumero($dados[22]);
        $registro['complemento_funcionario'] = $dados[23];
        $registro['bairro_funcionario'] = $dados[24];
        $registro['cidade_funcionario'] = $dados[25];
        $registro['estado_funcionario'] = $dados[26];
        $registro['cep_funcionario'] = Comum::soNumero($dados[27]);
        $registro['possui_deficiencia'] = $dados[28];
        $registro['codigo_cbo'] = $dados[29];
        $registro['codigo_gfip'] = $dados[30];
        $registro['centro_custo'] = $dados[31];
        // $registro['data_ultimo_aso'] = $dados[32];
        // $registro['aptidao'] = $dados[33];
        $registro['turno'] = $dados[32];
        $registro['descricao_detalhada_cargo'] = $dados[33];
        $registro['celular_funcionario'] = Comum::soNumero($dados[34]);
        $registro['autoriza_envio_sms_funcionario'] = $dados[35];
        $registro['email_funcionario'] = $dados[36];
        $registro['autoriza_envio_email_funcionario'] = $dados[37];
        $registro['contato_responsavel_alocacao'] = $dados[38];
        $registro['telefone_responsavel_alocacao'] = Comum::soNumero($dados[39]);
        $registro['email_responsavel_alocacao'] = $dados[40];
        $registro['endereco_alocacao'] = $dados[41];
        $registro['numero_alocacao'] = Comum::soNumero($dados[42]);
        $registro['complemento_alocacao'] = $dados[43];
        $registro['bairro_alocacao'] = $dados[44];
        $registro['cidade_alocacao'] = $dados[45];
        $registro['estado_alocacao'] = $dados[46];
        $registro['cep_alocacao'] = Comum::soNumero($dados[47]);
        $registro['cnpj_alocacao'] = Comum::soNumero($dados[48]);
        $registro['inscricao_estadual'] = $dados[49];
        $registro['inscricao_municipal'] = $dados[50];
        $registro['cnae'] = $dados[51];
        $registro['grau_risco'] = $dados[52];
        $registro['razao_social_alocacao'] = $dados[53];
        $registro['unidade_negocio'] = $dados[54];
        $registro['regime_tributario'] = $dados[55];
        $registro['codigo_externo_alocacao'] = $dados[56];
        $registro['tipo_alocacao'] = $dados[57];
        $registro['conselho_profissional'] = $dados[58];
        $registro['numero_conselho'] = $dados[59];
        $registro['conselho_uf'] = $dados[60];
        $registro['chave_externa'] = str_replace("'","",$dados[61]); //retira as aspas simples pois este campo é validado por pos~ições
        $registro['codigo_cargo_externo'] = $dados[62];

        return $registro;
    }//FINAL FUNCTION carregaColunas

    function excluir($codigo) {
        try {
            $arquivo = $this->carregar($codigo);
            if ($arquivo[$this->name]['codigo_status_importacao'] != StatusImportacao::SEM_PROCESSAR) {
                return false;
            }
            $this->query('begin transaction');
            $this->bindModel(array('hasMany' => array('RegistroImportacao' => array('foreignKey' => 'codigo_importacao_estrutura'))));
            if (!$this->RegistroImportacao->deleteAll(array('codigo_importacao_estrutura' => $codigo))) throw new Exception("Error Processing Request", 1);
            if (!parent::excluir($codigo)) throw new Exception("Error Processing Request", 1);
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
        }
    }//FINAL FUNCTION excluir

    function importar($codigo) {

        # busca no banco a estrutura à ser importada por código
        $importacao_estrutura = $this->read(null, $codigo);
        // (
        //     [ImportacaoEstrutura] => Array
        //         (
        //             [codigo] => 2535
        //             [codigo_grupo_economico] => 7639
        //             [codigo_usuario_inclusao] => 72208
        //             [codigo_empresa] => 1
        //             [codigo_status_importacao] => 2
        //             [data_inclusao] => 21/08/2019 11:23:03
        //             [data_processamento] => 21/08/2019 11:23:03
        //             [nome_arquivo] => 20190820135907ex - Copia.csv
        //         )

        // )

        echo "Importacao Estrutura ({$codigo})"."\n";
        echo "---------------------------------------"."\n";

        // se não encontrou uma estrutura
        if(!$importacao_estrutura){
            echo "Importacao Estrutura nao encontrado ({$codigo})"."\n";
            $dbo = $this->getDataSource();
            $logs = $dbo->getLog();
            pr($logs);
            exit;
        }

        $codigo_grupo_economico = $importacao_estrutura['ImportacaoEstrutura']['codigo_grupo_economico'];

        $status = $importacao_estrutura['ImportacaoEstrutura']['codigo_status_importacao'];
        if ($status != StatusImportacao::SEM_PROCESSAR) {
            echo "Estrutura nao pode ser processada com status  ({$status})"."\n";
            exit;
        }

        $this->set('codigo_status_importacao', StatusImportacao::PROCESSANDO);
        $this->save();

        $this->bindModel(array('hasMany' => array('RegistroImportacao' => array('foreignKey' => 'codigo_importacao_estrutura'))));

        $order = array('RegistroImportacao.codigo');
        $conditions = array(
            'codigo_importacao_estrutura' => $importacao_estrutura['ImportacaoEstrutura']['codigo'],
            'RegistroImportacao.codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
        );

        $registros = $this->RegistroImportacao->findImportacao('all', compact('conditions', 'order'));

        // termina se registros estiver zerados
        if(!is_array($registros) || (is_array($registros) && count($registros) == 0)){
            echo "Estrutura nao pode ser processada"."\n";
            echo "Registros invalidos ou nao encontrados"."\n";
            exit;
        }

        // total registros
        $qtd_total = count($registros);
        $qtd_processado = 0;
        $alocacao_existente = array();
        foreach ($registros as $key => $registro) {
            foreach ($registro[0] as $campo => $value) $registro[0][$campo] = trim($registro[0][$campo]);

            try {               

                $invalidFields = $this->RegistroImportacao->validaRegistro($registro, array('ignorar_endereco' => true));                

                if (empty($invalidFields)) {

                    $this->query('begin transaction');

                    if(!isset($alocacao_existente[$registro[0]['codigo_externo_alocacao']])){                        

                        //Importação Cliente
                        $retorno_alocacao = $this->RegistroImportacao->importarAlocacao($registro[0], $codigo_grupo_economico);

                        $codigo_alocacao = $retorno_alocacao['codigo_alocacao'];
                        $alocacao_existente[$retorno_alocacao['codigo_externo']] = $retorno_alocacao['codigo_alocacao'];

                    } else {
                        $codigo_alocacao = $alocacao_existente[$registro[0]['codigo_externo_alocacao']];
                    }

                    // echo "codigo_alocacao = {$codigo_alocacao}"."\n";
                    if (!$codigo_alocacao) throw new Exception("Erro ao processar Unidade:".$retorno_alocacao['invalidFields'], 1);

                    // //Importação Funcionario
                    $retorno_funcionario =  $this->RegistroImportacao->importarFuncionario($registro[0], $codigo_grupo_economico);
                    $codigo_funcionario = $retorno_funcionario['codigo_funcionario'];
                    // echo "codigo_funcionario = {$codigo_funcionario}"."\n";
                    if (!$codigo_funcionario) throw new Exception("Erro ao processar Funcionario:".$retorno_funcionario['invalidFields'], 1);

                    // //Importação Cliente_Funcionario
                    $retorno_matricula = $this->RegistroImportacao->importarMatricula($registro[0], $codigo_grupo_economico, $codigo_funcionario);
                    $codigo_matricula = $retorno_matricula['codigo_matricula'];
                    // echo "codigo_matricula = {$codigo_matricula}"."\n";
                    if (!$codigo_matricula) throw new Exception("Erro ao processar Matricula:".$retorno_matricula['invalidFields'], 1);

                    // //Importação Funcionario_Setor_Cargo
                    $retorno_setor_cargo = $this->RegistroImportacao->importarSetorCargo($registro[0], $codigo_grupo_economico, $codigo_matricula, $codigo_alocacao, $codigo_funcionario);
                    $codigo_setor_cargo = $retorno_setor_cargo['codigo_setor_cargo'];
                    if (!$codigo_setor_cargo) throw new Exception("Erro ao processar SetorCargo:".$retorno_setor_cargo['invalidFields'], 1);

                    //Importação Médico Coordenador
                    $retorno_medico = $this->RegistroImportacao->importarMedicoCoord($registro[0]);
                    $codigo_medico = $retorno_medico;
                    if (!$codigo_medico) throw new Exception("Erro ao processar Medico:".$retorno_medico['invalidFields'], 1);

                    $this->RegistroImportacao->read(null, $registro[0]['codigo']);
                    $this->RegistroImportacao->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
                    $this->RegistroImportacao->set('data_processamento', date('Y-m-d H:i:s'));
                    $this->RegistroImportacao->save();
                    // echo "Importado registro ".$key."({$registro[0]['codigo']})"."\n";

                    $this->commit();

                }
                else {
                    // echo "Registro inválido ".$key."({$registro[0]['codigo']})"."\n";
                    // echo print_r($invalidFields, 1)."\n";

                    $this->RegistroImportacao->read(null, $registro[0]['codigo']);
                    $this->RegistroImportacao->set('codigo_status_importacao', StatusImportacao::ERRO);
                    $this->RegistroImportacao->set('data_processamento', date('Y-m-d H:i:s'));
                    $this->RegistroImportacao->set('observacao', implode(',',$invalidFields));
                    $this->RegistroImportacao->save();
                }
            } catch (Exception $ex) {
                // echo "Falha registro ".$key."({$registro[0]['codigo']})"."\n";
                // echo $ex->getMessage();

                $this->rollback();
                $this->RegistroImportacao->read(null, $registro[0]['codigo']);
                $this->RegistroImportacao->set('codigo_status_importacao', StatusImportacao::ERRO);
                $this->RegistroImportacao->set('data_processamento', date('Y-m-d H:i:s'));
                $this->RegistroImportacao->set('observacao', $ex->getMessage());
                $this->RegistroImportacao->save();
            }
            $qtd_processado++;
        }

        // echo "Processou [ {$qtd_processado} ] registros de um total de [ {$qtd_total} ]."."\n";

        $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');

        $conditions_gec = array('codigo_grupo_economico ' => $importacao_estrutura['ImportacaoEstrutura']['codigo_grupo_economico']);
        $retorno_gec = $this->GrupoEconomicoCliente->find('all', array('conditions' => $conditions_gec));

        $codigo_cliente = array();

        foreach($retorno_gec as $key_gec => $value_gec){
            $codigo_cliente[] = $value_gec['GrupoEconomicoCliente']['codigo_cliente'];
        }

        //$per_capita_sem_servico = self::calculaTotalPerCapitaSemServico($codigo_cliente);

        $per_capita = self::calculaTotalPerCapita($codigo_cliente);

        $codigo_cliente_matricula = $retorno_gec['0']['GrupoEconomico']['codigo_cliente'];

        self::processamentoPerCapita($codigo_cliente_matricula, $qtd_processado, $per_capita, $importacao_estrutura['ImportacaoEstrutura']['codigo']);

        $this->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
        $this->set('data_processamento', date('Y-m-d H:i:s'));
        $this->save();

    }//FINAL FUNCTION importar

    /**
     * [processamentoPerCapita description]
     * @param  [int] $codigo_cliente                [codigo do cliente pagador/matriz]
     * @param  [int] $qtd_processado                [quantidade de linhas que foi processado no arquivo]
     * @param  [int] $per_capita                    [quantidade total vidas]
     * @param  [int] $codigo_importacao_estrutura   [Código importação estrutura]
     * @return [void]
     */
    private function processamentoPerCapita($codigo_cliente, $qtd_processado, $per_capita, $codigo_importacao_estrutura){

        // debug('codigo_cliente: ' . $codigo_cliente);
        // debug('qtd_processado: ' . $qtd_processado);
        // debug('per_capita: ' . $per_capita);
        // debug('codigo_importacao_estrutura: ' . $codigo_importacao_estrutura);

        //pega o mes passado
        $base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));

        $mes = date('m', $base_periodo);
        $ano = date('Y', $base_periodo);

        $this->Pedido = ClassRegistry::init('Pedido');

        $conditions_p = array('mes_referencia'  => $mes,
            'ano_referencia'  => $ano,
            'manual'          => '0',
            'data_integracao IS NULL'
        );

        $retorno_p = $this->Pedido->find('all', array('conditions' => $conditions_p));

        if($retorno_p){

            $this->CtrPreFatPerCapita = ClassRegistry::init('CtrPreFatPerCapita');

            $conditions = array('mes_referencia' => $mes, 'ano_referencia' => $ano, 'codigo_cliente_matricula' => $codigo_cliente);

            $retorno_find = $this->CtrPreFatPerCapita->find('first', array('conditions' => $conditions));

            if(isset($retorno_find['CtrPreFatPerCapita']['codigo'])){

                $dados = array();

                $dados['CtrPreFatPerCapita']['qtd_processado']              = $qtd_processado;
                $dados['CtrPreFatPerCapita']['qtd_a_faturar']               = $per_capita;
                $dados['CtrPreFatPerCapita']['data_alteracao']              = date('Y-m-d H:i:s');
                $dados['CtrPreFatPerCapita']['codigo']                      = $retorno_find['CtrPreFatPerCapita']['codigo'];
                $dados['CtrPreFatPerCapita']['codigo_importacao_estrutura'] = $codigo_importacao_estrutura;

                if($this->CtrPreFatPerCapita->atualizar($dados)){
                    echo("Atualizando Controle Pre Faturamento Per Capita {$dados['CtrPreFatPerCapita']['codigo']} \n");
                }else{
                    echo("PROBLEMA ao Atualizar Controle Pre Faturamento Per Capita {$dados['CtrPreFatPerCapita']['codigo']} \n");
                }
            }//FINAL SE isset($retorno_find['CtrPreFatPerCapita']['codigo']
        }//FINAL SE $retorno_p
    }//FINAL FUNCTION processamentoPerCapita

    /**
     * [calculaTotalPerCapita calcula total vidas per capita]
     * @param  [array] $dados
     * @return [int]            [total de registros]
     */
    private function calculaTotalPerCapita($codigo_cliente){

        $this->Pedido = ClassRegistry::init('Pedido');

        $dados = self::basePeriodo($codigo_cliente);

        $retorno = Set::extract($this->Pedido->calcula_percapita($dados), '{n}.0');

        $qtd_registros = 0;
        foreach($retorno as $value){
            $qtd_registros  += $value['qtd'];
        }//FINAL FOREACH $retorno

        return $qtd_registros;
    }//FINAL FUNCTION calculaTotalPerCapita


    /**
     * [base_periodo calcula base periodo]
     * @param  [array] $codigo_cliente [codigo do cliente]
     */
    private function basePeriodo($codigo_cliente){

        $codigo_cliente = implode(',', $codigo_cliente);

        $dados = array();

        $dados['codigo_cliente']    = $codigo_cliente;

        $base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));
        $dados['mes']               = date('m', $base_periodo);
        $dados['ano']               = date('Y', $base_periodo);

        //seta a data de inicio
        $dados['data_inicial']  = Date('Ym01', $base_periodo);
        $dados['data_final']    = Date('Ymt', $base_periodo);

        return $dados;
    }//FINAL FUNCTION basePeriodo

    public function getImportacaoEstrutura($grupo_economico){

        $conditions = array('codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']);

        $order = array('ImportacaoEstrutura.data_inclusao DESC');
        
        $this->bindModel(array('belongsTo' => array('StatusImportacao' => array('foreignKey' => 'codigo_status_importacao'))));
        $this->bindModel(array('belongsTo' => array('Usuario' => array(
            'foreignKey' => 'codigo_usuario_inclusao', 
            'fields' => array('codigo', 'apelido')
        ))));

        $joins = array(
            array(
            'table' => 'usuario',
            'alias' => 'Usuario',
            'type' => 'INNER',
            'conditions' => array('Usuario.codigo = ImportacaoEstrutura.codigo_usuario_inclusao')
            )
        );
        
        $arquivos_importados = $this->find('all', compact('conditions', 'order'));

        // pr($this->find('sql', compact('conditions', 'order')));

        return $arquivos_importados;
    }

}//FINAL CLASS ImportacaoEstrutura
