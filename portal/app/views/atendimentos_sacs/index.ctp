<div class='well'>
    <?php echo $this->BForm->create('AtendimentoSac', array('type' => 'get', 'autocomplete' => 'off', 'url' => array('controller' => 'AtendimentosSacs', 'action' => 'index'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo required', 'label' => false, 'placeholder' => 'Placa', 'readonly' => $this->layout == 'new_window')) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn', 'id' => 'gerar')); ?>
    <?php echo $this->BForm->end() ?>
</div>


<?php $this->addScript($this->Javascript->codeBlock("
	jQuery(document).ready(function() {
    setup_mascaras();
})")); 
?>