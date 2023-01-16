<?php echo $this->BForm->create('TRefeReferencia', array('url' => array('controller' => 'Referencias','action' => 'alterar_referencia_compartilhada',$this->data['TRefeReferencia']['refe_codigo'])));?>
	<ul class="nav nav-tabs">
	  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
	  <li><a href="#historico" data-toggle="tab">Histórico</a></li>
	</ul>
	<?php echo $this->BForm->hidden('refe_utilizado_sistema', array('value' => 'N')) ?>
	<?php echo $this->BForm->hidden('refe_codigo') ?>	

	<div class="tab-content">
		<div class="tab-pane active" id="gerais">
			<div id="info" style="width:50%;float:left;">
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_descricao', array('label' => 'Descrição', 'type' => 'text','class' => 'input-xlarge')) ?>
				</div>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_cep', array('label' => 'CEP', 'type' => 'text','class' => 'input-small formata-cep')) ?>	
					<?php echo $this->BForm->input('refe_endereco_empresa_terceiro', array('label' => 'Endereço', 'type' => 'text','class' => 'input-xlarge endereco')) ?>
				</div>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_bairro_empresa_terceiro', array('label' => 'Bairro', 'type' => 'text','class' => 'input-medium bairro')) ?>
					<?php echo $this->BForm->input('refe_estado', array('label' => 'Estado', 'type' => 'text','class' => 'input-small estado', 'maxlength' => 2)) ?>
					<?php echo $this->BForm->input('refe_cidade', array('label' => 'Cidade', 'type' => 'text','class' => 'input-medium cidade')) ?>
				</div>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_numero', array('label' => 'Numero', 'type' => 'text','class' => 'input-small')) ?>
				</div>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_latitude', array('label' => 'Latitude <a title="Utilize o formato decimal. (Ex: -9.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>
					<?php echo $this->BForm->input('refe_longitude', array('label' => 'Longitude <a title="Utilize o formato decimal. (Ex: -54.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>		
				
					<div class="control-group input text">
						<label for="RefeReferenciaLongitude">&nbsp</label>
						<?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')) ;?>
					</div>					
				</div>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('refe_cref_codigo', array('label' => 'Classe','class' => 'input-large', 'empty' => 'Classe', 'options' => $classes)) ?>
					<?php echo $this->BForm->input('tloc_tloc_codigo', array('label' => 'Tipo','class' => 'input-medium', 'empty' => 'Tipo', 'options' => $tipos)) ?>
				</div>
			</div>
			<div id="canvas_mapa" style="width:48%;height:580px;float:right;background:#E5E3DF;">

			</div>
		</div>

		<div class="tab-pane" id="historico" style="overflow-x:auto">
			<table class="table table-striped">
			    <thead>
			        <tr>
			            <th>Data</th>
			            <th>Usuário Adicionou</th>
			            <th>Usuário Alterou</th>
			            <th>Descrição</th>
			            <th>CNPJ</th>
			            <th>CEP</th>
			            <th>Cidade</th>
			            <th>Estado</th>
			            <th>Endereço</th>
			            <th>Bairro</th>
			            <th>Número</th>
			            <th>Latitude</th>
			            <th>Longitude</th>
			            <th>Raio</th>
			            <th>Inativo?</th>
			        </tr>
			    </thead>
			    <tbody>
					<?php foreach($dados_historico as $dado): ?>
						<tr>
							<td><?php echo substr(AppModel::DbDateToDate($dado['TRefeReferenciaHistorico']['refe_data_alteracao']),0,19) ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_usuario_adicionou'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_usuario_alterou'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_descricao'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_cnpj_empresa_terceiro'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_cep'] ?></td>
							<td><?php echo $dado['TCidaCidade']['cida_descricao'] ?></td>
							<td><?php echo $dado['TEstaEstado']['esta_sigla'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_endereco_empresa_terceiro'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_bairro_empresa_terceiro'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_numero'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_latitude'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_longitude'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_raio'] ?></td>
							<td><?php echo $dado['TRefeReferenciaHistorico']['refe_inativo'] ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>


	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'Referencias', 'action' => 'adicionar_referencia_compartilhada'), array('class' => 'btn')) ;?>
	</div>

<?php echo $this->BForm->end(); ?>


<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();
		ver_mapa("#TRefeReferenciaRefeLatitude","#TRefeReferenciaRefeLongitude", "#TRefeReferenciaRefeRaio","alterar");

		$("#TRefeReferenciaRefeCep").blur(function(){
			buscar_endereco("#TRefeReferenciaRefeCep");
			return false;
		});

		$(".bt-send-xy").click(function(){
			busca_xy($("#TRefeReferenciaAlterarForm"),$("#TRefeReferenciaRefeLatitude"),$("#TRefeReferenciaRefeLongitude"),$("#TRefeReferenciaRefeRaio"));
			return false;
		});
		
	});', false);
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style type="text/css">
	img { max-width: none !important; }
</style>