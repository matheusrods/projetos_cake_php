<?php $codigo_viagem_sm = $viagem['TViagViagem']['viag_codigo_sm']; ?>
<!--<?php echo $this->BForm->create('FotosViagem', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'inserir_fotos_checklist',$codigo_viagem_sm, $cliente['Cliente']['codigo'], $viag_codigo))); ?>-->
	<?php echo $this->BForm->hidden('FotosViagem.viag_codigo_sm')?>
	<div class='row-fluid-inline'>
		<?php echo $this->BForm->input('FotosViagem.arquivo', array('name' => 'data[arquivo][]','type'=>'file', 'label' => false, 'multiple'=>true)); ?>
	</div>
	<div class='row-fluid-inline'>
		<?php echo $this->Html->link('<i class="icon-eye-open icon-white"></i> Visualizar Fotos', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Incluir fotos ao processo', 'onclick'=>'javascript: visualizar_foto(event);')); ?>
		<?php echo $this->BForm->button('Incluir fotos', array('escape' => false, 'div' => false, 'class' => 'btn btn-primary', 'id' => 'importar', 'onclick'=>'javascript: incluir_fotos(this);', 'type'=>'button')); ?>
		<img src="/portal/img/loading.gif" style="display: none;" id="img_loading" />
	</div>
	<?php foreach ($fotos_adicionadas as $key => $foto): ?>
		<?php echo $this->Html->link('<i class="icon-trash icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small", 'title' => 'Excluir', 'onclick'=>'javascript:excluir_foto('.$key.');')); ?>
		<b>Imagem: <?= $key.' - '?></b><?= $foto ?>
		</br>
	<?php endforeach; ?>
<!--<?php echo $this->BForm->end(); ?>-->
<?php echo $this->Javascript->codeBlock('

    function visualizar_foto(event) {
   		event.preventDefault();
    	var newwindow = window.open("/portal/viagens/fotos_checklist/'.$codigo_viagem_sm.'/", "_blank", "top=0,left=0,width=600,height=600,scrollbars=yes");
    	if (window.focus){
        	newwindow.focus();
    	}
    }

    function excluir_foto(codigo_imagem) {
    	if (confirm("Deseja realmente excluir?")){
 			location.href = "/portal/viagens/excluir_foto_checklist/'.$cliente['Cliente']['codigo'].'/'.$viag_codigo.'/" + codigo_imagem + "/";
        }
	 
    }

    function incluir_fotos(campo) {
    	var id = campo.id;

    	var fd = new FormData(document.getElementById(campo.form.id));
		fd.append("label", "WEBUPLOAD");
		$("#img_loading").show();
		$.ajax({
          url: "/portal/viagens/inserir_fotos_checklist/'.$codigo_viagem_sm.'/'.$cliente['Cliente']['codigo'].'/'.$viag_codigo.'",
          type: "POST",
          data: fd,
          enctype: "multipart/form-data",
          processData: false,  // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        }).done(function( data ) {
        	if (data.indexOf(" ")>=0) data = "";
			$.ajax({
				url: baseUrl + "viagens/fotos_checklist_inline/'.$cliente['Cliente']['codigo'].'/'.$viag_codigo.'/"+data,
				dataType: "html",
				success: function(data){
					$("#divFotos").html(data);
		        	$("#img_loading").hide();
				}
			});
        });
		/*

    	var campo = $("#FotosViagemViagCodigoSm");
    	
		var form_id = campo.get(0).form.id;
		alert(form_id);

    	var form = $("#"+form_id);
    	var action = form.attr("action");
    	var enctype = form.attr("enctype");
    	var method = form.attr("method");
    	var target = form.attr("target");

		alert(form.attr("action"));
    	form.attr("action",);
    	form.attr("enctype","multipart/form-data");
    	form.attr("method","POST");
    	form.attr("target","postImage");

    	form.submit();
    	alert(form.attr("action"));

    	form.attr("action",action);
    	form.attr("enctype",enctype);
    	form.attr("method",method);
    	form.attr("target",target);
    	*/
    }
   ');
?>