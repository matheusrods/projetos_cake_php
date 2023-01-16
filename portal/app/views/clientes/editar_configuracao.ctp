<ul class="nav nav-tabs">
  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
  <li><a href="#historico-valorpadrao" data-toggle="tab">Histórico Valor Padrão</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="gerais">
        <?php echo $this->BForm->create('Cliente', array('action' => 'editar_configuracao', $codigo_cliente)); ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->hidden('codigo'); ?>
            <?php echo $this->BForm->hidden('TPjurPessoaJuridica.pjur_pess_oras_codigo'); ?>
            <?php echo $this->BForm->input('codigo_do_cliente',array('value'=>$this->data['Cliente']['codigo'],'label' => 'Código', 'readonly' => TRUE, 'class' => 'input-small')); ?>
            <?php echo $this->BForm->input('razao_social',array('label' => 'Razão Social', 'readonly' => TRUE, 'class' => 'input-xxlarge')); ?>
            <?php echo $this->BForm->input('codigo_documento',array('label' => 'CNPJ', 'readonly' => TRUE)); ?>
            <?php echo $this->BForm->input('codigo_gestor_npe', array('label' => 'Gestor NPE','class' => 'input-medium', 'options' => $gestores_npe, 'empty' => 'Selecione')); ?>
        </div>
        <h4>Dados que serão inseridos por padrão na SM</h4>
        <div class='configuracoes-clientes' style='overflow:auto'></div>

        <h4>Regras Aceite de SM</h4>
        <div class='regras-aceite-sm' style='overflow:auto'></div>

        <h4>Tecnologias</h4>
        <div class='actionbar-right'>
            <?php echo $this->Html->link('Incluir', array('controller' => 'clientes_tecnologias_sm','action' => 'incluir', $this->data['TPjurPessoaJuridica']['pjur_pess_oras_codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Tecnologia", 560)', 'title' => 'Adicionar Tecnologia', 'class' => 'btn btn-success'));?>
        </div>

        <div class="tecnologias-cliente"></div>
        
        <h4>Gerenciadoras</h4>
        <div class='row-fluid inline'>
            <?php echo $this->element('/clientes/ver_gerenciadoras'); ?>
        </div>        
        <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?= $html->link('Voltar', array('action' => 'clientes_configuracoes'), array('class' => 'btn')); ?>
        </div>    
        <?php echo $this->BForm->end(); ?>
    </div>
    
    
    <div class="tab-pane" id="historico-valorpadrao">
        <br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Usuário Adicionou</th>
                    <th>Data Adicionou</th>
                    <th>Usuário Alterou</th>
                    <th>Data Alterou</th>
                    <th>Monitorar retorno</th>
                    <th>Rotograma</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($dados_historico_vppj)): ?>
                <?php foreach($dados_historico_vppj as $dado): ?>
                    <tr>
                        <td><?php echo substr(AppModel::DbDateToDate($dado[0]['_date_']),0,19) ?></td>
                        <td><?php echo $dado[0]['vppj_usuario_adicionou'] ?></td>
                        <td><?php echo substr(AppModel::DbDateToDate($dado[0]['vppj_data_cadastro']),0,19) ?></td>
                        <td><?php echo $dado[0]['vppj_usuario_alterou'] ?></td>
                        <td><?php echo substr(AppModel::DbDateToDate($dado[0]['vppj_data_alteracao']),0,19) ?></td>
                        <td><?php echo ($dado[0]['vppj_monitorar_retorno'] == 0) ? 'Não' : 'Sim'; ?></td>
                        <td><?= ($dado[0]['vppj_rota_sm']) ? 'Sim' : 'Não'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
            <tr><td colspan='7'>Não há registro(s) para exibição</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>
<?php echo $this->Javascript->codeBlock('
    $(function() {
        atualizaListaConfiguracaoTecnologia('.$this->data["TPjurPessoaJuridica"]["pjur_pess_oras_codigo"].');
        atualizaListaRegrasAceiteSm('.$this->data["TPjurPessoaJuridica"]["pjur_pess_oras_codigo"].');
        atualizaListaConfiguracoesCliente('.$this->data["Cliente"]["codigo"].');
        setup_time();
        setup_mascaras();
        setup_datepicker();
        $(".checkbox-checklist").change(function(){
            if($(this).is(":checked")){
                $(".checklist").show();
            }else{
                $(".checklist").hide();
            }
        });

        $(".checkbox-checklist").change();
    });
');
?>

<?php echo $this->Javascript->codeBlock("

    function valida_campo_nivel() {
        var nivel1 = $('#TVppjValorPadraoPjurVppjTempoRetencao1');
        var nivel2 = $('#TVppjValorPadraoPjurVppjTempoRetencao2');
        var nivel3 = $('#TVppjValorPadraoPjurVppjTempoRetencao3');


        if(nivel1.val() != '') {
            nivel2.prop('readonly', false);
        }else {
            nivel2.val('');
            nivel2.prop('readonly', true);
            nivel3.val('');
            nivel3.prop('readonly', true);
        }

        if(nivel2.val() != '') {
            nivel3.prop('readonly', false);
        }else {
            nivel3.val('');
            nivel3.prop('readonly', true);
        }
    }


    jQuery(document).ready(function(){
        valida_campo_nivel();

        $('#TVppjValorPadraoPjurVppjTempoRetencao1').change(function(){
           valida_campo_nivel();
        });

         $('#TVppjValorPadraoPjurVppjTempoRetencao2').change(function(){
            valida_campo_nivel();
        });

        $('#TVppjValorPadraoPjurVppjTempoRetencao3').change(function(){
            valida_campo_nivel();
        });
    });

    function atualizaListaConfiguracoesCliente(codigo_cliente){
        var div = jQuery('.configuracoes-clientes');
        bloquearDiv(div);
        div.load(baseUrl + 'clientes/listar_configuracoes_clientes/' + codigo_cliente + '/' + Math.random());
    }

");
?>

