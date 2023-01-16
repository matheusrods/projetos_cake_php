<?php
class Ghe extends AppModel
{
    public $name = 'Ghe';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'ghe';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');

    const GHE_APROVADA = 'GHE Aprovada';
    const DIVERGENCIA_APONTADA = 'Divergência Apontada';
    const AVALIAR_GHE = 'Avaliar GHE';

    public $validate = array(
        'chave_ghe' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a chave GHE',
            'required' => true,
        ),
        'aprho_parecer_tecnico' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o APRHO parecer técnico',
            'required' => true,
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a unidade',
            'required' => true,
        ),
    );

    public function getByCodigo($codigo)
    {
        $modelCscGhe = ClassRegistry::init('CscGhe');
        $modelArrtpariGhe = ClassRegistry::init('ArrtpariGhe');

        $ghe = $this->find(
            'first',
            array(
                'fields' => array(
                    'Ghe.codigo',
                    'Ghe.chave_ghe',
                    'Ghe.aprho_parecer_tecnico',
                    'Ghe.codigo_usuario_inclusao',
                    'Ghe.codigo_usuario_alteracao',
                    'Ghe.data_inclusao',
                    'Ghe.data_alteracao',
                    'Ghe.codigo_cliente',
                    'Ghe.ativo',
                    'Cliente.codigo',
                    'Cliente.razao_social',
                ),
                'joins' => array(
                    array(
                        'table' => 'RHHealth.dbo.cliente',
                        'alias' => 'Cliente',
                        'type' => 'LEFT',
                        'conditions' => 'Cliente.codigo = Ghe.codigo_cliente',
                    ),
                ),
                'conditions' => array('Ghe.codigo' => $codigo),
            )
        );

        if (empty($ghe)) {
            return array();
        }

        $ghe['Ghe']['codigo_cliente_name'] = $ghe['Cliente']['razao_social'];

        $ghe['Ghe']['setores'] = $modelCscGhe->getSetoresByCodigoGhe($codigo, $ghe['Cliente']['codigo']);
        $ghe['Ghe']['riscos_impactos'] = $modelArrtpariGhe->getRiscosImpactosByCodigoGhe($codigo);

        return $ghe;
    }

    public function getListaGhe($filtros = null)
    {
        $fields = array(
            'Ghe.codigo',
            'Ghe.chave_ghe',
            'Ghe.aprho_parecer_tecnico',
            'Ghe.codigo_usuario_inclusao',
            'Ghe.codigo_usuario_alteracao',
            'Ghe.data_inclusao',
            'Ghe.data_alteracao',
            'Ghe.codigo_cliente',
            'Ghe.ativo',
            'Cliente.codigo',
            'Cliente.razao_social',
        );

        $conditions = $this->converteFiltroEmCondition($filtros);      

        $joins = array(
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'LEFT',
                'conditions' => 'Cliente.codigo = Ghe.codigo_cliente',
            ),
        );

        $ghes = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'Ghe.codigo desc',
        );

        return $ghes;
    }

    public function cadastrar($dados)
    {
        $modelClienteSetorCargo = ClassRegistry::init('ClienteSetorCargo');
        $modelCscGhe = ClassRegistry::init('CscGhe');
        $modelArrtpariGhe = ClassRegistry::init('ArrtpariGhe');

        try {
            $this->query('begin transaction');

            $dadosGhe = array();
            $dadosGhe['codigo_cliente'] = $dados['Ghe']['codigo_cliente'];
            $dadosGhe['chave_ghe'] = $dados['Ghe']['chave_ghe'];
            $dadosGhe['aprho_parecer_tecnico'] = $dados['Ghe']['aprho_parecer_tecnico'];
            $dadosGhe['ghe_status'] = self::AVALIAR_GHE;

            $cadastroGhe = parent::incluir($dadosGhe);

            if (!$cadastroGhe) {
                throw new Exception('Não incluiu o ghe!');
            }

            $csc = array();

            $quantidadeSetoresCargos = 0;

            if (isset($dados['Ghe']['setores'])) {
                foreach ($dados['Ghe']['setores'] as $setor) {
                    if (!empty($setor['codigo_setor']) && !empty($setor['codigo_cargo']) && $this->id) {
                        foreach ($setor['codigo_cargo'] as $cargo) {
                            $existeClienteSetorCargo = $modelClienteSetorCargo->find('first',
                                array(
                                    'conditions' => array(
                                        'ClienteSetorCargo.codigo_cliente' => $dados['Ghe']['codigo_cliente'],
                                        'ClienteSetorCargo.codigo_setor' => $setor['codigo_setor'],
                                        'ClienteSetorCargo.codigo_cargo' => $cargo,
                                    ),
                                )
                            );

                            if (empty($existeClienteSetorCargo)) {
                                throw new Exception("Código {$cargo} do Cargo inexistente");
                            }

                            $csc = array();
                            $csc['codigo_ghe'] = $this->id;
                            $csc['codigo_clientes_setores_cargos'] = $existeClienteSetorCargo['ClienteSetorCargo']['codigo'];

                            if (!$modelCscGhe->incluir($csc)) {
                                throw new Exception('Não incluiu o setor e cargo no ghe!');
                            }

                            $quantidadeSetoresCargos++;
                        }
                    }
                }
            }

            if ($quantidadeSetoresCargos === 0) throw new Exception("É obrigatório pelo menos um setor e cargo");

            if (
                !isset($dados['Ghe']['codigo_arrtpa_ri'])
                || count($dados['Ghe']['codigo_arrtpa_ri']) === 0
            ) throw new Exception("É obrigatório pelo menos um risco/impacto");

            if (isset($dados['Ghe']['codigo_arrtpa_ri'])) {
                foreach ($dados['Ghe']['codigo_arrtpa_ri'] as $valor) {
                    if (!empty($valor) && $this->id) {
                        $arrtpariGhe = array();
                        $arrtpariGhe['codigo_ghe'] = $this->id;
                        $arrtpariGhe['codigo_arrtpa_ri'] = $valor;

                        if (!$modelArrtpariGhe->incluir($arrtpariGhe)) {
                            throw new Exception('Não incluiu o no ghe!');
                        }
                    }
                }
            }

            $this->commit();

            return true;
        } catch (Exception $e) {
            $this->rollback();

            return false;
        }
    }

    public function editar($dados)
    {
        $modelClienteSetorCargo = ClassRegistry::init("ClienteSetorCargo");
        $modelCscGhe = ClassRegistry::init("CscGhe");
        $modelArrtpariGhe = ClassRegistry::init("ArrtpariGhe");

        try {
            $this->query("begin transaction");
            
            $ghe = $this->read(null, $dados["Ghe"]["codigo"]);

            $ghe["Ghe"]["codigo_cliente"] = $dados["Ghe"]["codigo_cliente"];
            $ghe["Ghe"]["chave_ghe"] = $dados["Ghe"]["chave_ghe"];
            $ghe["Ghe"]["aprho_parecer_tecnico"] = $dados["Ghe"]["aprho_parecer_tecnico"];
            $ghe["Ghe"]["ativo"] = $dados["Ghe"]["ativo"];

            if (!parent::atualizar($ghe)) {
                throw new Exception("Não foi possível atualizar o GHE");
            }

            $setoresCargosSelecionados = isset($dados["Ghe"]["setores"]) && is_array($dados['Ghe']['setores'])
                ? $dados["Ghe"]["setores"]
                : array();

            $setoresCargosFormatados = array();

            # Formatando dados para que fique mais fácil a implementação de remover/adicionar vínculos
            foreach ($setoresCargosSelecionados as $setorCargo) {
                if (!empty($setorCargo["codigo_cargo"])) {
                    if (!isset($setoresCargosFormatados[$setorCargo["codigo_setor"]])) $setoresCargosFormatados[$setorCargo["codigo_setor"]] = array();
                    
                    foreach ($setorCargo["codigo_cargo"] as $codigoCargo) {
                        if (array_search($codigoCargo, $setoresCargosFormatados[$setorCargo["codigo_setor"]]) !== false) continue;

                        $setoresCargosFormatados[$setorCargo["codigo_setor"]][] = $codigoCargo;
                    }
                }
            }
            
            $setoresCargosCadastrados = $modelCscGhe->find("all",
                array(
                    "fields" => array(
                        "CscGhe.codigo",
                        "ClientesSetoresCargos.codigo_setor",
                        "ClientesSetoresCargos.codigo_cargo",
                    ),
                    "conditions" => array(
                        "CscGhe.codigo_ghe" => $ghe['Ghe']['codigo'],
                    ),
                    "joins" => array(
                        array(
                            "table" => "clientes_setores_cargos",
                            "alias" => "ClientesSetoresCargos",
                            "type" => "INNER",
                            "conditions" => array(
                                "ClientesSetoresCargos.codigo = CscGhe.codigo_clientes_setores_cargos"
                            )
                        )
                    )
                )
            );

            # Remover vínculos do GHE com setores e cargos
            foreach ($setoresCargosCadastrados as $setorCargo) {
                $index = array_search(
                    $setorCargo["ClientesSetoresCargos"]["codigo_cargo"], 
                    isset($setoresCargosFormatados[$setorCargo["ClientesSetoresCargos"]["codigo_setor"]]) 
                        ? $setoresCargosFormatados[$setorCargo["ClientesSetoresCargos"]["codigo_setor"]]
                        : array()
                );

                if ($index === false) {
                    if (!$modelCscGhe->excluir($setorCargo["CscGhe"]["codigo"])) {
                        throw new Exception("Não foi possível remover vinculo do GHE com os setores e cargos");
                    }
                } else {
                    unset($setoresCargosFormatados[$setorCargo["ClientesSetoresCargos"]["codigo_setor"]][$index]);
                }
            }

            # Removendo setores sem cargos
            $setoresCargosFormatados = array_filter($setoresCargosFormatados);

            if (count($setoresCargosFormatados) === 0) throw new Exception("É obrigatório pelo menos um setor e cargo");

            # Adicionar vínculos do GHE com os setores e cargos
            foreach ($setoresCargosFormatados as $codigoSetor => $cargos) {
                foreach ($cargos as $cargo) {
                    $clienteSetorCargo = $modelClienteSetorCargo->find('first',
                        array(
                            'conditions' => array(
                                'ClienteSetorCargo.codigo_cliente' => $ghe['Ghe']['codigo_cliente'],
                                'ClienteSetorCargo.codigo_setor' => $codigoSetor,
                                'ClienteSetorCargo.codigo_cargo' => $cargo,
                            ),
                        )
                    );

                    if (empty($clienteSetorCargo)) {
                        throw new Exception("Código {$cargo} do cargo inexistente");
                    }

                    $csc = array(
                        "codigo_ghe" => $ghe['Ghe']['codigo'],
                        "codigo_clientes_setores_cargos" => $clienteSetorCargo['ClienteSetorCargo']['codigo']
                    );

                    if (!$modelCscGhe->incluir($csc)) {
                        throw new Exception("Não foi possível vincular GHE com os setores e cargos");
                    }
                } 
            }
            
            $riscosSelecionados = isset($dados["Ghe"]["codigo_arrtpa_ri"]) && is_array($dados['Ghe']['codigo_arrtpa_ri'])
                ? $dados["Ghe"]["codigo_arrtpa_ri"]
                : array();

            if (count($riscosSelecionados) === 0) throw new Exception("É obrigatório pelo menos um risco/impacto");

            $riscosImpactosCadastrados = $modelArrtpariGhe->find("all",
                array(
                    "fields" => array(
                        "ArrtpariGhe.codigo",
                        "ArrtpariGhe.codigo_ghe",
                        "ArrtpariGhe.codigo_arrtpa_ri",
                    ),
                    "conditions" => array(
                        "ArrtpariGhe.codigo_ghe" => $ghe["Ghe"]["codigo"],
                    ),
                )
            );

            # Remover vínculos do GHE com os riscos
            foreach ($riscosImpactosCadastrados as $risco) {
                $index = array_search($risco["ArrtpariGhe"]["codigo_arrtpa_ri"], $riscosSelecionados);

                if ($index === false) {
                    if (!$modelArrtpariGhe->excluir($risco["ArrtpariGhe"]["codigo"])) {
                        throw new Exception("Não foi possível remover vinculo do GHE com o risco");
                    }
                } else {
                    unset($riscosSelecionados[$index]);
                }
            }

            # Adicionar vínculos do GHE com os riscos
            foreach ($riscosSelecionados as $codigoArrtpaRi) {
                if (!empty($codigoArrtpaRi)) {
                    $arrtpariGhe = array(
                        "codigo_ghe" => $ghe['Ghe']['codigo'],
                        "codigo_arrtpa_ri" => $codigoArrtpaRi,
                    );

                    if (!$modelArrtpariGhe->incluir($arrtpariGhe)) {
                        throw new Exception("Não foi possível criar os vínculos do GHE com os riscos");
                    }
                }
            }

            $this->commit();

            return true;
        } catch (Exception $e) {
            $this->rollback();

            return false;
        }
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['Ghe.codigo'] = $data['codigo'];
        }

        if (!empty($data['chave_ghe'])) {
            $conditions['Ghe.chave_ghe LIKE'] = '%' . $data['chave_ghe'] . '%';
        }

        if (!empty($data['aprho_parecer_tecnico'])) {
            $conditions['Ghe.aprho_parecer_tecnico'] = $data['aprho_parecer_tecnico'];
        }

        if (isset($data['ativo'])) {
            if ($data['ativo'] === '0') {
                $conditions[] = '(Ghe.ativo = ' . $data['ativo'] . ' OR Ghe.ativo IS NULL)';
            } elseif ($data['ativo'] == '1') {
                $conditions['Ghe.ativo'] = $data['ativo'];
            }
        }

        if (isset($data["codigo_unidade"]) && !empty($data["codigo_unidade"])) {
            $conditions[] = "codigo_cliente IN (" . $data["codigo_unidade"] . ")";
        } else if (!empty($data["codigo_cliente"])) {
            $grupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');

            $unidades = array();

            foreach ($data["codigo_cliente"] as $codigo_cliente) {
                $clientes = $grupoEconomicoCliente->listaAjax((int) $codigo_cliente);

                foreach ($clientes as $cliente) {
                    array_push($unidades, (int) $cliente['Cliente']['codigo']);
                }
            }

            if (count($unidades) > 0) {
                $conditions[] = "codigo_cliente IN (" . implode(",", $unidades) . ")";
            }
        }

        return $conditions;
    }
}
