<div class="well">
    <div id="filtros">
        <?php echo $bajax->form('MotivoRecusaExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MotivoRecusaExame', 'element_name' => 'motivos_recusa_exame'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('motivos_recusa/exames_fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        atualizaMotivoRecusaExame();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MotivoRecusaExame/element_name:motivos_recusa_exame/" + Math.random())
        });
        function atualizaMotivoRecusaExame() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "motivos_recusa/exames_listagem/" + Math.random());
        }

    });
</script>
