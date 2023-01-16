<legend>Critérios</legend>
<div class="row-fluid">
	<?php $indice = 0; ?>
	<?php $qtd = count($criterios); ?>
	<?php $metade = ceil($qtd/2); ?>
	<?php foreach ($criterios as $codigo_criterio => $criterio): ?>
		<?php if($indice == 0): ?>
			<div class="span6">
		<?php endif; ?>
		<?php if ($criterio['codigo']==1 or $criterio['codigo']==15 or $criterio['codigo']==3 or $criterio['codigo']==20) {?>
            <div class="criterio">				
        <?php echo $this->BForm->input('FichaStatusCriterio.'.$criterio['codigo'].'.codigo_status_criterio', array('label' => $criterio['descricao'], 'class' => 'input-xlarge status', 'type' => 'select', 'options' =>  $criterio['StatusCriterio'],'for' => $criterio['codigo'], 'empty' => 'SELECIONE'));
        ?>    
			</div>
      <?php echo $this->BForm->input('FichaStatusCriterio.'.$codigo_criterio.'.observacao', array('maxlength' => 2048, 'rows'=>2,'class' => 'input-xxlarge observacao', 'placeholder' => 'Observação', 'label' =>false, 'type' => 'textarea' ,'value'=>$this->data['FichaStatusCriterio'][$codigo_criterio]['observacao'] , 'div'=> 'control-group input textarea' )) ; ?>
      
		<?php }else{  ?>

			<div class="criterio">
				<?php echo '<h5>'.$criterio['descricao'].'</h5>'.
				(!empty($criterio['opcional']) ? '' : ' <span class="text-error obrigatorio">*</span>').
				(!empty($this->data['FichaStatusCriterio'][$codigo_criterio]['automatico']) ? '<span class="text-warning automatico">Preenchido automaticamente</span>' : ''); ?>
				<p><?php echo isset($this->data['FichaStatusCriterio'][$codigo_criterio]) ? @$criterio['StatusCriterio'][$this->data['FichaStatusCriterio'][$codigo_criterio]['codigo_status_criterio']] : ''; ?></p>
						
				<?php if (!empty($this->data['FichaStatusCriterio'][$codigo_criterio]['observacao'])): ?>
					<p><?php echo $this->data['FichaStatusCriterio'][$codigo_criterio]['observacao']; ?></p>				
				<?php endif; ?>
			</div>
		<?php } ?>	
		<hr>
		<?php $indice++; ?>			
		<?php if($metade == $indice): ?>
			</div>
			<div class="span6">
		<?php elseif($qtd == $indice): ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div> <!-- Fecha  row-fluid -->
<?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn\').click(function(){
        $(\'#serasa\').dialog(\'open\');
    });
  });', false);?>


  <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa0").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe0\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn0\').click(function(){
        $(\'#serasa0\').dialog(\'open\');
    });
  });', false);?>

    <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa1").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe1\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn1\').click(function(){
        $(\'#serasa1\').dialog(\'open\');
    });
  });', false);?>

  <?php echo $this->Javascript->codeBlock('
  $(document).ready(function() {
    $("#serasa2").dialog({
        autoOpen: false,
        modal: true,
        width:  900,
        height: 800,
        maxHeight:1020,
        open: function(ev, ui){
                 $(\'#myIframe2\').attr(\'src\',\'http://tstportal.buonny.com.br/bcb/index/visualizar-relatorio/tipoPessoa/fisica\');
              }
    });

    $(\'#dialogBtn2\').click(function(){
        $(\'#serasa2\').dialog(\'open\');
    });
  });', false);?>