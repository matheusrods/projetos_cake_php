<?php echo $this->Buonny->link_css('dynatree/skin-vista/ui.dynatree'); ?>
<?php $this->addScript($this->Buonny->link_js('dynatree/jquery.dynatree.min.js')) ?>

<div class='well'>
	<strong>Código: </strong><?php echo $this->Html->tag('span', $uperfil['Uperfil']['codigo']); ?>
	<strong>Perfil: </strong><?php echo $this->Html->tag('span', $uperfil['Uperfil']['descricao']); ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Codigo</th>
            <th>Usuario</th>
            <th>Data Alteração</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
		<?php if(!empty($uperfilLogs)): ?>
			<?php foreach ($uperfilLogs as $uperfilLog): ?>
			<tr>
				<td><?= $uperfilLog['UperfilLog']['codigo'] ?></td>
				<td><?= $uperfilLog['Usuario']['nome'] ?></td>
				<td><?= $uperfilLog['UperfilLog']['data_inclusao'] ?></td>
				<td>
					<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#collapse-<?= $uperfilLog['UperfilLog']['codigo'] ?>">
						Exibir
					</button>
				</td>
			</tr>

			<?php 
				$antes = (array) json_decode($uperfilLog['UperfilLog']['antes'], true);
				$depois = (array) json_decode($uperfilLog['UperfilLog']['depois'], true);
				
				$itensDynatreeAntes = $this->Tree->itensDynatreeLog($objetos, $antes);
				$objetosAntes = $this->Tree->itensTextLog($objetos, $antes);
				$arrayAntes = $this->Tree->itensConverteArray($objetosAntes);

				$itensDynatreeDepois = $this->Tree->itensDynatreeLog($objetos, $depois);
				$objetosDepois = $this->Tree->itensTextLog($objetos, $depois);
				$arrayDepois = $this->Tree->itensConverteArray($objetosDepois);
			
				$this->addScript($this->Javascript->codeBlock('
					$(document).ready( function() {
						$("#tree-antes-' . $uperfilLog['UperfilLog']['codigo'] . '").dynatree({
							checkbox:true, 
							selectMode:3, 
							children:'.$itensDynatreeAntes.'
						});
						$("#tree-antes-' . $uperfilLog['UperfilLog']['codigo'] . '").dynatree("getRoot").visit(function(node){
							node.expand(false);
						});
						$("#tree-depois-' . $uperfilLog['UperfilLog']['codigo'] . '").dynatree({
							checkbox:true, 
							selectMode:3, 
							children:'.$itensDynatreeDepois.'
						});
						$("#tree-depois-' . $uperfilLog['UperfilLog']['codigo'] . '").dynatree("getRoot").visit(function(node){
							node.expand(false);
						});
					});
				')); 
			?>

			<tr>
				<td colspan="4" style="padding: 0px;">
					<div id="collapse-<?= $uperfilLog['UperfilLog']['codigo'] ?>" class="collapse row-fluid">
						
						<div class="span12">
							<div class="row-fluid">
								<div class="span6">
									<b>Antes</b>
								</div>
								<div class="span6">
									<b>Depois</b>
								</div>
							</div>

							<div class="row-fluid">
								<div class="span6">
									<ul>
									<?php
										foreach ($arrayAntes as $key => $texto) {
											if (in_array($texto, $arrayDepois)){
												echo "<li>$texto</li>";
											}else{
												echo "<li>$texto <span class='label label-important'>Desabilitado</span></li>";
											}
										}
									?>
									</ul>
								</div>
								<div class="span6">
									<ul>
									<?php
										foreach ($arrayDepois as $key => $texto) {
											if (in_array($texto, $arrayAntes)){
												echo "<li>$texto</li>";
											}else{
												echo "<li>$texto <span class='label label-success'>Habilitado</span></li>";
											}
										}
									?>
									</ul>
								</div>
							</div>

							<div class="row-fluid">
								<div class="span6">
									<br>
									<div id='tree-antes-<?= $uperfilLog['UperfilLog']['codigo'] ?>'></div>
								</div>
								<div class="span6">
									<br>
									<div id='tree-depois-<?= $uperfilLog['UperfilLog']['codigo'] ?>'></div>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="4">
					Não há itens!
				</td>
			</tr>
		<?php endif; ?>		
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

<div class="row-fluid">
  	<div class="span12">
		<br>
		<?= $html->link('Voltar', array('controller'=>'uperfis','action'=>'index'), array('class' => 'btn')); ?>
	</div>
</div>