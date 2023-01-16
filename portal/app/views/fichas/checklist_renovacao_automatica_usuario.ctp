    <?php echo $this->BForm->create('Ficha', array('url' => array('controller' => 'fichas', 'action' => 'checklist_renovacao_automatica_usuario'))); ?>
    <div class='row-fluid inline'>      
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true, 'Ficha') ?>     
        <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
        <?php echo $this->BForm->input('codigo_produto', array('label' =>'Produto', 'div'=>'control-group input', 'options'=>$produtos, 'empty'=>'Selecione')) ?>
    </div>
    <div class='row-fluid inline motorista-data'>
        <?php echo $this->BForm->hidden('Profissional.codigo') ?>
        <?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => 'Cod Documento', 'class' => 'input-medium formata-rne', 'placeholder' => 'CPF', 'div'=>array('class'=>'control-group input text documento'), 'error'=>array('escape'=>false))) ?>
        <?php echo $this->BForm->input('Profissional.nome', array('label' => 'Profissional', 'class' => 'input-xxlarge','placeholder' => 'Nome', 'readonly'=>true)) ?>
    </div>
    <div class="form-actions">
        <?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    </div>
<?php echo $this->BForm->end(); ?>
<?if( !empty($checklist)) :?>
    <div class="checklist">
        <h5>
            <div class='row-fluid inline'>      
                <span>Cliente Ativo?</span>
                <?=($checklist['cliente_ativo']== 1 ? 'Sim (Dentro da regra)':'<span class="text-error">Não (Fora da regra)</span>')?>
            </div>
            <div class='row-fluid inline'>      
                <span>Cliente Ativo nos últimos 30 dias?</span>
                <?=($checklist['cliente_inativo30_dias']== 0 ? 'Sim (Dentro da regra)':'<span class="text-error">Não (Fora da regra)</span>')?>
            </div>            
            <div class='row-fluid inline'>      
                <span>Cliente possui produto TLC ativo?</span>            
                <?=($checklist['produtos_ativos'] == 1 ? 'Sim (Dentro da regra)':'<span class="text-error">Não (Fora da regra)</span>')?>
            </div>

            <div class='row-fluid inline'>      
                <span>Cliente possui produto Bloqueado nos últimos 30 dias?</span>
                <?php
                    if($checklist['cliente_produto_bloqueio'] == 1 ){
                      echo '<span id="cliente_prod_bloq">Sim (Fora da regra)</span>';
                    } else {
                      echo '<span>Não (Dentro da regra)</span>';
                    }
                ?>
            </div>

            <div class='row-fluid inline'>      
                <span>Ultima ficha do profissional está vinculada ao Cliente?</span>
                <?=(!empty( $checklist['utltima_ficha']['Ficha']['ativo'] ) && $checklist['utltima_ficha']['Ficha']['ativo'] == 1 ? 'Sim (Dentro da regra)':'<span class="text-error">Não (Fora da regra)</span>')?>
            </div>

            <div class='row-fluid inline'>      
                <span>Cliente possui serviço de renovação automática?</span>
                <?=($checklist['possui_renovacao_auto'] == 1 ? 'Sim (Dentro da regra)':'<span class="text-error">Não (Fora da regra)</span>')?>
            </div>
            <div class='row-fluid inline'>      
                <span>Data inclusão do serviço de renovação automática: </span>
                <?php
                    if(!empty($checklist['data_inclusao_renovacao_auto']['ClienteProdutoServico2']['data_inclusao'])){
                      echo '<span id="cliente_prod_bloq">'.$checklist['data_inclusao_renovacao_auto']['ClienteProdutoServico2']['data_inclusao'].'</span>';
                    }
                ?>
            </div>
            <div class='row-fluid inline'>      
                <span>A ficha atual está vencida?</span>
                <?=($checklist['ficha_atual_vencida']== 1 ? '<span class="text-error">Sim (Fora da regra)</span>':'Não (Dentro da regra)')?>
            </div>

            <div class='row-fluid inline'>      
                <span>Existe ficha com vencimento posterior?</span>
                <?=( !empty($checklist['vencimento_posterior']) ? '<span class="text-error">Sim (Fora da regra)</span>':'Não (Dentro da regra)')?>
            </div>

            <div class='row-fluid inline'>      
                <span>
                    <u><?php echo ($dados_renovacao_automatica['Usuario']['apelido']);?></u> 
                    incluiu o profissional para a Renovação Automática em</span>
                    <?=($dados_renovacao_automatica['RenovacaoAutomatica']['data_inclusao']);?>
            </div>           
            <div class='row-fluid inline'>      
                <span>Vencimento da ficha atual: </span>
                <?=($checklist['utltima_ficha']['Ficha']['data_validade']);?>
            </div>   
            <div>
                <?= '<span id="log"><a href="#"><img alt="" title="Logs Cliente Produto" src="/portal/img/icon-log.png" ></a></span>'; ?>
            </div>     


        </h5>
    <div>
    <br />
