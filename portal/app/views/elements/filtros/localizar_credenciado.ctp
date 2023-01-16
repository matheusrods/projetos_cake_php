<div class='well'>
	<div id="filtros">
		<?php echo $this->Bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'localizar_credenciado'), 'divupdate' => '.form-procurar')) ?>
		<div class="row-fluid inline endereco">
			<?php echo $this->BForm->hidden('Cliente.codigo', array('value' => isset($this->data['Cliente']['codigo']) ? $this->data['Cliente']['codigo'] : '')); ?>
			<?php echo $this->BForm->hidden('Cliente.razao_social', array('value' => isset($this->data['Cliente']['razao_social']) ? $this->data['Cliente']['razao_social'] : '')); ?>
			<?php echo $this->BForm->hidden('Cliente.endereco', array('value' => isset($this->data['Cliente']['endereco']) ? $this->data['Cliente']['endereco'] : '')); ?>
			<?php echo $this->BForm->hidden('Cliente.codigo_servico', array('value' => isset($this->data['Cliente']['codigo_servico']) ? $this->data['Cliente']['codigo_servico'] : '')); ?>
			<?php echo $this->BForm->hidden('Cliente.codigo_unidade', array('value' => isset($this->data['Cliente']['codigo_unidade']) ? $this->data['Cliente']['codigo_unidade'] : $unidade)); ?>

			<?php echo $this->BForm->hidden('var_aux', array('value' => $var_aux)); ?>
			
			<?php echo $this->BForm->input('razao_social', array('label' => 'Unidade', 'placeholder' => 'Unidade', 'class' => 'input-xlarge', 'value' => isset($this->data['Cliente']['razao_social']) ? $this->data['Cliente']['razao_social'] : '', 'disabled' => 'true')); ?>
			<?php echo $this->BForm->input('endereco', array('label' => 'Endereço', 'placeholder' => 'Endereço', 'class' => 'input-xxlarge', 'value' => isset($this->data['Cliente']['endereco']) ? $this->data['Cliente']['endereco'] : '', 'disabled' => 'true')); ?>
			<?php echo $this->BForm->input('raio', array('label' => 'Raio (KM)', 'placeholder' => 'Raio (KM)', 'class' => 'input-small numeric', 'value' => isset($this->data['Cliente']['raio']) ? $this->data['Cliente']['raio'] : '')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'id' => 'buscar-filtro', 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		jQuery('#limpar-filtro').click(function(){
			bloquearDiv(jQuery('.form-procurar'));
			jQuery('.form-procurar').load(baseUrl + '/filtros/limpar/model:Fornecedor/element_name:localizar_credenciado/'+ Math.random())
		});	

		jQuery('#buscar-filtro').click(function(){
	        var div = jQuery('div.lista-credenciados');
	        bloquearDiv(div);
			div.load(baseUrl + 'clientes_implantacao/localizar_credenciados_listagem/' + Math.random());
		});
	})
")  ?>