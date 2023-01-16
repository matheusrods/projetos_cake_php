<?php $aba_categorizacao = $this->Buonny->class_invalidated(array('Cliente' => array('codigo_corretora'))) ?>
<?php $aba_atendimento = $this->Buonny->class_invalidated(array('Cliente' => array('codigo_endereco_regiao', 'codigo_gestor'))) ?>
<?php $permite_atualizar_logotipo = (bool)($upload['permite_atualizar_logotipo']);  ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>

    <?php if ($edit_mode) : ?><li><a href="#outros-enderecos" data-toggle="tab">Outros Endereços</a></li><?php endif; ?>

    <?php if (!empty($referencia)) : ?><?php if ($referencia != 'implantacao_terceiros') : ?><li><a class="<?= $aba_atendimento ?>" href="#atendimento" data-toggle="tab">Atendimento</a></li><?php endif; ?><?php else : ?><li><a class="<?= $aba_atendimento ?>" href="#atendimento" data-toggle="tab">Atendimento</a></li><?php endif; ?>

<?php if ($edit_mode) : ?><li><a href="#contatos" data-toggle="tab">Contatos</a></li><?php endif; ?>

<?php if (!empty($referencia)) : ?><?php if ($referencia != 'implantacao_terceiros') : ?><li><a href="#faturamento" data-toggle="tab">Faturamento</a></li><?php endif; ?><?php else : ?><li><a href="#faturamento" data-toggle="tab">Faturamento</a></li><?php endif; ?>

<?php if ($edit_mode) : ?><li><a href="#historico" data-toggle="tab">Histórico</a></li><?php endif; ?>

<?php if ($edit_mode && $permite_atualizar_logotipo) : ?><li><a href="#imagens" data-toggle="tab">Logo</a></li><?php endif; ?>

<?php if ($edit_mode && $eh_matriz) : ?><li><a href="#fontes-autenticacao" data-toggle="tab">Fontes Autenticação</a></li><?php endif; ?>
</ul>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
    <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')); ?>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="gerais">
        <?php echo $this->element('clientes/dados_gerais', array('edit_mode' => $edit_mode)) ?>
    </div>
    <?php if ($edit_mode) : ?>
        <div class="tab-pane" id="outros-enderecos">
            <?php echo $this->element('clientes/outros_enderecos') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($referencia)) : ?>
        <?php if ($referencia != 'implantacao_terceiros') : ?>
            <div class="tab-pane" id="atendimento">
                <?php echo $this->element('clientes/atendimento') ?>
            </div>
        <?php else : ?>
            <?php echo $this->Form->hidden('codigo_medico_pcmso', array('value' => $codigo_medico_pcmso)); ?>
        <?php endif; ?>
    <?php else : ?>
        <div class="tab-pane" id="atendimento">
            <?php echo $this->element('clientes/atendimento') ?>
        </div>
    <?php endif; ?>

    <?php if ($edit_mode) : ?>
        <div class="tab-pane" id="contatos">
            <?php echo $this->element('clientes/contatos') ?>
        </div>
    <?php endif; ?>
    <?php if ($referencia != 'implantacao_terceiros') : ?>
        <div class="tab-pane" id="faturamento">
            <?php echo $this->element('clientes/faturamento', array('edit_mode' => $edit_mode)) ?>
        </div>
    <?php endif; ?>
    <?php if ($edit_mode) : ?>
        <div class="tab-pane" id="historico">
            <?php echo $this->element('clientes/historico') ?>
        </div>
    <?php endif; ?>
    <?php if ($edit_mode && $permite_atualizar_logotipo) : ?>
        <div class="tab-pane" id="imagens">
            <?php echo $this->element('clientes/imagens') ?>
        </div>
    <?php endif; ?>
    <?php if ($edit_mode) : ?>
        <div class="tab-pane" id="fontes-autenticacao">
            <?php echo $this->element('clientes/fontes_autenticacao') ?>
        </div>
    <?php endif; ?>
</div>


<?php if (!empty($inclusao_cliente) && !empty($dados_matriz)) : ?>
    <div class="modal fade" id="modal_editar_cliente" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
        <div class="modal-dialog modal-sm" style="position: static;">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center;">
                    <h3>NOVA UNIDADE:</h3>
                </div>
                <div class="modal-body">

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
    <div>
        <?php //echo $this->BForm->submit('Salvar', array('href' => 'javascript:void(0);', 'onclick' => 'enviaDados();', 'id' => 'button_submit', 'div' => false, 'class' => 'btn btn-primary')); 
        ?>
        <span id="div_salvar">
            <a href="javascript:void(0);" onclick="enviaDados();" class="btn btn-primary" id="button_submit"><i class="glyphicon glyphicon-share"></i> Salvar</a>
        </span>

        <?php if (!empty($referencia)) : ?>
            <?php if ($referencia == 'implantacao_terceiros') : ?>
                <?php echo $html->link('Voltar', array('action' => 'listagem_terceiros_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
            <?php else : ?>
                <?php if ($terceiros_implantacao == 'terceiros_implantacao') : ?>
                    <?php echo $html->link('Voltar', array('action' => 'index_unidades', $codigo_matriz, $referencia, 'null', $terceiros_implantacao), array('class' => 'btn')); ?>
                <?php else : ?>
                    <?php echo $html->link('Voltar', array('action' => 'index_unidades', $codigo_matriz, $referencia), array('class' => 'btn')); ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else : ?>
            <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
        <?php endif; ?>
    </div>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>