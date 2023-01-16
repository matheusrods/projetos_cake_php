<?php
App::import('model', 'StatusImportacao');

/*
 * Model da Manipulação das tabelas de cabeçalho e registros da importação de pedidos
 */
class ImportacaoPedidosExame extends AppModel {
    var $name           = 'ImportacaoPedidosExame';
    var $tableSchema    = 'dbo';
    var $databaseTable  = 'RHHealth';
    var $useTable       = 'importacao_pedidos_exames';
    var $primaryKey     = 'codigo';
    var $actsAs         = array('Secure');

    /**
     * Método de inclusão de novo de dados para importação de pedidos de exames
     * @param string $path Caminho de pastas do Arquivo que será importado
     * @param atring $arquivo nome do Arquivo que será importado
     * @param integer $codigo_cliente Código de Identificação do Cliente/Empresa para onde os pedidos serão gerados
     * @return void
     */
    function incluir($path, $arquivo, $codigo_cliente) {
        if (file_exists($path.$arquivo)) {

            $this->bindModel(array(
                'belongsTo' => array(
                    'GrupoEconomicoCliente' => array('foreignKey' => 'codigo')
                ),
                'hasMany' => array(
                    'ImportacaoPedidosExamesRegistros' => array('foreignKey' => 'codigo_importacao_pedidos_exames')
                )
            ));
            $grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
            if ($grupo_economico) {
                try {
                    $importacao_pedidos = array(
                        'codigo_grupo_economico' => $grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'],
                        'nome_arquivo' => $arquivo,
                        'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
                    );
                    $this->create();
                    $this->query('BEGIN TRANSACTION');
                    if (!parent::incluir($importacao_pedidos)) throw new Exception("Erro ao importar arquivo de pedidos de exame", 1);
                    $columns = $this->ImportacaoPedidosExamesRegistros->schema();
                    $columns = array_keys($columns);
                    
                    $handle = fopen($path.$arquivo, "r");
                    if ($handle) {
                        $registro = 0;
                        while (!feof($handle)) {
                            $registro++;
                            $linha = utf8_encode(trim(fgets($handle)));
                            if (trim($linha) != '') {
                                $dados = explode(';', $linha );
                                if ($registro > 1) {
                                    if (count($dados) == 13) { // 13 colunas existentes no arquivo de importação
                                        $registro = $this->carregaColunas($dados, $this->id);
                                        if (!$this->ImportacaoPedidosExamesRegistros->incluir($registro)) {

                                            $msg = "Erro ao incluir registro do arquivo de importação de pedidos de exame.";

                                            //valida data
                                            if(!empty($registro['data_solicitacao'])) {

                                                //data separada
                                                $ano = substr($registro['data_solicitacao'],0,4);
                                                $mes = substr($registro['data_solicitacao'],5,2);
                                                $dia = substr($registro['data_solicitacao'], 8);

                                                if(!checkdate($mes, $dia, $ano)) {
                                                    $msg .= " Data Solicitação invalida: " . $registro['data_solicitacao'];
                                                }

                                            }
                                            else {
                                                $msg .= " Data Solicitação em branco";
                                            }

                                            //valida data
                                            if(!empty($registro['data_realizacao'])) {

                                                //data separada
                                                $ano_r = substr($registro['data_realizacao'],0,4);
                                                $mes_r = substr($registro['data_realizacao'],5,2);
                                                $dia_r = substr($registro['data_realizacao'], 8);

                                                if(!checkdate($mes_r, $dia_r, $ano_r)) {
                                                    $msg .= " Data Realização invalida: " . $registro['data_realizacao'];
                                                }

                                            }
                                            else {
                                                $msg .= " Data Realização em branco";
                                            }

                                            throw new Exception($msg, 1);
                                        }
                                    } else {
                                        throw new Exception("Arquivo inválido, não possui as 13 colunas do modelo", 1);
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
     * @param integer $codigo_importacao_pedido_exame Código de identificação do arquivo importado
     * @return array Array onde cada coluna tem seu dados respectivo da linha lida do arquivo importado
     */
    function carregaColunas($dados, $codigo_importacao_pedido_exame) {
        $registro = array(
            'codigo_importacao_pedidos_exames' => $codigo_importacao_pedido_exame,
            'codigo_status_importacao' => StatusImportacao::SEM_PROCESSAR
        );

        foreach ($dados as $key => $value)
            $dados[$key] = trim($dados[$key]);

        $registro['nome_empresa']         = $dados[0];
        $registro['nome_unidade']         = $dados[1];
        $registro['nome_setor']           = $dados[2];
        $registro['nome_cargo']           = $dados[3];
        $registro['cpf']                  = Comum::soNumero($dados[4]);
        $registro['data_solicitacao']     = AppModel::dateToDbDate2($dados[5]);
        $registro['tipo_item_pedido']     = $dados[6];
        $registro['nome_exame']           = $dados[7];
        $registro['tipo_exame']           = $dados[8];
        $registro['fornecedor']           = Comum::soNumero($dados[9]);
        $registro['data_realizacao']      = AppModel::dateToDbDate2($dados[10]);
        $registro['resultado_exame']      = $dados[11];
        $registro['resultado_observacao'] = $dados[12];

        return $registro;
    }

    /**
     * Método de deleção de todos os registros de linhas de um arquivo importado
     * @param integer $codigo Código de identificação de arquivo importado
     * @return avoid
     */
    function excluir($codigo) {
        $this->ImportacaoPedidosExamesRegistros = & ClassRegistry::init('ImportacaoPedidosExamesRegistros');
        try {
            $arquivo = $this->carregar($codigo);
            if ($arquivo[$this->name]['codigo_status_importacao'] != StatusImportacao::SEM_PROCESSAR) {
                return false;
            }
            $this->query('BEGIN TRANSACTION');

            $deleta_itens = $this->ImportacaoPedidosExamesRegistros->deleteAll(array('codigo_importacao_pedidos_exames' => $codigo),false);
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
     * registro de Pedido de Exame Médico do Funcionário
     * IMPORTANTE: Este processo é executado através do console do cake, onde um arquivo importacao.php na pasta de shells do cake é executado, e * processamento não fica vinculado a requisição do navegador, e é processado num processo separado em background.
     * @param integer $codigo Código de identificação de arquivo importado
     * @return avoid
     */
    function importar($codigo) {
        $Exame =& ClassRegistry::init("Exame");
        $MotivoCancelamento =& ClassRegistry::init("MotivoCancelamento");
        $importacao_pedidos_exame = $this->read(null, $codigo);
        if ($importacao_pedidos_exame) {
            if ($importacao_pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) {
                echo 'GrupoEconomico '.$importacao_pedidos_exame['ImportacaoPedidosExame']['codigo_grupo_economico']."\n";
                $this->set('codigo_status_importacao', StatusImportacao::PROCESSANDO);
                $this->save();
                $conditions = array('codigo_importacao_pedidos_exames' => $importacao_pedidos_exame['ImportacaoPedidosExame']['codigo']);
                $this->bindModel(array('hasMany' => array('ImportacaoPedidosExamesRegistros' => array('foreignKey' => 'codigo_importacao_pedidos_exames'))));
                $order = array('ImportacaoPedidosExamesRegistros.codigo');
                $registros = $this->ImportacaoPedidosExamesRegistros->findImportacao('all', compact('conditions', 'order'));
                
                foreach ($registros as $key => $registro) {
                    foreach ($registro[0] as $campo => $value) {
                        $registro[0][$campo] = trim($registro[0][$campo]);
                    }
                    try {
                        echo "Processar registro ".$key."({$registro[0]['codigo']})"."\n";
                        $validaCadastrosRegistro = $this->ImportacaoPedidosExamesRegistros->alertasRegistroCadastros($registro);
                        if(empty($validaCadastrosRegistro['validacoes'])) {
                            $this->query('BEGIN TRANSACTION');
                            
                            $conditions = array('conditions' => array(
                                'UPPER(Exame.descricao)' => strtoupper($registro[0]['nome_exame'])
                            ));

                            if($codigo_exame = $Exame->find('first',$conditions)) {
                                $codigo_exame = $codigo_exame['Exame']['codigo'];
                            }

                            $pedido_exame = array(
                                'codigo_empresa'            => $importacao_pedidos_exame['ImportacaoPedidosExame']['codigo_empresa'],
                                'codigo_cliente_funcionario'=> $registro[0]['codigo_cliente_funcionario'],
                                'codigo_funcionario'        => $registro[0]['codigo_funcionario'],
                                'exame_admissional'         => $registro[0]['exame_admissional'],
                                'exame_periodico'           => $registro[0]['exame_periodico'],
                                'exame_demissional'         => $registro[0]['exame_demissional'],
                                'exame_retorno'             => $registro[0]['exame_retorno'],
                                'exame_mudanca'             => $registro[0]['exame_mudanca'],
                                'exame_monitoracao'         => $registro[0]['exame_monitoracao'],
                                'qualidade_vida'            => $registro[0]['qualidade_vida'],
                                'pontual'                   => $registro[0]['pontual'],
                                'resultado'                 => $registro[0]['resultado'],
                                'resultado_observacao'      => $registro[0]['resultado_observacao'],
                                'codigo_func_setor_cargo'   => $registro[0]['codigo_func_setor_cargo'],
                                'cpf'                       => $registro[0]['cpf'],
                                'data_solicitacao'          => $registro[0]['data_solicitacao'],
                                'codigo_grupo_economico'    => $importacao_pedidos_exame['ImportacaoPedidosExame']['codigo_grupo_economico'],
                                'codigo_fornecedor'         => $registro[0]['codigo_fornecedor'],
                                'codigo_exame'              => $codigo_exame,
                                'data_realizacao'           => $registro[0]['data_realizacao'],
                                'codigo_cliente'            => $registro[0]['codigo_cliente_alocacao']
                            );

                            $retorno_pedido_exame = $this->ImportacaoPedidosExamesRegistros->importarPedidoExame($pedido_exame);
                            $codigo_pedido_exame = $retorno_pedido_exame['codigo_pedido_exame'];
                            if (!$codigo_pedido_exame) {
                                throw new Exception("Erro no Processamento do Pedido de Exame Médico " . $retorno_pedido_exame['invalidFields'], 1);
                            }
                            $this->ImportacaoPedidosExamesRegistros->read(null, $registro[0]['codigo']);
                            $this->ImportacaoPedidosExamesRegistros->set('data_processamento', date('Y-m-d H:i:s'));
                            if($retorno_pedido_exame['invalidFields']) {
                                $this->ImportacaoPedidosExamesRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
                                $this->ImportacaoPedidosExamesRegistros->set('observacao', $retorno_pedido_exame['invalidFields']);
                            } else {
                                $this->ImportacaoPedidosExamesRegistros->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
                                $this->ImportacaoPedidosExamesRegistros->set('codigo_pedido_exame', $codigo_pedido_exame); //seta o codigo do pedido de exame

                                echo "Importado registro ".$key."({$registro[0]['codigo']})"."\n";
                            
                            }
                            $this->ImportacaoPedidosExamesRegistros->save();
                            $this->commit();
                        } else {
                            echo "Registro inválido ".$key."({$registro[0]['codigo']})"."\n";
                            $this->ImportacaoPedidosExamesRegistros->read(null, $registro[0]['codigo']);
                            $this->ImportacaoPedidosExamesRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
                            $this->ImportacaoPedidosExamesRegistros->set('data_processamento', date('Y-m-d H:i:s'));
                            $this->ImportacaoPedidosExamesRegistros->set('observacao', implode(',',$validaCadastrosRegistro['validacoes']));
                            $this->ImportacaoPedidosExamesRegistros->save();
                        }
                    } catch (Exception $ex) {
                        $dbo = $this->getDataSource();
                        $logs = $dbo->getLog();
                        $this->log($logs, 'debug');
                        $this->rollback();
                        $this->ImportacaoPedidosExamesRegistros->read(null, $registro[0]['codigo']);
                        $this->ImportacaoPedidosExamesRegistros->set('codigo_status_importacao', StatusImportacao::ERRO);
                        $this->ImportacaoPedidosExamesRegistros->set('data_processamento', date('Y-m-d H:i:s'));
                        $this->ImportacaoPedidosExamesRegistros->set('observacao', $ex->getMessage());
                        $this->ImportacaoPedidosExamesRegistros->save();
                    }
                }
                $this->set('codigo_status_importacao', StatusImportacao::PROCESSADO);
                $this->set('data_processamento', date('Y-m-d H:i:s'));
                $this->save();
            }
        } else {
            echo "Importação de Pedidos de Exame não encontrada ({$codigo})"."\n";
            $dbo = $this->getDataSource();
            $logs = $dbo->getLog();
            pr($logs);
        }
    }
}
