<?php
	if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("window.location = window.location");
        exit;
    }
?>

<?php if(!$cliente): ?>
	<?php echo $this->element('viagens/cliente') ?>
<?php else: ?>
	<?php echo $this->Bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'Viagens', 'action' => 'add_viagem', 'model' => 'Recebsm', 'element_name' => 'add_viagem'), 'divupdate' => '.lista')) ?>
	<?php echo $this->element('viagens/cliente') ?>
	<?php if (!empty($data['TViagViagem']['pedido_cliente'])): ?>
		<?php echo $this->element('viagens/status_viagem') ?>
	<?php endif; ?>
	<ul class="nav nav-tabs">
	    <li class="active"><a href="#sms" data-toggle="tab">Solicitação Monitoramento</a></li>
	    <li><a href="#macros" data-toggle="tab">Macros Logísticas</a></li>
	    <li><a href="#msg_livre" data-toggle="tab">Mensagem Livre</a></li>
    	<?//php if ($permite_checklist_entrada): ?>
    	<?php if ($data['TViagViagem']['placa']): ?>
	    <li class="pull-right" >
	    	<?php echo $html->link('Fazer Checklist de Entrada', array('controller' => 'Viagens', 'action' => 'checklist_entrada',$cliente['Cliente']['codigo'], $data['TViagViagem']['placa'], $data['TViagViagem']['checklist_dias_validos']));?>
	   	</li>
    	<?php endif; ?>
    </ul>

    <div class="tab-content">
		<div class="tab-pane active" id="sms">
			<?php echo $this->element('viagens/sm') ?>
		</div>

		<div class="tab-pane" id="macros">
			<h4>Tempos</h4>
			<div id='tempos_por_placa'>
				<table class='table table-striped'>
					<thead>
						<th>Inicio</th>
						<th>Fim</th>
						<th>Posição</th>
						<th class='numeric'>Em Viagem</th>
						<th class='numeric'>Parado</th>
					</thead>
				</table>
			</div>
			<h4>Macros Enviadas pelo Motorista</h4>
			<div id='macros_por_placa'>
				<table class='table table-striped'>
					<thead>
						<th>Hora</th>
						<th>Posição</th>
						<th>Macro</th>
					</thead>
				</table>
			</div>
		</div>

		<div class="tab-pane" id="msg_livre">
			<br/>
		</div>
	</div>

	<?php echo $this->BForm->end(); ?>

	<?php $this->data['rinaldo'] = $data; ?>
	<?php echo $this->Buonny->link_js('solicitacoes_monitoramento'); ?>
	<?php if ($data['TViagViagem']['placa']): ?>
	<?php echo $this->Javascript->codeBlock("
		carregar_mensagem_livre('".serialize($data)."','#msg_livre');
		macros_por_placa('".$data['TViagViagem']['placa']."', '".$data['TViagViagem']['data_inicial']."', '".$data['TViagViagem']['data_final']."');
		tempos_por_placa('".$data['TViagViagem']['placa']."', '".$data['TViagViagem']['data_inicial']."', '".$data['TViagViagem']['data_final']."');
	"); ?>
	<?php endif; ?>


<?php endif; ?>
