<?php

class ProprietarioLog extends AppModel {

    var $name = 'ProprietarioLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'Proprietario' => array(
            'class' => 'Proprietario',
            'foreignKey' => 'codigo_proprietario'
        )
    );

    function bindLazyFicha() {
        $this->bindModel(array(
            'hasOne' => array(
                'Ficha' => array(
                    'className' => 'Ficha',
                    'foreignKey' => 'codigo_proprietario_log'
            ))));
    }

    function unbindFicha() {
        $this->unbindModel(array(
            'belongsTo' => array(
                'Ficha'
            )
        ));
    }

    function buscaStatusPenultimaFicha($codigo_produto, $codigo_ficha_atual, $codigo_proprietario) {
        $this->bindLazyFicha();

        $condicoes = array('Ficha.ativo' => array(0, 1),
            'ProprietarioLog.codigo_proprietario' => $codigo_proprietario,
            'not' => array('Ficha.codigo' => $codigo_ficha_atual));

        $status = $this->find('first', array('conditions' => $condicoes,
            'fields' => 'Ficha.codigo_status',
            'order' => array('Ficha.data_inclusao DESC')));

        $this->unbindFicha();
        if (!empty($status)) {
            return $status['Ficha']['codigo_status'];
        } else {
            return null;
        }
    }
    function buscaStatusPenultimaFichaScorecard($codigo_produto, $codigo_ficha_atual, $codigo_proprietario) {
        $this->unbindFicha();

        $condicoes = array('FichaScorecard.ativo' => array(0, 1),
            'ProprietarioLog.codigo_proprietario' => $codigo_proprietario,
            'not' => array('FichaScorecard.codigo' => $codigo_ficha_atual));

        $status = $this->find('first', array('conditions' => $condicoes,
            'fields' => 'FichaScorecard.codigo_status',
            'order' => array('Ficha.data_inclusao DESC')));

        $this->unbindFicha();
        if (!empty($status)) {
            return $status['Ficha']['codigo_status'];
        } else {
            return null;
        }
    }

    //obterProprietarioPeloCodigoProprietarioLog
    public function obterProprietarioPeloCodigoProprietarioLog($codigo_proprietario_log) {
        $retorno = $this->findByCodigo($codigo_proprietario_log);
        return $retorno;
    }

    /**
     * Duplica um proprietario log
     * 
     * @param int $codigo
     * @return false|int 
     */
    public function duplicar($codigo) {
        try {
            if (empty($codigo)) {
                throw new Exception('Sem Código');
            }

            $resultadoAtual = $this->find('first', array(
                'conditions' => array(
                    "{$this->name}.codigo" => $codigo
                )
                    ));

            $resultado = $this->incluir($resultadoAtual);

            if ($resultado) {
                return $this->id;
            } else {
                throw new Exception('Não foi possível gravar');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Inclui um ProprietarioLog
     * 
     * @param array $dados 
     * 
     * @return boolean
     */
    public function incluir($dados) {
        if (!isset($dados[$this->name])) {
            return false;
        }
        unset($dados[$this->name]['codigo']);
        unset($dados[$this->name]['data_inclusao']);
        $this->create();
        $resultado = $this->save($dados);
        return $resultado;
    }

}