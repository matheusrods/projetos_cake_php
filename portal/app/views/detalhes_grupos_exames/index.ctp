<?php echo $this->BForm->input('codigo_cliente', array('class' => 'small', 'value' => $dados_cliente['Cliente']['codigo'], 'type' => 'hidden')) ?>
<div class = 'form-procurar'>
	<?= $this->element('/filtros/detalhes_grupos_exames') ?>
</div>
<div class='well'>
    <strong>Código: </strong><?= $dados_cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $dados_cliente['Cliente']['razao_social'] ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'DetalhesGruposExames', 'action' => 'incluir', $dados_grupo_economico_cliente['GruposEconomicosClientes']['codigo_grupo_economico'],$dados_cliente['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Grupo de Exames'));?>
</div>
<div class='lista'></div>
<?php 
    echo $this->Javascript->codeBlock(" 
        setup_mascaras();
    ");
?>