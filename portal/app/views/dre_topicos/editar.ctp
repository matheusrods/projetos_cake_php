<?php echo $this->BForm->create('DreTopico'); ?>
<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('numero', array('label' => 'Número', 'class' => 'input-mini')) ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Descrição', 'class' => 'input-large')) ?>
    <?php echo $this->BForm->input('tipo_topico', array('label' => 'Tipo:', 'options'=>array(1 => 'Regras', 2=>'Fórmula'), 'class' => 'input-medium', 'onchange' => 'toggleVisibilidade()')) ?>
    <?php echo $this->BForm->input('formula', array('label' => 'Fórmula', 'class' => 'input-xlarge')) ?>
</div>
<div class='row-fluid inline'>
	<h4>Regras</h4>
	<table class='table table-striped regras'>
		<thead>
			<tr>
				<th class='input-large'>Centro de Custo</th>
				<th class='input-large'>Grupo</th>
				<th class='input-large'>Subgrupo</th>
				<th class='input-large'>
					Ação
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_regra()")); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->element('/dre_topicos/linha_regra', array('indice'=>'#', 'ccusto_codigo'=>'', 'grflux_codigo'=>'', 'sbflux_codigo'=>'', 'modelo'=>true)); ?>
			<?php if(isset($this->data['DreTopicoRegra'])): ?>
				<?php foreach($this->data['DreTopicoRegra'] as $key=>$regra): ?>
					<?php echo $this->element('/dre_topicos/linha_regra', array('indice'=>$key, 'ccusto_codigo'=>$regra['ccusto'], 'grflux_codigo'=>$regra['grflux'], 'sbflux_codigo'=>$regra['sbflux'], 'modelo'=>false)); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Buonny->link_css('dre'); ?>
<?php echo $this->Javascript->codeBlock('
    function toggleVisibilidade() {
		var tipo_topico = jQuery("#DreTopicoTipoTopico").val();
		if(tipo_topico == 2){
			jQuery("tbody tr:visible").remove();
		}else{
			jQuery("#DreTopicoFormula").val("");
		}
        jQuery("#DreTopicoFormula").parent().toggle(tipo_topico == "2");
        jQuery(".regras").parent().toggle(tipo_topico == "1");
    }
    toggleVisibilidade();
		
	function carregaSubgrupo(grupo){
		var grupo_codigo = jQuery(grupo).val();
		var sbflux = jQuery(grupo).parent().parent().parent().find(".subgrupo");
		sbflux.html("<option>Aguarde...</option>");
		jQuery.get("/portal/dre_topicos/lista_subgrupos/"+grupo_codigo, function(data){
			sbflux.html(data);
			sbflux.val(sbflux.parent().parent().find(":hidden").val());
		});
	}
		
	jQuery(".grupo").each(function(){
		carregaSubgrupo(this);
	});
		
	function adiciona_regra() {
		var indice = jQuery(".regras tbody tr").size() - 1;
		var nova_linha_regra = jQuery(".regra-modelo").clone(); 
		nova_linha_regra.removeClass("regra-modelo");
		nova_linha_regra.html(nova_linha_regra.html().replace(/#/g, indice));
		nova_linha_regra.appendTo(".regras tbody");
	}
		
	function remove_regra(linha) {
		linha.remove();
	}
'); ?>