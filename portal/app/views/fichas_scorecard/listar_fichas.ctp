<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
    $usuario_sistema = $this->viewVars['authUsuario']['Usuario']['codigo'];
?>
<div class="row-fluid">
    <span class="span4">
        <strong>Total:</strong> 
        <?php echo $this->Paginator->params['paging']['FichaScorecard']['count']; ?>
    </span>
    <span class="span4">
        <strong>No Prazo:</strong>
        <?php echo $totaldentroprazo ?>
    </span>
    <span class="span4" style="color:red";>
        <strong>Fora do Prazo:</strong>
        <?php echo $totalforaprazo; ?>
    </span>
</div>

<table class="table table-striped" style='table-layout:fixed'>
    <thead>    
        <tr>
            <th class='input-medium'>Responsável</th>
            <th class='input-small'>CPF</th>
            <th class='input-medium'>Data do Cadastro</th>
            <th class='input-medium'>Categoria</th>
            <th class='input-small' style="text-align:center" >Cliente Vip</th>
            <th class='input-small'>Status</th>
            <th width="80">Tempo para conclusão <br>(minutos)</th>
            <th width="16">&nbsp;</th>  
            <th width="16">&nbsp;</th>
            <th width="16">&nbsp;</th>
        </tr>
    </thead>
        <tbody>
    <?php foreach($listar as $key => $lista): ?>
        <?php if(empty($lista[0]['tempo_sla'])){
            $lista[0]['tempo_sla'] = 'Cliente com o tempo de SLA não configurado.';
            $minutos_restantes = 'Tempo de SLA não configurado.';
            }else{
                $minutos_restantes = round((strtotime('+'.$lista[0]['tempo_sla'].' minutes', strtotime(AppModel::dateTimeToDbDateTime($lista[0]['data_inclusao']))) - strtotime('now')) / 60);  
            }?>                
        <tr>
            <td><?php echo empty($lista[0]['nome_responsavel']) ? '-' : $lista[0]['nome_responsavel']; ?></td>
            <td><?php echo substr($buonny->documento($lista[0]['codigo_documento']), 0, 7); ?>...</td>
            <td><?php echo substr($lista[0]['data_inclusao'], 0,16); ?></td>
            <td><?php echo $lista[0]['profissional_descricao']; ?></td>
            <td style="text-align:center"><?php echo (isset($lista[0]['cliente_vip']) && $lista[0]['cliente_vip']==0)? "Não" :"Sim"; ?></td>                    
            <td style="width:90px"> 
                <?php echo FichaScorecardStatus::descricao($lista[0]['codigo_status']); ?></td>
            <td <?php echo $minutos_restantes < 0 ? "style='color:red;text-align:right;'" : "style='text-align:right;'"; ?>><?php echo $minutos_restantes; ?></td>
            <td style="width:50px; border:0px solid black; padding:0px Important; margin:0px Important;">
                <?php if( $tem_permissao_liberacao=="S" ) : ?>
                    <?php if( $action == FichaScorecardStatus::A_PESQUISAR && !empty($lista[0]['codigo_usuario_em_pesquisa']) ) :?>
                    <?php echo $html->link('', array('controller' => 'fichas_scorecard', 'action' => 'liberar_ficha', $lista[0]['codigo_ficha'],'pesquisa'), array('class' => 'icon-lock', 'title' => 'Liberar Fichas'));?>
                    <?php endif;?>
                    <?php if( $action == FichaScorecardStatus::A_APROVAR && !empty($lista[0]['codigo_usuario_em_aprovacao'])) :?>
                    <?php echo $html->link('', array('controller' => 'fichas_scorecard', 'action' => 'liberar_ficha', $lista[0]['codigo_ficha'],'aprovacao'), array('class' => 'icon-lock', 'title' => 'Liberar Fichas'));?>
                    <?php endif;?>
                <?php endif;?>
            </td>
            <td>
                <?php if( $tem_permissao_visualizacao=='S' ) : ?> 
                    <?php if( $action == FichaScorecardStatus::A_PESQUISAR ) :?>
                        <?php  echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'resultado_ficha', $lista[0]['codigo_ficha']), array('class' => 'icon-search', 'title' => 'Visualizar Fichas')); ?>
                    <?php endif;?>
                <?php endif;?>
            </td>
            <td>
                <?php if( $action == FichaScorecardStatus::A_PESQUISAR ) : ?>
                    <?php if( empty($lista[0]['codigo_usuario_em_pesquisa']) || ($lista[0]['codigo_usuario_em_pesquisa']===$usuario_sistema) ):?>
                        <?//php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'editar', $lista[0]['codigo_ficha']), array('class' => 'icon-briefcase', 'title' => 'Pesquisar'));?>
                        <?php echo $html->link('', 'javascript:void(0)', array( 'onclick' => "analisa_ficha( 3, {$lista[0]['codigo_ficha']} )" ,'class' => 'icon-briefcase'));?>
                    <?php endif;?>
                <?php endif;?>

                <?php if( $action == FichaScorecardStatus::A_APROVAR ) : ?>
                    <?php if( empty($lista[0]['codigo_usuario_em_aprovacao']) || ($lista[0]['codigo_usuario_em_aprovacao']===$usuario_sistema) ):?>
                        <?//php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'aprovar', $lista[0]['codigo_ficha']), array('class' => 'icon-briefcase', 'title' => 'Aprovar')); ?>
                        <?php echo $html->link('', 'javascript:void(0)', array( 'onclick' => "analisa_ficha( 5, {$lista[0]['codigo_ficha']} )" ,'class' => 'icon-briefcase'));?>
                    <?php endif;?>
                <?php endif;?>
             </td>            
        </tr>            
    <?php endforeach; ?>
    </tbody>    
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
function analisa_ficha(act, codigo ){
    if( act == 5 ){
        url= baseUrl + "fichas_status_criterios/aprovar/"+codigo;
    } else {
        url= baseUrl + "fichas_status_criterios/editar/"+codigo;
    }
    var janela = window_sizes();
    window.open(url, "_blank", "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
}', false);?>