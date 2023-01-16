<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaViagemEscoltas(".$this->data['TViagViagem']['viag_codigo'].");");
        exit;
    }
?>
<?php echo $this->BForm->hidden('TViagViagem.viag_codigo') ?>
    <?php echo $this->BForm->hidden('TViagViagem.viag_codigo_sm') ?>
    <div class='row-fluid inline'>
        <table>
            <tr>
                <td>
                    <?php $id = isset($this->data['TPjurEscolta']['pjur_pess_oras_codigo'])?$this->data['TPjurEscolta']['pjur_pess_oras_codigo']:NULL ?>
                    <input type="hidden" value="<?php echo $id ?>" class="complete-id" name="data[TPjurEscolta][pjur_pess_oras_codigo]" />
                    <?php echo $this->BForm->input('TPjurEscolta.pjur_razao_social', array('label' => 'Empresa', 'class' => 'input-xxlarge escolta-complete')) ?>
                </td>
            </tr>
        </table>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('TVescViagemEscolta.vesc_equipe', array('label' => 'Equipe', 'class' => 'input-medium')) ?>
        <?php echo $this->BForm->input('TVescViagemEscolta.vesc_telefone', array('label' => 'Telefone', 'class' => 'input-medium telefone')) ?>
        <?php echo $this->BForm->input('TVescViagemEscolta.vesc_placa', array('label' => 'Placa', 'class' => 'input-medium placa-veiculo')) ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('TTecnTecnologia.tecn_codigo', array('label' => 'Tecnologia', 'options' => $tecnologias, 'empty' => 'Tecnologia')) ?>
        <?php echo $this->BForm->input('TVescViagemEscolta.vesc_vtec_codigo', array('label' => 'Versão', 'empty' => 'Versão da Tecnologia', 'options' => $versoes)) ?>
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('TVescViagemEscolta.vesc_numero_terminal', array('label' => 'Número Terminal')) ?>
    </div>
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
          <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
    </div>
<?php
if(empty($versoes)){
    echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){
            if($("#TTecnTecnologiaTecnCodigo").val() != ""){
                buscar_t_versao("#TTecnTecnologiaTecnCodigo", "#TVescViagemEscoltaVescVtecCodigo");
            }else{
                $("#TVescViagemEscoltaVescVtecCodigo").html("<option value=\"\">Versão da Tecnologia</option>");
            }
        });', false);
} 
echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        autocomplete_escolta("Escolta");
        
        $(".ui-autocomplete").css({"z-index":"1000"});

        $("#TTecnTecnologiaTecnCodigo").change(function(){
            if($("#TTecnTecnologiaTecnCodigo").val() != ""){
                buscar_t_versao("#TTecnTecnologiaTecnCodigo", "#TVescViagemEscoltaVescVtecCodigo");
            }else{
                $("#TVescViagemEscoltaVescVtecCodigo").html("<option value=\"\">Versão da Tecnologia</option>");
            }
        });
    });', false);
?>