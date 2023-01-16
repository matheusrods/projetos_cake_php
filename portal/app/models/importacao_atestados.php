<?php
App::import('model', 'StatusImportacao');

/*
 * Model da Manipulação das tabelas de cabeçalho e registros da importação de atestados
 */
class ImportacaoAtestados extends AppModel {
    var $name           = 'ImportacaoAtestados';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'importacao_atestados';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');

    /**
     * Método de inclusão de novo de dados para importação de atestados médicos
     * @param string $path Caminho de pastas do Arquivo que será importado
     * @param atring $arquivo nome do Arquivo que será importado
     * @param integer $codigo_cliente Código de Identificação do Cliente/Empresa para onde os atestados serão gerados
     * @return void
     */
    function incluir($path, $arquivo, $codigo_cliente) {
        if (file_exists($path.$arquivo)) {
            $this->bindModel(array(
                'belongsTo' => array(
                    'GrupoEconomicoCliente' => array('foreignKey' => 'codigo')
                ),
                'hasMany' => array(
                    'ImportacaoAtestadosRegistros' => array('foreignKey' => 'codigo_importacao_atestados')
                )
            ));
            $grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
            if ($grupo_economico) {
                try {
                    $importacao_atestados = array(
                        'codigo_grupo_economico' => $grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'],
                        'nome_arquivo' => $arquivo,
                        'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
                    );
                    $this->create();
                    $this->query('BEGIN TRANSACTION');
                    if (!parent::incluir($importacao_atestados)) throw new Exception("Erro ao importar arquivo de atestados", 1);
                    $columns = $this->ImportacaoAtestadosRegistros->schema();
                    $columns = array_keys($columns);
                    
                    $handle = fopen($path.$arquivo, "r");
                    if ($handle) {
                        $registro = 0;
                        while (!feof($handle)) {
                            $registro++;
                            $linha = utf8_encode(trim(fgets($handle)));
                            if (trim($linha) != '') {
                                $dados = explode(';', $linha );
                                // debug(count($dados));
                                // debug($dados);exit;

                                if ($registro > 1) {
                                    if (count($dados) == 39) { // 39 colunas existentes no arquivo de importação
                                        $registro = $this->carregaColunas($dados, $this->id);
                                        // debug($registro);exit;
                                        if (!$this->ImportacaoAtestadosRegistros->incluir($registro))
                                            throw new Exception("Erro ao incluir registro do arquivo de importação de atestados", 1);
                                    } else {
                                        throw new Exception("Arquivo de importação de atestados inválido, não possui as 32 colunas de acordo com o modelo de arquivo de importação", 1);
                                    }
                                }
                            }
                        }
                    }
                    $this->commit();
                    return true;
                } catch (Exception $ex) {
                    $this->invalidate('codigo', $ex->getMessage());
                    $this->rollback();
                }
            }
        }
    }

