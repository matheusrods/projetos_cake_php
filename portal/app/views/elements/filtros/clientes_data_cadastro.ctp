<div class='well'>
    <?php echo $bajax->form('ClienteData', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteData', 'element_name' => 'clientes_data_cadastro'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->hidden('codigo_usuario'); ?>
            <?php echo $form->input('data_inicio', array('class' => 'data input-small', 'placeholder' => 'A partir de', 'label' => false, 'type' => 'text')); ?>
            <?php echo $form->input('data_fim', array('class' => 'data input-small', 'placeholder' => 'Data até', 'label' => false, 'type' => 'text')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-filtro')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        atualizaListaClientesDataCadastro();
        setup_datepicker();
    });", false);
?>
