<?php echo $this->BForm->create('Questionario', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'Questionarios', 'action' => 'editar	',$this->data['Questionario']['codigo']), 'divupdate' => '.form-procurar')); ?>
<div class='well'>


	<div class="row-fluid inline">
		<?php if(isset($this->data['Questionario']['codigo'])) { 
			$codigo = $this->data['Questionario']['codigo'];
			echo $this->BForm->input('codigo', array('value'=>$codigo)); 
		} ?>
		<?php
			$descricao = (isset($this->data['Questionario']['descricao'])) ?  $this->data['Questionario']['descricao'] : '';
			echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'label' => 'Descrição do questionário', 'value'=> $descricao )) 
		?>

		<div class="clear"></div>
		<?php 
			$observacoes = (isset($this->data['Questionario']['observacoes'])) ?  $this->data['Questionario']['observacoes'] : '';
			echo $this->BForm->input('observacoes', array('class' => 'input-xxlarge', 'label' => 'Observações', 'rows' => 2, 'value'=>$observacoes)) 
		?>

		<div class="clear"></div>
		<?php 
			$protocolo = (isset($this->data['Questionario']['protocolo'])) ?  $this->data['Questionario']['protocolo'] : '';
			echo $this->BForm->input('protocolo', array('class' => 'input-xxlarge', 'label' => 'Protocolo', 'rows' => 2, 'value'=>$protocolo)) 
		?>

		<div class="clear"></div>
		<label for="">Aplicação do questionário para o sexo:</label>
		<?php 
			$aplicacao_sexo = (isset($this->data['Questionario']['aplicacao_sexo'])) ?  $this->data['Questionario']['aplicacao_sexo'] : 'A';
			echo $this->BForm->input('aplicacao_sexo', array('class' => 'input-small', 'type' => 'radio', 'options' => array('M' => 'Masculino', 'F' => 'Feminino', 'A' => 'Ambos'), 'default' => 'A', 'legend' => false, 'selected'=>$aplicacao_sexo)) 
		?>

		<div class="clear"></div>
		<label for="">Tipo Questionário:</label>
		<?php 
			$questionario_tipo_sel = (isset($this->data['Questionario']['codigo_questionario_tipo'])) ?  $this->data['Questionario']['codigo_questionario_tipo'] : 1;
			echo $this->BForm->input('codigo_questionario_tipo', array('class' => 'input-small', 'label' => false, 'options' => $questionario_tipo , 'selected'=>$questionario_tipo_sel));
		?>

		<div class="clear"></div>
		<?php 
			$status = (isset($this->data['Questionario']['status'])) ?  $this->data['Questionario']['status'] : 1;
			echo $this->BForm->input('status', array('class' => 'input-small', 'options' => array(1 => 'Ativo', 0 => 'Inativo') , 'selected'=>$status)) 
		?>
	
	</div>


<!-- icone -->
<div class="row">
	<div class="span9">
		Icone
		<div class="row">
			<div class="span3">

				<?php echo $this->BForm->input('Questionario.icone', array('type' => 'file', 'class' => 'input-xlarge', 'label' => '')); ?>
				<i>Dimensões mínimas do ícone '211px' por '141px'</i>
				<p style="color:red;font-style: italic;font-size: 11px;">As imagens são limitadas às extensões png, jpg e jpeg. O tamanho limite é de 2MB. </p>

			</div>
			<div class="span6">
				<div style="width:80%;height:80%; border:1px solid #ccc; padding:5px;">
				<?php if(isset($this->data['Questionario']['icone']) && !empty($this->data['Questionario']['icone'])) : ?>
					<img src="https://api.rhhealth.com.br<?php echo $this->data['Questionario']['icone']; ?>" />
				<?php else: ?>		
					<p style="margin:30px">Imagem não encontrada.</p>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- background -->
<div class="row">
	<div class="span9">
		Background
		<div class="row">
			<div class="span3">

				<?php echo $this->BForm->input('Questionario.background', array('type' => 'file', 'class' => 'input-xlarge', 'label' => '')); ?>
				<i>Dimensões mínimas do background '1200px' por '800px'</i>
				<p style="color:red;font-style: italic;font-size: 11px;">As imagens são limitadas às extensões png, jpg e jpeg. O tamanho limite é de 2MB. </p>

			</div>
			<div class="span6">
				<div style="width:80%;height:80%; border:1px solid #ccc; padding:5px;">
				<?php if(isset($this->data['Questionario']['background']) && !empty($this->data['Questionario']['background'])) : ?>
						<img src="https://api.rhhealth.com.br<?php echo $this->data['Questionario']['background']; ?>" />
				<?php else: ?>		
						<p style="margin:30px">Imagem não encontrada.</p>
				<?php endif; ?>
				</div>				
			</div>
		</div>
	</div>
</div>

<!--app lyn -->
<div class="row">
	<div class="span9">
		App Lyn
		<div class="row">
			<div class="span3">

				<?php echo $this->BForm->input('Questionario.img_app', array('type' => 'file', 'class' => 'input-xlarge', 'label' => '')); ?>
				<i>Dimensões mínimas do background '1200px' por '800px'</i>
				<p style="color:red;font-style: italic;font-size: 11px;">As imagens são limitadas às extensões png, jpg e jpeg. O tamanho limite é de 2MB. </p>

			</div>
			<div class="span6">
				<div style="width:80%;height:80%; border:1px solid #ccc; padding:5px;">				
				<?php if(isset($this->data['Questionario']['img_app']) && !empty($this->data['Questionario']['img_app'])) : ?>
						<img src="https://api.rhhealth.com.br<?php echo $this->data['Questionario']['img_app']; ?>" />
				<?php else: ?>		
						<p style="margin:30px">Imagem não encontrada.</p>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

</div>	
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?> &nbsp;
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>