<?endif;?>

<div id="dialog-confirm" title="Logs Cliente Produto" style="display:none">
    <h5>Cliente: <?=$cliente['Cliente']['razao_social'] ?></h5>    
    <div>
        <ul class="nav nav-tabs">
          <li class="active"><a href="#log_dados_gerais" data-toggle="tab">Dados Gerais</a></li>
          <li><a href="#log_enderecos" data-toggle="tab">Endereço</a></li>
          <li><a href="#log_contatos" data-toggle="tab">Contato</a></li>
          <li><a href="#log_produtos" data-toggle="tab" class="validation-error">Produto</a></li>
          <li><a href="#log_produtos_servicos" data-toggle="tab">Produtos e Serviços</a></li>
        </ul>
        <div class="tab-content">
            <div class="lista tab-pane active" id="log_dados_gerais">
            </div>
            <div class="lista_endereco tab-pane" id="log_enderecos">
            </div>
            <div class="lista_contato tab-pane" id="log_contatos">
            </div>
            <div class="lista_produto tab-pane" id="log_produtos">
            </div>
            <div class="lista_produto_servico tab-pane" id="log_produtos_servicos">
            </div>
        </div>
    </div>    
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){            
            atualizaListaClientesLog(); 
            atualizaListaEnderecoClientesLog();
            atualizaListaContatoClientesLog();
            atualizaListaProdutoClientesLog();
            atualizaListaProdutoServicosClientesLog();

        $('#log').click(function(){
            $( '#dialog-confirm' ).dialog({
                resizable: false,
                height:800,
                width:900
            });      
            return false;        
        });

        $('#FichaCodigoCliente').blur(function(){
            var codigo_cliente = $('#FichaCodigoCliente').val();
            if (codigo_cliente != '') {
                $.ajax({
                    url:baseUrl + 'embarcadores_transportadores/listar_por_cliente/' + codigo_cliente + '/' + Math.random(),
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#ClienteRazaoSocial').val(data.razao_social);
                        }
                    }
                });
            }
        });
                

        $('#FichaCodigoCliente').blur(function(){
            var codigo_cliente = $('#FichaCodigoCliente').val();
            if (codigo_cliente != '') {                
                buscar_cliente_produto(  codigo_cliente, $('#FichaCodigoProduto') );
            }
        });

    });", false);?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){        
        $('#ProfissionalCodigoDocumento').blur(function(){
            var cpf = $('#ProfissionalCodigoDocumento').val();
            cpf = cpf.replace(/[^\d]+/g,''); 
            if (cpf != '') {
                $.ajax({
                    url:baseUrl + 'solicitacoes_monitoramento/busca_dados_motorista/' + cpf + '/' + Math.random(),
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#ProfissionalNome').val(data.nome);
                            $('#ProfissionalCodigo').val(data.codigo);
                        }
                    }
                })
            }
        });
    });", false);?>








