<?php

class ClienteProcuracao extends AppModel {

    public $name = 'ClienteProcuracao';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'cliente_procuracao';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $_mode = 'KIXa2V3bgEGdvBybgU3b4lWZkBSZgMXah1WZkBSdvRHbhhXZgU2cgMmd';
    var $virtualFields = array(
        'restante' => "datediff(SECOND, getDate(), ClienteProcuracao.data_vigencia_fim)"
    );
    var $validate = array(
        'data_vigencia_inicio' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe uma data de inicio'
        ),
        'data_vigencia_fim' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe uma data de fim'
        ),
    );

    public function incluir($data = '') {
        try {
            $this->create();
            
            if (!empty($data['ClienteProcuracao']['data_vigencia_fim'])) {
                $data['ClienteProcuracao']['data_vigencia_fim'] .= ' 23:59:59';
            }

            if (!empty($data[$this->name]['codigo'])) {
                throw new Exception('Não é permitido informar um código na inclusão');
            }

            $result = $this->save($data);

            if ($result) {
                return true;
            } else {
                throw new Exception('Não foi possível incluir');
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function inativar($codigo_procuracao) {
        $procuracao = $this->findByCodigo($codigo_procuracao);
        if ($procuracao) {
            $procuracao[$this->name]['data_inativacao'] = date('d/m/Y H:i:s');
            $result = $this->save($procuracao);
            return $result;
        }
        return false;
    }
    
    public function reativar($codigo_procuracao) {
        $procuracao = $this->findByCodigo($codigo_procuracao);
        if ($procuracao) {
            $procuracao[$this->name]['data_inativacao'] = null;
            $result = $this->save($procuracao);
            return $result;
        }
        return false;
    }

    public function listarProcuracoesDoCliente($codigo_cliente) {
        $result = $this->find('all', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
            ),
            'order' => 'codigo desc'
                ));
        return $result;
    }

}