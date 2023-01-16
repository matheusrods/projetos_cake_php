<?php
class Onboarding extends AppModel {

    var $name = 'Onboarding';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'onboarding';
    var $primaryKey = 'codigo';

    function carregar($codigo) {
        $dados = $this->find ( 'first', array (
                'conditions' => array (
                        $this->name . '.codigo' => $codigo 
                ) 
        ) );
        return $dados;
    }

    function incluir($dados){

        if (!parent::incluir($dados)){
            return false;
        }
        else{
            return true;
        }
    }
    
    function atualizar($dados){

        if (!parent::atualizar($dados)){
            return false;
        }
        else{
            return true;
        }
    }

    function excluir($codigo) {
        return $this->delete($codigo);
    }

    function obterListaPorSistema($codigo_sistema) {
        return $this->find('all', array('conditions' => array('codigo_sistema' => $codigo_sistema)));
    }
}