    /**
     * Método de conversão dos dados lidos das linhas do arquivo em dados e suas respectivas colunas correspondentes
     * @param array $dados Dados da linha importada do arquivo informado
     * @param integer $codigo_importacao_atestado Código de identificação do arquivo importado
     * @return array Array onde cada coluna tem seu dados respectivo da linha lida do arquivo importado
     */
    function carregaColunas($dados, $codigo_importacao_atestado) {
        $registro = array(
            'codigo_importacao_atestados' => $codigo_importacao_atestado,
            'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
        );

        foreach ($dados as $key => $value)
            $dados[$key] = trim($dados[$key]);

        $registro['nome_empresa']               = $dados[0] == '' ? null : $dados[0];
        $registro['nome_unidade']               = $dados[1] == '' ? null : $dados[1];
        $registro['nome_setor']                 = $dados[2] == '' ? null : $dados[2];
        $registro['nome_cargo']                 = $dados[3] == '' ? null : $dados[3];
        $registro['matricula']                  = $dados[4] == '' ? null : $dados[4];
        $registro['cpf']                        = $dados[5] == '' ? null : Comum::soNumero($dados[5]);
        $registro['tipo_atestado']              = $dados[6] == '' ? null : $dados[6];
        $registro['sem_profissional']           = $dados[7] == '' ?  null : $dados[7];
        $registro['codigo_medico']              = $dados[8] == '' ? null : $dados[8];
        $registro['medico_solicitante']         = $dados[9] == '' ? null : $dados[9];
        $registro['conselho_classe']            = $dados[10] == '' ? null : $dados[10];
        $registro['UF']                         = $dados[11] == '' ? null : $dados[11];
        $registro['sigla_conselho']             = $dados[12] == '' ? null : $dados[12];
        $registro['especialidade']              = $dados[13] == '' ? null : $dados[13];
        $registro['data_inicio_afastamento']    = $dados[14] == '' ? null : $dados[14];
        $registro['data_retorno_afastamento']   = $dados[15] == '' ? null : $dados[15];    
        $registro['dias']                       = (float) str_replace(',','.',$dados[16]);
        $registro['hora_inicio_afastamento']    = $dados[17] == '' ? null : $dados[17];
        $registro['hora_termino_afastamento']   = $dados[18] == '' ? null : $dados[18];
        $registro['horas']                      = $dados[19] == '' ? null : $dados[19];
        $registro['codigo_cid']                 = $dados[20] == '' ? null : $dados[20];
        $registro['nome_cid']                   = $dados[21] == '' ? null : $dados[21];
        $registro['restricao_retorno']          = $dados[22] == '' ? null : $dados[22];
        $registro['motivo_licenca']             = $dados[23] == '' ? null : $dados[23];
        $registro['tipo_licenca']               = $dados[24] == '' ? null : $dados[24];
        $registro['tabela_18_esocial']          = $dados[25] == '' ? null : $dados[25];
        $registro['tp_acid_transito']           = $dados[26];
        $registro['tipo_acidente_transito']     = $dados[27];
        $registro['motivo_afastamento']         = $dados[28] == '' ? null : $dados[28];
        $registro['origem_retificacao']         = $dados[29] == '' ? null : $dados[29];
        $registro['tipo_processo']              = $dados[30] == '' ? null : $dados[30];
        $registro['numero_processo']            = $dados[31] == '' ? null : $dados[31];
        $registro['codigo_documento_entidade']  = $dados[32] == '' ? null : Comum::soNumero($dados[32]);
        $registro['onus_remuneracao']           = $dados[33] == '' ? null : $dados[33];
        $registro['onus_requisicao']            = $dados[34] == '' ? null : $dados[34];
        $registro['obs_afastamento']            = $dados[35] == '' ? null : $dados[35];
        $registro['renumeracao_cargo']          = $dados[36] == '' ? null : $dados[36];
        $registro['data_inicio_p_aquisitivo']   = $dados[37] == '' ? null : $dados[37];
        $registro['data_fim_p_aquisitivo']      = $dados[38] == '' ? null : $dados[38];

        return $registro;
    }

    /**
     * Método de deleção de todos os registros de linhas de um arquivo importado
     * @param integer $codigo Código de identificação de arquivo importado
     * @return avoid
     */
    function excluir($codigo) {
        $this->ImportacaoAtestadosRegistros = & ClassRegistry::init('ImportacaoAtestadosRegistros');
        try {
            $arquivo = $this->carregar($codigo);
            if ($arquivo[$this->name]['codigo_status_importacao'] != StatusImportacao::SEM_PROCESSAR) {
                return false;
            }
            $this->query('BEGIN TRANSACTION');

            $deleta_itens = $this->ImportacaoAtestadosRegistros->deleteAll(array('codigo_importacao_atestados' => $codigo),false);
            if (!$deleta_itens)
                throw new Exception("Error Processing Request", 1);

            $deleta_arquivo = parent::excluir($codigo);
            if (!$deleta_arquivo)
                throw new Exception("Error Processing Request", 1);

            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
        }
    }

