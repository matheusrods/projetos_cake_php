<?php foreach ($fotos_checklist_entrada as $key => $foto): ?>
    <?php echo $this->Html->link('<i class="icon-trash icon-black"></i>', 'javascript:void(0)', array('escape' => false, 'class'=> "btn btn-small", 'title' => 'Excluir', 'onclick'=>'javascript:excluir_foto('.$foto['TVcefViagChecklistEntrFoto']['vcef_codigo'] .');')); ?>
    <b>Imagem: <?=($key+1).' - '?></b><?=$foto['TVcefViagChecklistEntrFoto']['vcef_diretorio'] ?>
    </br>
<?php endforeach;?>
<?php echo $this->Javascript->codeBlock('
function excluir_foto(codigo_imagem) {
	if (confirm("Deseja realmente excluir?")){
		$("#img_loading").show();
		$.ajax({
	      url: "/portal/viagens/excluir_foto_checklist_entrada/'.$codigo_checklist.'/" + codigo_imagem + "/",
	      type: "POST",
	    }).done(function( data ) {
	    	if (data.indexOf(" ")>=0) data = "";
			$.ajax({
				url: baseUrl + "viagens/fotos_checklist_entrada_inline/'.$cliente['Cliente']['codigo'].'/'.$codigo_checklist.'/"+data,
				dataType: "html",
				success: function(data){
					$("#divFotos").html(data);
		        	$("#img_loading").hide();
				}
			});
	    });
		//location.href = "/portal/viagens/excluir_foto_checklist_entrada/'.$cliente['Cliente']['codigo'].'/'.$codigo_checklist.'/" + codigo_imagem + "/";
    }
}
');
?>