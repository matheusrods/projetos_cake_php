<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();
        carrega_escala(".$codigo_usuario.");
        function carrega_escala(codigo_usuario){
            var div = $('#escalas');
            bloquearDiv(div);
            $.get(baseUrl + 'usuarios_escalas/carrega_usuario_escala/'+codigo_usuario+'/'+Math.random(),function(data){
                div.html(data);
                div.unblock();
            });
        }");exit;
    }
?>
<?php echo $this->Bajax->form('UsuarioEscala',array('url' => array('controller'=>'usuarios_escalas', 'action' => 'incluir', $this->passedArgs[0]))) ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->input("UsuarioEscala.data_entrada", array('label' => 'Data Entrada', 'class' => 'input-small data', 'type'=>'text')) ?>
    <?php echo $this->BForm->input("UsuarioEscala.entrada", array('type'=>'text','label' => 'Entrada', 'class' => 'hora input-mini'))?>
    <?php echo $this->BForm->input("UsuarioEscala.data_saida", array('label' => 'Data Saída', 'class' => 'input-small data', 'type'=>'text')) ?>
    <?php echo $this->BForm->input("UsuarioEscala.saida", array('type'=>'text','label' => 'Saída', 'class' => 'hora input-mini' ))?>
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_time();
        setup_datepicker();
    });', false);?>