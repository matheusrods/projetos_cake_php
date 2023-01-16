<div class="form-procurar">
	<?php echo $this->element('/filtros/motivos_atendimentos');?>

</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'incluir', rand()), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>
<div class='row-fluid inline tipos_alertas'>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Descrição</th>
				<th style='width:40px'></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($motivos_atendimentos as $motivos): ?>
	        <tr>
	            <td><?= $motivos['MotivoAtendimento']['descricao'] ?></td>
	            <td>
					<?= $this->Html->link('', array('action' => 'editar', $motivos['MotivoAtendimento']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					<?= $this->Html->link('', array('action' => 'excluir', $motivos['MotivoAtendimento']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
				</td>
	        </tr>
	        <?php endforeach; ?>        
	    </tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock('
    $(function() {
        $("a.icon-trash").click(function(){
        	if(!confirm("Deseja remover este registro?")){ 
        		return false;
        	}else{
    			bloquearDiv($(".tipos_alertas"));
        	}
        })
    });
');