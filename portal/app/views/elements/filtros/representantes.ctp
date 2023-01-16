<div class='well'>
    <?php echo $bajax->form('Representante', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Representante', 'element_name' => 'representantes'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Representante', 'label' => false)) ?>  
        <?php echo $this->BForm->input('codigo_endereco_regiao', array('label' => false, 'class' => 'input-medium', 'options' => $regioes, 'empty' => 'Todas as Regiões')) ?>
        <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-medium', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Ativos e Inativos')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaRepresentantes();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Representante/element_name:representantes/" + Math.random())
        });      
        
    });', false);
?>
