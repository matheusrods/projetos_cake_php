<?php
class AcoesMelhoriasTipo extends AppModel
{
    public $name = 'AcoesMelhoriasTipo';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'acoes_melhorias_tipo';
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
        )
    );

    public function getListaAcoesMelhoriasTipo($filtros = null)
    {
        $fields = array(
            'AcoesMelhoriasTipo.codigo',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasTipo.ativo',
            'AcoesMelhoriasTipo.codigo_cliente'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $acoes_melhorias_tipo = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'AcoesMelhoriasTipo.codigo desc',
        );

        return $acoes_melhorias_tipo;
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
            'AcoesMelhoriasTipo.codigo',
            'AcoesMelhoriasTipo.descricao',
            'AcoesMelhoriasTipo.ativo',
            'AcoesMelhoriasTipo.codigo_cliente'
        );

        $conditions = array('AcoesMelhoriasTipo.codigo' => $codigo);

        $acoes_melhorias_tipo = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($acoes_melhorias_tipo)){
            return array();
        }

        return $acoes_melhorias_tipo;
    }


    public function getSubperfil($codigo_cliente, $interno)
    {
        $fields = array(
            'Subperfil.codigo',
            'Subperfil.descricao'
        );

        $conditions = array(
            'ativo'=>1,
            'codigo_cliente'=> $codigo_cliente,
            'interno' => $interno
        );

        //executa os dados
        $dados = $this->find('list', compact('fields','conditions'));
        return $dados;
    }

    public function getSubperfilUsuario($codigo_usuario)
    {
        $fields = array(
            'Subperfil.codigo'
        );

        $joins = array(
            array(
                "table" => "usuario_subperfil",
                "alias" => "UsuarioSubperfil",
                "type" => "INNER",
                "conditions" => "Subperfil.codigo = UsuarioSubperfil.codigo_subperfil"
            ),
        );

        $conditions = array(
            'Subperfil.ativo'=>1,
            'UsuarioSubperfil.codigo_usuario'=> $codigo_usuario
        );

        //executa os dados
        $dados = $this->find('list', compact('fields','joins','conditions'));
        return $dados;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['AcoesMelhoriasTipo.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['AcoesMelhoriasTipo.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(AcoesMelhoriasTipo.ativo = ' . $data ['ativo'] . ' OR AcoesMelhoriasTipo.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['AcoesMelhoriasTipo.ativo'] = $data ['ativo'];
            }
        }

        if (isset($data ['codigo_cliente'])) {

            if(!is_array($data['codigo_cliente'])) {
                $data ['codigo_cliente'] = explode(',',$data ['codigo_cliente']);
            }

            $conditions ['AcoesMelhoriasTipo.codigo_cliente'] = $data ['codigo_cliente'];
        }

        return $conditions;
    }
}
