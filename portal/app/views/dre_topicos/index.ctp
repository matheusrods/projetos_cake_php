<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Tópico'));?>
</div>
<div class='lista'>
	<ul id="topicos">
		<?php $indice = 1; ?>
		<?php foreach($topicos as $topico): ?>
			<?php
				$numero = $topico['DreTopico']['numero'];
				if(substr_count($numero, ".") == 0){
					$a_class = 'topico-branco';
				}elseif(substr_count($numero, ".") == 1){
					$a_class = 'topico-azul';
					$indice = 1;
				}else{
					if($indice % 2){
						$a_class = 'sub-topico-azul';
					}else{
						$a_class = 'sub-topico-branco';
					}
				}
				$indice++;
			?>
			<li class="<?php echo $a_class; ?>">
				<?php echo $this->Form->hidden('codigo', array('value'=>$topico['DreTopico']['codigo'])); ?>
				<?php echo $this->Form->hidden('ordenacao', array('value'=>$topico['DreTopico']['ordenacao'])); ?>
				<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<?php echo $topico['DreTopico']['numero'].' '.$topico['DreTopico']['descricao']; ?>
				<?php if($topico['DreTopico']['tipo_topico'] == 2): ?>
					<span class="formula"><b>fr:</b> <?php echo $topico['DreTopico']['formula']?></span>
				<?php else: ?>
					<?php foreach($topico['DreTopicoRegra'] as $topico_regra): ?>
						<span class="regra">
							<?php 
								$regras = "";
								if(!empty($topico_regra['ccusto']))
									$regras .= "<b>cc:</b> {$topico_regra['ccusto']} ";
								if(!empty($topico_regra['grflux'])){
									foreach($grflux as $dado){
										if($dado['Grflux']['codigo'] == $topico_regra['grflux']){
											$regras .= "<b>gr:</b> {$dado['Grflux']['descricao']} ";
											if(!empty($topico_regra['sbflux'])){
												foreach($dado['Sbflux'] as $sbflux){
													if($sbflux['codigo'] == $topico_regra['sbflux']){
														$regras .= "<b>sb:</b> {$sbflux['descricao']} ";
													}
												}
											}
										}
									}
								}
								echo $regras;
							?>
						</span>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="acoes">
					<?php echo $html->link('', array('action' => 'editar', $topico['DreTopico']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
	                <?php echo $this->Html->link('', array('action' => 'excluir', $topico['DreTopico']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), "Deseja realmentttttte excluir esse tópico?") ?>
                </div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php echo $this->Buonny->link_css('dre'); ?>
<?php echo $this->Buonny->link_js('jqueryui/jquery-ui-1.10.1.custom.min', false); ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){ 
		jQuery('#topicos').sortable({
			cursor: 'move',
			handle: '.ui-icon',
			update : function(event, ui) {
				var codigo, posicao_atual, posicao_nova;
				var topico = jQuery(ui.item);
				codigo = topico.find('#codigo').val();
				posicao_atual = topico.find('#ordenacao').val();
		
				posicao_nova = topico.next().find('#ordenacao').val();
				if(posicao_nova === undefined || posicao_atual < posicao_nova){
					posicao_nova = topico.prev().find('#ordenacao').val();
				}
		
				jQuery.get('/portal/dre_topicos/atualizar_ordenacao/'+codigo+'/'+posicao_nova, function(){
					window.location.reload(false);
				});
			}
		}); 
	});
"); ?>