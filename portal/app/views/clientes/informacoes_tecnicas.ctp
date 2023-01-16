<br />
<?php if($cliente_buonny): ?>

<div id="cliente" class='well'>
	<strong>Código: </strong><?= $cliente_buonny['Cliente']['codigo'] ?>
	<strong>Cliente: </strong><?= $cliente_buonny['Cliente']['razao_social'] ?>
</div>


<?php if(isset($cliente_monitora)): ?>
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'adicionar_contato', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Contato", 580)', 'title' => 'Adicionar Contato', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="7">1 - Contatos Operacionais / Filiais</th>
		</thead>
		<thead>
			<th class='input-large'>Nome Contato</th>
			<th class='input-medium'>Cargo</th>
			<th class='input-large'>Filial</th>
			<th class='input-medium'>Tel. Comercial</th>
			<th class='input-medium'>Tel. Celular</th>
			<th class='input-medium'>E-mail</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($contatos as $contato):?>
				<tr>
					<td><?php echo 	$contato['MMonContato']['CON_Nome']?></td>
					<td><?php echo $contato['MMonContato']['CON_Cargo']?></td>
					<td><?php echo $contato['MMonContato']['CON_Filial']?></td>
					<td>(<?php echo str_replace(array('(',')'), '', $contato['MMonContato']['CON_DDDTelefone'])?>) <?php echo $contato['MMonContato']['CON_Telefone']?></td>
					<td>(<?php echo str_replace(array('(',')'), '', $contato['MMonContato']['CON_DDDCelular'])?>) <?php echo $contato['MMonContato']['CON_Celular']?></td>
					<td><?php echo $contato['MMonContato']['CON_EMail']?></td>
					<td>
						<?= $this->Html->link('', array('action' => 'edita_contato', $contato['MMonContato']['CON_Codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Contato", 580)', 'title' => 'Editar Contato', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('action' => 'remove_contato', $contato['MMonContato']['CON_Codigo'], rand()), array('title' => 'Excluir Contato', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir',array('action' => 'adicionar_mercadorias', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Mercadoria", 560)', 'title' => 'Adicionar Mercadoria', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="3">2 - Mercadorias Transportadas</th>
		</thead>
		<thead>
			<th class='input-xxlarge'>Tipo</th>
			<th class='input-xxlarge'>Representativo</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($mercadorias_transportadas as $mercadoria):?>
				<tr>
					<td><?php echo $mercadoria['MercadoriaTransportada']['descricao'] ?></td>
					<td><?php echo $mercadoria['MercadoriaTransportada']['representativo'] ?> %</td>
					<td>
						<?= $this->Html->link('', array('action' => 'edita_mercadoria', $mercadoria['MercadoriaTransportada']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Mercadoria", 560)', 'title' => 'Editar Contato', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('action' => 'remove_mercadoria', $mercadoria['MercadoriaTransportada']['codigo'], rand()), array('title' => 'Excluir Mercadoria', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'adicionar_embarques', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Embarque", 560)', 'title' => 'Adicionar Embarque', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="6">3 - Principais Embarques</th>
		</thead>
		<thead>
			<th class='input-large' colspan="2" >Local de Origem</th>
			<th class='input-large' colspan="2" >Local de Destino</th>
			<th class='input-large'></th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<thead>
			<th>Cidade</th>
			<th>Estado</th>
			<th>Cidade</th>
			<th>Estado</th>
			<th>Percentual</th>
			<th></th>
		</thead>
		<tbody>
			<?php foreach ($principais_embarques as $embarque):?>
				<tr>
					<td><?php echo $embarque['PrincipalEmbarque']['cidade_origem'] ?></td>
					<td><?php echo $embarque['PrincipalEmbarque']['estado_origem'] ?></td>
					<td><?php echo $embarque['PrincipalEmbarque']['cidade_destino'] ?></td>
					<td><?php echo $embarque['PrincipalEmbarque']['estado_destino'] ?></td>
					<td><?php echo $embarque['PrincipalEmbarque']['percentual'] ?> %</td>
					<td>
						<?= $this->Html->link('', array('action' => 'edita_embarque', $embarque['PrincipalEmbarque']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Contato", 560)', 'title' => 'Editar Embarque', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('action' => 'remove_embarque', $embarque['PrincipalEmbarque']['codigo'], rand()), array('title' => 'Excluir Embarque', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />
<div class='actionbar-right'>
	<?php 
		if(!$conta_autotrac)
		echo $this->Html->link('Incluir', array('action' => 'adicionar_autotrac', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Conta Autotrac", 560)', 'title' => 'Adicionar Conta Autotrac', 'class' => 'btn btn-success')) ;
	?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th colspan="6">4 - Autotrac</th>
		</thead>
		<thead>
			<th class='input-medium'>Possui conta</th>
			<th class='input-large'>Buonny</th>
			<th class='input-medium'>Macro Buonny</th>
			<th class='input-xlarge'>Analista</th>
			<th class='input-xlarge'>Celular</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($conta_autotrac as $autotrac):?>
				<tr>
					<td><?php echo (($autotrac['ContaAutotrac']['possui_conta'] == 'S')?'SIM':'NÃO') ?></td>
					<td><?php echo $autotrac['ContaAutotrac']['conta_buonny'] ?></td>
					<td><?php echo (($autotrac['ContaAutotrac']['macro_buonny'] == 'S')?'SIM':'NÃO') ?></td>
					<td><?php echo $autotrac['ContaAutotrac']['analista'] ?></td>
					<td><?php echo $autotrac['ContaAutotrac']['telefone_contato'] ?></td>
					<td>
						<?= $this->Html->link('', array('action' => 'edita_autotrac', $autotrac['ContaAutotrac']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Contato", 560)', 'title' => 'Editar Conta Autotrac', 'class' => 'icon-edit')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir',array(
	  'controller' => 'informacoes_tecnicas', 'action' => 'adicionar_quantidade_embarque', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Quantidade de Embarques", 560)', 'title' => 'Adicionar Quantidade de Embarques', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="4">5 - Quantidade de Embarques</th>
		</thead>
		<thead>
			<th class='input-xlarge'>Diário</th>
			<th class='input-xlarge'>Semanal</th>
			<th class='input-xlarge'>Mensal</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($quantidade_embarque as $qtd_embarque):?>
				<tr>
					<td><?php echo $qtd_embarque['QuantidadeEmbarque']['diario'] ?></td>
					<td><?php echo $qtd_embarque['QuantidadeEmbarque']['semanal'] ?></td>
					<td><?php echo $qtd_embarque['QuantidadeEmbarque']['mensal'] ?></td>
					<td>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'editar_quantidade_embarque', $qtd_embarque['QuantidadeEmbarque']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Quantidade Embarque", 560)', 'title' => 'Editar Contato', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'remove_quantidade_embarque', $qtd_embarque['QuantidadeEmbarque']['codigo'], rand()), array('title' => 'Excluir Quantidade Embarque', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />

<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir',array('controller' => 'informacoes_tecnicas','action' => 'adicionar_valor_embarque', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Valores de Embarques", 560)', 'title' => 'Adicionar Valores de Embarques', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="4">6 - Valores de Embarques</th>
		</thead>
		<thead>
			<th class='input-large'>Mínimo</th>
			<th class='input-large'>Médio</th>
			<th class='input-large'>Máximo</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($valor_embarque as $vl_embarque):?>
				<tr>
					<td><?php echo number_format($vl_embarque['ValorEmbarque']['minimo'],2,',','.') ?></td>
					<td><?php echo number_format($vl_embarque['ValorEmbarque']['medio'],2,',','.') ?></td>
					<td><?php echo number_format($vl_embarque['ValorEmbarque']['maximo'],2,',','.') ?></td>
					<td>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'editar_valor_embarque', $vl_embarque['ValorEmbarque']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Valor Embarque", 560)', 'title' => 'Editar Valor Embarque', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'remove_valor_embarque', $vl_embarque['ValorEmbarque']['codigo'], rand()), array('title' => 'Excluir Valor Embarque', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<br />
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir',array('controller' => 'informacoes_tecnicas','action' => 'adicionar_principal_cliente', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Principais Clientes", 560)', 'title' => 'Adicionar Principais Clientes', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="4">7 - Principais Clientes</th>
		</thead>
		<thead>
			<th class='input-xxlarge'>Cliente</th>
			<th class='input-xxlarge'>Mercadoria Principal</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php foreach ($principais_clientes as $principal_cliente):?>
				<tr>
					<td><?php echo $principal_cliente['PrincipalCliente']['cliente'] ?></td>
					<td><?php echo $principal_cliente['PrincipalCliente']['produto'] ?></td>
					<td>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'editar_principal_cliente', $principal_cliente['PrincipalCliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Principais Clientes", 560)', 'title' => 'Editar Principais Clientes', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'remove_principal_cliente', $principal_cliente['PrincipalCliente']['codigo'], rand()), array('title' => 'Excluir Principais Clientes', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<br />
<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir',array('controller' => 'informacoes_tecnicas','action' => 'adicionar_sinistro_ultimo_mes', $cliente_buonny['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Sinistros nos Últimos 12 Meses", 560)', 'title' => 'Adicionar Sinistros nos Últimos 12 Meses', 'class' => 'btn btn-success')) ?>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-small' colspan="6">8 - Sinistros nos Últimos 12 Meses</th>
		</thead>
		<thead>
			<th class='input-large'>Data</th>
			<th class='input-xxlarge'>Local (Cidade / UF)</th>
			<th class='input-large'>Mercadoria</th>
			<th class='input-large'>Tipo</th>
			<th class='input-large'>Valor (R$)</th>
			<th class='pagination-centered' style='width:40px'></th>
		</thead>
		<tbody>
			<?php
			 foreach ($sinistro_ultimo_mes as $sinistro_ultimo):?>
				<tr>
					<td><?php echo  substr($sinistro_ultimo['SinistroUltimoMes']['data'],0,10 ) ?></td>
					<td><?php echo $sinistro_ultimo['SinistroUltimoMes']['local'] ?></td>
					<td><?php echo $sinistro_ultimo['SinistroUltimoMes']['mercadoria'] ?></td>
					<td><?php echo $sinistro_ultimo['TipoSinistro']['descricao'] ?></td>
					<td><?php echo number_format($sinistro_ultimo['SinistroUltimoMes']['valor'],2,',','.') ?></td>
					<td>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'editar_sinistro_ultimo_mes', $sinistro_ultimo['SinistroUltimoMes']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Sinistros nos Últimos 12 Meses", 560)', 'title' => 'Editar Sinistros nos Últimos 12 Meses', 'class' => 'icon-edit')) ?>
						<?= $this->Html->link('', array('controller' => 'informacoes_tecnicas','action' => 'remove_sinistro_ultimo_mes', $sinistro_ultimo['SinistroUltimoMes']['codigo'], rand()), array('title' => 'Excluir Sinistros nos Últimos 12 Meses', 'class' => 'icon-trash')) ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
			$(function(){
				$("a.icon-trash").click(function(){
					if(confirm("Deseja remover este registro?")){
						$.ajax({
							url:$(this).attr("href"),
							dataType: "html",
							success: function(data){
								atualizaInformacoesTecnicas();
							}
						});
						
					}

					return false;
				});

			})
	');
?>
<?php endif; ?>	