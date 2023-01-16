<div class='well'>
	<?php echo $this->Bajax->form('TCdisCriterioDistribuicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCdisCriterioDistribuicao', 'element_name' => 'criterio_distribuicao'), 'divupdate' => '.form-procurar')) ?>
  		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_emb', 'Embarcador', true,'TCdisCriterioDistribuicao') ?>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_tra', 'Transportador', true,'TCdisCriterioDistribuicao') ?>
			<?php echo $this->BForm->input('prod_descricao', array('class' => 'input-medium', 'label' => 'Produto', 'type' => 'text')) ?>
			<?php echo $this->BForm->input('cdis_tecn_codigo', array('class' => 'input-medium','empty' => 'Selecione um' ,'label' => 'Tecnologia', 'options' => $tecnologias )) ?>
			<?php echo $this->BForm->input('cdis_ttra_codigo', array('class' => 'input-medium','empty' => 'Selecione um', 'label' => 'Tipo Transporte', 'options' => $ttransportes )) ?>
			<?php echo $this->BForm->input('cdis_cdfv_codigo', array('class' => 'input-medium','empty' => 'Selecione um', 'label' => 'Faixa de Valor', 'options' => $faixas )) ?>
			<?php echo $this->BForm->input('cdis_aatu_codigo', array('class' => 'input-medium','empty' => 'Selecione um', 'label' => 'Area Atuação', 'options' => $aatuacao )) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();		
		atualizaListaCriteriosDistribuicao();

		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCdisCriterioDistribuicao/element_name:criterio_distribuicao/" + Math.random())
		});
	});', false);
?>