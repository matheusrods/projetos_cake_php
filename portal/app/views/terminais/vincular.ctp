
<?php echo $this->BForm->create('TTermTerminal', array('type' => 'POST','url' => array('controller' => 'Terminais','action' => 'vincular',$this->data['TVeicVeiculo']['veic_placa'])));?>

<?php echo $this->BForm->hidden('TVeicVeiculo.veic_oras_codigo') ?>
<div id="form-pai" class='row-fluid inline'>
	<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('label' => 'Placa','readonly' => TRUE,'class' => 'input-small')) ?>
</div>

<div id="form-pai" class='row-fluid inline'>
	<?php echo $this->BForm->input('TTecnTecnologia.tecn_codigo', array('label' => 'Tecnologia', 'empty' => 'Selecione uma Tecnologia','class' => 'tecnologia input-large', 'options' => $tecnologias)) ?>
	<?php echo $this->BForm->input('term_numero_terminal', array('label' => '* Numero de Série', 'class' => 'input-medium','type' => 'text','maxlength' => 29)) ?>
</div>

<h4>Perifericos</h4>
<div id="form-pai" class='row-fluid'>
	<?php $index = 0 ?>
	<?php foreach($perifericos as $ppad_codigo => $ppad_descricao): ?>
		<?php $checked = checa_checked($ppad_codigo,$this->data['TPpinPerifericoPadraoInstal']) ?>
		<?php echo $this->BForm->input("TPpinPerifericoPadraoInstal.{$index}.TPpadPerifericoPadrao.ppad_codigo", array('label' => $ppad_descricao, 'value' => $ppad_codigo, 'type' => 'checkbox', 'checked' => $checked)) ?>
		<?php ++$index ?>
	<?php endforeach; ?>
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar',array('controller' => 'Veiculos', 'action' => 'adicionar_veiculo'), array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php 
	function checa_checked($valor,$lista){

		if(count($lista) > 0 ){
			foreach ($lista as $item) {
				if($item['TPpadPerifericoPadrao']['ppad_codigo'] == $valor){
					return TRUE;
				}
			}
		}

		return FALSE;
	}
?>