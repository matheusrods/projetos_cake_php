<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('PerigosAspectos', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PerigosAspectos', 'element_name' => 'perigos_aspectos'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php

            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PerigosAspectos');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'PerigosAspectos');
                }
            } else {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PerigosAspectos');
                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
               
                }               
            }
            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>

            <?php echo $this->BForm->input('codigo_risco_tipo', array('label' => 'Risco tipo (*)','class' => 'input-medium riscos_tipo', 'options'=> $combo_riscos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

            <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => ' ')); ?>

        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaPerigosAspectos();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PerigosAspectos/element_name:perigos_aspectos/" + Math.random())
        });
        
        function atualizaListaPerigosAspectos() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "perigos_aspectos/listagem/" + Math.random());
        }
           
    });', false);
?>

<script type="text/javascript">
    jQuery('#PerigosAspectosCodigoCliente').change(function() {
        var codigo_cliente = this.value;

        comboRiscosTipo(codigo_cliente);
    });

    var comboRiscosTipo = function(codigo_cliente) {
        jQuery('.riscos_tipo').html('<option value="">Carregando...</option>');

        jQuery.ajax({
            url: baseUrl + 'perigos_aspectos/obtem_riscos_tipo_por_ajax',
            type: 'POST',
            dataType: 'html',
            data: {
                'codigo_cliente': codigo_cliente
            }
        })
            .done(function(response) {
                if (response) {
                    jQuery('.riscos_tipo').html(response);
                }
            });
    }
</script>
