<div class='well'>
	<?php echo $bajax->form('TUsuaUsuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TUsuaUsuario', 'element_name' => 'operadores'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('usua_login', array('class' => 'input-small', 'placeholder' => 'Login', 'label' => false)) ?>
            <?php echo $this->BForm->input('pess_nome', array('class' => 'input-xlarge', 'placeholder' => 'Nome', 'label' => false)) ?>
            <?php echo $this->BForm->input('oras_eobj_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $status, 'empty' => 'Status')); ?>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaOperadores();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TUsuaUsuario/element_name:operadores/" + Math.random())
        });
    });', false);
?>