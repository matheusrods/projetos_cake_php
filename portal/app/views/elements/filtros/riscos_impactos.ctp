<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('RiscosImpactos', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RiscosImpactos', 'element_name' => 'riscos_impactos'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php
            
            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RiscosImpactos');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'RiscosImpactos');
                }
            } else {

                if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {

                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RiscosImpactos', $this->data['RiscosImpactos']['codigo_cliente']);

                } else {
                    echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));

                }
            }

            ?>
        </div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>

            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>

            <?php echo $this->BForm->input('codigo_risco_tipo', array('label' => 'Risco tipo (*)','class' => 'input-medium riscos_tipo', 'options'=> $combo_riscos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

            <?php echo $this->BForm->input('codigo_perigo_aspecto', array('label' => 'Perigo/aspecto(*)','class' => 'input-medium perigos_aspectos', 'options'=> $combo_perigos_aspectos, 'empty' => 'Todos', 'default' => ' ')); ?>

            <?php echo $this->BForm->input('codigo_risco_impacto_tipo', array('label' => 'Risco/impacto tipo','class' => 'input-medium', 'options'=> $combo_riscos_impactos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

            <?php echo $this->BForm->input('codigo_metodo_tipo', array('label' => 'Tipos de metodos','class' => 'input-medium', 'options'=> $combo_metodos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

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
        atualizaListaRiscosImpactos();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RiscosImpactos/element_name:riscos_impactos/" + Math.random())
        });
        
        function atualizaListaRiscosImpactos() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos_impactos/listagem/" + Math.random());
        }
           
    });', false);
?>

<script type="text/javascript">
    jQuery('#RiscosImpactosCodigoCliente').change(function() {
        var codigo_cliente = this.value;

        comboPerigosAspectos(codigo_cliente);
    });

    var comboPerigosAspectos = function(codigo_cliente) {
        jQuery('.perigos_aspectos').html('<option value="">Carregando...</option>');

        jQuery.ajax({
            url: baseUrl + 'riscos_impactos/obtem_perigos_aspectos_por_ajax',
            type: 'POST',
            dataType: 'html',
            data: {
                'codigo_cliente': codigo_cliente
            }
        })
            .done(function(response) {
                if (response) {
                    jQuery('.perigos_aspectos').html(response);
                }
            });
    }
</script>
