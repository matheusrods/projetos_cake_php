<?php
class AreaProcesso extends AppModel
{
    public $name = 'AreaProcesso';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'area_processo';
    public $primaryKey = 'codigo';

    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código cliente',
            'required' => true
        ),
        'codigo_empresa' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o código empresa',
            'required' => true
        )
    );

    public function getListaAreaProcesso($filtros = null)
    {
        $fields = array(
            'AreaProcesso.codigo',
            'AreaProcesso.descricao',
            'AreaProcesso.ativo',
            'AreaProcesso.codigo_cliente'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $area_processo = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'AreaProcesso.codigo desc',
        );

        return $area_processo;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'AreaProcesso.codigo',
            'AreaProcesso.descricao',
            'AreaProcesso.ativo',
            'AreaProcesso.codigo_cliente'
        );

        $conditions = array('AreaProcesso.codigo' => $codigo);

        $area_processo = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($area_processo)){
            return array();
        }

        return $area_processo;
    }

    public function getAreaProcesso($codigo_empresa, $codigo_cliente = null)
    {
        $fields = array(
            'AreaProcesso.codigo',
            'AreaProcesso.descricao'
        );

        $conditions = array(
            'ativo'=>1,
            'codigo_empresa'=> $codigo_empresa,
        );

        if(!is_null($codigo_cliente)) {
            $conditions[] = array(
                "codigo_cliente IN ({$codigo_cliente})"
            );
        }

        //executa os dados
        $dados = $this->find('list', compact('fields','conditions'));

        return $dados;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['AreaProcesso.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['AreaProcesso.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }

        if (!empty($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(AreaProcesso.ativo = ' . $data['ativo'] . ' OR AreaAtuacao.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['AreaProcesso.ativo'] = $data['ativo'];
            }
        }

        if (!empty($data['codigo_cliente'])) {
            $conditions ['AreaProcesso.codigo_cliente'] = $data['codigo_cliente'];
        }

        return $conditions;
    }

    public function getUsuarioAreaAtuacao($codigo_usuario)
    {
        $fields = array(
            'AreaAtuacao.codigo'
        );

        $joins = array(
            array(
                "table" => "usuario_area_atuacao",
                "alias" => "UsuarioAreaAtuacao",
                "type" => "INNER",
                "conditions" => "AreaAtuacao.codigo = UsuarioAreaAtuacao.codigo_area_atuacao"
            ),
        );

        $conditions = array(
            'AreaAtuacao.ativo'=>1,
            'UsuarioAreaAtuacao.codigo_usuario'=> $codigo_usuario
        );

        //executa os dados
        $dados = $this->find('list', compact('fields','joins','conditions'));
        return $dados;
    }
}
