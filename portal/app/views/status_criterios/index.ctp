<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-large'>Critérios</th>
			<th class='input-large'></th>
			<th class='input-large'>Status</th>
			<th class='input-large'></th>
			<th style="width:13px" ></th>
			<th style="width:13px"></th>
		</thead>
		<tbody>
			
			<?php 
                $Status_criterio_anterior = null;
                $Status_Criterio = 0;

            ?>

			<?php foreach ($statuscriterios as $statuscriterios):?>
				</tr>				
					<tr>
						<?php if ($Status_criterio_anterior != $statuscriterios['Criterio']['codigo']): ?>	
							<tr>
								<tr id="<?php echo $statuscriterios['Criterio']['codigo']; ?>" style="cursor:pointer"  
								StatusCriterio="<?=++$Status_Criterio?>" >
										
									<td>
									<i class="icon-chevron-right"></i> 
									<strong><?php echo $statuscriterios['Criterio']['descricao'];?> </strong>
									</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
				                        <td>&nbsp;</td>
				                        <td>&nbsp;</td>
				                        <td>&nbsp;</td>
				
				             	 	
									<tr style="display:none" class="StatusCriterio-<?=$Status_Criterio?>">
										<td>&nbsp;</td>
										<td></td>
										<td><?php echo $statuscriterios['StatusCriterio']['descricao']?></td>
										<td>&nbsp;</td>
									   <?php if($statuscriterios['Criterio']['campo_sistema'] == 0){
                                       ?>
										<td>
										<?php echo $html->link('', array('controller' => 'status_criterios', 'action' => 'editar',$statuscriterios['StatusCriterio']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar Status  Critério')); ?>
										</td>
										<td>	
										<?php echo $html->link('', array('controller' => 'status_criterios', 'action' => 'delete', $statuscriterios['StatusCriterio']['codigo']), array('onclick' => 'return confirm("Confirma a exclusão do Status Critério?")', 'title' => 'Excluir Status Critério', 'class' => 'icon-trash')) ?>
										</td>
										<?php }else{ ?>

                                             <td>
												<span class ="icon-edit icon-white" ></span>
											</td>
											<td>	
			                                    <span class ="icon-trash icon-white" ></span>
											</td>	
                                        <?php } ?>  
                                        
										</tr>
								</tr>							
							</tr>		
						<?php else: ?>
							<tr style="display:none" class="StatusCriterio-<?=$Status_Criterio?>">
								<td>&nbsp;</td>
								<td></td>
								<td><?php echo $statuscriterios['StatusCriterio']['descricao']?></td>
								<td>&nbsp;</td>
								<?php if($statuscriterios['Criterio']['campo_sistema'] == 0){
                                       ?>
								<td>
									<?php echo $html->link('', array('controller' => 'status_criterios', 'action' => 'editar',$statuscriterios['StatusCriterio']['codigo']), array('class' => 'icon-edit', 'title' => 'Alterar Status  Critério')); ?>
								</td>
								<td>	
									<?php echo $html->link('', array('controller' => 'status_criterios', 'action' => 'delete', $statuscriterios['StatusCriterio']['codigo']), array('onclick' => 'return confirm("Confirma a exclusão do Status Critério?")', 'title' => 'Excluir Status Critério', 'class' => 'icon-trash')) ?>
								</td>
								<?php }else{?>
                                <td>
									<span class ="icon-edit icon-white" ></span>
								</td>
								<td>	
                                    <span class ="icon-trash icon-white" ></span>
								</td>	
							<?php } ?>
							</tr> 
                            

						<?php endif; ?>	
					</tr>
					<?php $Status_criterio_anterior = $statuscriterios['Criterio']['codigo'] ?>
				</tr>
		    <?php endforeach;?>
		</tbody>
	</table>
</div>

<?php
        echo $this->Javascript->codeBlock('
            $(function() {
                $("tr a").click(function(){
                    window.location = $(this).attr("href");
                    return false;
                });

                $("tr").click(function(){
                    $(".StatusCriterio-"+$(this).attr("StatusCriterio")).toggle();
                    
                    if($(this).find("i.icon-chevron-down").length > 0){
                        $(this).find("i").addClass("icon-chevron-right");
                        $(this).find("i").removeClass("icon-chevron-down");
                    }else{
                        $(this).find("i").addClass("icon-chevron-down");
                        $(this).find("i").removeClass("icon-chevron-right");
                    }

                    return false;
                });
        	});', 
        false);
?>
