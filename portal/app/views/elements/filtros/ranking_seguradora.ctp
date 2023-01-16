<div class='well'>
	<?php echo $this->Bajax->form('NotafisSeguradora', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'NotafisSeguradora', 'element_name' => 'ranking_seguradora'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
            <?php echo $this->BForm->input('mes', array('options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
            <?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaRankingSeguradora();
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:NotafisSeguradora/element_name:ranking_seguradora/" + Math.random())
        });
    });', false);
?>