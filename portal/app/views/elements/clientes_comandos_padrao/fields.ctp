<div class="row-fluid inline">
	<?= $this->BForm->hidden('cpjc_codigo') ?>
	<?= $this->BForm->hidden('cpjc_pjur_pess_oras_codigo') ?>
	<?= $this->BForm->input('cpjc_cpad_codigo', array('label' => 'Comando Padrão','empty' => 'Selecione um comando', 'options' => $comandos_padrao, 'class' => 'input-xxlarge', 'readonly' => (isset($readonly) && $readonly) )); ?>
	<?= $this->BForm->input('cpjc_codigo_omnilink', array('label' => 'Descricao Omnilink', 'class' => 'input-xxlarge', 'options' => $perfis_omnilink, 'readonly' => (isset($readonly) && $readonly))); ?>
</div>