<div class='well'>
  <?php echo $this->Bajax->form('GrupoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoExame', 'element_name' => 'grupos_exames'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>
    <?php echo $this->BForm->input('codigo_detalhe_grupo_exame', array('class' => 'small', 'type' => 'hidden')) ?>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Voltar', 'javascript:void(0)', array('id' => 'voltar', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        $("#GrupoExameIndexForm").submit();
        div.load(baseUrl + "/grupos_exames/listagem/" + Math.random());
    });
    jQuery("#voltar").click(function(){
        codigo_cliente = $("#codigo_cliente").val();
        window.location = baseUrl + "DetalhesGruposExames/index/" + codigo_cliente;
    });
    ', false);
?>