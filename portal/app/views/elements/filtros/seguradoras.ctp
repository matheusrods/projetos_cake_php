<div class='well'>
    <?php echo $bajax->form('Seguradora', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Seguradora', 'element_name' => 'seguradoras'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => false)) ?>
            <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
            <?php echo $this->BForm->input('status', array('label' => false, 'class' => 'input-large', 'default' => 1,'options' => array(1 => 'Ativo',2=> 'Inativo'), 'empty' => 'Selecione o status')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaSeguradoras("seguradoras");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Seguradora/element_name:seguradoras/" + Math.random())
        });
    });', false);

?>
