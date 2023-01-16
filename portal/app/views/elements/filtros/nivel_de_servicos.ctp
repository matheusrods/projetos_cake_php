<?php $filtrado = (isset($this->data['TViagViagem']['codigo_cliente']) && $this->data['TViagViagem']['codigo_cliente'] != null && count($this->validationErrors) == 0); ?>
<div class='well'>
    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
	    <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'nivel_de_servicos'))) ?>
	    <div class="row-fluid inline">
	    	<?php echo $this->Buonny->input_periodo($this) ?>
			<?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
            <?php echo $this->BForm->input('mesclar_prazo_adiantado', array('type'=>'checkbox', 'label' => 'Mesclar adiantado e no prazo', 'class' => 'input-small')) ?>
		</div>
		<div class="row-fluid inline" id="div-tipo-alvo">
			<?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo','force_model' => 'TViagViagem', 'input_codigo_cliente' => 'codigo_cliente')))?>
		</div>
	    <div class="row-fluid inline">
			<span class="label label-info">Agrupar por:</span>
            <div id='agrupamento'>
				<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
			</div>
		</div>
		<div class="row-fluid inline descricao_grafico" style="display:none">
			<span class="label label-info">Descrição do Gráfico:</span>
            <div>
				<?php echo $this->BForm->input('descricao_grafico', array('type' => 'radio', 'options' => array(1 => 'Descrição', 2 => 'Código Externo'), 'default' => 2, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
			</div>
		</div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	   	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	    <?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
	    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "viagens/nivel_de_servicos_listagem/" + Math.random());':'').'

    	$.placeholder.shim();
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
		

		$("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:TViagViagem/element_name:nivel_de_servicos/" + Math.random())
        });

		$.tablesorter.addParser({
			debug:true,
			id: "qtd",
			is: function(s) {
				// return false so this parser is not auto detected
				// poderia ser detectado pelo simbolo do real R$
				return false;
			},
			format: function(s) {
			   return $.tablesorter.formatInt(s.replace(new RegExp(/\(\d*\)/g),""));
			},
			type: "numeric"
		});

		jQuery("table.nivel-servico").tablesorter({
			headers: {
				2: {sorter: "qtd"},
				3: {sorter: "qtd"},
				4: {sorter: "qtd"}
			},
			widgets: ["zebra"]
		});

		$("[name*=\"data[TViagViagem][agrupamento]\"]").change(function(){
			if($(this).val() == 1 || $(this).val() == 4){
				$(".descricao_grafico").show();
			}else{
				$(".descricao_grafico").hide();
			}
		});

		$("[name*=\"data[TViagViagem][agrupamento]\"]:checked").change();

    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>