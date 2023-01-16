<?php
class PontoEletronico extends AppModel {
    var $name = 'PontoEletronico';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'ponto_eletronico';
    var $primaryKey = 'codigo';
    var $belongsTo = array(
        'TipoPontoEletronico' => array(
            'className' => 'TipoPontoEletronico',
            'foreignKey' => FALSE,
            'conditions' => "TipoPontoEletronico.codigo = PontoEletronico.codigo_tipo_ponto_eletronico"
        ),
        'Usuario' => array(
            'foreignKey' => FALSE,
            'conditions' => "codigo_usuario = Usuario.codigo",
            'type' => 'INNER'
        ),
    );
   

    public function cadastrar($usuario, $ip,$tipo_ponto_eletronico) {    
        $umMinutoAtras = date('Y-m-d H:i:s', strtotime('-1 minute', strtotime($this->agora($usuario))));
        if ($this->dataHoraUltimoPonto($usuario) > $umMinutoAtras)
            return "Ponto já cadastrado a menos de 1 minuto.";
        
        $dados = array(
            'codigo_usuario' => (int)$usuario,
            'numero_ip' => $ip,
            'data_ponto' => $this->agora($usuario),
        );
        $dados['codigo_tipo_ponto_eletronico'] = $tipo_ponto_eletronico;
        if($this->incluir($dados)) {
            return $this->id;
        }        
    }
    
    public function ultimoDo($usuario) {
        return $this->find('first', array('order'=>array('data_ponto DESC'), 'conditions'=>array('codigo_usuario'=>$usuario)));
    }
    
    public function filtradoComCracha($filtros) {
        $conditions = array();
        $conditions['data_ponto >='] = AppModel::dateToDbDate2($filtros['data_inicial']) . ' 00:00:00';
        $conditions['data_ponto <='] = AppModel::dateToDbDate2($filtros['data_final']) . ' 23:59:59';
        if(!empty($filtros['codigo_usuario'])){
            $conditions['codigo_usuario'] = $filtros['codigo_usuario'];
        }
        
        $this->bindModel(array('belongsTo' => array(
            'Usuario' => array('foreignKey' => 'codigo_usuario')
        )));
        return $this->find('all', array('fields'=>array('Usuario.cracha', 
           "CONVERT(varchar,DATEADD(HOUR,3,DATEADD(HOUR,fuso_horario,created)) ,120) as hora_ponto",
        ), 'order'=>array('data_ponto DESC'), 'conditions'=>$conditions));
    }
    
  
    function convertFiltrosEmConditions($filtros){
        $conditions = array();
        if (!empty($filtros['codigo_usuario']))
            $conditions['codigo_usuario'] = $filtros['codigo_usuario'];

        if(!empty($filtros['data_inicial']) && !empty($filtros['data_final'])){
            $conditions['data_ponto >='] = AppModel::dateToDbDate2($filtros['data_inicial']) . ' 00:00:00';
            $conditions['data_ponto <='] = AppModel::dateToDbDate2($filtros['data_final']) . ' 23:59:59';
        }
        return $conditions;
    }
    
    private function dataHoraUltimoPonto($usuario) {
        $ultimoPonto = $this->ultimoDo($usuario);
        return AppModel::dateToDbDate2($ultimoPonto['PontoEletronico']['data_ponto']);
    }
    
    private function agora($usuario) {
        if (empty($this->agora))
            $this->agora = gmdate('Y-m-d H:i:s');
        
        $this->Usuario = ClassRegistry::init('Usuario');
        $usuario = $this->Usuario->find('first', array('conditions'=>array('codigo'=>$usuario), 'fields'=>array('fuso_horario', 'horario_verao')));
        
        if(date('I', strtotime($this->agora)) && $usuario['Usuario']['horario_verao'])
        	$usuario['Usuario']['fuso_horario']++;
        return date('Y-m-d H:i:s', strtotime($this->agora.' '.$usuario['Usuario']['fuso_horario'].' hours'));
    }

    function existeTipoRegistro($data){
        $verifica =  $this->find('count',array(
            'conditions' => array(
                'codigo_usuario' => $data['codigo_usuario'],
                'codigo_tipo_ponto_eletronico' => $data['tipos_ponto_eletronico'],
                'CONVERT(VARCHAR(20), [PontoEletronico].[data_ponto], 105)' => date('d-m-Y'),
            )));
        if($verifica > 0){
            return TRUE;
        }else{
            return FALSE;
        }

    }

    function verificaUsuarioConfigurado($usuario){
        $this->Usuario = ClassRegistry::init('Usuario');
        return $this->Usuario->find('count', array('conditions'=>array(
            'codigo'=>$usuario, 'cracha IS NOT NULL', 'fuso_horario IS NOT NULL', 'horario_verao IS NOT NULL')));
    }

    public function validaHorarioPontoEletronico($ponto_eletronico) {
        $escala = isset($ponto_eletronico['Usuario']['escala'])?$ponto_eletronico['Usuario']['escala']:FALSE;
        if ($escala){
            $this->UsuarioEscala = ClassRegistry::init('UsuarioEscala');
            return $this->UsuarioEscala->validaHorarioEscala($ponto_eletronico);
        }
        else {
            $this->UsuarioExpediente = ClassRegistry::init('UsuarioExpediente');            
            return $this->UsuarioExpediente->validaHorarioExpediente($ponto_eletronico);
        }        
    }

    public function verificaPossivelHorasExtrasNaoAutorizadas( ){
        $data_inicial    = date('Y-m-d 00:00:00');
        $data_final      = date('Y-m-d 23:59:59');
        $belongsTo       = $this->belongsTo;
        $this->belongsTo = array();
        $subquery = $this->find('sql', array(
            'fields' => 'PontoEletronico.codigo',
            'limit' => 1,
            'conditions'=>array(
                'codigo_tipo_ponto_eletronico' => 2,
                'codigo_usuario = Usuario.codigo',
                'data_ponto BETWEEN ? AND ? '=> array( $data_inicial, $data_final )
            )) 
        );
        $this->belongsTo = $belongsTo;
        $fields     = array('Usuario.codigo', 'Usuario.escala');
        $conditions = array(
            'codigo_tipo_ponto_eletronico' => 1,
            'data_ponto BETWEEN ? AND ? '=> array( $data_inicial, $data_final ),            
        );
        $conditions[] = "NOT EXISTS(".$subquery.")";
        return $this->find('all', compact('fields', 'conditions'));        
    }

    public function obtemHoraConfigurada($ponto_eletronico) {
        $escala = isset($ponto_eletronico['Usuario']['escala'])?$ponto_eletronico['Usuario']['escala']:FALSE;
        if ($escala){
            $this->UsuarioEscala = ClassRegistry::init('UsuarioEscala');
            return $this->UsuarioEscala->obtemHoraPontoEscala($ponto_eletronico);
        } else {
            $this->UsuarioExpediente = ClassRegistry::init('UsuarioExpediente');
            return $this->UsuarioExpediente->obtemHoraExpediente($ponto_eletronico);
        }        
    }
}
?>