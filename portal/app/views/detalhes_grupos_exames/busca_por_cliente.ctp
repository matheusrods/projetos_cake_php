<div class='form-procurar-cliente'> 
    <div class='well'>
        <?php echo $this->BForm->create('DetalheGrupoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'DetalhesGruposExames', 'action' => 'busca_por_cliente'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php 
    echo $this->Javascript->codeBlock(" 
        setup_mascaras();
    ");
?>