<?php

class ProprietarioSerasa extends AppModel {

    var $name = 'ProprietarioSerasa';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbteleconsult';
    var $useTable = 'proprietario_serasa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $httpSocket = null;
    var $belongsTo = array(
        'Proprietario' => array(
            'className' => 'Proprietario',
            'foreignKey' => 'codigo_proprietario'
        )
    );
    
    function getHttpSocket() {
        if ($this->httpSocket == null) {
            App::import('Core', 'HttpSocket');
            $this->setHttpSocket(new HttpSocket());
        }
        return $this->httpSocket;
    }

    function setHttpSocket($sk) {
        $this->httpSocket = $sk;
    }
    
    
    function valorSerasa($codigo_proprietario) {
        if (empty($codigo_proprietario))
            return false;
        $data_limite = Date('Ymd H:i:s', strtotime('-180 day'));
        $condicoes = array('ProprietarioSerasa.codigo_proprietario' => $codigo_proprietario, 'ProprietarioSerasa.data_inclusao > ?' => $data_limite);
        $group = array('convert(varchar, ProprietarioSerasa.data_inclusao, 102)');
        $fields = array('convert(varchar, ProprietarioSerasa.data_inclusao, 102) as nova_data', 'sum(valor_ocorrencias) as total');
        $resultado = $this->find('first', array('conditions' => $condicoes, 'fields' => $fields, 'group' => $group, 'order' => 'nova_data desc'));
        return $resultado[0]['total'];
    }
    
    function quantidadeChequesSemFundo($codigo_proprietario, $apelido_usuario) {
        if (empty($codigo_proprietario) || empty($apelido_usuario))
            return false;
            
        $proprietario = $this->Proprietario->findByCodigo($codigo_proprietario);
            
        $responseSerasa = $this->getHttpSocket()->post(URL_INFORMACOES . '/bcb/index/consulta-informacoes/resumo/1', array(
            'apelido' => $apelido_usuario,
            'codigoDocumento' => $proprietario['Proprietario']['codigo_documento'],
            'consultaProfissional' => 0
                ));

        $responseSerasa = json_decode($responseSerasa);

        if (!in_array($responseSerasa, array('finalizado', 'erro'))) {
            return false;
        }
        if ($responseSerasa == 'erro') {
            return false;
        }

        $data_limite = Date('Ymd H:i:s', strtotime('-180 day'));

        $condicoes = array('ProprietarioSerasa.codigo_proprietario' => $codigo_proprietario,
            'ProprietarioSerasa.data_inclusao > ?' => $data_limite,
            'ProprietarioSerasa.descricao' => 'Cheques sem fundo'
        );
        $group = array('convert(varchar, ProprietarioSerasa.data_inclusao, 102)');
        $fields = array('convert(varchar, ProprietarioSerasa.data_inclusao, 102) as nova_data', 'sum(quantidade_ocorrencias) as total');
        $resultado = $this->find('first', array('conditions' => $condicoes, 'fields' => $fields, 'group' => $group, 'order' => 'nova_data desc'));
        return $resultado[0]['total'];
    }
}

?>
