<?php

class ProfissionalSerasa extends AppModel {

    var $name = 'ProfissionalSerasa';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbteleconsult';
    var $useTable = 'profissional_serasa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $httpSocket = null;
    var $belongsTo = array(
        'Profissional' => array(
            'className' => 'Profissional',
            'foreignKey' => 'codigo_profissional'
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
    
    function valorSerasa($codigo_profissional) {
        if (empty($codigo_profissional))
            return false;
        $data_limite = Date('Ymd H:i:s', strtotime('-180 day'));
        $condicoes = array('ProfissionalSerasa.codigo_profissional' => $codigo_profissional, 'ProfissionalSerasa.data_inclusao > ?' => $data_limite);
        $group = array('convert(varchar, ProfissionalSerasa.data_inclusao, 102)');
        $fields = array('convert(varchar, ProfissionalSerasa.data_inclusao, 102) as nova_data', 'sum(valor_ocorrencias) as total');
        $resultado = $this->find('first', array('conditions' => $condicoes, 'fields' => $fields, 'group' => $group, 'order' => 'nova_data desc'));
        return $resultado[0]['total'];
    }
    
    function quantidadeChequesSemFundo($codigo_profissional, $apelido_usuario) {
        if (empty($codigo_profissional) || empty($apelido_usuario))
            return false;
            
        $profissional = $this->Profissional->findByCodigo($codigo_profissional);
            
        $responseSerasa = $this->getHttpSocket()->post(URL_INFORMACOES . '/bcb/index/consulta-informacoes/resumo/1', array(
            'apelido' => $apelido_usuario,
            'codigoDocumento' => $profissional['Profissional']['codigo_documento'],
            'consultaProfissional' => 1
                ));

        $responseSerasa = json_decode($responseSerasa);

        if (!in_array($responseSerasa, array('finalizado', 'erro'))) {
            return false;
        }
        if ($responseSerasa == 'erro') {
            return false;
        }
        $data_limite = Date('Ymd H:i:s', strtotime('-180 day'));

        $condicoes = array('ProfissionalSerasa.codigo_profissional' => $codigo_profissional,
            'ProfissionalSerasa.data_inclusao > ?' => $data_limite,
            'ProfissionalSerasa.descricao' => 'Cheques sem fundo'
        );
        $group = array('convert(varchar, ProfissionalSerasa.data_inclusao, 102)');
        $fields = array('convert(varchar, ProfissionalSerasa.data_inclusao, 102) as nova_data', 'sum(quantidade_ocorrencias) as total');
        $resultado = $this->find('first', array('conditions' => $condicoes, 'fields' => $fields, 'group' => $group, 'order' => 'nova_data desc'));
        return $resultado[0]['total'];
    }

}

?>
