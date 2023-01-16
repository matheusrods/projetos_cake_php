<table class="table table-striped table-bordered"
style="margin-bottom: 0px; width: 100%; border-top: 0px; border-bottom: 0px; border-right: 0px !important; margin-left: -1px">
<?php if(count($lista) > 0){ ?>
<?php 
	$total_dentro = 0;
	$total_fora = 0;
	$count = 0;
	$servicos = array(
			'CADASTRO DE FICHA',
			'ATUALIZAÇÃO DE FICHA',
			'RENOVAÇÃO AUTOMÁTICA'
		);
	foreach($lista as $mes => $linha){  
		 foreach($servicos as $servico){ 
		 	$descricao = $servico;
		 	if($count%2)
		 		$fundo = "background: transparent !important;";
		 	else
		 		$fundo = "";
		 	$count++;
		 	if(!empty($linha[$descricao])){
		 		$valores = $linha[$descricao];

		 ?>
	 	<tr>	    
	    <td class="numeric vl_dentro_<?=$count ?> valor" style="display:none; width:50%; <?=$fundo?>"><?=number_format($valores['dentro_sla'],0,',','.')?></td>
	    <td class="numeric porcentagem" style="width: 50%; <?=$fundo?>"><?=$valores['percentual_dentro']?>%</td>
	    <td class="numeric vl_fora_<?=$count ?> valor" style="display:none; width: 50%; <?=$fundo?>"><?=number_format($valores['fora_sla'],0,',','.')?></td>
	    <td class="numeric porcentagem" style="width: 50%; border-right: 0px !important; <?=$fundo?>"><?=$valores['percentual_fora']?>%</td>
	    </tr>

		<?php }else{ ?>
			<tr>
				<td style="width:50%; <?=$fundo?>">&nbsp;</td>				
				<td style="width:50%; <?=$fundo?>">&nbsp;</td>
				
			</tr>
		<?php 
			}
		}
	}
 }else{ 
 	for($i=0; $i<3; $i++){
 		if($i%2)
	 		$fundo = "background: transparent !important;";
	 	else
	 		$fundo = "";
	?>
 			<tr>
				<td style="width:50%; <?=$fundo?>">&nbsp;</td>
				<td style="width:50%; <?=$fundo?>">&nbsp;</td>
			</tr>
 <?php 
 	}
 } ?>
</table>