<div class='row-fluid inline'>
	<div class="row-fluid">
		<?php echo $this->BForm->input('cep', array('class' => 'input-small formata-cep', 'label' => 'CEP', 'maxlength' => '8', 'onchange' => '$("#pesquisa_cep").show();')); ?>
		<img src="/portal/img/default.gif" id="carregando" style="padding: 30px 0 0 10px; display: none;">
		<label style="float: left; padding: 30px 0 0 10px; font-size: 10px;" id="pesquisa_cep"><a href="javascript:void(0);" onclick="atestado.buscaCEP();">COMPLETAR ENDEREÇO</a></label>
		    				
	</div>
	<div class="row-fluid">
		<?php echo $this->BForm->input('endereco', array('class' => 'input-xxlarge', 'label' => 'Endereço')); ?>
		<?php echo $this->BForm->input('numero', array('class' => 'input-mini', 'label' => 'Número')); ?>
		<?php echo $this->BForm->input('complemento', array('class' => 'input', 'label' => 'Complemento')); ?>
	</div>
	<div class="row-fluid">
		<?php echo $this->BForm->input('bairro', array('class' => 'input', 'label' => 'Bairro')); ?>
		<?php echo $this->BForm->input('codigo_estado', array('label' => 'Estado', 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'atestado.buscaCidade(this.value)')) ?>
		<span id="cidade_combo">
			<?php echo $this->BForm->input('codigo_cidade', array('label' => 'Cidade', 'class' => 'form-control', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $cidades)) ?>
		</span>
		<div id="carregando_cidade" style="display: none; text-align: left; border: 0; padding: 30px 60px;">
	    	<img src="/portal/img/ajax-loader.gif" border="0"/>
	    </div>
	</div>
	<div class="row-fluid">
		<h4>Local de Atendimento:</h4>	
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_tipo_local_atendimento', array('class' => 'input-xxlarge', 'label' => 'Tipo do Local de Atendimento', 'options' => $TipoLocalAtendimento, 'empty' => 'Selecione uma opção')); ?>
	</div>
</div>