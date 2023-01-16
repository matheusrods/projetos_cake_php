<div class='well'>
	<?php echo $bajax->form('TipoAcao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TipoAcao', 'element_name' => 'tipos_acoes'), 'divupdate' => '.form-procurar', 'callback' => 'atualizaListaTiposAcoes')) ?>
    	<div class="row-fluid inline">
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('descricao', array('type' => 'text', 'class' => 'input-xxlarge', 'label' => 'Descrição', 'maxlength' => 255)) ?>
                <?php echo $this->BForm->input('classificacao', array('label' => 'Classificação', 'class' => 'input-small', 'options' => array(0 => 'PGR', 1 => 'PCMSO'), 'empty' => 'Todos')); ?>
                <?php echo $this->BForm->input('status', array( 'label' => 'Status', 'class' => 'input-small', 'options' => array(0 => 'Inativos', 1 => 'Ativos'), 'empty' => 'Todos')); ?>
            </div>
    	</div> 
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TipoAcao/element_name:tipos_acoes/" + Math.random());
        });
    });
</script>