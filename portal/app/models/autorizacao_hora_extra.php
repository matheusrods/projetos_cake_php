<?php
class AutorizacaoHoraExtra extends AppModel {
    var $name = 'AutorizacaoHoraExtra';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'autorizacao_hora_extra';
    var $primaryKey = 'codigo';

  

    function dataValida($check) {
        $timestamp = strtotime($this->dateTimeToDbDateTime($check['data_hora_extra_de']));
        return $timestamp > time();
    }

    function validacoes($data,$valida = false){
        if(empty($data['AutorizacaoHoraExtra']['motivo_hora_extra'])){ 
            return 'Informe o motivo da hora extra';
        }
        if(!$valida){
            if(empty($data['AutorizacaoHoraExtra']['codigo_usuario'])){
                return 'Informe o usuário';
            }
            if(empty($data['AutorizacaoHoraExtra']['data_hora_extra'])){ 
                return 'Informe a data de inicio e fim';
            }
            if(!$this->combinacao_unica($data)){
                return 'Usuário e data já cadastrados';
            }
            if(!$this->combinacao_unica($data)){
                return 'Usuário e data já cadastrados';
            }
            if(strtotime($data['AutorizacaoHoraExtra']['data_hora_extra']) < strtotime(date('Y-m-d'))){ 
                return 'Data menor que hoje';
            }

            if($this->diferenca_mes($data)){ 
                return 'Data menor que hoje';
            }
        }
        return TRUE;
    }
    function diferenca_mes($data){      
        $data_final = strtotime(AppModel::dateToDbDate2($data['AutorizacaoHoraExtra']['data_hora_extra_ate']));
        $data_inicial = strtotime(AppModel::dateToDbDate2($data['AutorizacaoHoraExtra']['data_hora_extra_de']));
        $seconds_diff = $data_final - $data_inicial;
        $dias = floor($seconds_diff/3600/24);
        if ($dias > 31) {
            return TRUE;
        }
    }    
    function combinacao_unica($data){  
        $conditions = array(
            'codigo_usuario' => $data['AutorizacaoHoraExtra']['codigo_usuario'],
            'data_hora_extra' => AppModel::dateTimeToDbDateTime2($data['AutorizacaoHoraExtra']['data_hora_extra']).' 00:00:00',
        );
        return $this->find('count', compact('conditions')) == 0;
    }

    function bindAutorizacaoHoraExtra(){
        $this->bindModel(
            array(
                'hasOne'=>array(                    
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array("Usuario.codigo = AutorizacaoHoraExtra.codigo_usuario"),
                    ),
                    'Gestor' => array(
                        'className'  =>  'Gestor',
                        'foreignKey' => false,
                        'conditions' => array("Gestor.codigo = AutorizacaoHoraExtra.codigo_gestor"),
                    ),                   
            )), false
        ); 
        
    }

    function converteFiltrosEmConditions($dados){
        $conditions = array();
        if (!empty($dados['AutorizacaoHoraExtra']['codigo_usuario'])) {
            $conditions['AutorizacaoHoraExtra.codigo_usuario'] = $dados['AutorizacaoHoraExtra']['codigo_usuario'];
        }
        if (!empty($dados['AutorizacaoHoraExtra']['data_hora_extra'])) {
            $conditions['AutorizacaoHoraExtra.data_hora_extra'] = AppModel::dateTimeToDbDateTime2($dados['AutorizacaoHoraExtra']['data_hora_extra']).' 00:00:00';
        }        
        return $conditions;
    }

    function permissaoHoraExtra($codigo){
        if($this->find('count',array('conditions' => array(
            'codigo_usuario' => $codigo,
            'data_hora_extra' => date('Y-m-d 00:00:00')
        ))) > 0){
            return TRUE;
        }
        return FALSE; 
        
    }

    function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    function incluir_hora_extra($data){
        $retorno  = false;
        $de = AppModel::dateTimeToDbDateTime2($data['AutorizacaoHoraExtra']['data_hora_extra_de']); 
        $ate = AppModel::dateTimeToDbDateTime2($data['AutorizacaoHoraExtra']['data_hora_extra_ate']);

        $dias = round((strtotime($ate)-strtotime($de))/86400);     
        if($dias > 0){
            for($i = 0; $i < $dias+1; $i++) {  
                    $nova_data = strtotime("$de + $i day");             
                    $data_periodo =  date('Y-m-d', $nova_data);              
                    $data['AutorizacaoHoraExtra']['data_hora_extra'] = $data_periodo;
                 
                    $validar = $this->validacoes($data);
                    if($validar === TRUE){
                        if(!parent::incluir($data)){
                            return FALSE;
                        }
                        $retorno['sucesso'] = TRUE;
                    }else{
                        $retorno['erro'] = $validar;
                    }    
               
            }
        }else{
            $retorno['erro'] = 'Data final maior que a data de inicio';
        }     
        return $retorno;
    }


}    
?>