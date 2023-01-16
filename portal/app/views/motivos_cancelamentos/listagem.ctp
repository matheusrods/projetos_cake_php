<div class='row-fluid inline tipos_alertas'>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Descrição</th>
				<th style='width:40px'></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($motivos_cancelamentos as $motivo_cancelamento): ?>
	        <tr>
	            <td><?= $motivo_cancelamento['MotivoCancelamento']['descricao'] ?></td>
	            <td>
					<?= $this->Html->link('', array('action' => 'editar', $motivo_cancelamento['MotivoCancelamento']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					<?= $this->Html->link('', array('action' => 'excluir', $motivo_cancelamento['MotivoCancelamento']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
				</td>
	        </tr>
	        <?php endforeach; ?>        
	    </tbody>
	</table>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
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