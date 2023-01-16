<div class='well'>
    <?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => 'usuario_expediente'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login')) ?>
            <?php echo $this->BForm->input('nome', array('class' => 'input-medium', 'label' => 'Nome')) ?>
            <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium just-number', 'label' => 'CPF/CNPJ')) ?>
        </div>
        <?php echo $this->Javascript->codeBlock('$(document).ready(function() {setup_mascaras();});');?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/listagem_usuario_expediente/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Usuario/element_name:usuario_expediente/" + Math.random())
        });
    });', false);?>