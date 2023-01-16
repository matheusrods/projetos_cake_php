<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class="input-large">Periferico</th>
			<th style="padding-left:20px">
			<?php if(false)://if(!$readonly): ?>
				<?php foreach ($status as $esta_codigo => $esta_descricao): ?>
					<input style="margin-left:7px" type="radio" name="option1" value="<?php echo $esta_codigo ?>" />
					<?php echo $esta_descricao ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</th>
			<th class="input-large">Periferico</th>
			<th style="padding-left:20px">
			<?php if(false)://if(!$readonly): ?>
				<?php foreach ($status as $esta_codigo => $esta_descricao): ?>
					<input style="margin-left:7px" type="radio" name="option2" value="<?php echo $esta_codigo ?>" />
					<?php echo $esta_descricao ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</th>
		</thead>				
		<? if( $perifericos): ?>			
			<tbody>				
				<tr>
					<?php $count = 1;?> 
					<?php $i=0; ?>
					<?php foreach ($perifericos as $ppadKey => $ppad):?>						
						<?php 
							if(isset($icveLista[$i]['TEstaEstatus']['esta_codigo']) && $icveLista[$i]['TEstaEstatus']['esta_codigo'] == 2 ){
					 			$style = "style='color:red' title='CHECKLIST ANTERIOR COM PROBLEMA'";
					 		}elseif(isset($this->data['TIcveItemChecklistVeiculo'][$ppadKey]['icve_esta_codigo']) && $this->data['TIcveItemChecklistVeiculo'][$ppadKey]['icve_esta_codigo'] == 2){
					 			$style = "style='color:red'";
					 		}else{
					 			$style = "style='color:black;'";
					 		}
					 	?>
						<?php $class = ($ppadKey%2 === 0)?'option1':'option2'?>							
								<td <?php echo $style ?> >
									<?php echo $ppad['TPpadPerifericoPadrao']['ppad_descricao'] ?>
									<?php echo $this->BForm->hidden("TIcveItemChecklistVeiculo.{$ppadKey}.icve_codigo"); ?>
									<?php echo $this->BForm->hidden("TIcveItemChecklistVeiculo.{$ppadKey}.icve_ppad_codigo", array('value' => $ppad['TPpadPerifericoPadrao']['ppad_codigo'] )); ?>
								</td>
								<td>
									<?php if($readonly): ?>
										<?php echo $this->BForm->input("TEstaEstatus.{$ppadKey}.esta_descricao", array('readonly' => TRUE,'class' => $class,'label' => FALSE)) ?>
									<?php else: ?>
										<?php echo $this->BForm->input("TIcveItemChecklistVeiculo.{$ppadKey}.icve_esta_codigo", array('type' => 'radio','class' => $class,'options' => $status,'legend' => FALSE, 'label' => array('class' => 'radio inline status_item_checklist'))) ?>
									<?php endif; ?>
								</td>																					
							<?php if($ppadKey%2 !== 0): ?>
								</tr><tr> 
							<?php endif; ?>
						<?php $i++;?>						
					<?php endforeach;?>
				</tr>
			</tbody>
		<? endif; ?>
	</table>
	<? if( !$perifericos): ?>
		<div class="alert">Cliente não possui Periféricos configurados.</div>
	<? endif; ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(function(){
				
		$("input[name=\'option1\']").change(function(){
			var valor = $(this).val();
			$("input.option1[value="+valor+"]").prop("checked", "checked");
		});
	
		$("input.option1").change(function(){
			$("input[name=\'option1\']").prop("checked", "");
		});
	
		$("input[name=\'option2\']").change(function(){
			var valor = $(this).val();
			$("input.option2[value="+valor+"]").prop("checked", "checked");
		});

		$("input.option2").change(function(){
			$("input[name=\'option2\']").prop("checked", "");
		});
		
	});', false);
?>