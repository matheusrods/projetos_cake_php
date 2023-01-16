<?php

class PosObsLocal extends AppModel
{
    public $name          = 'PosObsLocal';
    var    $tableSchema   = 'dbo';
    var    $databaseTable = 'RHHealth';
    var    $useTable      = 'pos_obs_local';
    var    $primaryKey    = 'codigo';
    var    $recursive     = 2;

    public function obterLocaisDeObservacaoPeloCliente($codigo_cliente, $filtros)
    {
        $conditions = $this->converteFiltroEmCondition($filtros);
        $conditions['PosObsLocal.codigo_cliente'] = $codigo_cliente;

        return $this->find('all', array(
            'conditions' => $conditions,
            'limit'      => 20,
            'order'      => "PosObsLocal.descricao",
            'recursive'  => 2
        ));
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['descricao'])) {
            $conditions['PosObsLocal.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }

        if (!empty($data['codigo_local'])) {
            $conditions['PosObsLocal.codigo'] = $data['codigo_local'];
        }

        if (isset($data['ativo'])) {
            if ($data['ativo'] === '0') {
                $conditions[] = '(PosObsLocal.ativo = ' . $data['ativo'] . ' OR PosObsLocal.ativo IS NULL)';
            } else if ($data['ativo'] == '1') {
                $conditions['PosObsLocal.ativo'] = $data['ativo'];
            }
        }

        return $conditions;
    }

    public function obterPaginacao($codigo_cliente, $filtros)
    {

        $conditions = $this->converteFiltroEmCondition($filtros);
        $conditions['PosObsLocal.codigo_cliente'] = $codigo_cliente;

        return array(
            'conditions' => $conditions,
            'order'      => "PosObsLocal.descricao",
        );
    }
}
