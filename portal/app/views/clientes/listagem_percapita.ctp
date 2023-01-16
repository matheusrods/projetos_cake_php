<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Nome', 'razao_social') ?></th>
            <th colspan="3"><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente) :?>
        <tr>
            <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
            <td class="pagination-centered">
                	<?php echo $html->link('', array('controller' => 'funcionarios', 'action' => 'index_percapita', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Funcionários do Cliente')); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>

<?php echo $javascript->codeblock('
	jQuery(document).ready(function() {});
	function carregaFuncionarios(id, element) {
	
		if($("#cliente_" + id).is( ":visible" ) === false) {
			var onclick = $(element).attr("onclick");
			$("#cliente_" + id).fadeIn();

			$.ajax({
		        type: "POST",
		        url: "/portal/funcionarios/listagem_percapita/" + id,
		        dataType: "html",
		        beforeSend: function() {
		        	$(element).removeAttr("onclick");
		        	$("#carregando_" + id).fadeIn();
		        },
		        success: function(html) {
		        	$("#resultado_" + id).html(html);
		        },
		        complete: function() {
		        	$(element).bind("click", function() { carregaFuncionarios(id); });
		        	$("#carregando_" + id).fadeOut();
		        }
		    });			
		} else {
			$("#cliente_" + id).fadeOut();		
		}	
	}
'); ?>



