<div class='well'>
	<?php echo $bajax->form('TipoRetorno', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TipoRetorno', 'element_name' => 'tipos_retornos'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('descricao', array('class' => 'input-medium', 'placeholder' => 'Descrição', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->label('proprietario', $this->BForm->checkbox('proprietario').'Proprietario', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
            <?php echo $this->BForm->label('profissional', $this->BForm->checkbox('profissional').'Profissional', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
            <?php echo $this->BForm->label('usuario_interno', $this->BForm->checkbox('usuario_interno').'Usuario Interno', array('class' => 'checkbox inline input-xlarge', 'escape'=>false)); ?> 
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "tipos_retornos/listagem/" + Math.random());
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TipoRetorno/element_name:tipos_retornos/" + Math.random())
        });
    });', false);

?>