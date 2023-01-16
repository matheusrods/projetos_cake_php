
<div class='well'>
	<?php echo  $this->BForm->create('ItemPedido',array('url' => array('controller' => 'itens_pedidos','action' =>'taxa_administrativa_sintetica')));?>
	
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('ano_faturamento', array('options' => $ano_faturamento, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
	</div>
	
	<div class="row-fluid inline">
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $html->link('Limpar', 'taxa_administrativa_sintetica', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>

<?php if(isset($dados)): ?>
	 	<table class="table table-striped table-bordered">
	        <thead>
	            <tr>
	                <th class=" input-medium"> Mês </th>
	                <th class=" input-xxlarge numeric"> Taxa Adm.(R$)</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php $total         	= 0;?>
		        <?php $total_taxa_adm   = 0;?>

	            <?php foreach($dados as $key => $dado): ?>
	                <?php $mes = $dado[0]['mes'] ?>
	                <?php $ano =  str_replace("/","-",$dado[0]['ano']);?> 
		                
		                <tr>
		                    <td><?php echo $this->Buonny->mes_extenso($mes); ?></td>
		                    <td class="numeric">
		                    	<?php echo $this->Html->link($this->Buonny->moeda($dado[0]['valor_taxa_bancaria']), "javascript:analitico('{$ano}','{$mes}')") ?></td>
						</tr>
			                   
		                <?php $total_taxa_adm   = $total_taxa_adm + $dado[0]['valor_taxa_bancaria'];?>
		                
						        
		        <?php endforeach; ?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <td><strong>Total</strong></td>
	                <td class="numeric"><?php echo $this->Buonny->moeda(round($total_taxa_adm, 2)) ?></td>
	            </tr>
	        </tfoot>
	    </table>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock("
	function analitico(ano,mes) {
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
	    form.setAttribute('method', 'post');
	    form.setAttribute('action', '/portal/ItensPedidos/taxa_administrativa_analitica/');
	    form.setAttribute('target', form_id);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[ItemPedido][ano_faturamento]');
	    field.setAttribute('value', ano);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[ItemPedido][mes_faturamento]');
	    field.setAttribute('value', mes);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);	    	   
	    document.body.appendChild(form);
	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    form.submit();
	};") ?>