    /**
     * Método de processamento dos dados válidos do arquivo de importação, e geração dos registros de tabelas relacionadas não existentes e o
     * registro de Atestado Médico do Funcionário
     * IMPORTANTE: Este processo é executado através do console do cake, onde um arquivo importacao.php na pasta de shells do cake é executado, e * processamento não fica vinculado a requisição do navegador, e é processado num processo separado em background.
     * @param integer $codigo Código de identificação de arquivo importado
     * @return avoid
     */
    function importar($codigo) {

        $cid =& ClassRegistry::init('Cid');
        $importacao_atestados = $this->read(null, $codigo);

        if ($importacao_atestados) {

            if ($importacao_atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) {

                echo 'GrupoEconomico '.$importacao_atestados['ImportacaoAtestados']['codigo_grupo_economico']."\n";
                
                $this->set('codigo_status_importacao', StatusImportacao::PROCESSANDO);
                $this->save();
                
                $conditions = array('codigo_importacao_atestados' => $importacao_atestados['ImportacaoAtestados']['codigo']);

                $this->bindModel(array('hasMany' => array('ImportacaoAtestadosRegistros' => array('foreignKey' => 'codigo_importacao_atestados'))));
                $order = array('ImportacaoAtestadosRegistros.codigo');
                $registros = $this->ImportacaoAtestadosRegistros->findImportacao('all', compact('conditions', 'order'));
                
                foreach ($registros as $key => $registro) {
                    foreach ($registro[0] as $campo => $value) {
                        $registro[0][$campo] = trim($registro[0][$campo]);
                    }

                    try {
                        echo "Processar registro ".$key."({$registro[0]['codigo']}) ".date('Y-m-d H:i:s')."\n";
                        $this->query('BEGIN TRANSACTION');

                        $pesquisa_cid = array('conditions' => array(
                            'codigo_cid10' => strtoupper($registro[0]['codigo_cid'])
                            )
                        );
                        if(!$registro[0]['codigo_cid'] = $cid->find('first',$pesquisa_cid)) {
                            $registro[0]['codigo_cid'] = '';
                        } else {
                            $registro[0]['codigo_cid'] = $registro[0]['codigo_cid']['Cid']['codigo'];
                        }

                        $retorno_tipo_afastamento = $this->ImportacaoAtestadosRegistros->importarTipoAfastamento($registro[0]);
                        
                        $registro[0]['tipo_afastamento'] = $retorno_tipo_afastamento['codigo_tipo_afastamento'];//tipo afastamento
                        
                        $retorno_motivo_licenca = $this->ImportacaoAtestadosRegistros->importarMotivoLicenca($registro[0]);
                        
                        $codigo_motivo_licenca = $retorno_motivo_licenca['codigo_motivo_licenca'];

                        if (!$codigo_motivo_licenca) {
                            throw new Exception("Erro no Processamento do Motivo de Afastamento " . $retorno_motivo_licenca['invalidFields'], 1);
                        }

                        $retorno_conselho = $this->ImportacaoAtestadosRegistros->importarConselhoProfissional($registro[0]);
                        $codigo_conselho = $retorno_conselho['codigo_conselho'];
                        if (!$codigo_conselho) {
                            throw new Exception("Erro no Processamento do Conselho Profissional do Médico Solicitante " . $retorno_conselho['invalidFields'], 1);
                        }

                        $registro[0]['codigo_conselho'] = $codigo_conselho;

                        $retorno_medico = $this->ImportacaoAtestadosRegistros->importarMedico($registro[0]);
                        $codigo_medico = $retorno_medico['codigo_medico'];

                        if (!$codigo_medico) {
                            throw new Exception("Erro no Processamento do Médico" . $retorno_medico['invalidFields'], 1);
                        }

                        //*** tratamento para poder achar o codigo esocial que foi informado na importacao para colocar corretamente no atestado
                        //referencia a tabela esocial
                        $Esocial = & ClassRegistry::init('Esocial');
                        $buscar_codigo_esocial = $Esocial->find('first',array('conditions' => array('tabela' => 18,'codigo_descricao' => $registro[0]['tabela_18_esocial'])));
                        //se achar o codigo esocial ele seta para incluir
                        if($buscar_codigo_esocial){
                            $codigo_motivo_esocial = $buscar_codigo_esocial['Esocial']['codigo'];
                        } else{
                            //senao vazio
                            $codigo_motivo_esocial = "";
                        }

                        $atestado['Atestado'] = array(
                            'codigo_cliente_funcionario'=> $registro[0]['codigo_cliente_funcionario'],
                            'data_afastamento_periodo'  => $registro[0]['data_inicio_afastamento'],
                            'data_retorno_periodo'      => $registro[0]['data_retorno_afastamento'],
                            'afastamento_em_horas'      => $registro[0]['horas'],
                            'hora_afastamento'          => $registro[0]['hora_inicio_afastamento'],
                            'hora_retorno'              => $registro[0]['hora_termino_afastamento'],
                            'afastamento_em_dias'       => $registro[0]['dias'],
                            'codigo_func_setor_cargo'   => $registro[0]['codigo_func_setor_cargo'],
                            'codigo_motivo_licenca'     => $codigo_motivo_licenca,
                            'codigo_medico'             => $codigo_medico,
                            'codigo_empresa'            => $importacao_atestados['ImportacaoAtestados']['codigo_empresa'],
                            'cpf'                       => $registro[0]['cpf'],
                            'codigo_cid'                => $registro[0]['codigo_cid'],
                            'dias'                      => $registro[0]['dias'],
                            'horas'                     => $registro[0]['horas'],
                            'codigo_motivo_esocial'     => $codigo_motivo_esocial,
                            'motivo_afastamento'        => $registro[0]['motivo_afastamento'],
                            'origem_retificacao'        => $registro[0]['origem_retificacao'],
                            'tipo_acidente_transito'    => $registro[0]['tipo_acidente_transito'],
                            'tipo_processo'             => $registro[0]['tipo_processo'],
                            'numero_processo'           => $registro[0]['numero_processo'],
                            'codigo_documento_entidade' => $registro[0]['codigo_documento_entidade'],
                            'onus_remuneracao'          => $registro[0]['onus_remuneracao'],
                            'onus_requisicao'           => $registro[0]['onus_requisicao'],
                            'tp_acid_transito'          => $registro[0]['tp_acid_transito'],
                            'tipo_atestado'             => $registro[0]['tipo_atestado'],
                            'obs_afastamento'           => $registro[0]['obs_afastamento'],
                            'renumeracao_cargo'         => $registro[0]['renumeracao_cargo'],
                            'sem_profissional'          => $registro[0]['sem_profissional'],
                            'data_inicio_p_aquisitivo'  => $registro[0]['data_inicio_p_aquisitivo'],
                            'data_fim_p_aquisitivo'     => $registro[0]['data_fim_p_aquisitivo'],
                        );
                    
                        $retorno_atestado = $this->ImportacaoAtestadosRegistros->importarAtestado($atestado);

                        $codigo_atestado = $retorno_atestado['codigo_atestado'];
                        if (!$codigo_atestado) {
                            throw new Exception("Erro no Processamento do Atestado " . $retorno_atestado['invalidFields'], 1);
                        }

                        // echo "LEITURA ATESTADO ".date('Y-m0d H:i:s')."\n";
                        $this->ImportacaoAtestadosRegistros->read(null, $registro[0]['codigo']);
                        $this->ImportacaoAtestadosRegistros->set('data_processamento', date('Y-m-d H:i:s'));

                        if($retorno_atestado['invalidFields'] || $retorno_medico['invalidFields'] || $retorno_conselho['invalidFields'] || $retorno_motivo_licenca['invalidFields'] || $retorno_tipo_afastamento['invalidFields']) {
                            $this->ImportacaoAtestadosRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
                            $this->ImportacaoAtestadosRegistros->set('observacao', $ex->getMessage());
                        } else {
                            $this->ImportacaoAtestadosRegistros->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
                            $this->log("Importado registro ".$key."({$registro[0]['codigo']})", 'debug');
                            // echo "Importado registro ".$key."({$registro[0]['codigo']})"."\n";
                           
                        }
                        $this->commit();
                        $this->ImportacaoAtestadosRegistros->save();
                    } catch (Exception $ex) {
                        $dbo = $this->getDataSource();
                        $logs = $dbo->getLog();
                        $this->log($logs, 'debug');
                        $this->rollback();
                        $this->ImportacaoAtestadosRegistros->read(null, $registro[0]['codigo']);
                        $this->ImportacaoAtestadosRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
                        $this->ImportacaoAtestadosRegistros->set('data_processamento', date('Y-m-d H:i:s'));
                        $this->ImportacaoAtestadosRegistros->set('observacao', $ex->getMessage());
                        $this->ImportacaoAtestadosRegistros->save();
                    }
                }
                $this->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
                $this->set('data_processamento', date('Y-m-d H:i:s'));
                $this->save();
            }
        } else {
            echo "Importação de Atestados não encontrada ({$codigo})"."\n";
            $dbo = $this->getDataSource();
            $logs = $dbo->getLog();
            pr($logs);
        }
    }
    
