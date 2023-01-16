 <?php
class UsuarioExpediente extends AppModel {
    var $name = 'UsuarioExpediente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_expediente';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'saida' => array(            
            'validaHorarioEntrada' => array(
                'rule' => 'validaHorarioEntrada',
                'message' => 'Horário invalido!'
            )
        ),        
        'entrada' => array(            
            'validaHorarioSaida' => array(
                'rule' => 'validaHorarioSaida',
                'message' => 'Horário invalido!'
            )
        ),               
    );

    public function validaHorarioExpediente($ponto_eletronico) {
        $codigo_usuario = $ponto_eletronico['Usuario']['codigo'];
        $tipo_ponto     = $ponto_eletronico['PontoEletronico']['codigo_tipo_ponto_eletronico'];
        $data_ponto     = strtotime($this->dateToDbDate($ponto_eletronico['PontoEletronico']['data_ponto']));
        $hora           = date('H:i', $data_ponto);
        $dia_semana     = date('N',$data_ponto);        
        $conditions     = array('codigo_usuario' => $codigo_usuario, 'dia_semana' => $dia_semana );
        if ($tipo_ponto == 1) {
            $hora = date('H:i',(strtotime($hora) - (30*60) ) );
            array_push($conditions, array('entrada <=' => $hora )); 
        } else {
            $hora = date('H:i',(strtotime($hora) + (30*60) ) );        
            array_push($conditions,array('saida >=' => $hora ));
        }  
        $retorno = $this->find('count', compact('conditions'));
        return $retorno > 0;      
    }    

    public function preveHoraExtra(){
        $hora_saida = date('H:i:s'); 
        $dia_semana = date('N');
        $PontoEletronico = &ClassRegistry::init('PontoEletronico');
       
        $this->bindModel(array('belongsTo' => array(
            'PontoEletronico' => array('foreignKey' => False,
                'conditions' => array(
                    'UsuarioExpediente.codigo_usuario = PontoEletronico.codigo_usuario',
                    'PontoEletronico.codigo_tipo_ponto_eletronico = 1'
                ),
                'type' => 'INNER'
            ) 
        )));

        $conditions = array('UsuarioExpediente.dia_semana <=' => $dia_semana);
        array_push($conditions,array('cast(DATEADD(mi,20,saida) as time) <'=>$hora_saida)); 
        array_push($conditions,array('cast(data_ponto as date) <= cast(getdate() as date)')); 

        $subselect = $PontoEletronico->find('sql', 
            array(
                'fields' => 'PontoEletronico.codigo_usuario',
                'limit' => 1,
                'conditions' => array(
                    'PontoEletronico.codigo_usuario = UsuarioExpediente.codigo_usuario',
                    'PontoEletronico.codigo_tipo_ponto_eletronico = 2',
                    'cast(data_ponto as date) < cast(getdate() as date)'
                ),                    
            ));        
        
        $subselect = str_replace("LEFT","INNER",$subselect);
        $conditions[] = "NOT EXISTS(".$subselect.")";
        $fields = array('UsuarioExpediente.*');         
        $retorno = $this->find('all',compact('conditions','fields'));
        
        return $retorno;
    }

    public function incluir_expediente( $dados_expediente ){
        $entrada = ($dados_expediente['UsuarioExpediente']['entrada'] == '__:__' ? NULL : $dados_expediente['UsuarioExpediente']['entrada']);
        $saida   = ($dados_expediente['UsuarioExpediente']['saida']   == '__:__' ? NULL : $dados_expediente['UsuarioExpediente']['saida']);
        $entrada = !empty($entrada) ? $entrada : NULL;
        $saida   = !empty($saida)   ? $saida   : NULL;
        if( isset($dados_expediente['UsuarioExpediente']['codigo_usuario']) ){
            $conditions = array(
                'codigo_usuario' => $dados_expediente['UsuarioExpediente']['codigo_usuario'],
                'dia_semana' => $dados_expediente['UsuarioExpediente']['dia_semana']
            );
            $expediente = $this->find('first', compact('conditions') );
            if( $expediente ) {
                $expediente['UsuarioExpediente']['entrada'] = $entrada;
                $expediente['UsuarioExpediente']['saida']   = $saida;
                return $this->atualizar( $expediente );
            } else{
                return $this->incluir( $dados_expediente );
            }
        }
    }


    function validaHorarioEntrada(){
        if( isset($this->data['UsuarioExpediente']['entrada']) && $this->data['UsuarioExpediente']['entrada'] ){
            $horas = substr($this->data['UsuarioExpediente']['entrada'], 0,2);
            $minutos = substr($this->data['UsuarioExpediente']['entrada'], 3,2);            
            if ((($horas > 23) || ($minutos > 59)) || ( !is_numeric($horas) || !is_numeric($minutos)) ) {
                return false;
            }            
        }
        return true;
    }
    
    function validaHorarioSaida(){
        if( isset($this->data['UsuarioExpediente']['saida']) && $this->data['UsuarioExpediente']['saida'] ){
            $horas = substr($this->data['UsuarioExpediente']['saida'], 0,2);
            $minutos = substr($this->data['UsuarioExpediente']['saida'], 3,2);
            if ((($horas > 23) || ($minutos > 59)) || ( !is_numeric($horas) || !is_numeric($minutos)) ) {
                return false;
            }
        }
        return true;
    }

    public function obtemHoraExpediente($ponto_eletronico) {
        $codigo_usuario = $ponto_eletronico['Usuario']['codigo'];
        $tipo_ponto     = $ponto_eletronico['PontoEletronico']['codigo_tipo_ponto_eletronico'];
        $data_ponto     = strtotime($this->dateToDbDate($ponto_eletronico['PontoEletronico']['data_ponto']));        
        $dia_semana     = date('N',$data_ponto);
        $conditions     = array('codigo_usuario' => $codigo_usuario, 'dia_semana' => $dia_semana );
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'className'  => 'Usuario',
                    'foreignKey' => 'codigo_usuario'),
            ),
        ));
        return $this->find('first', compact('conditions','fields'));        
    }  

}
?>