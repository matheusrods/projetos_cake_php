<?php

class FichaCt extends AppModel {

    var $name = 'FichaCt';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_ct';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    

    function insere($ficha) {
        $this->create();
        $codigo_ficha = $ficha['Ficha']['codigo'];
        $codigo_status = $ficha['Ficha']['codigo_status'];
        $fichaModel = & ClassRegistry::init('Ficha');
        $liberacao = $fichaModel->buscaLiberacao($codigo_ficha);
        $ct = array(
            'codigo_ficha' => $codigo_ficha,
            'numero_liberacao' => $liberacao['FichaLiberacao']['numero_liberacao'],
            'data_validade' => $ficha['Ficha']['data_validade'],
            'data_inclusao' => date('Y-m-d H:i:s'),
            'data_alteracao' => null,
            'codigo_usuario_alteracao' => null,
            'codigo_usuario_inclusao' => $liberacao['FichaLiberacao']['codigo_usuario_inclusao'],
            'data_inclusao' => date('Y-m-d H:i:s'),
            'codigo_status' => $codigo_status
        );
        $this->save($ct);
    }
    
    public function duplicar($codigo_ficha, $codigo_ficha_nova) {
        $codigos = $this->find('all', array('conditions' => array(
            'codigo_ficha' => $codigo_ficha,
        )));
        
        if (count($codigos) == 0) {
            return true;
        }
        try {
            foreach ($codigos as $ficha_ct) {
                $ficha_ct['FichaCt']['codigo_ficha'] = $codigo_ficha_nova;
                if (!$this->incluir($ficha_ct)) {
                    throw new Exception();
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
        
    }
}