    public function getAtestados($codigo_importacao_atestado){//retorna todos os atestados de uma determinada importacao

        $this->bindModel(array('belongsTo' => array(
            'ImportacaoAtestadosRegistros' => array('foreignKey' => false, 'conditions' => array(
                'ImportacaoAtestados.codigo = ImportacaoAtestadosRegistros.codigo_importacao_atestados'
            ))
        )));

        $conditions = array('ImportacaoAtestados.codigo' => $codigo_importacao_atestado);// conditions for the query
        
        $fields = array(// fields for the query
            'ImportacaoAtestados.nome_arquivo AS nome_arquivo',
            'ImportacaoAtestadosRegistros.nome_empresa AS nome_empresa',
            'data_inclusao',
            'COUNT(ImportacaoAtestadosRegistros.codigo) AS qtd_atestados'
        );// fields
        
        $group = array('nome_arquivo','nome_empresa', 'data_inclusao');// group by
        
        $atestados = $this->find('all', compact('fields', 'conditions', 'group'));// find all the records
       
        // pr($this->find('sql', compact('fields', 'conditions', 'group')));

        return $atestados;
    }

    public function getAtestadosListagem($codigo_grupo_economico){

        $conditions = array('ImportacaoAtestados.codigo_grupo_economico' => $codigo_grupo_economico);

        $order = array('ImportacaoAtestados.data_inclusao DESC');
        
        $this->bindModel(
            array('belongsTo' =>
                array(
                    'StatusImportacao' => array('foreignKey' => 'codigo_status_importacao'),
                    'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = ImportacaoAtestados.codigo_grupo_economico')),
            )
        );

        $this->bindModel(array('belongsTo' => array('Usuario' => array(
            'foreignKey' => 'codigo_usuario_inclusao', 
            'fields' => array('codigo', 'apelido')
        ))));

        $arquivos_importados = $this->find('all', compact('conditions','order'));

        return $arquivos_importados;

    }
}