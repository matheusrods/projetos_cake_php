<?php
class Subperfil extends AppModel
{
    public $name = 'Subperfil';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'subperfil';
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

    public function getListaSubperfil($filtros = null)
    {
        $fields = array(
            'Subperfil.codigo',
            'Subperfil.descricao',
            'Subperfil.ativo',
            'Subperfil.codigo_cliente',
            'Subperfil.interno'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);

        $subperfil = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Subperfil.codigo desc',
        );

        //Retorna os tipos das ações que podem ser selecionadas
        $sql = "select at.codigo as codigo_acao_tipo, at.descricao as descricao_acao_tipo,
                 a.codigo, a.descricao
                from acoes_tipo at
                inner join acoes a on at.codigo = a.codigo_acao_tipo";

        $retorno = $this->query($sql);

        //Trata o retorno dos indices do array
        foreach ($retorno as $key => $obj) {
            $array['acao_tipo'][$key]['codigo_acao_tipo'] = $obj[0]['codigo_acao_tipo'];
            $array['acao_tipo'][$key]['descricao_acao_tipo'] = $obj[0]['descricao_acao_tipo'];
            $array['acao_tipo'][$key]['codigo'] = $obj[0]['codigo'];
            $array['acao_tipo'][$key]['descricao'] = $obj[0]['descricao'];
        }

        $subperfil['acao_tipo'] = $array['acao_tipo'];

        return $subperfil;
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
            'Subperfil.codigo',
            'Subperfil.descricao',
            'Subperfil.ativo',
            'Subperfil.codigo_cliente',
            'Subperfil.interno'
        );

        $conditions = array('Subperfil.codigo' => $codigo);

        $subperfil = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        //Ações
        $fields = array(
            'Subperfil.codigo',
            'Subperfil.descricao',
            'Subperfil.ativo',
            'Subperfil.codigo_cliente',
            'Subperfil.interno'
        );

        $conditions = array('Subperfil.codigo' => $codigo);

        $subperfil = $this->find('first',
            array(
                'fields' => $fields,
                'conditions' => $conditions
            )
        );

        if(empty($subperfil)){
            return array();
        }

        //Retorna os tipos das ações que podem ser selecionadas
        $sql = "select at.codigo as codigo_acao_tipo, at.descricao as descricao_acao_tipo,
                 a.codigo, a.descricao, sa.codigo as codigo_subperfil_acao
                from acoes_tipo at
                inner join acoes a on at.codigo = a.codigo_acao_tipo
                left join subperfil_acoes sa on sa.codigo_acao = a.codigo and sa.codigo_subperfil = {$codigo};";

        $retorno = $this->query($sql);

        //Trata o retorno dos indices do array
        foreach ($retorno as $key => $obj) {
            $array['acao_tipo'][$key]['codigo_acao_tipo'] = $obj[0]['codigo_acao_tipo'];
            $array['acao_tipo'][$key]['descricao_acao_tipo'] = $obj[0]['descricao_acao_tipo'];
            $array['acao_tipo'][$key]['codigo'] = $obj[0]['codigo'];
            $array['acao_tipo'][$key]['descricao'] = $obj[0]['descricao'];
            $array['acao_tipo'][$key]['codigo_subperfil_acao'] = $obj[0]['codigo_subperfil_acao'];
        }

        $subperfil['acao_tipo'] = $array['acao_tipo'];

        return $subperfil;
    }

    public function getSubperfil($codigo_cliente, $interno, $perfil = null)
    {
        $fields = array(
            'Subperfil.codigo',
            'Subperfil.descricao'
        );

        if(!empty($perfil['Usuario']['codigo_cliente'])){//pegar o codigo de cliente que o usuario estar vinculado
            $codigo_cliente = $perfil['Usuario']['codigo_cliente'];
        }

        if (!empty($codigo_cliente) && $codigo_cliente != 'null') {
            $conditions = array(
                'ativo'=>1,
                "codigo_cliente IN ({$codigo_cliente})",
                'interno' => $interno
            );
        } else {
            $conditions = array(
                'ativo'=>1,
                'interno' => $interno
            );
        }

        $group = $fields;

        // pr($this->find('sql', compact('fields','conditions','group')));

        //executa os dados
        $dados = $this->find('list', compact('fields','conditions','group'));
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
            $conditions['Subperfil.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['Subperfil.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (isset($data ['ativo'])) {
            if ($data ['ativo'] === '0') {
                $conditions [] = '(Subperfil.ativo = ' . $data ['ativo'] . ' OR Subperfil.ativo IS NULL)';
            } elseif ($data ['ativo'] == '1') {
                $conditions ['Subperfil.ativo'] = $data ['ativo'];
            }
        }

        if (isset($data ['interno'])) {
            if ($data ['interno'] === '0') {
                $conditions [] = '(Subperfil.interno = ' . $data ['interno'] . ' OR Subperfil.interno IS NULL)';
            } elseif ($data ['interno'] == '1') {
                $conditions ['Subperfil.interno'] = $data ['interno'];
            }
        }

        if (isset($data ['codigo_cliente'])) {
            $conditions ['Subperfil.codigo_cliente'] = $data ['codigo_cliente'];
        }

        return $conditions;
    }
}
