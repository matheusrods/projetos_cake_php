<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
    <?php echo $this->BForm->create('DemonstrativoDeServico', array('url' => array('controller' => 'clientes', 'action' => 'demonstrativo_de_servico_buonnycredit', $this->passedArgs[0]))); ?>
    <div class="fullwide inline escolher-cliente">
        <?php echo $this->Buonny->input_periodo($this) ?>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
    });', false);
?> 