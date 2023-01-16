<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 style="text-decoration: none;">Exportação de Dados</h3>
</div>
    
<div id="modal_export_base_dados" class="modal-body"> 
    
    <div class='well'>
        <p><strong>Cliente: </strong></p>
        <strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?> </strong> - <?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
    </div>

    <div class="control-group input select">
        <label for="unidades">Unidades</label>
        <select name="data[unidades]" class="input-xlarge" id="unidades">
            <option value="0">Todas as unidades</option>
            <?php
                foreach ($unidades as $key => $unidade) {                    
                    ?>
                        <option value="<?= $key ?>"><?= $unidade ?></option>
                    <?php
                }                
            ?>               
        </select>
    </div>

    <div class='well'>
        <p><strong>Status: </strong></p>

        <div class="checkbox_content row-fluid inline" style="display: inline-flex;">
            <div class="control-group input clear checkbox">
                <input type="hidden" name="data[ativo]" id="ativo_" value="0">
                <input type="checkbox" name="data[ativo]" value="1" checked="checked" class="input-large" id="ativo">
                <label for="ativo">Ativo</label>
            </div>            
            
            <div class="control-group input clear checkbox">
                <input type="hidden" name="data[inativo]" id="inativo_" value="0">
                <input type="checkbox" name="data[inativo]" value="0" class="input-large" id="inativo">
                <label for="inativo">Inativo</label>
            </div>                

            <div class="control-group input clear checkbox">
                <input type="hidden" name="data[ferias]" id="ferias_" value="0">
                <input type="checkbox" name="data[ferias]" value="2" checked="checked" class="input-large" id="ferias">
                <label for="ferias">Ferias</label>
            </div>              
            
            <div class="control-group input clear checkbox">
                <input type="hidden" name="data[afastado]" id="afastado_" value="0">
                <input type="checkbox" name="data[afastado]" value="3" checked="checked" class="input-large" id="afastado">
                <label for="afastado">Afastado</label>
            </div>        
        </div>
    </div>

   <div class='row-fluid inline pull-right'>
    <?php echo $this->element('clientes_implantacao/emails_campos') ?>
    </div>
</div>
    
<div class="modal-footer">
    <div id="fechar_modal" href="#" class="btn btn-danger" data-dismiss="modal">Fechar</div>
    <div id="exportar_base_de_dados" href="#" class="btn btn-success">Exportar</div>
    <div id="export_base_por_email" href="javascript:void(0);" class="btn btn-warning">Enviar base de dados por e-mail</div>
</div>

<?php echo $this->Buonny->link_js('estrutura'); ?>