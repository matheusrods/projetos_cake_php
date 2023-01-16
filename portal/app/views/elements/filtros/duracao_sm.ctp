<div class='well'>
    <?php echo $this->Bajax->form('DuracaoSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'DuracaoSm', 'element_name' => 'duracao_sm'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false)); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaDuracaoSM()
    });', false);
?>