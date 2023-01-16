<?php

class FuncionarioAreaAtuacao extends AppModel {

    var $name = 'FuncionarioAreaAtuacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'Monitora';
    var $useTable = 'funcionarios_areas_atuacoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function atualizarAreaAtuacaoAoFuncionario($data) {
        $codigo_funcionario = $data['Funcionario']['codigo'];
        $this->deleteAll(array('codigo_funcionario'=>$codigo_funcionario));

        $save_data = array();
        foreach($data['Funcionario']['codigo_area_atuacao'] as $codigo_area_atuacao){
            $save_data[] = array(
                'codigo_funcionario' => $codigo_funcionario,
                'codigo_area_atuacao' => $codigo_area_atuacao
            );
        }

        return $this->saveAll($save_data);
    }

    function listarCodigoAreaAtuacaoPorFuncionario($codigo_funcionario){
        return $this->find('list', array(
            'conditions' => array('codigo_funcionario'=> $codigo_funcionario),
            'fields' => array('codigo_area_atuacao')
        ));
    }
}