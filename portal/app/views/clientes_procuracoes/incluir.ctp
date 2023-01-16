<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaClientesProcuracoes(".$this->passedArgs[0].")");
        exit;
    }
?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_datepicker(); });'); ?>

<?php echo $bajax->form('ClienteProcuracao',array('url' => array('action' => 'incluir', $this->data['ClienteProcuracao']['codigo_cliente']))) ?>
<?php echo $this->BForm->hidden('codigo_cliente') ?>
<div class="fullwide">   
    <?php echo $this->BForm->input('data_vigencia_inicio', array('type' => 'hidden')); ?>
    <?php echo $this->BForm->input('data_vigencia_fim', array('type' => 'text', 'label' => 'Término da vigência', 'class' => 'text-small data')) ?>
</div>
<div class="fullwide">
    <?php echo $this->BForm->input('observacao', array('type' => 'textarea', 'label' => 'Observação', 'class' => 'text-medium')) ?>
</div>
<div class="fullwide">   
<?php echo $this->BForm->end(array('label' => 'Adicionar', 'div' => array('class' => 'salvar_procuracao'))); ?>
</div>