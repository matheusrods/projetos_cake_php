<?php
class LojaNaveg extends AppModel {
    var $name = 'LojaNaveg';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'loja';
    var $primaryKey = 'codigo';
    var $displayField = 'razaosocia';
    
    const GRUPO_BUONNY = 1;
    const GRUPO_LIDER = 2;
    const GRUPO_NATEC = 3;
    const GRUPO_SOLEN = 4;
    const GRUPO_RHHEALTH = 5;

    function listEmpresas($grupo_empresa) {
        $conditions['codigo'] = array('17','18','19','20','21','22');
        $results = $this->find('list', compact('conditions'));
        return $results;
    }

    function listGrupos($grupos_selecionados = array(5 => 'RHHealth'))
    {
       $grupos = array();
        if(is_array($grupos_selecionados)){
            foreach ($grupos_selecionados as $key => $grupo)
                $grupos[$key] = $grupo;
            return $grupos;
        }
    }
    
    function nomeGrupoPorId( $codigo ){
        $grupos = $this->listGrupos();
        return (isset( $grupos[$codigo] ) ? $grupos[$codigo] : '');
    }
    
    function carregarEmpresaPelaDescricao($descricao = null) {
        return $this->find('first', array('conditions' => array($this->name.'.descricao' => $descricao)));
    }
}
?>