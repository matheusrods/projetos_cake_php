<a style="display:block;float:right;" href="#" onclick="imprimir_checklist()" title="Imprimir">
	<i class="icon-print icon-black"></i>
</a>
<div class="page-title">
	<h3>Visualizar Checklist</h3>
		<?php if(isset($cvei_codigo)):?>
			<?php 
				echo $html->link('', array('action' => 'enviar_email', $this->data['Cliente']['codigo'],$this->data['TVeicVeiculo']['veic_placa'],$cvei_codigo), array('class' => 'icon-envelope', 'style'=>'display:block;float:right;padding-left:10px;','title'=>'Enviar E-mail')) ;?>
		  <?php endif?>
	</h3>
</div>  
<?php echo $this->element('veiculos/checklist_dados',array('voltar' => array('controller' => 'Veiculos','action' => 'checklist',$this->data['TVeicVeiculo']['veic_placa']))) ?>
<?php echo $this->BForm->create('TCveiChecklistVeiculo', array('type' => 'POST','url' => array('controller' => 'Veiculos','action' => 'atualizar_checklist',$this->data['TCveiChecklistVeiculo']['cvei_codigo'],$this->data['Cliente']['codigo'],$this->data['TVeicVeiculo']['veic_placa'])));?>		
	<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_codigo') ?>
	<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_pess_oras_codigo') ?>
	<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_veic_oras_codigo') ?>
	<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_status') ?>
	<?php if( $perifericos ):?>
		<?php echo $this->element('veiculos/checklist_perifericos') ?>	
	<?php endif;?>
<div class="form-actions">
	<?php if(isset($tipo_pesquisa) && ($tipo_pesquisa == 'VeiculoSinteticoChecklist')) : ?>
	<?php echo $html->link('Voltar', array('action' => 'checklist',$this->data['TVeicVeiculo']['veic_placa'],0,$codigo_cliente,0), array('class' => 'btn')) ;?>
	<? else:?>
	<?php echo $html->link('Voltar', array('action' => 'checklist',$this->data['TVeicVeiculo']['veic_placa']), array('class' => 'btn')) ;?>
	<? endif;?>
</div>
<?php echo $this->Javascript->codeBlock("
	function imprimir_checklist() {		
		window.print();
		return false;
	}	
", false);
?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_mascaras();	   
	});', 
false);
?>