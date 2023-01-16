<?php $aba_categorizacao = $this->Buonny->class_invalidated(array('Cliente' => array('codigo_corretora'))) ?>
<?php $aba_atendimento = $this->Buonny->class_invalidated(array('Cliente' => array('codigo_endereco_regiao', 'codigo_gestor'))) ?>
<ul class="nav nav-tabs">
  <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
</ul>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
    <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')); ?>
</div>
<div class="tab-content">    
	<div class="tab-pane active" id="gerais">
        <?php echo $this->element('clientes/dados_gerais_tomador', array('edit_mode' => $edit_mode)) ?>
	</div>
</div>


<?php if (!empty($inclusao_cliente) && !empty($dados_matriz)): ?>
    <div class="modal fade" id="modal_editar_cliente" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
        <div class="modal-dialog modal-sm" style="position: static;">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center;">
                    <h3>NOVA UNIDADE:</h3>
                </div>
                <div class="modal-body" >

                    <p>Olá, <b><?php echo $dados_matriz['Cliente']['nome_fantasia']; ?></b>, tudo bem?</p> <br>
                    <p>Notamos que você incluiu uma nova unidade em sua estrutura.</p> <br>
                    <p>O próximo passo é agendar uma vistoria para a elaboração do PGR e do PCMSO. Estou avisando porque é uma marca registrada da RH Health sempre antecipar essas situações e manter o máximo de transparência possível na nossa relação.</p><br>
                    <p>Em breve, a nossa CS vai entrar em contato para falar mais sobre isso, tudo bem?</p><br>
                    <p>Até lá, claro, se tiver qualquer dúvida, não pense duas vezes antes de nos procurar:</p>
                    <p>E-MAIL: <a href="mailto:relacionamento@rhhealth.com.br">relacionamento@rhhealth.com.br</a></p>
                    <p>TELEFONE: (11) 5079-2550</p><br>
                    <p>Obrigado pela atenção!</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="right">
                    <a href="javascript:void(0);" onclick="mostra_modal(0);" class="btn btn-danger">FECHAR</a>
                </div>
            </div>                                      
        </div>
    </div>
    <div>
        <label>
            
        </label>
    </div>

    <?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function() {
       mostra_modal = function(mostra){
            if(mostra == 1){
                $('#modal_editar_cliente').css('z-index', '1050');
                $('#modal_editar_cliente').modal('show');
            }
            else{
                $('#modal_editar_cliente').css('z-index', '-1');
                $('#modal_editar_cliente').modal('hide');
            }
        }

        mostra_modal(1); 

    });
    "); ?>  

<?php endif; ?>

<div class="form-actions">
    <?php //echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <span id="div_salvar">
        <a href="javascript:void(0);" onclick="enviaDados();" class="btn btn-primary" id="button_submit"><i class="glyphicon glyphicon-share"></i> Salvar</a>
    </span>
    <?php echo $html->link('Voltar', array('action' => 'listagem_tomador_servico', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
</div>    
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>