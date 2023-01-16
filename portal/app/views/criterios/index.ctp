<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false ));?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped tablesorter'>
		<thead>
			<th id='criteriosxxx'><?php echo $this->Html->link('Critérios','javascript:void(0)')?></th>
			<th><?php echo $this->Html->link('Data inclusão','javascript:void(0)')?></th>
			<th style="width:13px"></th>
            <th style="width:13px"></th>
		</thead>
		<tbody>
			<?php foreach ($criterios as $criterios):?>
				<tr>
					<td> 
						<?php echo $criterios['Criterio']['descricao'];?>
					</td>
					<td> 
						<?php echo $criterios['Criterio']['data_inclusao'];?>
					</td>					
					<?php if($criterios['Criterio']['campo_sistema']!='1') { ?>
						<? $iconEdit = 'icon-edit'; $iconTrash='icon-trash'; ?>
						<td>
							<?php echo $html->link('', array('controller' => 'criterios', 'action' => 'editar',$criterios['Criterio']['codigo']), array('class' => $iconEdit, 'title' => 'Alterar Critério')); //+++ ?>
						</td>
						<td>	
							<?php echo $html->link('', array('controller' => 'criterios', 'action' => 'delete', $criterios['Criterio']['codigo'], rand()), array('onclick' => 'return confirm("Confirma a exclusão do Status Critério?")', 'title' => 'Excluir Critério', 'class' => $iconTrash)) ?>
	    				</td>

					<? } else { ?>
						
						<? $iconEdit = 'icon-white icon-edit'; $iconTrash='icon-white icon-trash'; ?>
				
						<td>
							<?php echo '<span class="'. $iconEdit.'" title="Sem Permissão">  </span>'; ?>
						</td>
						<td>
							<?php echo '<span class="'. $iconTrash.'" title="Sem Permissão">  </span>'; ?>
							
	    				</td>
	    			<?php } ?>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter( { headers : {1:{sorter:false},2:{sorter:false}} } )")) ?>