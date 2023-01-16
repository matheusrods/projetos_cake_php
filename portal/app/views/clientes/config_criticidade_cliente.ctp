<div class="form-procurar">
    <?= $this->element('/filtros/config_criticidade_cliente') ?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes', 'action' => 'incluir_config_criticidade', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar nova configuração de criticidade')); ?>
</div>
<div class='lista'></div>

<style>
    h3 {
        text-decoration: none;
    }
</style>
