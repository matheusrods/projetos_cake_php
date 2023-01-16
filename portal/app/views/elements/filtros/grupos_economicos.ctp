<div class='well'>
  <?php echo $this->Bajax->form('GrupoEconomico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoEconomico', 'element_name' => 'grupos_economicos'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "/grupos_economicos/listagem/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:GrupoEconomico/element_name:grupos_economicos/" + Math.random())
        });
    });', false);
?>