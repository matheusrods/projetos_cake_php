<?php $vcen_codigo = $this->data['TVcenViagemChecklistEntrada']['vcen_codigo']; ?>
<?php if (!isset($inline)) $inline = 1;?>
<!--<?php echo $this->BForm->create('FotosViagem', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'inserir_fotos_checklist',$codigo_viagem_sm, $cliente['Cliente']['codigo'], $viag_codigo))); ?>-->
	<?php echo $this->BForm->hidden('TVcenViagenChecklistEntrada.vcen_codigo')?>
	<div class='row-fluid-inline'>
		<?php echo $this->BForm->input('FotosViagem.arquivo', array('name' => 'data[arquivo][]','type'=>'file', 'label' => false, 'multiple'=>true)); ?>
	</div>
	<div class='row-fluid-inline'>
		<?php echo $this->BForm->button('Incluir fotos', array('escape' => false, 'div' => false, 'class' => 'btn btn-primary', 'id' => 'importar', 'onclick'=>'javascript: incluir_fotos(this,'.$inline.');', 'type'=>'button')); ?>
		<img src="/portal/img/loading.gif" style="display: none;" id="img_loading" />
	</div>

<!--<?php echo $this->BForm->end(); ?>-->
<?php echo $this->Javascript->codeBlock('

    function incluir_fotos(campo, inline) {
    	if (inline==null || inline==undefined) inline = true;
    	var id = campo.id;
    	var fd = new FormData(document.getElementById(campo.form.id));
		fd.append("label", "WEBUPLOAD");
		$("#img_loading").show();
		$.ajax({
          url: "/portal/viagens/inserir_fotos_checklist_entrada/'.$vcen_codigo.'/'.$cliente['Cliente']['codigo'].'",
          type: "POST",
          data: fd,
          enctype: "multipart/form-data",
          processData: false,  // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        }).done(function( data ) {
        	if (data.indexOf(" ")>=0) data = "";
        	if (inline) {
				$.ajax({
					url: baseUrl + "viagens/fotos_checklist_entrada_inline/'.$cliente['Cliente']['codigo'].'/'.$vcen_codigo.'/"+data,
					dataType: "html",
					success: function(data){
						$("#divFotos").html(data);
			        	$("#img_loading").hide();
					}
				});
			} else {
				//console.log(data);
				close_window = false;
				top.location.href = "/portal/viagens/fotos_checklist_entrada/'.$vcen_codigo.'/";
			}
        });
    }
   ');
?>