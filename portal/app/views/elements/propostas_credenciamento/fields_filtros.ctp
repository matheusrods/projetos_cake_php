<div class='well'>
  <?php echo $bajax->form('PropostaCredenciamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PropostaCredenciamento', 'element_name' => 'propostas_credenciamento'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
    	<?php echo $this->BForm->input('codigo', array('class' => 'input-small', 'placeholder' => 'Código', 'label' => false)) ?>
    	<?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => false)) ?>
		<?php echo $this->BForm->hidden('input_id', array('value' => !empty($input_id)? $input_id : $this->data['PropostaCredenciamento']['input_id'])); ?>
		<?php echo $this->BForm->hidden('input_display', array('value' => !empty($input_display) ? $input_display : $this->data['PropostaCredenciamento']['input_display'])); ?>    	
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-credenciados', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>