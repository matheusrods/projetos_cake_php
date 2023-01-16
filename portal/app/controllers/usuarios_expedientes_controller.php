<?php
class UsuariosExpedientesController extends AppController {
	var $name = 'UsuariosExpedientes';
	var $uses      = array('Usuario', 'UsuarioExpediente');    
    var $helpers   = array('Paginator');
	
    function carrega_usuario_expediente( $codigo_usuario ){
        $expediente  = $this->UsuarioExpediente->find('all', array(
            'conditions'=>array('codigo_usuario'=>$codigo_usuario),
            'order' => 'dia_semana'
        ));
        $dados_expediente = array();
        foreach ($expediente as $key => $value) {
            $dados_expediente[$value['UsuarioExpediente']['dia_semana']] = $value;
        }
        $dias_semana = array(1=>'Segunda-Feira', 2=>'Terça-Feira', 3=>'Quarta-Feira', 4=>'Quinta-Feira', 5=>'Sexta-Feira', 6=>'Sábado',7=>'Domingo');
        $this->set(compact('dados_expediente', 'dias_semana'));
    }


}
?>
