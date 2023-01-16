<div class="form-procurar">
	<?php echo $this->element('/filtros/alertas_tipos_filtros'); ?>
</div>

<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'incluir', rand()), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>
<div class='row-fluid inline tipos_alertas'>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Código</th>
	            <th>Descrição</th>
	            <th>Interno</th>
				<th style='width:40px'></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($alertasTipos as $alertaTipo): ?>
	        <tr>	            
	            <td><?= $alertaTipo['AlertaTipo']['codigo'] ?></td>
	            <td><?= $alertaTipo['AlertaTipo']['descricao'] ?></td>
	            <td><?php echo ($alertaTipo['AlertaTipo']['interno'] == 'S') ? 'Sim' : 'Não'; ?></td>
	            <td>
					<?= $this->Html->link('', array('action' => 'editar', $alertaTipo['AlertaTipo']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					<?= $this->Html->link('', array('action' => 'excluir', $alertaTipo['AlertaTipo']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
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