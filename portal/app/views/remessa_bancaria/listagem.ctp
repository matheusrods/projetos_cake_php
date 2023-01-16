<style type="text/css">
/*.table {
    table-layout:fixed;
    max-width:1170px;
    display: block;
}
*/
.table td {
  white-space: nowrap;
  /*overflow-y: auto;*/
}
.table th {
  white-space: nowrap;
}
</style>
<?php if( !empty($remessa_bancaria) ): ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<!-- <div style="width: 1170px; height: 500px; overflow-y: scroll;"> -->
		<table class="table table-striped">
			<thead>
				<th >Código</th>
				<th >CPF/CNPJ</th>
				<th >Cliente</th>
				<th >Código Pedido</th>
				<th >Banco</th>
				<th >Nosso Numero</th>
				<th >Dt. Emissão</th>
				<th >Dt. Vencimento</th>
				<th >Dt. Pagamento</th>
				<th >Status</th>
				<th >Status Retorno</th>
				<th >Usuário Remessa</th>
				<th >Usuário Retorno</th>
				<th class='input-small numeric'>Valor Juros/Multa</th>
				<th class='input-small numeric'>Valor Tarifa</th>
				<th class='input-small numeric'>Valor a Receber</th>
				<th class='input-small numeric'>Valor Principal</th>
				<th></th>
			</thead>
			<tbody>			
				<?php foreach($remessa_bancaria as $key => $value):?>
					<?php $retorno = (!empty($value['RemessaRetorno']['codigo'])) ? $value['RemessaRetorno']['codigo_ocorrencia']."-".$value['RemessaRetorno']['descricao'] : '';	?>
					<?php $cliente = (isset($value['Cliente']['nome_fantasia'])) ? $value['Cliente']['nome_fantasia'] : $value['RemessaBancaria']['nome_pagador']; ?>
					<tr>
						<td><?php echo $value['Cliente']['codigo']; ?></td>
						<td><?php echo $this->Buonny->documento($value['Cliente']['codigo_documento']); ?></td>
						<td><?php echo $cliente; ?></td>
						<td><?php echo $value['RemessaBancaria']['codigo_pedido']; ?></td>
						<td><?php echo $value['RemessaBancaria']['codigo_banco']; ?></td>
						<td><?php echo $value['RemessaBancaria']['nosso_numero']; ?></td>
						<td><?php echo $value['RemessaBancaria']['data_emissao']; ?></td>
						<td><?php echo $value['RemessaBancaria']['data_vencimento']; ?></td>
						<td><?php echo $value['RemessaBancaria']['data_pagamento']; ?></td>
						<td><?php echo $value['RemessaStatus']['descricao']; ?></td>
						<td><?php echo $retorno; ?></td>
						<td><?php echo $value['UsuarioRemessa']['nome']; ?></td>
						<td><?php echo $value['UsuarioRetorno']['nome']; ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor_juros']); ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor_tarifa']); ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor_pago']); ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['RemessaBancaria']['valor']); ?></td>
						<td>
							<?php if(empty($value['Cliente']['codigo'])): ?>
		                    	<?php echo $this->Html->link('', array('action' => 'editar', $value['RemessaBancaria']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
		                    <?php endif;?>
		                </td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan = "13"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['RemessaBancaria']['count']; ?></td>
					<td class='input-small numeric '><b><?php echo $this->Buonny->moeda($remessa_bancaria_total[0]['total_juros']); ?></b></td>
					<td class='input-small numeric'><b><?php echo $this->Buonny->moeda($remessa_bancaria_total[0]['total_tarifa']); ?></b></td>
					<td class='input-small numeric'><b><?php echo $this->Buonny->moeda($remessa_bancaria_total[0]['total_pago']); ?></b></td>
					<td class='input-small numeric'><b><?php echo $this->Buonny->moeda($remessa_bancaria_total[0]['total']); ?></b></td>
					<td class="text-right">&nbsp;</td>
				</tr>
			</tfoot>  
			
		</table>
	<!-- </div> -->
	<div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>  
<?php echo $this->Js->writeBuffer(); ?>
    <?php 
echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		setup_mascaras();
		setup_datepicker();
	});
    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'remessa_bancaria/listagem/' + Math.random());	
    }
   
    function fecharMsg(){
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );     
    }
    function gerarMensagem(css, mens){
        $('div.message.container').css({ 'opacity': '1', 'display': 'block' });
        $('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
        fecharMsg();
    }
    function marcardesmarcar(){ 
	    $('.marcar').each(function() {
	   		
			if ($(this).prop('checked')) {
			    $(this).prop('checked', false);
			} else {
			    $(this).prop('checked', true);
			}
	    });
	}
");
?>
