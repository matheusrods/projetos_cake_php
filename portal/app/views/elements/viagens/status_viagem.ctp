<?php if($dados_viagem): ?>
	<? if (!empty($titulo_status_viagem)): ?>
		<div style="height: 30px; line-height: 30px; vertical-align: top;">
			<div style="display: inline-block; vertical-align: bottom"><?=$titulo_status_viagem?>:</div>
			<? if (!empty($dados_viagem['TViagViagem']['viag_codigo_sm']) && !empty($cliente['Cliente']['codigo'])): ?>
				<div style="float:right">
					<?= $html->link('<i class="icon-duplicate"></i> Duplicar SM', 'javascript: void(0)', array('escape' => false, 'class' => 'btn btn_refresh', 'title' =>'Duplicar SM', 'onclick'=>'javascript: carregar_sm_copia('.$cliente['Cliente']['codigo'].','.$dados_viagem['TViagViagem']['viag_codigo_sm'].' ); return false;')) ?>			
				</div>
			<? endif; ?>
		</div>
	<? endif; ?>
	<div id="dados_viagem" class='well'>
		<strong>SM: </strong><?=$this->Buonny->codigo_sm($dados_viagem['TViagViagem']['viag_codigo_sm']) ?>
		 &nbsp; &nbsp;
		<strong>Status: </strong><?=$dados_viagem[0]['status']?>&nbsp; &nbsp;		 
	</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
	function carregar_sm_copia(codigo_cliente, viag_codigo_sm) {
		var html = "/portal/viagens/copia_viagem/"+codigo_cliente+"/"+viag_codigo_sm+"/"+Math.random();
		open_dialog(html,"Duplicar SM "+viag_codigo_sm,900);
	}

	$(document).ready(function(){
	});', false);
?>		