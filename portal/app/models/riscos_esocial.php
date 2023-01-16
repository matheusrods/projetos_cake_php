<?php
class RiscosEsocial extends AppModel
{
    public $name = 'RiscosEsocial';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'riscos_esocial';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'risco' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o risco',
            'required' => true
        ),
        'codigo_esocial' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código e-social.',
            'required' => true
        ),
        'codigo_grupo_risco' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o grupo do risco',
            'required' => true
        )
    );

    public function getListaRiscos($filtros = null)
    {
        $fields = array(
            'Risco.codigo',
            'Risco.nome_agente',
            'Risco.codigo_agente_nocivo_esocial',
            'Risco.codigo_grupo',
            'Risco.ativo',
            'GruposRiscos.codigo',
            'GruposRiscos.descricao'
        );

        $joins = array(
            array(
                'table' => 'grupos_riscos',
                'alias' => 'GruposRiscos',
                'type' => 'INNER',
                'conditions' => "Risco.codigo_grupo = GruposRiscos.codigo and Risco.codigo_agente_nocivo_esocial <> ''  "
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);


        $risco = array(
            'fields'     => $fields,
            'joins'      => $joins,
            'conditions' => $conditions,
            'limit'      => 50,
            'order'      => 'Risco.codigo ASC',
        );

        return $risco;
    }

    public function getListaRiscosEsocial($filtros = null)
    {
        $fields = array(
            'RiscosEsocial.codigo',
            'RiscosEsocial.risco',
            'RiscosEsocial.codigo_esocial',
            'RiscosEsocial.codigo_grupo_risco',
            'RiscosEsocial.ativo',
            'GruposRiscos.codigo',
            'GruposRiscos.descricao'
        );

        $joins = array(
            array(
                'table' => 'grupos_riscos',
                'alias' => 'GruposRiscos',
                'type' => 'INNER',
                'conditions' => 'RiscosEsocial.codigo_grupo_risco = GruposRiscos.codigo'
            )
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $riscos_esocial = array(
            'fields'     => $fields,
            'joins'      => $joins,
            'conditions' => $conditions,
            'limit'      => 50,
            'order'      => 'RiscosEsocial.codigo desc',
        );

        return $riscos_esocial;
    }

    public function retornaRiscosEsocial($data = null)
    {
        $conditions = array(
            'RiscosEsocial.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'codigo_esocial', 'risco', 'ativo')));;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'RiscosEsocial.codigo',
            'RiscosEsocial.risco',
            'RiscosEsocial.codigo_esocial',
            'RiscosEsocial.codigo_grupo_risco',

        );

        $conditions = array('RiscosEsocial.codigo' => $codigo);

        $riscos_esocial = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($riscos_esocial)){
            return array();
        }

        return $riscos_esocial;
    }

    //Retorna o risco_esocial vinculado ao riscos_impacto
    function getByCodigoRiscosImpactos($codigo) {
        $fields = array(
            'RiscosEsocial.codigo as codigo',
            'RiscosEsocial.risco as risco',
            'RiscosEsocial.codigo_esocial as codigo_esocial',
            'RiscosEsocial.codigo_grupo_risco as codigo_grupo_risco',
            'RiscosEsocial.ativo as ativo',

            'GruposRiscos.descricao as descricao'
        );

        $joins = array(
            array(
                'table' => 'grupos_riscos',
                'alias' => 'GruposRiscos',
                'type' => 'INNER',
                'conditions' => 'RiscosEsocial.codigo_grupo_risco = GruposRiscos.codigo'
            )
        );

        $conditions = array('RiscosEsocial.codigo' => $codigo);

        $riscos_esocial = $this->find('first',
            array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions
            )
        );

        if(empty($riscos_esocial)){
            return array();
        }

        return $riscos_esocial;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['RiscosEsocial.codigo'] = $data['codigo'];
        }

        if (!empty($data ['risco'])) {
            $conditions ['RiscosEsocial.risco LIKE'] = '%' . $data ['risco'] . '%';
        }

        if (!empty($data ['codigo_esocial'])) {
            $conditions ['RiscosEsocial.codigo_esocial'] = $data['codigo_esocial'];;
        }

        if (!empty($data ['codigo_grupo_risco'])) {
            $conditions ['RiscosEsocial.codigo_grupo_risco'] = $data['codigo_grupo_risco'];;
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(RiscosEsocial.ativo = ' . $data ['ativo'] . ' OR RiscosEsocial.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['RiscosEsocial.ativo'] = $data ['ativo'];
            }
        }

        return $conditions;
    }
}
