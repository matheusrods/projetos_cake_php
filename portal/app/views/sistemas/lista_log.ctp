
<?php $nome_arquivo = null?> 
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th >Arquivos de logs</th> 
			
		</thead>
			<tbody>
				<?php foreach ($arquivos as $arquivo): ?>						
					<tr>
						<td>  
							<?php $separa = explode(DS,$arquivo);?>
	 						<?php $localiza = array_pop($separa);?>
	 						<?php $lista_arquivos= $localiza;?>
							<?php if($lista_arquivos): ?>
								
							<strong><?php echo $lista_arquivos?></strong>&nbsp;
							<?php $nome_arquivo = $lista_arquivos;?>
							<?php echo $this->Html->link('Limpar',array('controller'=>'Sistemas', 'action' => 'excluir_log',$nome_arquivo), array('class' => 'btn')); ?>
							<?php endif;?>
						</td>		
					</tr>
					<tr>	
						<td style="background: #333; color: #EFEFEF; text-align: justify;">
							<?php if($lista_arquivos == 'debug.log'):?>
								<?php  foreach ($debug_log as $debug_log1):?> 
									<?php echo 	$debug_log1;?><br /><br />
								<?php endforeach ?>	
							<?php elseif($lista_arquivos == 'error.log'):?>
								<?php  foreach ($error_log as $error_log1):?> 
									<?php  echo $error_log1;?><br /><br />
							<?php endforeach ?>	
							<?php elseif($lista_arquivos == 'ldap.error.log'):?>	
								<?php  foreach ($ldap_error_log as $ldap_error_log1):?> 
									<?php echo 	$ldap_error_log1;?><br /><br />
								<?php endforeach ?>
							<?php endif;?>			
						</td> 
					</tr>					
				<?php endforeach; ?>		
			</tbody>
	</table>
</div>

