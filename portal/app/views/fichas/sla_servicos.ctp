<div id="form">
	<!-- <?php echo $this->BForm->create('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas', 'action' => 'sla_servicos'))) ?>
	<?php echo $this->BForm->end();?> -->
		<div class="row-fluid inline">
		<?php echo $this->BForm->input('ano', array('selected'=> date('Y'), 'class' => 'input-small',  'options' => $anos, 'label' => 'Ano')); ?><br>
		<?php echo $this->BForm->input('tipo', 
			array( 
			'type' => 'radio', 
			'options' => array('valor' => 'Quantidade', 'porcentagem' => 'Porcentagem'), 
			'default' => 'porcentagem', 
			'legend' => false, 
			'label' => array('class' => 'radio inline input-mini'))) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'id' => 'enviar', 'class' => 'btn')); ?>
</div>
<br>
<div id="lista">
		<table class='table table-striped table-bordered sla' style="display: none; width: 2020px !important; ">
		    <thead>
		        <tr>		            
		            <th style="text-align: center; width: 9%">Descrição</th>	           
				    <?php foreach($meses as $mes => $desc_mes){ ?>
			    	<th colspan="2" style="text-align: center; width: 7%"><?php echo $desc_mes;?></th>
				    <?php } ?>
    				<th colspan="2" style="text-align: center; width: 7%" class="numeric">Total</th>
		        </tr>
    	        <tr>		            
		            <th></th>	           
				    <?php foreach($meses as $mes => $desc_mes){ ?>
			    	<th class="numeric" style="width: 70px;">Dentro</th>
			    	<th class="numeric" style="width: 70px;">Fora</th>
				    <?php } ?>
			    	<th class="numeric" style="width: 70px;">Dentro</th>
			    	<th class="numeric" style="width: 70px;">Fora</th>	
		        </tr>

		    </thead>
		    <tbody id="linhas">
				<tr>
					<td>Cadastro de Ficha</td>
				    <?php foreach($meses as $mes => $desc_mes){ ?>
				    	<td id="mes_<?php echo $mes ?>" rowspan="3" colspan="2" style="padding: 0px; background: transparent !important;"></td>
					<?php } ?>						
					<td id="total" style="padding: 0px; background: transparent !important;" rowspan="3" colspan="2" ></td>
				</tr>
				<tr>
					<td>Atualização de Ficha</td>
				</tr>
				<tr>
					<td>Renovação Automática</td>
				</tr>

		    </tbody>	
		    <tfoot id="footer"></tfoot>        
		</table> 		

	<div id="conteudo"></div>
<?php
$bloco = 
'jQuery(document).ready(function(){
	
	function mostra_valores(){
		if(jQuery("#TipoPorcentagem").is(":checked")==true){
			jQuery(".porcentagem").show();
			jQuery(".valor").hide();
		}else{			
			jQuery(".porcentagem").hide();
			jQuery(".valor").show();
		}
	}
	function calcula_total(){
		html = "<table style=\"width: 100%; margin-left: -1px;\">";
		for(i=1; i<=3; i++){
			total_dentro = 0;
			total_fora = 0;
			jQuery(".vl_dentro_"+i).each(function(item, valor){							
				valor = parseInt(jQuery(this).text().replace(".", ""));				
				total_dentro = total_dentro+valor;
			});	
			jQuery(".vl_fora_"+i).each(function(item, valor){			
				valor = parseInt(jQuery(this).text().replace(".", ""));				
				total_fora = total_fora+valor;
			});	
			if(i%2){
				fundo = "";
			}else{
				fundo = "background: transparent !important";
			}

			if(total_dentro > 0 || total_fora > 0){
				total = total_dentro + total_fora;
				porc_dentro = Math.round(100 * total_dentro / total);
				porc_fora = Math.round(100 * total_fora / total);

				total_dentro = total_dentro+"";
				total_fora = total_fora+"";

				total_dentro = total_dentro.replace(/(\d)(\d{3}(\.|$))/, "$1.$2");
				total_fora = total_fora.replace(/(\d)(\d{3}(\.|$))/, "$1.$2");
				html += "<tr><td style=\"width: 50%; display:none;"+fundo+"\" class=\"numeric valor\">"+total_dentro+"</td><td style=\"width: 50%;"+fundo+"\" class=\"numeric porcentagem\">"+porc_dentro+"%</td><td style=\"width: 50%; display:none;"+fundo+"\" class=\"numeric valor\">"+total_fora+"</td><td style=\"width: 50%;"+fundo+"\" class=\"numeric porcentagem\">"+porc_fora+"%</td></tr>";
			}else{
				html += "<tr><td style=\"width: 50%; "+fundo+"\" class=\"numeric\">&nbsp;</td><td style=\"width: 50%; "+fundo+"\" class=\"numeric\">&nbsp;</td></tr>";
			}			
		}
		
		html += "</table>";
		jQuery("#total").html(html);
		mostra_valores();
	}
	jQuery("input:radio").change(function(){
		mostra_valores();
	});
	jQuery("#enviar").click(function(){
		
			jQuery("#conteudo").html(""); 
			jQuery(".sla").show(); ';
foreach($meses as $mes => $desc_mes){
	$bloco .= 'jQuery("td#mes_'.$mes.'").html("<div style=\"margin: 10px; text\"><i>Carregando '.$desc_mes.'...</i></div>");';
}
$bloco .= ' jQuery("#total").html("<div style=\"margin: 10px; text\"><i>Carregando Total...</i></div>"); ';
foreach($meses as $mes => $desc_mes){
	$bloco .= '			
			jQuery.ajax({
		        "url": baseUrl + "fichas/sla_servicos_mes/'.$mes.'/'.$desc_mes.'/"+jQuery("#ano").val()+"/" + Math.random(),
		        "success": function(data){
		            jQuery("td#mes_'.$mes.'").html(data); 
		         	mostra_valores();   

        ';
	//if($mes==12){
		$bloco .= ' calcula_total(); ';
	//}
	$bloco .= ' 

			}

        });
		
';
}	
$bloco .= '	
		
	});

});';
echo $this->Javascript->codeBlock($bloco);
?>

</div>