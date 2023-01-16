
<?php if(!empty($codigo_usuario_grupo_covid)):  ?>
	<?php echo $this->BForm->input('UsuarioExame.codigo', array('type' => 'hidden', 'value' => $codigo_usuario_grupo_covid)); ?>
<?php endif; ?>

<ul class="nav nav-tabs">
  <li class="active"><a href="#atendimento" data-toggle="tab">Atendimento</a></li>
  <?php if(isset($codigo_atestado) && $codigo_atestado) : ?>
	  <!-- <li><a href="#detalhecid" data-toggle="tab">Detalhe do CID</a></li> -->
	  <li><a href="#local" data-toggle="tab">Local Atendimento</a></li>	  
	  <li><a href="#anexo" data-toggle="tab">Anexo</a></li>	  
  <?php endif; ?>
</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="atendimento">
	        <?php echo $this->element('atestados/atendimento') ?>
		</div>
		<?php if(isset($codigo_atestado) && $codigo_atestado) : ?>	        
		    <div class="tab-pane" id="local">
		        <?php echo $this->element('atestados/local') ?>
		    </div>
		    <div class="tab-pane" id="anexo">
		        <?php echo $this->element('atestados/anexo') ?>
		    </div>
		<?php endif; ?>
	</div>	

			
 <div class='form-actions'>
	<?php echo $this->BForm->submit(($edit_mode == true) ? 'Salvar' : 'Avançar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'atestados', 'action' => 'lista_atestados', $this->passedArgs[0], $this->passedArgs[1]), array('class' => 'btn')); ?>
</div>

<?php echo $this->Buonny->link_js('atestados.js'); ?>