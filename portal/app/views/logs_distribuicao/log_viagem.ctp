<?php if(empty($listagem)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<p>
		<i>*: <b>D</b> = DISTRIBUIÇÃO; <b>L</b> = LIBERAÇÃO; <b>R</b> = RECUPERAÇÃO</i>
	</p>
    <table class='table table-striped'>
	    <thead>
	        <tr>
	            <th>SM</th>
	            <th>Data</th>
	            <th>Tipo*</th>
	            <th>Usuario</th>
	            <th>A. Atuação</th>
	            <th>Motivo</th>
	            <th>Evento</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($listagem as $log):?>
		        <tr>
		            <td><?php echo $log['TViagViagem']['viag_codigo_sm'] ?></td>
		            <td><?php echo $log['TLdisLogDistribuicao']['ldis_data_cadastro'] ?></td>
		            <td><?php echo $log['TLdisLogDistribuicao']['ldis_tipo'] ?></td>
		            <td><?php echo $log['TUsuaUsuario']['usua_login'] ?></td>

		            <?php $link = $log['TAatuAreaAtuacao']['aatu_descricao']?$log['TLdisLogDistribuicao']['ldis_cdis_codigo'].'.'.$log['TAatuAreaAtuacao']['aatu_descricao']:NULL ?>
		            <td><?php echo $link?$this->Html->link($link, array('controller' => 'criterios_distribuicao','action' => 'visualizar',$log['TLdisLogDistribuicao']['ldis_cdis_codigo']), array('class' => 'criterio' ,'escape' => false, 'title'=>'Critério Distribuição')):NULL ?></td>
		            <td><?php echo $log['TTamoTipoAcessoMonitor']['tamo_descricao'] ?></td>
		            <td><?php echo $log['TLdisLogDistribuicao']['ldis_observacao'] ?></td>
		        </tr>
	        <?php endforeach; ?>  
	    </tbody>
	</table>
<?php endif; ?>
<div id="dialog-criterio" title="Criterio de Distribuição" style="display:none"></div>
<?php $this->addScript($this->Javascript->codeBlock('
	$(function(){

		$(".criterio").click(function() {
			var url = $(this).attr("href");

			$("html, body").animate({ scrollTop: 0 });
			$( "#dialog-criterio" ).dialog({
				width: 500,
				open: function(){
					bloquearDiv($( this ));
					$(this).load(url);
				}
			});
			return false;
		});
		
	});'));
?>