<?php echo $this->BForm->create('Veiculo', array('url' => array('controller' => 'veiculos', 'action' => 'sincroniza_cancelamentos_veiculo_cliente'))); ?>
    <div class='row-fluid inline'>      
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'Veiculo') ?>     
        <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input("Veiculo.placa", array('label' => 'Placa', 'class' => 'input-small placa-veiculo'))?>
    </div>
    <div class="form-actions">
        <?php echo $this->BForm->submit('Sincronizar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end(); ?>
<?if( !empty($sincroniza)) :?>
<div id="lista">
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Mensagem</th>
	            <th>Cnpj</th>
	            <th>Placa</th>
	            <th>Status</th>
	        </tr>
	    </thead>
		<tbody>
		<?php foreach( $sincroniza as $key => $dados ):?>
		<tr>
			<td><?=$dados['msg']?></td>
			<td><?=Comum::formatarDocumento($dados['cnpj'])?></td>
			<td><?=Comum::formatarPlaca($dados['placa'])?></td>
			<td><?=$dados['tipo']?></td>
	 	<?php endforeach; ?>
	    </tbody>
</table>
</div>
<?endif;?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){ 
       setup_mascaras();
        jQuery('#limpar-filtro').click(function(){
            $('#ClienteRazaoSocial').val('');
            $('#VeiculoPlaca').val('');
            $('#VeiculoCodigoCliente').val('');
            $('#lista').hide();
        });
   
        $('#VeiculoCodigoCliente').blur(function(){
            var codigo_cliente = $('#VeiculoCodigoCliente').val();
            if (codigo_cliente != '') {
                $.ajax({
                    url:baseUrl + 'embarcadores_transportadores/listar_por_cliente/' + codigo_cliente + '/' + Math.random(),
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#ClienteRazaoSocial').val(data.razao_social);                            
                        }
                    }
                })
            }
        });
    });", false);?>