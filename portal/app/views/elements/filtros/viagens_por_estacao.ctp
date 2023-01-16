<div class='well'>
	<div id='filtros'>
		<?php echo $this->Bajax->form('RelatorioSm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RelatorioSm', 'element_name' => 'viagens_por_estacao'), 'divupdate' => '.form-procurar')) ?>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('eras_codigo', array('class' => 'input-large','label' =>FALSE, 'div'=>'control-group input', 'options'=>$estacao, 'empty' => 'Estação de Rastreamento')) ?>
				<?php echo $this->BForm->input('operador_logado', array('class' => 'input-large','label' =>FALSE, 'div'=>'control-group input', 'options'=>array('1' => 'Com Operador', '2' => 'Sem Operador'), 'empty' => 'Todos Operadores')) ?>
			</div>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar', 'id'=>'filtrar')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
	        <?php echo $this->BForm->end() ?>
	    </div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
	var div = jQuery("div.lista");
	bloquearDiv(div);
	div.load(baseUrl + "relatorios_sm/listagem_viagens_por_estacao/" + Math.random());
	$("#limpar-filtro").click(function(){
		$(".form-procurar :input").not(":button, :submit, :reset, :hidden").val("");
		$(".form-procurar form").submit();
	});
});', false);?>
<?php echo $this->Javascript->codeBlock('setInterval(function(){ location.reload();}, 100000);', false); ?>