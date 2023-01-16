<?php $filtrado = (isset($this->data['Proposta']) ? true : false)?>
<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('Proposta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Proposta', 'element_name' => 'propostas'), 'divupdate' => '.form-procurar')) ?>
        <div class='row-fluid inline'>    
            <?php echo $this->BForm->input('numero_proposta', array('class' => 'input-small just-number','label' =>'Num. Proposta')); ?>
            <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge','label' =>'Razão Social')); ?>

            <?php echo $this->BForm->input("tipo_cliente", array('class'=>'input-medium','options' => $tipo_cliente,'label' => 'Tipo Cliente', 'empty'=>'Todos')) ?>
            <?php echo $this->BForm->input('cpf_cnpj', array('class' => 'input-medium','label' => 'CPF / CNPJ','div'=>Array('id'=>'divPropostaCPFCNPJ','style'=>'display:none;'))); ?>

            <?php echo $this->Buonny->input_periodo($this,'Proposta','data_inclusao_inicial','data_inclusao_final',true,null,'Dt. Proposta') ?>
        </div>

        <div class='row-fluid inline'>

            <?php echo $this->Buonny->input_periodo($this,'Proposta','data_validade_inicial','data_validade_final',true,null,'Validade') ?>
            
            <?php echo $this->BForm->input("codigo_usuario_gestor", array('class'=>'input-large','options' => $gestores,'label' => 'Gestor', 'empty'=>'Todos')) ?>

            <?php echo $this->BForm->input("apenas_versao_corrente", array('class'=>'input-small','options' => $arraySimNao,'label' => 'Apenas versão corrente')) ?>


        <?php if (count($status_proposta)>1): ?>
            </div>
            <div class='row-fluid inline' id="checkboxes_status">
                <span class="label label-info">Status</span>
                <span class='pull-right'>
                    <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes_status")')) ?>
                    <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes_status")')) ?>
                </span>
                <?php echo $this->BForm->input('codigo_status_proposta', array('label'=>false, 'options'=>$status_proposta, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-medium status' )); 
                ?>
            </div>

        <?else: ?>
                <?php echo $this->BForm->input("codigo_status_proposta", array('class'=>'input-medium','options' => $status_proposta,'label' => 'Status')) ?>
            </div>
        <?endif; ?>

        <div class='row-fluid inline'>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    function selecionaTipoCliente(tipo_cliente) {
        var div = $("#divPropostaCPFCNPJ");
        var label = div.find("label");

        if(tipo_cliente=="'.Documento::PESSOA_FISICA.'") {
            div.show();
            label.html("CPF");
            $("#PropostaCpfCnpj").addClass("cpf");
            $("#PropostaCpfCnpj").removeClass("cnpj");
            $("#PropostaCpfCnpj").removeClass("format-cnpj");
        } else if(tipo_cliente=="'.Documento::PESSOA_JURIDICA.'") {
            div.show();
            label.html("CNPJ");
            $("#PropostaCpfCnpj").removeClass("cpf");
            $("#PropostaCpfCnpj").removeClass("format-cpf");
            $("#PropostaCpfCnpj").addClass("cnpj");
        } else {
            div.hide();
            label.html("CPF / CNPJ");
            $("#PropostaCpfCnpj").removeClass("cpf");
            $("#PropostaCpfCnpj").removeClass("format-cpf");
            $("#PropostaCpfCnpj").removeClass("cnpj");
            $("#PropostaCpfCnpj").removeClass("format-cnpj");
        }
        setup_mascaras();
    }

    $(document).ready(function(){
        setup_mascaras();   
        selecionaTipoCliente($("#PropostaTipoCliente").val());
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "propostas/listagem/" + Math.random());':'').'

        $("#PropostaTipoCliente").change(function() {
            selecionaTipoCliente(this.value);
        });

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Proposta/element_name:propostas/" + Math.random())
           
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });   
    
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>