<?php
class UsuariosDados extends AppModel {
    var $databaseTable = 'RHHealth';
    var $tableSchema = 'dbo';
    var $useTable = 'usuarios_dados';
    var $name = 'UsuariosDados';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    public function buscaFuncionarioPorEmpresa($conditions){

        $joins = array(
            array(
                'table' => 'funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => array(
                    'Funcionario.cpf = UsuariosDados.cpf'
                    )
                ),
            array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => array(
                    'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
                    )
                ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)"
                    )
                ),
            array(
                'table' => 'grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
                    )
                ),
            array(
                'table' => 'grupos_economicos',
                'alias' => 'GrupoEconomico',
                'type' => 'INNER',
                'conditions' => array(
                    'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
                    )
                ),
            );

        return $this->find('all', array('conditions' => $conditions, 'joins' => $joins));
    }

}
?>