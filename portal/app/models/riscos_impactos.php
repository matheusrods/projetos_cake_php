<?php
class RiscosImpactos extends AppModel
{
    public $name = 'RiscosImpactos';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'riscos_impactos';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'codigo_perigo_aspecto' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_perigo_aspecto',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe código cliente',
            'required' => true
        ),
        'codigo_metodo_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo do método',
            'required' => true
        ),
        'codigo_risco_impacto_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe se é do tipo risco ou impacto',
            'required' => true
        ),
    );

    public function getListaRiscosImpactos($filtros = null)
    {

        $fields = array(
            'RiscosImpactos.codigo',
            'RiscosImpactos.descricao',
            'RiscosImpactos.codigo_perigo_aspecto',
            'RiscosImpactos.codigo_metodo_tipo',
            'RiscosImpactos.ativo',
            'RiscosImpactos.codigo_risco_impacto_tipo',
            'PerigosAspectos.codigo_risco_tipo',
            'PerigosAspectos.descricao',
            'RiscosTipo.codigo',
            'RiscosTipo.descricao',
            'MetodosTipo.descricao',
            'RiscosImpactos.codigo_cliente',
            'RiscosImpactosTipo.codigo',
            'RiscosImpactosTipo.descricao'
        );

        $joins = array(
            array(
                'table' => 'perigos_aspectos',
                'alias' => 'PerigosAspectos',
                'type' => 'INNER',
                'conditions' => 'PerigosAspectos.codigo = RiscosImpactos.codigo_perigo_aspecto'
            ),
            array(
                'table' => 'riscos_tipo',
                'alias' => 'RiscosTipo',
                'type' => 'INNER',
                'conditions' => 'PerigosAspectos.codigo_risco_tipo = RiscosTipo.codigo'
            ),
            array(
                'table' => 'metodos_tipo',
                'alias' => 'MetodosTipo',
                'type' => 'INNER',
                'conditions' => 'MetodosTipo.codigo = RiscosImpactos.codigo_metodo_tipo'
            ),
            array(
                'table' => 'riscos_impactos_tipo',
                'alias' => 'RiscosImpactosTipo',
                'type' => 'INNER',
                'conditions' => 'RiscosImpactosTipo.codigo = RiscosImpactos.codigo_risco_impacto_tipo'
            ),
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $perigos_aspectos = array(
            'fields' => $fields,
            'joins'  => $joins,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'RiscosImpactos.codigo desc',
        );

        return $perigos_aspectos;
    }

    public function getByCodigo($codigo) {
        $fields = array(
            'RiscosImpactos.codigo',
            'RiscosImpactos.descricao',
            'RiscosImpactos.codigo_perigo_aspecto',
            'RiscosImpactos.codigo_metodo_tipo',
            'RiscosImpactos.ativo',
            'RiscosImpactos.codigo_cliente',
            'RiscosImpactos.codigo_risco_impacto_tipo',

            'RiscosImpactos.unidade_medida',
            'RiscosImpactos.meio_propagacao',
            'RiscosImpactos.nivel_acao',
            'RiscosImpactos.limite_tolerancia',

            'RiscosImpactos.risco_caracterizado_por_altura',
            'RiscosImpactos.risco_caracterizado_por_trabalho_confinado',
            'RiscosImpactos.risco_caracterizado_por_ruido',
            'RiscosImpactos.risco_caracterizado_por_calor',
            'RiscosImpactos.ausencia_de_risco',

            'RiscosImpactos.aso',
            'RiscosImpactos.convocacao',
            'RiscosImpactos.nocivo_ppp',
            'RiscosImpactos.pcmso',
            'RiscosImpactos.ppra',

            'RiscosImpactos.periculoso',
            'RiscosImpactos.insalubridade',
            'RiscosImpactos.grau_insalubridade',
            'RiscosImpactos.aposentadoria_especial',
            'RiscosImpactos.tempo_atividade',

            'RiscosImpactos.codigo_risco',
        );

        $conditions = array('RiscosImpactos.codigo' => $codigo);

        $riscos_impactos = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($riscos_impactos)){
            return array();
        }

        return $riscos_impactos;
    }

    public function getRiscosImpactosCombo($codigo_cliente = null)
    {
        $modelAgentesRiscosClientes = ClassRegistry::init('AgentesRiscosClientes');

        $conditions = array();
        if ($codigo_cliente) {
            $conditions['AgentesRiscosClientes.codigo_cliente'] = $codigo_cliente;
        }

        $riscos_impactos = $modelAgentesRiscosClientes->find('all', 
            array(
                'fields' => array(
                    'AgentesRiscosClientes.codigo AS codigo',
                    'AgentesRiscosClientes.codigo_cliente AS codigo_cliente',
                    'AgentesRiscosClientes.codigo_arrtpa_ri AS codigo_arrtpa_ri',
                    'AgentesRiscosClientes.codigo_agente_risco AS codigo_agente_risco',
                    
                    'ArrtpaRi.codigo_risco_impacto AS codigo_risco_impacto',
                    'ArrtpaRi.codigo_arrt_pa AS codigo_arrt_pa',
                    'RiscosImpactos.descricao AS riscos_impactos',

                    'PerigosAspectos.codigo AS codigo_perigo_aspecto',
                    'PerigosAspectos.descricao AS perigos_aspectos'
                ),
                'joins' => array(
                    array(
                        'table' => 'arrtpa_ri',
                        'alias' => 'ArrtpaRi',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ArrtpaRi.codigo = AgentesRiscosClientes.codigo_arrtpa_ri'
                        )
                    ),
                    array(
                        'table' => 'riscos_impactos',
                        'alias' => 'RiscosImpactos',
                        'type' => 'INNER',
                        'conditions' => array(
                            'RiscosImpactos.codigo = ArrtpaRi.codigo_risco_impacto'
                        )
                    ),
                    array(
                        'table' => 'arrt_pa',
                        'alias' => 'ArrtPa',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ArrtPa.codigo = ArrtpaRi.codigo_arrt_pa'
                        )
                    ),
                    array(
                        'table' => 'perigos_aspectos',
                        'alias' => 'PerigosAspectos',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PerigosAspectos.codigo = ArrtPa.codigo_perigo_aspecto'
                        )
                    ),
                ),
                'conditions' => $conditions,
                'order' => array('PerigosAspectos.descricao', 'RiscosImpactos.descricao')
            )
        );

        foreach ($riscos_impactos as $key => $risco) {
            $riscos_impactos[$key] = $risco[0];
		}

        return $riscos_impactos;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();


        if (!empty($data['codigo'])) {
            $conditions['RiscosImpactos.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['RiscosImpactos.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data ['codigo_perigo_aspecto'])) {
            $conditions ['RiscosImpactos.codigo_perigo_aspecto'] = $data ['codigo_perigo_aspecto'];
        }

        if (!empty($data ['codigo_metodo_tipo'])) {
            $conditions ['RiscosImpactos.codigo_metodo_tipo'] = $data ['codigo_metodo_tipo'];
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(RiscosImpactos.ativo = ' . $data ['ativo'] . ' OR RiscosImpactos.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['RiscosImpactos.ativo'] = $data ['ativo'];
            }
        }

        if (!empty($data ['codigo_cliente'])) {
            $conditions ['RiscosImpactos.codigo_cliente'] = $data ['codigo_cliente'];
        }

        if (!empty($data ['codigo_risco_tipo'])) {
            $conditions ['PerigosAspectos.codigo_risco_tipo'] = $data ['codigo_risco_tipo'];
        }

        if (!empty($data ['codigo_risco_impacto_tipo'])) {
            $conditions ['RiscosImpactos.codigo_risco_impacto_tipo'] = $data ['codigo_risco_impacto_tipo'];
        }

        return $conditions;
    }
}
