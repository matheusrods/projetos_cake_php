<?php
class AgentesRiscosClientes extends AppModel
{
    public $name = 'AgentesRiscosClientes';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'agentes_riscos_clientes';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $uses = array(
        'Cliente'
    );

    public $validate = array(
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_cliente',
            'required' => true
        ),
        'codigo_arrtpa_ri' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_arrtpa_ri',
            'required' => true
        ),
        'codigo_agente_risco' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo_agente_risco',
            'required' => true
        )
    );

    public function getListaAgentesRiscosCliente($filtros = null)
    {
        $fields = array(
            'AgentesRiscosClientes.codigo',
            'AgentesRiscosClientes.codigo_cliente',
            'AgentesRiscosClientes.codigo_arrtpa_ri',
            'AgentesRiscosClientes.codigo_agente_risco',
            'RiscosImpactos.descricao',
            'PerigosAspectos.descricao',
            'RiscosTipo.descricao',
        );

        $joins = array(
            array(
                'table' => 'agentes_riscos',
                'alias' => 'AgentesRiscos',
                'type' => 'INNER',
                'conditions' => array('AgentesRiscosClientes.codigo_agente_risco = AgentesRiscos.codigo and AgentesRiscos.data_remocao is null')
            ),
            array(
                'table' => 'arrtpa_ri',
                'alias' => 'ArrtpaRi',
                'type' => 'INNER',
                'conditions' => array('AgentesRiscosClientes.codigo_arrtpa_ri = ArrtpaRi.codigo')
            ),
            array(
                'table' => 'riscos_impactos',
                'alias' => 'RiscosImpactos',
                'type' => 'INNER',
                'conditions' => array('ArrtpaRi.codigo_risco_impacto = RiscosImpactos.codigo')
            ),
            array(
                'table' => 'arrt_pa',
                'alias' => 'ArrtPa',
                'type' => 'INNER',
                'conditions' => array('ArrtpaRi.codigo_arrt_pa = ArrtPa.codigo')
            ),
            array(
                'table' => 'perigos_aspectos',
                'alias' => 'PerigosAspectos',
                'type' => 'INNER',
                'conditions' => array('ArrtPa.codigo_perigo_aspecto = PerigosAspectos.codigo')
            ),
            array(
                'table' => 'ar_rt',
                'alias' => 'ArRt',
                'type' => 'INNER',
                'conditions' => array('ArrtPa.codigo_ar_rt = ArRt.codigo')
            ),
            array(
                'table' => 'riscos_tipo',
                'alias' => 'RiscosTipo',
                'type' => 'INNER',
                'conditions' => array('ArRt.codigo_risco_tipo = RiscosTipo.codigo')
            ),
        );

        $conditions = $this->converteFiltroEmCondition($filtros);
        
        $agentes_riscos = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'AgentesRiscosClientes.codigo desc',
        );

        return $agentes_riscos;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['AgentesRiscosClientes.codigo'] = $data['codigo'];
        }

        if (!empty($data ['codigo_cliente'])) {
            $conditions ['AgentesRiscosClientes.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data ['codigo_arrtpa_ri'])) {
            $conditions ['AgentesRiscosClientes.codigo_arrtpa_ri'] = $data['codigo_arrtpa_ri'];
        }

        if (!empty($data ['codigo_agente_risco'])) {
            $conditions ['AgentesRiscosClientes.codigo_agente_risco'] = $data['codigo_agente_risco'];
        }

        if (!empty($data ['descricao'])) {
            $conditions [] = array(
                "RiscosImpactos.descricao like '%{$data['descricao']}%' "
            );
        }

        return $conditions;
    }

    public function dadosCliente() {
        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 && $this->authUsuario['Usuario']['codigo_uperfil'] == 43) {
            //Filtro para usuario não admin
            $codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $codigo_cliente = null;
            $is_admin = 1;
            $nome_fantasia = null;
        }

        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }
}
