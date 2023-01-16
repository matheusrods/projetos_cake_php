<?php
class AreaAtuacao extends AppModel
{
    public $name = 'AreaAtuacao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'area_atuacao';
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

    public function getListaAreaAtuacao($filtros = null)
    {
        $fields = array(
            'AreaAtuacao.codigo',
            'AreaAtuacao.descricao',
            'AreaAtuacao.ativo',
            'AreaAtuacao.codigo_cliente'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $area_atuacao = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'AreaAtuacao.codigo desc',
        );

        return $area_atuacao;
    }

    public function retornaSubperfil($data = null)
    {

        if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
            $codigo_cliente = array(
                'Subperfil.codigo_cliente' => $data['codigo_cliente']
            );
        } else {
            $codigo_cliente = array();
        }

        $conditions = array(
            $codigo_cliente,
            'Subperfil.ativo' => 1
        );

        return $this->find('list', array('conditions' => $conditions,'fields' => array('codigo', 'descricao')));;
    }

    function getByCodigo($codigo) {
        $fields = array(
            'AreaAtuacao.codigo',
            'AreaAtuacao.descricao',
            'AreaAtuacao.ativo',
            'AreaAtuacao.codigo_cliente'
        );

        $conditions = array('AreaAtuacao.codigo' => $codigo);

        $area_atuacao = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($area_atuacao)){
            return array();
        }

        return $area_atuacao;
    }

    public function getAreaAtuacao($codigo_empresa, $codigo_cliente = null)
    {
        $fields = array(
            'AreaAtuacao.codigo',
            'AreaAtuacao.descricao'
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
            $conditions['AreaAtuacao.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['AreaAtuacao.descricao LIKE'] = '%' . $data['descricao'] . '%';
        }

        if (!empty($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(AreaAtuacao.ativo = ' . $data['ativo'] . ' OR AreaAtuacao.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['AreaAtuacao.ativo'] = $data['ativo'];
            }
        }

        if (!empty($data['codigo_cliente'])) {
            $conditions ['AreaAtuacao.codigo_cliente'] = $data['codigo_cliente'];
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
