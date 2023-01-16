<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<?php if(!empty($pedidos_exames_baixa)):?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Número Pedido</th>
            <th>Cliente</th>
            <th>Funcionário</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pedidos_exames_baixa as $pedido): ?>
        <tr>
            <td><?php echo $pedido['PedidoExame']['codigo'] ?></td>
            <td><?php echo $pedido['Cliente']['razao_social'] ?></td>
            <td><?php echo $pedido['Funcionario']['nome'] ?></td>
            <td>

                <?php

                    /* 
                    Checa as seguintes opções:

                        1 = Pendente de Baixa
                        2 = Baixado Parcialmente
                        3 = Baixado Total
                    */

                    // Define padrões icones
                    $iconStatus = array( 1 => array( "title" => "Pendente de Baixa", "icon" => "badge-important" ),
                                         2 => array( "title" => "Baixado Parcialmente", "icon" => "badge-warning" ),
                                         3 => array( "title" => "Baixado Total", "icon" => "badge-success" ),
                                         4 => array( "title" => "PENDENTE AGENDAMENTO (PRÉ CADASTRO)", "icon" => "" ),
                                         5 => array( "title" => "CANCELADO", "icon" => "" ),
                                         6 => array( "title" => "Concluído Parcialmente", "icon" => "badge-warning" ),
                                     );
                    // Codigo Status
                    $stPedidoCodigo = $pedido['StatusPedidoExame']['codigo'];                    
                    // Escreve Icon Status
                    echo '<span class="badge-empty badge '. $iconStatus[$stPedidoCodigo]['icon'] .'" title="'. $iconStatus[$stPedidoCodigo]['title'] .'"></span>';

                    // Regras por codigo do status para definir IMAGEM SINAL 
                    switch( $stPedidoCodigo ){

                        case 1 :  // Pendente de Baixa
                            echo $this->Html->link('',  array(  'action' => 'baixa', $pedido['PedidoExame']['codigo']), 
                                                        array(  'class' => 'icon-download-alt', 
                                                                'title' => 'Baixa de Pedido', 
                                                                'style' => 'margin-left: 5px;'));                                 
                            break;
                        case 2 : // Baixado Parcialmente
                            
                            // Botão Baixar
                            echo $this->Html->link('',  array(  'action' => 'baixa', $pedido['PedidoExame']['codigo']), 
                                                        array(  'class' => 'icon-download-alt', 
                                                                'title' => 'Baixa de Pedido', 
                                                                'style' => 'margin-left: 5px;')); 

                        case 3 : // Baixado Total
                        
                            // Botão Editar
                            echo $this->Html->link('',  array(  'action' => 'baixa', $pedido['PedidoExame']['codigo'],'1'), 
                                                        array(  'class' => 'icon-edit', 
                                                                'title' => 'Editar Baixa de Pedido', 
                                                                'style' => 'margin-left: 5px;'));   

                            
                            // Botão Reverter
                            // Se a inclusão tiver sido HOJE
                            if( $pedido['PedidoExame']['allow_revert'] == 'yes' || $revert == '1'){
                                echo $this->Html->link('', 'javascript:void(0)',
                                                            array(  'class' => 'cus-arrow-undo', 
                                                                    'escape' => false, 
                                                                    'title'=>'Reverte Baixa',
                                                                    'onclick' => "reverte_baixa('{$pedido['PedidoExame']['codigo']}')"));    
                            }
                            
                            
                            break;
                    }       
                ?>               

			</td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class="modal fade" id="modal_reverte" data-backdrop="static"></div>
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	  <?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Js->writeBuffer(); ?>
<?php 
echo $this->Javascript->codeBlock('
 
 jQuery(document).ready(function() {
     $(".modal").css("z-index", "-1");
 });

 function reverte_baixa(codigo){
    
    var div = jQuery("div#modal_reverte");
    bloquearDiv(div);
    div.load(baseUrl + "itens_pedidos_exames_baixa/modal_reverte_baixa/" + codigo + "/" + Math.random());

    div.css("z-index", "1050");
    div.modal("show");
    
}

   function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "itens_pedidos_exames_baixa/listagem/" + Math.random());
    }

    function gerarMensagem(css, mens){
        $("div.message.container").css({"opacity": "1", "display": "block" });
        $("div.message.container").html("<div class=\"alert alert-"+css+"\"><p>"+mens+"</p></div>");
        fecharMsg();
    }

    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem("success",mensagem);
                break;
            case 2:
                gerarMensagem("success",mensagem);
                break;
            default:
                gerarMensagem("error",mensagem);
                break;
        }    
    }

    function fecharMsg(){
        setInterval(
            function(){
                $("div.message.container").css({ "opacity": "0", "display": "none" });
            },
            4000
        );     
    }
');
?>
