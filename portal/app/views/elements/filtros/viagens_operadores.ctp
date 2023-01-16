<div class='well'>

	<div id='filtros'>
		<?php echo $bajax->form('TVusuViagemUsuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TVusuViagemUsuario', 'element_name' => 'viagens_operadores'), 'divupdate' => '.form-procurar')) ?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('usua_login', array('class' => 'input-medium', 'label' => FALSE, 'placeholder' => 'Operador')) ?>
			<?php echo $this->BForm->input('usua_status', array('class' => 'input-medium', 'label' => FALSE, 'empty' => 'Status', 'options' => array(1 => 'LOGADO',3 => 'INTERVALO',2 => 'DESLOGADO'))) ?>
			<?php echo $this->BForm->input('usua_aatu', array('class' => 'input-medium', 'label' => FALSE, 'empty' => 'Area de Atuação', 'options' => $aatu_lista)) ?>
			<?php echo $this->BForm->input('eras_codigo', array('class' => 'input-large','label' =>FALSE, 'div'=>'control-group input', 'options'=>$estacao, 'empty' => 'Estação de Rastreamento')) ?>
			<?php echo $this->BForm->input('timer', array('class' => 'input-small just-number', 'label' => FALSE, 'placeholder' => 'Timer(min)', 'maxlength' => 2)) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		var timer 	= $("#TVusuViagemUsuarioTimer").val()*60000;
		var refresh = 0;

		if(timer)
			setInterval("atualizaViagensOperador()",timer);

		$("#TVusuViagemUsuarioTimer").change(function(){
			timer 	= $( this ).val()*60000;
			
			clearInterval(refresh);
			if(timer)
				refresh = setInterval("atualizaViagensOperador()",timer);

		});

		setup_mascaras();

		

		atualizaViagensOperador();
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TVusuViagemUsuario/element_name:viagens_operadores/" + Math.random());
		});
	
	});', false);
?>
