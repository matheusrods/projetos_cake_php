<div class='well'>
  <?php echo $this->Bajax->form('DetalheGrupoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'DetalheGrupoExame', 'element_name' => 'detalhes_grupos_exames'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->BForm->input('codigo_grupo_economico', array('class' => 'small', 'type' => 'hidden')) ?>
    <?php echo $this->BForm->input('codigo', array('class' => 'small just-number',  'placeholder' => 'Código', 'label' => false)) ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        codigo_cliente = $("#codigo_cliente").val();
        setup_mascaras();
        var div = jQuery(".lista");
        bloquearDiv(div);
        $("#DetalheGrupoExameIndexForm").submit();
        div.load(baseUrl + "/detalhes_grupos_exames/listagem/"+codigo_cliente+"/" + Math.random());
    });
    jQuery("#limpar-filtro").click(function(){
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:DetalheGrupoExame/element_name:detalhes_grupos_exames/" + Math.random());
        window.location = baseUrl + "DetalhesGruposExames/busca_por_cliente";
    });
    ', false);
?>