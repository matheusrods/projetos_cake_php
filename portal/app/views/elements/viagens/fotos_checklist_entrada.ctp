<div class='row-fluid inline'>
	<h4>Fotos
		<div style="float: right; font-weight: normal">
			<?php echo $this->Html->link('<i class="icon-eye-open icon-white"></i> Visualizar Fotos', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir fotos ao processo', 'onclick'=>'javascript: visualizar_foto(event);')); ?>
		</div>
	</h4>
</div>
<div class='well' id="divFotos">
	<?php echo $this->element('viagens/fields_fotos_checklist_entrada') ?>
	<br/>
	<?php echo $this->element('viagens/listagem_fotos_checklist_entrada') ?>
</div>
<?php echo $this->Javascript->codeBlock('

function visualizar_foto(event) {
		event.preventDefault();
	var newwindow = window.open("/portal/viagens/fotos_checklist_entrada/'.$codigo_checklist.'/", "_blank", "top=0,left=0,width=880,height=600,scrollbars=yes");
	if (window.focus){
    	newwindow.focus();
	}
}


');
?>