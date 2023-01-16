<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();");
        exit;
    }
?>
<?php echo $this->Bajax->form('TVeicVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'senha_veiculo'))) ?>
<div class="well">
<p><strong>Placa : </strong><?= substr_replace($placa,'-',3,0)?></p>
</div>
<div class="row-fluid inline">
        <?php echo $this->BForm->input('veic_oras_codigo'); ?>
        <?php echo $this->BForm->input('veic_senha_proprietario', array('label' => 'Senha Proprietario')); ?>
        <?php echo $this->BForm->input('veic_senha_coacao', array('label' => 'Senha Coação',)); ?>
</div>
<div class="form-actions" style="clear:both;">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>