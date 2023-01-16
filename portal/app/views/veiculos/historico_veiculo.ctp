
<?php echo $this->BForm->create('TVeicVeiculo', array('action' => 'post', 'url' => array('controller' => 'Veiculos','action' => 'historico_veiculo')));?>

<div class='row-fluid inline'>	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('veic_placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa')) ?> 
		<div class="control-group input text required">
			<label>&nbsp;</label>
			<?php echo (isset($veiculo) && $veiculo)?$this->Buonny->placa($veiculo['TVeicVeiculo']['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59')):NULL ?>
		</div>
		
	</div>
</div>

<?php echo $this->BForm->submit('Localizar', array('div' => false, 'class' => 'btn btn-success')) ?>
<?php echo $this->BForm->end() ?>


<?php echo $this->Buonny->link_js('estatisticas') ?>
<?php echo $this->Javascript->codeBlock('
	$(function(){
		setup_mascaras();
	});', false);
?>
<?php if(isset($this->data) && $this->data): ?> 
<div class='form-procurar'>
    <?php echo $this->element('/veiculos/consulta_veiculo'); ?>
</div>

<ul class="nav nav-tabs">
	<li class="active"><a href="#gerais" data-toggle="tab">Clientes</a></li>
	<li><a href="#historico" data-toggle="tab">Histórico</a></li>
	<li><a href="#terminal" data-toggle="tab">Terminal</a></li>
</ul>

<div class="tab-content">

	<div class="tab-pane active" id="gerais">
		<table class="table table-striped">
		    <thead>
		        <tr>
		            <th>Razão Social</th>
		            <th>CNPJ</th>
		        </tr>
		    </thead>
		    <tbody>
							
				<?php if(!empty($lista_clientes)):?>	
					<?php foreach($lista_clientes as $dado): ?>
						<tr>
							<td><?php echo $dado['TPjurPessoaJuridica']['pjur_razao_social'] ?></td>
							<td><?php echo  Comum::formatarDocumento($dado['TPjurPessoaJuridica']['pjur_cnpj']) ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
    				<tr><td colspan='2'>Não há registro(s) para exibição</td></tr>
    			<?php endif; ?>		

			</tbody>
		</table>
	</div>

	<div class="tab-pane" id="historico" style="overflow-x:auto">
		<table class="table table-striped">
		    <thead>
		        <tr>
		            <th>Tipo</th>
		            <th>Modelo</th>
		            <th>Cor</th>
		            <th>Chassi</th>
		            <th>Renavam</th> 
		            <th>Faricante</th>
		            <th>Ano Fabricacao</th>
		            <th>Ano Modelo</th>
		            <th>Cidade</th>
		            <th>UF</th>
		            <th>Usuario Alterou</th>
		            <th>Data Alterou</th>
		        </tr>
		    </thead>
		    <tbody>
		    	<?php $usuario = $this->data['TVeicVeiculo']['veic_usuario_alterou'] ?>
				<?php if(!empty($lista_veiculos)): ?>					
					<?php foreach($lista_veiculos as $key => $dado): ?>
						<tr>
							<td><?php echo $dado['TTveiTipoVeiculo']['tvei_descricao'] ?></td>
							<td><?php echo $dado['TMvecModeloVeiculo']['mvec_descricao'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico']['veic_cor'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico']['veic_chassi']?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico']['veic_renavam']?></td>
							<td><?php echo $dado['TMveiMarcaVeiculo']['mvei_descricao'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico']['veic_ano_fabricacao'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico']['veic_ano_modelo'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico'][0]['TCidaCidade']['cida_descricao'] ?></td>
							<td><?php echo $dado['TVeicVeiculoHistorico'][0]['TEstaEstado']['esta_sigla'] ?></td>
							<td><?php echo $usuario ?></td>
							<td><?php echo $dado[0]['data_inclusao'] ?></td>
							<?php $usuario = $dado['TVeicVeiculoHistorico']['veic_usuario_alterou']?$dado['TVeicVeiculoHistorico']['veic_usuario_alterou']:$dado['TVeicVeiculoHistorico']['veic_usuario_adicionou'] ?>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
    				<tr><td colspan='13'>Não há registro(s) para exibição</td></tr>
    			<?php endif; ?>		
			</tbody>
		</table>
	</div>

	<div class="tab-pane" id="terminal" style="overflow-x:auto">
		<table class="table table-striped">
		    <thead>
		        <tr>
		            <th>Tecnologia</th>
		            <th>Versão</th>
		            <th>Numero</th>
		            <th>Serie</th>
		            <th>Usuario Alterou</th>
		            <th>Data Alterou</th>
		        </tr>
		    </thead>
		    <tbody>
		    	<?php $usuario = $this->data['TOrteObjetoRastreadoTermina']['orte_usuario_alterou'] ?>
				<?php if(!empty($lista_terminais)): ?>	
					<?php foreach($lista_terminais as $key => $dado): ?>
						<tr>
							<td><?php echo $dado['TTecnTecnologia']['tecn_descricao'] ?></td>
							<td><?php echo $dado['TVtecVersaoTecnologia']['vtec_descricao'] ?></td>
							<td><?php echo $dado['TTermTerminal']['term_numero_terminal'] ?></td>
							<td><?php echo $dado['TTermTerminal']['term_numero_serie'] ?></td>
							<td><?php echo $usuario ?></td>
							<td><?php echo $dado[0]['data_inclusao'] ?></td>
							<?php $usuario = $dado['TOrteObjetoRastreadoHistor']['orte_usuario_alterou']?$dado['TOrteObjetoRastreadoHistor']['orte_usuario_alterou']:$dado['TOrteObjetoRastreadoHistor']['orte_usuario_adicionou'] ?>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
    				<tr><td colspan='6'>Não há registro(s) para exibição</td></tr>
    			<?php endif; ?>	
			</tbody>
		</table>
	</div>

</div>
<?php endif; ?>