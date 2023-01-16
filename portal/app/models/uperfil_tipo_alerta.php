<?php
class UperfilTipoAlerta extends AppModel {
    var $name = 'UperfilTipoAlerta';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'uperfis_tipos_alertas';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

        
    function excluir_por_perfil($codigo_perfil){
        return $this->query("DELETE FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} where codigo_uperfil = $codigo_perfil");        
    }

    function listar_tipos_por_perfil($codigo_perfil){
        $tipos_alertas = $this->find('all',array('fields' => 'codigo_alerta_tipo','conditions' => array('codigo_uperfil' => $codigo_perfil)));
        $alertas = array();
        foreach ($tipos_alertas as $tipo_alerta) {
            $alertas[] = $tipo_alerta['UperfilTipoAlerta']['codigo_alerta_tipo']; 
        }
        return $alertas;
    }
}
