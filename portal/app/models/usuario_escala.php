 <?php
class UsuarioEscala extends AppModel {
    var $name = 'UsuarioEscala';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_escala';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(        
        'data_entrada' => array(
            'required' => true,
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a data de entrada',
            ),
            'validaEntrada' => array(
                'rule' => 'validaEntrada',
                'message' => 'Data de entrada inválida',
            ),            
        ),        
        'data_saida' => array(
            'required' => true,
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a data de saída',
             ),
            'validaSaida' => array(
                'rule' => 'validaSaida',
                'message' => 'Data de saída inválida',
            ),
        ),                
    );

    function validaEntrada(){
        $entrada = ($this->data['UsuarioEscala']['entrada'] == '__:__' ? NULL : $this->data['UsuarioEscala']['entrada']);
        $entrada = !empty($entrada) ? $entrada : NULL;        
        if( !empty($this->data['UsuarioEscala']['data_entrada']) && !empty($entrada) ) {
            $horas = substr($this->data['UsuarioEscala']['entrada'], 0,2);
            $minutos = substr($this->data['UsuarioEscala']['entrada'], 3,2);
            if ((($horas > 23) || ($minutos > 59))) {
                return false;
            }
            $this->data['UsuarioEscala']['data_entrada'] = $this->data['UsuarioEscala']['data_entrada'].' '.$this->data['UsuarioEscala']['entrada'];
        } else {
            return false;            
        }  
        return true;
    }
    
    function validaSaida(){        
        $saida   = ($this->data['UsuarioEscala']['saida']   == '__:__' ? NULL : $this->data['UsuarioEscala']['saida']);
        $saida   = !empty($saida)   ? $saida   : NULL;
        if( !empty($this->data['UsuarioEscala']['data_saida']) && !empty($saida) ) {
            $horas = substr($this->data['UsuarioEscala']['saida'], 0,2);
            $minutos = substr($this->data['UsuarioEscala']['saida'], 3,2);
            if ((($horas > 23) || ($minutos > 59))) {
                return false;
            }
            $this->data['UsuarioEscala']['data_saida'] = $this->data['UsuarioEscala']['data_saida'].' '.$this->data['UsuarioEscala']['saida'];
        } else {
            return false;            
        }
        return true;
    }

    public function validaHorarioEscala($ponto_eletronico) {
        $codigo_usuario = $ponto_eletronico['Usuario']['codigo'];
        $tipo_ponto  = $ponto_eletronico['PontoEletronico']['codigo_tipo_ponto_eletronico'];
        $data_ponto  = $ponto_eletronico['PontoEletronico']['data_ponto']; 
        $data_inicio = date('Ymd 00:00:00');
        $data_final  = date('Ymd 23:59:59');
        $conditions  = array(
            'codigo_usuario' => $codigo_usuario,
            'data_saida BETWEEN ? AND ?' => array( $data_inicio, $data_final )
        );
        if ($tipo_ponto == 1) {
            array_push($conditions,array('DATEADD(mi,-30,data_entrada) <='=> $this->dateToDbDate($data_ponto) )); 
        } else {
            array_push($conditions,array('DATEADD(mi,30,data_saida) >='=> $this->dateToDbDate($data_ponto) )); 
        }
        $retorno = $this->find('count', compact('conditions'));
        return ($retorno > 0);
    }  

    public function obtemHoraPontoEscala($ponto_eletronico) {
        $codigo_usuario = $ponto_eletronico['Usuario']['codigo'];
        $tipo_ponto  = $ponto_eletronico['PontoEletronico']['codigo_tipo_ponto_eletronico'];
        $data_ponto  = $ponto_eletronico['PontoEletronico']['data_ponto']; 
        $data_inicio = date('Ymd 00:00:00');
        $data_final  = date('Ymd 23:59:59');
        $conditions  = array(
            'codigo_usuario' => $codigo_usuario,
            'data_saida BETWEEN ? AND ?' => array( $data_inicio, $data_final )
        );
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario' => array(
                    'className'  => 'Usuario',
                    'foreignKey' => 'codigo_usuario'),
            ),
        ));        
       return $this->find('first', compact('conditions'));
    }           

}
?>