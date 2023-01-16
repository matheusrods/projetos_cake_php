<?php

class Rastreador extends AppModel {

    var $name = 'Rastreador';
    var $tableSchema = 'monitoramento';
    var $databaseTable = 'dbBuonnysat';
    var $useTable = 'rastreador';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function bindLazyPosicaoUltima() {
        $this->bindModel(array(
            'hasOne' => array(
                'PosicaoUltima' => array(
                    'className' => 'PosicaoUltima',
                    'foreignKey' => 'codigo_rastreador',
                    'conditions' => 'Rastreador.ativo = 1',
            ))));
    }

    function unbindPosicaoUltima() {
        $this->unbindModel(array(
            'hasOne' => array(
                'PosicaoUltima'
            )
        ));
    }

    function buscaUltimaPosicao($placa) {
        $this->bindLazyPosicaoUltima();

        $this->Veiculo = & ClassRegistry::init('Veiculo');

        $codigo_veiculo = $this->Veiculo->buscaCodigodaPlaca($placa);

        $posicao = $this->find('first', array('fields' => array('PosicaoUltima.referencia'),
                                              'conditions' => array('Rastreador.codigo_veiculo' => $codigo_veiculo)));

        $this->unbindPosicaoUltima();

        return $posicao['PosicaoUltima']['referencia'];
    }

}

?>
