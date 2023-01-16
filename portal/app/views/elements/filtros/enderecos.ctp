<div class='well'>
    <?php echo $bajax->form('Endereco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Endereco', 'element_name' => 'enderecos'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('endereco_cep', array('class' => 'input-mini', 'placeholder' => 'CEP', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('endereco_logradouro', array('class' => 'input-xlarge', 'placeholder' => 'Logradouro', 'label' => false)) ?>
            <?php echo $this->BForm->input('endereco_bairro', array('class' => 'input-medium', 'placeholder' => 'Bairro', 'label' => false)) ?>
            <?php echo $this->BForm->input('endereco_cidade', array('class' => 'input-medium', 'placeholder' => 'Cidade', 'label' => false)) ?>
            <?php echo $this->BForm->input('endereco_estado', array('class' => 'input-medium', 'placeholder' => 'Estado', 'label' => false)) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaEnderecos();
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Endereco/element_name:enderecos/" + Math.random())
        });
    });', false);

?>
