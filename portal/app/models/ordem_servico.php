<?php
class OrdemServico extends AppModel
{

    var $name = 'OrdemServico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'ordem_servico';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');


    const PPRA = '2647';
    const PCMSO = '2340';

    /**
     * [getPCMSO description]
     * 
     * metodo para pegar o codigo do pcmso
     * 
     * @return [type] [description]
     */
    public static function getPCMSO()
    {

        $Servico = ClassRegistry::init('Servico');
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');
        //pega o tipo de servico pcmso
        $servico_pcmso = $Servico->find('first', array('conditions' => array('tipo_servico' => 'S', "descricao LIKE '%PCMSO - PROGRAMA DE CONTROLE MEDICO DE SAUDE OCUPACIONAL%' ")));
        //verifica se tem codigo de servico com ppra
        if (!empty($servico_pcmso)) {
            $codigo_servico_pcmso = $servico_pcmso['Servico']['codigo'];
        }

        return $codigo_servico_pcmso;
    }

    /**
     * [getPPRA description]
     * 
     * metodo para pegar o codigo de servico do ppra
     * @return [type] [description]
     */
    public static function getPPRA()
    {

        $Servico = &ClassRegistry::init('Servico');
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
        
        //pega o tipo de servico ppra
        $servico_ppra = $Servico->find('first', array('conditions' => array('tipo_servico' => 'G', "descricao LIKE '%PPRA (Programa de Preven%' ")));

        //verifica se tem codigo de servico com ppra
        if (!empty($servico_ppra)) {
            $codigo_servico_ppra = $servico_ppra['Servico']['codigo'];
        }

        return $codigo_servico_ppra;
    }

    function atualiza_status($codigo, $status, $codigo_servico = null, $data_inicio_vigencia = NULL, $vigencia_em_meses = NULL, $codigo_fornecedor = NULL)
    {
        // $this->log($codigo." -- ". $status ." -- ". $codigo_servico ." -- ". $data_inicio_vigencia . " -- " . $vigencia_em_meses  ,'debug');
        $conditions = array('codigo' => $codigo);
        // PD-154
        if(is_null($codigo_servico)){
            $Configuracao = &ClassRegistry::init('Configuracao');
            $codigo_servico = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');
        }

        if (!is_null($data_inicio_vigencia)) {
            if (!preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $data_inicio_vigencia)) {
                $data_inicio_vigencia = explode('-', $data_inicio_vigencia);
                $data_inicio_vigencia = array_reverse($data_inicio_vigencia);
                $data_inicio_vigencia = implode('-', $data_inicio_vigencia);
            }
        }

        // debug( $data_inicio_vigencia);
        // exit;

        $ordem = $this->find('first', array('conditions' => $conditions));
        if (!empty($ordem)) { //CLIENTE NÃO POSSUI PPRA.
            if (!empty($ordem['OrdemServico']['status_ordem_servico'])) {
                $dados = array(
                    'OrdemServico' =>
                    array(
                        'codigo' => $ordem['OrdemServico']['codigo'],
                        'status_ordem_servico' => $status,
                    )
                );

                if (!is_null($data_inicio_vigencia)) {
                    $dados['OrdemServico']['inicio_vigencia_pcmso'] = $data_inicio_vigencia;
                }
                if (!is_null($vigencia_em_meses)) {
                    $dados['OrdemServico']['vigencia_em_meses'] = $vigencia_em_meses;
                }
                if (!is_null($codigo_fornecedor)) {
                    $dados['OrdemServico']['codigo_fornecedor'] = $codigo_fornecedor;
                }

                // $this->log(print_r($dados,1),'debug');

                // debug($dados);
                // exit; 
                if ($this->atualizar($dados)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else { // CLIENTE JA POSSUI PPRA. CADASTRAR OS DADOS SOMENTE.
            return false;
        }
    } //FINAL FUNCTION atualiza_status    


    /**
     * [converteFiltroEmCondition description]
     * 
     * Metodo para gerar o filtro corretamente com os dados de suas respectivas models.
     * 
     * @param  [type] $data [description]
     * @return [type]       [description]
     * 
     */
    public function converteFiltroEmCondition_Vigencia($data)
    {
        $conditions = array_fill(0, 2, null);

        $conditions['Cliente.ativo'] = 1;
        // PD-154
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
        $codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');


        //cliente principal
        if (!empty($data['codigo_cliente'])) {
            $conditions['OR'] = array(
                'GruposEconomicosClientes.codigo_cliente' => $data['codigo_cliente'],
                'GruposEconomicos.codigo_cliente' => $data['codigo_cliente']
            );
        }

        //unidade
        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['GruposEconomicosClientes.codigo_cliente'] = $data['codigo_cliente_alocacao'];
        }

        //trazer os vigentes como default
        if (!isset($data['status']))
            $data['status'] = array('VI');

        //pesquisa quando o status tiver ativo
        if (isset($data['status']) && is_array($data['status']) && count($data['status']) > 0) {
            if (in_array("AV", $data['status'])) {
                //verifica se tem valor para setar o valor de hoje
                if (empty($data['data_inicio'])) {
                    $data['data_inicio'] = date('d/m/Y');
                }
                //verifica se tem o valor da data final
                if (empty($data['data_fim'])) {
                    $data['data_fim'] = date('d/m/Y', strtotime('+ 30 days'));
                }
                //gera a condição
                $conditions[0] = array(
                    "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) >= '" . AppModel::dateToDbDate2($data['data_inicio']) . "'",
                    "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) <= '" . AppModel::dateToDbDate2($data['data_fim']) . "'"
                );
            }
            if (in_array("VE", $data['status'])) {
                $conditions[1] = "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) < GETDATE()";
            }


            if (in_array("VI", $data['status'])) {
                if (in_array('AV', $data['status'])) {
                    $conditions[2] = "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) > '" . AppModel::dateToDbDate2($data['data_fim']) . "'";
                } else {
                    $conditions[2] = "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) >= GETDATE()";
                }
            }

            // if (in_array("SV", $data['status'])) {
            //     $conditions[2] = "DATEADD(DAY,-1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) >= GETDATE()";
            // }
        }
        //servico ppra ou pcmso
        if (!empty($data['produto'])) {
            $conditions['OrdemServicoItem.codigo_servico'] = $data['produto'];
        } else {
            $conditions['OrdemServicoItem.codigo_servico'] = array($codigo_servico_ppra, $codigo_servico_pcmso);
        }

        // debug($conditions);

        return $conditions;
    } //fim converfiltroemconditions_vigencia


    /**
     * [vigencia_ppra_pcmso description]
     * 
     * metodo para monta a query da vigencia do ppra e pcmso
     * 
     * @param  [type] $type    [description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function vigencia_ppra_pcmso($type, $options, $order_not = 0)
    {

        //debug($options);exit;
        //realiza os relacionamentos da para montar a query     
        $joins = array(
            array(
                'table' => 'ordem_servico_item',
                'alias' => 'OrdemServicoItem',
                'type' => 'INNER',
                'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
            ),
            array(
                'table' => 'servico',
                'alias' => 'Servico',
                'type' => 'INNER',
                'conditions' => array('Servico.codigo = OrdemServicoItem.codigo_servico')
            ),
            array(
                'table' => 'cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = OrdemServico.codigo_cliente')
            ),
            array(
                'table' => 'cliente_endereco',
                'alias' => 'ClienteEndereco',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
            ),
            array(
                'table' => 'grupos_economicos',
                'alias' => 'GruposEconomicos',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicos.codigo = OrdemServico.codigo_grupo_economico')
            ),
            array(
                'table' => 'grupos_economicos_clientes',
                'alias' => 'GruposEconomicosClientes',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicos.codigo = GruposEconomicosClientes.codigo_grupo_economico AND GruposEconomicosClientes.codigo_cliente = Cliente.codigo')
            ),
        );

        //campos
        $fields = array(
            'DISTINCT Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            'ClienteEndereco.cidade',
            'ClienteEndereco.estado_descricao',
            'ClienteEndereco.numero',
            'ClienteEndereco.complemento',
            'ClienteEndereco.logradouro',
            'ClienteEndereco.bairro',
            'OrdemServico.vigencia_em_meses',
            '',
            'OrdemServico.inicio_vigencia_pcmso',
        );

        $fields[] = "Servico.codigo";
        $fields[] = "Servico.descricao";
        $fields[] = "(SELECT COUNT(*) AS qtd FROM cliente_funcionario cf INNER JOIN  funcionario_setores_cargos fsc ON cf.codigo = fsc.codigo_cliente_funcionario AND fsc.codigo = (SELECT TOP 1 codigo FROM funcionario_setores_cargos f WHERE f.codigo_cliente_funcionario = cf.codigo  ORDER BY f.codigo DESC) WHERE cf.ativo = 1 AND fsc.codigo_cliente_alocacao = Cliente.codigo) as total_funcionario";
        $fields[] = "DATEADD(DAY, -1,DATEADD(MONTH, CONVERT(INT, OrdemServico.vigencia_em_meses), OrdemServico.inicio_vigencia_pcmso)) as final_vigencia";
        $fields[] = "(CASE WHEN Cliente.codigo_documento IS NULL OR Cliente.codigo_documento = '' THEN RHHealth.publico.ufn_formata_cnpj(Cliente.codigo_documento_real) WHEN Cliente.codigo_documento_real = '' OR Cliente.codigo_documento_real IS NULL THEN RHHealth.publico.ufn_formata_cnpj(Cliente.codigo_documento) ELSE RHHealth.publico.ufn_formata_cnpj(Cliente.codigo_documento_real)
            END) AS cnpj";

        $status = array(
            'AV' => 'CASE
                WHEN 
                    (DATEADD(DAY, -1, DATEADD(MONTH, CONVERT(int, [OrdemServico].[vigencia_em_meses]), [OrdemServico].[inicio_vigencia_pcmso])) >= \'' . (!empty($options['filtros']['data_inicio']) ? AppModel::dateToDbDate2($options['filtros']['data_inicio']) : null) . '\') 
                    AND
                    (DATEADD(DAY, -1, DATEADD(MONTH, CONVERT(int, [OrdemServico].[vigencia_em_meses]), [OrdemServico].[inicio_vigencia_pcmso])) <= \'' . (!empty($options['filtros']['data_fim']) ? AppModel::dateToDbDate2($options['filtros']['data_fim']) : null) . '\')
                    THEN \'A VENCER\'
                ELSE \'DESCONHECIDO\'
                END AS status',
            'VE' => 'CASE
                WHEN (DATEADD(DAY, -1, DATEADD(MONTH, CONVERT(int, [OrdemServico].[vigencia_em_meses]), [OrdemServico].[inicio_vigencia_pcmso])) < GETDATE()) THEN \'VENCIDO\'
                ELSE \'DESCONHECIDO\'
                END AS status',
            'VI' => 'CASE
                WHEN (DATEADD(DAY, -1, DATEADD(MONTH, CONVERT(int, [OrdemServico].[vigencia_em_meses]), [OrdemServico].[inicio_vigencia_pcmso])) >= GETDATE()) THEN \'VIGENTE\'
                ELSE \'DESCONHECIDO\'
                END AS status',
            'SV' => '\'SEM VIGÊNCIA\' AS status',
        );

        $fetch_conditions = array();
        $status_conditions = array();

        if (!empty($options['conditions'][0]) && !is_null($options['conditions'][0])) {
            $fetch_conditions[] = $options['conditions'][0];
            $status_conditions[] = "AV";
            unset($options['conditions'][0]);
        }
        if (!empty($options['conditions'][1]) && !is_null($options['conditions'][1])) {
            $fetch_conditions[] = $options['conditions'][1];
            $status_conditions[] = "VE";
            unset($options['conditions'][1]);
        }
        if (!empty($options['conditions'][2]) && !is_null($options['conditions'][2])) {
            $fetch_conditions[] = $options['conditions'][2];
            $status_conditions[] = "VI";
            unset($options['conditions'][2]);
        }

        $conditions = array(
            'OrdemServico.inicio_vigencia_pcmso IS NOT NULL',
            'OrdemServico.vigencia_em_meses IS NOT NULL',
            $options['conditions']
        );

        //montando a query para a consulta
        $query = array();
        /*** 
         * A resposta curta é que você está inadvertidamente pedindo CachingIteratorpara converter os sub-matrizes em strings durante a iteração. Para não      fazer isso, não use os sinalizadores CachingIterator::CALL_TOSTRINGou .CachingIterator::TOSTRING_USE_INNER

            Você pode definir nenhum sinalizador, usando 0como valor para o $flagsparâmetro, ou usar um sinalizador diferente: isso pode ser feito no construtor ou após a inicialização usando CachingIterator::setFlags(). 

            fonte: https://stackoverflow.com/questions/49927608/php-array-to-string-conversion-notice-with-cachingiterator
         */
        $interator = new CachingIterator(new ArrayIterator($fetch_conditions), 0);

        foreach ($interator as $k => $item) {
            $conditions[2][] = $item;

            $fields[10] = $status[$status_conditions[$k]];

            $query[] = $this->find('sql', compact('fields', 'joins', 'conditions'));
            end($conditions[2]);
            unset($conditions[2][key($conditions[2])]);
            if ($interator->hasNext())
                $query[] = "UNION ALL";
        }

        $sql = join(' ', $query);

        // debug($sql);exit;

        //verificacao se é sem vigencia para realizar o union all de todas as unidades
        if (isset($options['filtros']['status'])) {

            //varifica se tem o valor de sem vigência
            if (in_array("SV", $options['filtros']['status'])) {

                $fieldsSv = $fields;
                $fieldsSv[10] = $status['SV'];

                //implementa o uinion com os joins abaixo
                $joinsSV = array(
                    array(
                        'table' => 'grupos_economicos',
                        'alias' => 'GrupoEconomico',
                        'type' => 'INNER',
                        'conditions' => array('GrupoEconomico.codigo = OrdemServico.codigo_grupo_economico')
                    ),
                    array(
                        'table' => 'cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => array('Cliente.codigo = OrdemServico.codigo_cliente')
                    ),
                    array(
                        'table' => 'cliente_endereco',
                        'alias' => 'ClienteEndereco',
                        'type' => 'LEFT',
                        'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
                    ),
                    array(
                        'table' => 'ordem_servico_item',
                        'alias' => 'OrdemServicoItem',
                        'type' => 'INNER',
                        'conditions' => array('OrdemServicoItem.codigo_ordem_servico = OrdemServico.codigo')
                    ),
                    array(
                        'table' => 'servico',
                        'alias' => 'Servico',
                        'type' => 'INNER',
                        'conditions' => array('Servico.codigo = OrdemServicoItem.codigo_servico')
                    ),
                );

                $conditionsSv = array(
                    'Cliente.ativo' => '1',
                    'OrdemServico.inicio_vigencia_pcmso IS NULL',
                    'OrdemServico.vigencia_em_meses IS NULL',
                );

                // filtrar pela matriz                
                if (!empty($options['filtros']['codigo_cliente'])) {
                    $conditionsSv[] = 'GrupoEconomico.codigo_cliente = ' . $options['filtros']['codigo_cliente'];
                }
                //filtrar pela unidade
                if (!empty($options['filtros']['codigo_cliente_alocacao'])) {
                    $conditionsSv[] = 'OrdemServico.codigo_cliente = ' . $options['filtros']['codigo_cliente_alocacao'];
                }
                // PD-161
                $Configuracao = &ClassRegistry::init('Configuracao');
                $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');
                $codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');

                if (!empty($options['filtros']['produto'])) {
                    $conditionsSv[] = 'Servico.codigo = ' . $options['filtros']['produto'];
                } else {
                    $conditionsSv[] = 'Servico.codigo IN ('.$codigo_servico_ppra.','.$codigo_servico_pcmso.')';
                }

                // $this->Cliente = ClassRegistry::init('Cliente');
                $sql_sv = $this->find('sql', array(
                    'fields' => $fieldsSv,
                    'joins' => $joinsSV,
                    'conditions' => $conditionsSv
                ));
                //verifica se o filtro nao foi preenchido por outros status para que union all nao seja populado
                if (in_array("AV", $options['filtros']['status']) or in_array("VE", $options['filtros']['status']) or in_array("VI", $options['filtros']['status'])) {

                    $sql .= " UNION ALL ";
                    $sql .= $sql_sv;

                    /*** TRECHO MONTADO PARA A PEDIDO DO CHAMADO CDCT-569, O CLIENTE SOLICITOU QUE TAMBEM APARECA OS CLIENTES QUE NAO TENHAM PPRA OU PMCSO CADASTRADA COMO SEM VIGENCIA TB. */

                    if (!empty($options['filtros']['produto'])) {

                        if ($options['filtros']['produto'] == $codigo_servico_pcmso) { //PCMSO

                            $sql .= " UNION ALL ";

                            $joinsSVPendenciaPCMSO = array(
                                array(
                                    'table' => 'grupos_economicos',
                                    'alias' => 'GrupoEconomico',
                                    'type' => 'INNER',
                                    'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
                                ),
                                array(
                                    'table' => 'cliente',
                                    'alias' => 'Cliente',
                                    'type' => 'INNER',
                                    'conditions' => array('Cliente.codigo = GrupoEconomicoCliente.codigo_cliente')
                                ),
                                array(
                                    'table' => 'ordem_servico',
                                    'alias' => 'OrdemServico',
                                    'type' => 'LEFT',
                                    'conditions' => array('OrdemServico.codigo_grupo_economico = GrupoEconomico.codigo AND OrdemServico.codigo_cliente = GrupoEconomicoCliente.codigo_cliente AND OrdemServico.codigo IN ( SELECT codigo_ordem_servico FROM ordem_servico_item osi INNER JOIN ordem_servico os on osi.codigo_ordem_servico = os.codigo WHERE osi.codigo_servico = '.$codigo_servico_pcmso.' AND os.codigo_cliente = GrupoEconomicoCliente.codigo_cliente)')
                                ),
                                array(
                                    'table' => 'cliente_endereco',
                                    'alias' => 'ClienteEndereco',
                                    'type' => 'LEFT',
                                    'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
                                ),
                                array(
                                    'table' => 'servico',
                                    'alias' => 'Servico',
                                    'type' => 'INNER',
                                    'conditions' => array('Servico.codigo = '.$codigo_servico_pcmso)
                                ),
                            );

                            $conditionsSvPendenciaPCMSO = array(
                                'Cliente.ativo' => '1',
                                'OrdemServico.codigo IS NULL',
                            );

                            if (!empty($options['filtros']['codigo_cliente'])) {
                                $conditionsSvPendenciaPCMSO[] = 'GrupoEconomico.codigo_cliente = ' . $options['filtros']['codigo_cliente'];
                            }
                            //filtrar pela unidade
                            if (!empty($options['filtros']['codigo_cliente_alocacao'])) {
                                $conditionsSvPendenciaPCMSO[] = 'GrupoEconomicoCliente.codigo_cliente = ' . $options['filtros']['codigo_cliente_alocacao'];
                            }

                            $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
                            $sql_sv_pendencia_pcmso = $this->GrupoEconomicoCliente->find('sql', array(
                                'fields' => $fieldsSv,
                                'joins' => $joinsSVPendenciaPCMSO,
                                'recursive' => -1,
                                'conditions' => $conditionsSvPendenciaPCMSO
                            ));

                            $sql .= $sql_sv_pendencia_pcmso;
                        } else if ($options['filtros']['produto'] == $codigo_servico_ppra) { //PGR

                            $sql .= " UNION ALL ";

                            $joinsSVPendenciaPGR = array(
                                array(
                                    'table' => 'grupos_economicos',
                                    'alias' => 'GrupoEconomico',
                                    'type' => 'INNER',
                                    'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
                                ),
                                array(
                                    'table' => 'cliente',
                                    'alias' => 'Cliente',
                                    'type' => 'INNER',
                                    'conditions' => array('Cliente.codigo = GrupoEconomicoCliente.codigo_cliente')
                                ),
                                array(
                                    'table' => 'ordem_servico',
                                    'alias' => 'OrdemServico',
                                    'type' => 'LEFT',
                                    'conditions' => array('OrdemServico.codigo_grupo_economico = GrupoEconomico.codigo AND OrdemServico.codigo_cliente = GrupoEconomicoCliente.codigo_cliente AND OrdemServico.codigo IN ( SELECT codigo_ordem_servico FROM ordem_servico_item osi INNER JOIN ordem_servico os on osi.codigo_ordem_servico = os.codigo WHERE osi.codigo_servico = '.$codigo_servico_ppra.' AND os.codigo_cliente = GrupoEconomicoCliente.codigo_cliente)')
                                ),
                                array(
                                    'table' => 'cliente_endereco',
                                    'alias' => 'ClienteEndereco',
                                    'type' => 'LEFT',
                                    'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
                                ),
                                array(
                                    'table' => 'servico',
                                    'alias' => 'Servico',
                                    'type' => 'INNER',
                                    'conditions' => array('Servico.codigo = '.$codigo_servico_ppra)
                                ),
                            );

                            $conditionsSvPendenciaPGR = array(
                                'Cliente.ativo' => '1',
                                'OrdemServico.codigo IS NULL',
                            );

                            if (!empty($options['filtros']['codigo_cliente'])) {
                                $conditionsSvPendenciaPGR[] = 'GrupoEconomico.codigo_cliente = ' . $options['filtros']['codigo_cliente'];
                            }
                            //filtrar pela unidade
                            if (!empty($options['filtros']['codigo_cliente_alocacao'])) {
                                $conditionsSvPendenciaPGR[] = 'GrupoEconomicoCliente.codigo_cliente = ' . $options['filtros']['codigo_cliente_alocacao'];
                            }

                            $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
                            $sql_sv_pendencia_pgr = $this->GrupoEconomicoCliente->find('sql', array(
                                'fields' => $fieldsSv,
                                'joins' => $joinsSVPendenciaPGR,
                                'recursive' => -1,
                                'conditions' => $conditionsSvPendenciaPGR
                            ));

                            $sql .= $sql_sv_pendencia_pgr;
                        }
                    } else {

                        $sql .= " UNION ALL ";

                        $joinsSVPendenciaPCMSO = array(
                            array(
                                'table' => 'grupos_economicos',
                                'alias' => 'GrupoEconomico',
                                'type' => 'INNER',
                                'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
                            ),
                            array(
                                'table' => 'cliente',
                                'alias' => 'Cliente',
                                'type' => 'INNER',
                                'conditions' => array('Cliente.codigo = GrupoEconomicoCliente.codigo_cliente')
                            ),
                            array(
                                'table' => 'ordem_servico',
                                'alias' => 'OrdemServico',
                                'type' => 'LEFT',
                                'conditions' => array('OrdemServico.codigo_grupo_economico = GrupoEconomico.codigo AND OrdemServico.codigo_cliente = GrupoEconomicoCliente.codigo_cliente AND OrdemServico.codigo IN ( SELECT codigo_ordem_servico FROM ordem_servico_item osi INNER JOIN ordem_servico os on osi.codigo_ordem_servico = os.codigo WHERE osi.codigo_servico = '.$codigo_servico_pcmso.' AND os.codigo_cliente = GrupoEconomicoCliente.codigo_cliente)')
                            ),
                            array(
                                'table' => 'cliente_endereco',
                                'alias' => 'ClienteEndereco',
                                'type' => 'LEFT',
                                'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
                            ),
                            array(
                                'table' => 'servico',
                                'alias' => 'Servico',
                                'type' => 'INNER',
                                'conditions' => array('Servico.codigo = '.$codigo_servico_pcmso)
                            ),
                        );

                        $conditionsSvPendenciaPCMSO = array(
                            'Cliente.ativo' => '1',
                            'OrdemServico.codigo IS NULL',
                        );

                        if (!empty($options['filtros']['codigo_cliente'])) {
                            $conditionsSvPendenciaPCMSO[] = 'GrupoEconomico.codigo_cliente = ' . $options['filtros']['codigo_cliente'];
                        }
                        //filtrar pela unidade
                        if (!empty($options['filtros']['codigo_cliente_alocacao'])) {
                            $conditionsSvPendenciaPCMSO[] = 'GrupoEconomicoCliente.codigo_cliente = ' . $options['filtros']['codigo_cliente_alocacao'];
                        }

                        $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
                        $sql_sv_pendencia_pcmso = $this->GrupoEconomicoCliente->find('sql', array(
                            'fields' => $fieldsSv,
                            'joins' => $joinsSVPendenciaPCMSO,
                            'recursive' => -1,
                            'conditions' => $conditionsSvPendenciaPCMSO
                        ));

                        $sql .= $sql_sv_pendencia_pcmso;

                        $sql .= " UNION ALL ";

                        $joinsSVPendenciaPGR = array(
                            array(
                                'table' => 'grupos_economicos',
                                'alias' => 'GrupoEconomico',
                                'type' => 'INNER',
                                'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
                            ),
                            array(
                                'table' => 'cliente',
                                'alias' => 'Cliente',
                                'type' => 'INNER',
                                'conditions' => array('Cliente.codigo = GrupoEconomicoCliente.codigo_cliente')
                            ),
                            array(
                                'table' => 'ordem_servico',
                                'alias' => 'OrdemServico',
                                'type' => 'LEFT',
                                'conditions' => array('OrdemServico.codigo_grupo_economico = GrupoEconomico.codigo AND OrdemServico.codigo_cliente = GrupoEconomicoCliente.codigo_cliente AND OrdemServico.codigo IN ( SELECT codigo_ordem_servico FROM ordem_servico_item osi INNER JOIN ordem_servico os on osi.codigo_ordem_servico = os.codigo WHERE osi.codigo_servico = '.$codigo_servico_ppra.' AND os.codigo_cliente = GrupoEconomicoCliente.codigo_cliente)')
                            ),
                            array(
                                'table' => 'cliente_endereco',
                                'alias' => 'ClienteEndereco',
                                'type' => 'LEFT',
                                'conditions' => array('Cliente.codigo = ClienteEndereco.codigo_cliente and ClienteEndereco.codigo_tipo_contato = 2')
                            ),
                            array(
                                'table' => 'servico',
                                'alias' => 'Servico',
                                'type' => 'INNER',
                                'conditions' => array('Servico.codigo = '.$codigo_servico_ppra)
                            ),
                        );

                        $conditionsSvPendenciaPGR = array(
                            'Cliente.ativo' => '1',
                            'OrdemServico.codigo IS NULL',
                        );

                        if (!empty($options['filtros']['codigo_cliente'])) {
                            $conditionsSvPendenciaPGR[] = 'GrupoEconomico.codigo_cliente = ' . $options['filtros']['codigo_cliente'];
                        }
                        //filtrar pela unidade
                        if (!empty($options['filtros']['codigo_cliente_alocacao'])) {
                            $conditionsSvPendenciaPGR[] = 'GrupoEconomicoCliente.codigo_cliente = ' . $options['filtros']['codigo_cliente_alocacao'];
                        }

                        $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
                        $sql_sv_pendencia_pgr = $this->GrupoEconomicoCliente->find('sql', array(
                            'fields' => $fieldsSv,
                            'joins' => $joinsSVPendenciaPGR,
                            'recursive' => -1,
                            'conditions' => $conditionsSvPendenciaPGR
                        ));

                        $sql .= $sql_sv_pendencia_pgr;
                    }

                    /*** FIM DO TRECHO MONTADO PARA A PEDIDO DO CHAMADO CDCT-569, O CLIENTE SOLICITOU QUE TAMBEM APARECA OS CLIENTES QUE NAO TENHAM PPRA OU PMCSO CADASTRADA COMO SEM VIGENCIA TB. */
                } else {
                    $sql .= $sql_sv;
                }
                // debug($sql_sv); exit;
            } //fim in_array

        } //fim opntion filtros

        //seta a variavel order como branca para nao dar erro
        $order = '';
        //verifica se o parametro order_not esta ativo para nao colocar o order
        if (!$order_not) {
            if (isset($options['filtros']['ordenacao'])) {
                // debug('opa');
                if ($options['filtros']['ordenacao'] == 1) {
                    $order = " ORDER BY Cliente.nome_fantasia ASC;";
                } else if ($options['filtros']['ordenacao'] == 2) {
                    $order = " ORDER BY Cliente.nome_fantasia DESC;";
                }
            }
        } //fim order_not

        $sql .= $order;

        // pr($sql);

        if ($type == 'sql') {
            $results = $sql;
        } else {
            $results = $this->query($sql);
        }

        return $results;
    } //fim vigencia_ppra_pcmso


    //Envia o arquivo modelo 1 para os clientes
    /**
     * [envia_arquivo_vigencia_ppra_pcmso description]
     * 
     * metodo para montar o arquivo de vigencia a vencer e vencido para os usuarios que estao configurados para os alertas
     * 
     * @return [type] [description]
     */
    public function envia_arquivo_vigencia_ppra_pcmso()
    {

        //tira o tempo de limit para processamento
        set_time_limit(0);

        //recupera os usuarios que iram receber as vigencias configuradas como alertas
        $usuarios = $this->getUsersReceVigenciaPppraPcmso();

        // pr($usuarios);exit;

        //template do e-mail utilizado no envio do arquivo
        $template = 'envio_arquivo_vigencia_ppra_pcmso';
        $assunto = 'Vigência PPRA-PCMSO';

        $msgErro = "";

        //verifica se existe uruaios
        if (!empty($usuarios)) {

            //declara o array com os registros
            $reg = array();
            $contador = 0;

            //varre os usuarios
            foreach ($usuarios as $usuario) {

                //Se não possui e-mail de contato, não gera o arquivo
                if (empty($usuario['Usuario']['email'])) {
                    $msgErro = "Usuário não possui e-mail para enviar o arquivo de Vigência";
                }

                //pega os dados da vigencia caso exista
                $dados_vigencia = $this->get_vigencia_ppra_pcmso($usuario['Usuario']['codigo_cliente']);

                //verifica se tem dados para gerar o alerta da vigencia ppra pcmso
                if (!empty($dados_vigencia)) {
                    //monta os registros do alerta
                    $reg['Alerta']['codigo_cliente'] = null;
                    $reg['Alerta']['descricao'] = $assunto;
                    $reg['Alerta']['assunto'] = $assunto;
                    $reg['Alerta']['data_inclusao'] = date('Y-m-d H:i:s');
                    $reg['Alerta']['codigo_alerta_tipo'] = $usuario['UsuarioAlertaTipo']['codigo_alerta_tipo']; //codigo para o alerta para vigencia ppra pcmso
                    $reg['Alerta']['descricao_email'] = $this->montaEmail($usuario);
                    $reg['Alerta']['model'] = "Usuario"; //para processamento ao realizar o alerta
                    $reg['Alerta']['foreign_key'] = $usuario['Usuario']['codigo']; //codigo para buscar qual é o registro que vai ser processado

                    //verifica se o embarcador é o mesmo do transportador
                    if ($usuario['Usuario']['codigo_cliente'] != "") {

                        //pega o codigo do cliente
                        $reg['Alerta']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];

                        //realiza o insert na tabela dos alertas
                        if ($this->insereAlerta($reg)) {
                            $contador++;
                        }
                    } else {
                        //realiza o insert na tabela dos alertas
                        if ($this->insereAlerta($reg)) {
                            $contador++;
                        } //fim inseriu na alerta

                    } //fim verifica se emba = tran

                } //fim dados vigencia




                // $attachments = array();

                //gera o arquivo de vigencia
                // $arquivo = $this->gerar_arquivo_vigencia_ppra_pcmso($usuario['Usuario']['codigo_cliente']);

                //se o arquivo foi gerado
                //monta array de anexo
                // if(empty($arquivo)){
                //     throw new Exception("Não existe dados de vigencia ppra pcmso para disparar email", 1);
                // }

                // $attachments[$arquivo['nome_arquivo']] = $arquivo['url'].DS.$arquivo['nome_arquivo'];

                // debug($arquivo);continue;

                // if(!empty($attachments)){
                // $this->disparaEmail(null, $assunto, $template, $usuario['Usuario']['email'], json_encode($attachments));
                // echo("enviando e-mail COM anexo \n");
                // }
                /*else{                       
                    $assunto = 'Usuario ' . $usuario['Usuario']['nome'] . ' sem anexo Vigencia';
                    $this->disparaEmail(null, $assunto , null, $usuario['Usuario']['email'], null);
                    echo("enviando e-mail SEM anexo \n");
                }*/

                if ($msgErro != "") {
                    $this->log('Usuario:' . $usuario['Usuario']['nome'], 'debug');
                    $this->log($ex->getMessage(), 'debug');

                    $msgErro = "";
                }
            } //fim foreach

        } //fim if empty usuarios

        return true;
    } //FINAL FUNCTION envia_arquivo_vigencia_ppra_pcmso


    /**
     * [insereAlerta description]
     * 
     * metodo para inserir os alertas de entregas atrasadas
     * 
     * @return [type] [description]
     */
    private function insereAlerta($dados)
    {

        //instancia a tabela de alertas
        $this->alerta = ClassRegistry::init('Alerta');

        //array com os dados a serem inseridos na alerta
        if ($this->alerta->incluir($dados)) {
            return true;
        }

        return false;
    } //fim insereAlerta


    /**
     * [getUsersRecebimentoVigenciaPpraPcmso description]
     * 
     * metodo para pegar os usuarios que tem configurado que iram receber o alerta da vigencia a vencer e vencidas
     * 
     * @return [type] [description]
     */
    public function getUsersReceVigenciaPppraPcmso()
    {
        //carrega a model
        $UsuarioAlertaTipo = ClassRegistry::init('UsuarioAlertaTipo');


        //pega os campos
        $fields = array(
            'Usuario.codigo',
            'Usuario.email',
            'Usuario.nome',
            'Usuario.codigo_cliente',
            'UsuarioAlertaTipo.codigo_alerta_tipo',
        );

        //monta o join
        $joins = array(
            array(
                'table' => 'usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('Usuario.codigo = UsuarioAlertaTipo.codigo_usuario')
            ),
        );

        //tipos dos alertas
        $tipos = array(6, 7);
        //monta o filtro
        $conditions = array(
            'Usuario.email IS NOT NULL',
            'Usuario.alerta_email' => 1,
            'UsuarioAlertaTipo.codigo_alerta_tipo' => $tipos
        );

        //monta a chamada dos usuarios que iram ser disparados os alertas
        $usuarios = $UsuarioAlertaTipo->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));

        //retorna os usuarios, emails, e qual grupo economico se participar
        return $usuarios;
    } //fim getUsersReceVigenciaPppraAPcmso

    /**
     * [get_vigencia_ppra_pcmso description]
     * 
     * pega as vigencias do ppra e pcmso quando estão a vencer e vencido
     * 
     * @param  [int] $codigo_cliente [ codigo do cliente caso haja para saber quais vigencias estão a vencer e vencidos ]
     * @return [array]               [ array com os dados do ppra e pcmso ]
     */
    public function get_vigencia_ppra_pcmso($codigo_cliente = null)
    {
        //seta os filtros
        if (!is_null($codigo_cliente)) {
            $filtros['codigo_cliente'] = $codigo_cliente;
        }


        ####################################### À VENCER ########################################
        $filtros['status'] = array("AV"); //a vencer

        //configuracao da empresa
        //pega o codigo do produto/servico na tabela configuracao
        $this->bindModel(array('belongsTo' => array('Configuracao' => array('foreignKey' => false))));
        $configs = $this->Configuracao->find('list', array('conditions' => array('chave' => array('DIAS_ALERTA_VIGENCIA_PPRA_PCMSO')), 'fields' => array('chave', 'valor')));
        //pega o produto
        $dias = $configs['DIAS_ALERTA_VIGENCIA_PPRA_PCMSO'];

        //pega os dias passados
        $base_periodo = strtotime('+' . $dias . ' days', strtotime(Date('Y-m-d')));

        $filtros['data_inicio'] = date('d/m/Y');
        $filtros['data_fim'] = date('d/m/Y', $base_periodo);
        //monta os filtros relevantes
        $conditions = $this->converteFiltroEmCondition_Vigencia($filtros);

        //para recuperar todos os funcionarios até demitidos passar o segundo parâmetro como false
        $queryAV = $this->vigencia_ppra_pcmso('sql', compact('conditions', 'filtros'), 1);

        //destroi os indices
        unset($filtros['$data_inicio']);
        unset($filtros['$data_fim']);


        ####################################### VENCIDOS #######################################

        $filtros['status'] = array("VE"); //vencido

        //monta os filtros relevantes
        $conditions = $this->converteFiltroEmCondition_Vigencia($filtros);

        //para recuperar todos os funcionarios até demitidos passar o segundo parâmetro como false
        $queryVE = $this->vigencia_ppra_pcmso('sql', compact('conditions', 'filtros'), 1);

        //unifica as duas querys
        $query = $queryAV . ' UNION ALL ' . $queryVE;

        // print $query;exit;

        //executa as duas querys
        $dados = $this->query($query);

        return $dados;
    } //fim get_vigencia_ppra_pcmso


    /**
     * [gerar_arquivo_vigencia_ppra_pcmso description]
     * 
     * metodo para pegar os dados das vigencias dos ppra's e pcmso's
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function gerar_arquivo_vigencia_ppra_pcmso($codigo_cliente = null)
    {

        //pega os dados de vigencia
        $dados = $this->get_vigencia_ppra_pcmso($codigo_cliente);

        //verifica se tem registros para gerar o arquivo
        if (empty($dados)) {
            //não continua o processamento
            return true;
        }

        //gera o titulo do csv
        $planilha = "";
        $planilha .= utf8_decode('"Código do Cliente";"Razão Social";"Nome fantasia";"Cidade";"Estado";"Produto";"Funcionários alocados";"Início Vigência";"Período Vigência(em meses)";"Vencimento";"Status(À Vencer,Vencido)"') . "\n";

        //varre os dados das vigencias
        foreach ($dados as $key => $dado) {

            //verifica se o campo esta nulo caso esteja pula para o proximo
            if (is_null($dado[0]['codigo_cliente'])) {
                continue;
            }

            //seta corretamente o status
            $status = "Vigente";
            if ($dado[0]['status'] == 'a_vencer') {
                $status = "À Vencer";
            } else if ($dado[0]['status'] == 'vencido') {
                $status = "Vencido";
            }

            //monta os dados
            $linha  = $dado[0]['codigo_cliente'] . ';';
            $linha .= utf8_decode($dado[0]['razao_social']) . ';';
            $linha .= utf8_decode($dado[0]['nome_fantasia']) . ';';
            $linha .= $dado[0]['cidade'] . ';';
            $linha .= $dado[0]['estado'] . ';';
            $linha .= utf8_decode($dado[0]['produto']) . ';';
            $linha .= $dado[0]['total_funcionario'] . ';';
            $linha .= AppModel::dbDateToDate($dado[0]['inicio_vigencia']) . ';';
            $linha .= $dado[0]['vigencia_em_meses'] . ';';
            $linha .= AppModel::dbDateToDate($dado[0]['final_vigencia']) . ';';
            $linha .= utf8_decode($status) . ';';

            //dados da planilha
            $planilha .= $linha . "\n";
        } //fim foreach            

        return $planilha;
    } // gerar_arquivo_vigencia_ppra_pcmso

    /**
     * [montaEmail description]
     * 
     * monta o html que vai disparado no corpo do email
     * 
     * @param  [type] $dados [description]
     * @return [type]        [description]
     */
    public function montaEmail($dados)
    {
        //monta o link para disparar por email
        $link = $this->linkVigenciaPpraPcmso($dados['Usuario']['codigo_cliente']);

        //monta o html para disparar o alerta
        $html = utf8_encode('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">

                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            </head>

                            <body>
                                <div style="clear:both;">
                                    <div> <img style="display:block;" src="http://portal.rhhealth.com.br/portal/img/logo-rhhealth.png" style="float:left;">
                                        <hr style="border:1px solid #EEE; display:block;" /> </div>
                                        <div style="background: #fff; float:none; height: 10px; margin-top:5px; padding:8px 10px 0 0; width:99%;"></div>
                                    </div>
                                    <div style="clear:both;padding-top:50px;padding-left:50px;width:98.4%;min-height:300px;">
                                        <p>Olá, <strong>' . $dados['Usuario']['nome'] . '</strong>, tudo bem? Como você está?</p>                                        
                                        <p>Preciso de um minutinho da sua atenção para passar um recado importante.</p>                                        
                                        <p>Daqui 60 dias, documentos legais essenciais vão vencer. E, claro, sabemos da importância de manter isso em dia.</p>
                                        <p>Estou avisando porque é uma marca registrada da RH Health sempre antecipar essas situações e manter o máximo de transparência possível na nossa relação.</p>
                                        <p>Se precisar de mais detalhes sobre o assunto, <a href="' . $link . '" target="_blank">clique aqui</a>.</p>
                                        <p>Em breve, a nossa CS vai entrar em contato para falar mais sobre isso, tudo bem?</p>
                                        <p>Até lá, claro, se tiver qualquer dúvida, não pense duas vezes antes de nos procurar:</p>
                                        
                                        <p>E-MAIL</p>
                                        <p>relacionamento@rhhealth.com.br</p>
                                        
                                        <p>TELEFONE</p>
                                        <p>(11) 5079-2550</p>
                                        
                                        <p>Obrigado pela atenção!</p>
                                    
                                    <p>Um abraço,</p>
                                    <b>Equipe RH Health</b><br />
                                    <a href="http://www.rhhealth.com.br" target="_blank">www.rhhealth.com.br</a><br />
                                </div>
                            </body>
                            </html>');

        return $html;
    }

    /**
     * @param  [codigo_cliente] codigo do clietne que irá gerar o hash
     * @param  [mes] mes
     * @param  [ano] ano
     * @return [link] link para acessar o relatorio de demonstrativo
     */
    private function linkVigenciaPpraPcmso($codigo_cliente = null)
    {
        //verifica se codigo_cliente está nulo para trazer todas as vigencias a vencer e vencidas.
        if (is_null($codigo_cliente)) {
            $codigo_cliente = 'all';
        } else {
            $codigo_cliente = "'{$codigo_cliente}'";
        }

        //monta o hash para colocar no link
        $hash = Comum::encriptarLink($codigo_cliente);
        //monta o host
        $host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));

        //monta o link
        $link_vigencia = "http://{$host}/portal/clientes/gera_arquivo_vigencia_ppra_pcmso?key=" . urlencode($hash);

        //retorno o link a ser acessado
        return $link_vigencia;
    }

    /***
     * @param $codigo_cliente int codigo do cliente da ordem de serviço
     * @param $tipo string tipo da ordem de serviço: PPRA ou PCMSO
     * @return array dados da consulta ou null caso não traz nada!
     */
    public function busca_status($codigo_cliente, $tipo = 'PPRA')
    {
        $codigo_servico = ($tipo == 'PPRA' ? OrdemServico::PPRA : OrdemServico::PCMSO);
        $sql = "SELECT TOP(1)
                    [Cliente].[codigo] AS [Cliente_codigo],
                    [OrdemServico].[status_ordem_servico] AS [OrdemServico_status]
                FROM [Cliente] AS [Cliente] 
                INNER JOIN [ordem_servico] AS [OrdemServico] ON ([OrdemServico].[codigo_cliente] = [Cliente].[codigo]
                AND [OrdemServico].[codigo] IN (SELECT codigo_ordem_servico FROM ordem_servico_item WHERE codigo_servico = " . $codigo_servico . "))
                WHERE [Cliente].[codigo] = " . $codigo_cliente;

        $data = $this->query($sql);

        return (isset($data[0])) ? $data[0] : false;
    }

    public function getPPRAByCodigoCliente($codigo_cliente = null)
    {

        // PD-154
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');

        if(empty($codigo_cliente)) {

            return $codigo_servico_ppra;
        }

        $Servico = &ClassRegistry::init('Servico');
        $GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
        
        $clienteMatrizArr = $GrupoEconomicoCliente->getMatriz($codigo_cliente);

        if(!empty($clienteMatrizArr['ClienteMatriz']['codigo'])) {

            $codigo_cliente = $clienteMatrizArr['ClienteMatriz']['codigo'];
        }
        
        //pega o tipo de servico ppra
        $servico_ppra = $Servico->find('first', array(
            'joins' => array(
                array(
                    'table' => 'produto_servico',
                    'alias' => 'ProdutoServico',
                    'conditions' => 'ProdutoServico.codigo_servico = Servico.codigo',
                ),
                array(
                    'table' => 'cliente_produto',
                    'alias' => 'ClienteProduto',
                    'conditions' => 'ClienteProduto.codigo_produto = ProdutoServico.codigo_produto',
                ),
            ),
            'conditions' => array(
                'Servico.tipo_servico' => 'G', "descricao LIKE '%PPRA (Programa de Preven%' ",
                'Servico.ativo' => 1,
                'ClienteProduto.codigo_cliente' => $codigo_cliente
            )
        ));

        //verifica se tem codigo de servico com ppra
        if (!empty($servico_ppra['Servico']['codigo'])) {
            $codigo_servico_ppra = $servico_ppra['Servico']['codigo'];
        }

        return $codigo_servico_ppra;
    }

    public function getPCMSOByCodigoCliente($codigo_cliente = null)
    {

        
        // PD-154
        $Configuracao = &ClassRegistry::init('Configuracao');
        $codigo_servico_pcmso = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PCMSO');

        if(empty($codigo_cliente)) {

            return $codigo_servico_pcmso;
        }

        $Servico = &ClassRegistry::init('Servico');
        $GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
        
        $clienteMatrizArr = $GrupoEconomicoCliente->getMatriz($codigo_cliente);

        if(!empty($clienteMatrizArr['ClienteMatriz']['codigo'])) {

            $codigo_cliente = $clienteMatrizArr['ClienteMatriz']['codigo'];
        }               
        
        //pega o tipo de servico ppra
        $servico_pcmso = $Servico->find('first', array(
            'joins' => array(
                array(
                    'table' => 'produto_servico',
                    'alias' => 'ProdutoServico',
                    'conditions' => 'ProdutoServico.codigo_servico = Servico.codigo',
                ),
                array(
                    'table' => 'cliente_produto',
                    'alias' => 'ClienteProduto',
                    'conditions' => 'ClienteProduto.codigo_produto = ProdutoServico.codigo_produto',
                ),
            ),
            'conditions' => array(
                'Servico.tipo_servico' => 'G', "descricao LIKE '%PCMSO - PROGRAMA DE CONTROLE MEDICO DE SAUDE OCUPACIONAL%' ",
                'Servico.ativo' => 1,
                'ClienteProduto.codigo_cliente' => $codigo_cliente
            )
        ));

        //verifica se tem codigo de servico com ppra
        if (!empty($servico_pcmso['Servico']['codigo'])) {
            $codigo_servico_pcmso = $servico_pcmso['Servico']['codigo'];
        }

        return $codigo_servico_pcmso;
    }

    //%PCMSO - PROGRAMA DE CONTROLE MEDICO DE SAUDE OCUPACIONAL%
}//FINAL CLASS OrdemServico