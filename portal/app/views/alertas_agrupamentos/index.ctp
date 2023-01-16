<div class="form-procurar">
	<?php echo $this->element('/filtros/alertas_agrupamentos_filtros'); ?>
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
	        <?php foreach ($alertasTipos as $alertaTipo): ?>
	        <tr>
	            <td><?= $alertaTipo['AlertaAgrupamento']['descricao'] ?></td>
	            <td>
					<?= $this->Html->link('', array('action' => 'editar', $alertaTipo['AlertaAgrupamento']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					<?= $this->Html->link('', array('action' => 'excluir', $alertaTipo['AlertaAgrupamento']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
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