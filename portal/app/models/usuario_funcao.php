<?php
class UsuarioFuncao extends AppModel
{
    public $name = 'UsuarioFuncao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'usuario_funcao';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'codigo_funcao_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo da função.',
            'required' => true
        )
    );

    public function getUsuarioGestor($codigo_cliente)
    {
        //campos do select
        $fields = array(
            'usuario.codigo',
            'usuario.nome',
        );

        $joins = array(
            array(
                'table' => 'usuario',
                'alias' => 'usuario',
                'type' => 'INNER',
                'conditions' => 'usuario.ativo = 1'
            ),
            array(
                'table' => 'usuarios_dados',
                'alias' => 'UsuariosDados',
                'type' => 'LEFT',
                'conditions' => 'UsuariosDados.codigo_usuario = usuario.codigo'
            ),
            array(
                'table' => 'funcionarios',
                'alias' => 'Funcionarios',
                'type' => 'left',
                'conditions' => 'usuario.apelido = ISNULL(UsuariosDados.cpf,usuario.apelido)'
            ),
            array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionarios.codigo'
            ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetoresCargos',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetoresCargos.codigo_cliente_funcionario = ClienteFuncionario.codigo AND FuncionarioSetoresCargos.data_fim is NULL'
            ),
        );

//        pr($codigo_cliente);exit;
        $conditions = "UsuarioFuncao.codigo_usuario = usuario.codigo AND UsuarioFuncao.codigo_funcao_tipo = 4 AND UsuarioFuncao.ativo = 1
        AND FuncionarioSetoresCargos.codigo_cliente IN ({$codigo_cliente}) ";

        $group = $fields;

        //executa os dados
        $dados = $this->find('list', compact('fields', 'joins', 'conditions','group'));

        return $dados;
    }

}
