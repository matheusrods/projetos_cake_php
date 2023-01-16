<div class='well'>
	<?php echo $bajax->form('MensageriaEsocial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MensageriaEsocial', 'element_name' => 'index_certificado'), 'divupdate' => '.form-procurar')) ?>
    	
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'MensageriaEsocial'); ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('nome_arquivo', array('class' => 'input-xxlarge', 'placeholder' => 'Nome do Arquivo', 'label' => false)) ?>  
            <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        setup_mascaras();

        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "mensageria_esocial/listagem_certificado/" + Math.random());
        }

        atualizaLista();

        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MensageriaEsocial/element_name:index_certificado/" + Math.random())
        });
    });', false);

?>