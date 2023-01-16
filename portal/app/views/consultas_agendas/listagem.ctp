<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();


    //ANEXOS
    $com_anexo_aso = "";
    if($filtros['com_anexo_aso'] == 'S') {
        $com_anexo_aso = "S";
    }

    $com_anexo_ficha = "";
    if($filtros['com_anexo_ficha_clinica'] == 'S') {
        $com_anexo_ficha = "S";
    }
    

?>

<?php if(!empty($agenda)):?>
<div class='well'>
    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
</div>
<table class="table table-striped" style='width:1800px;max-width:none;'>
    <thead>
        <tr>
            <th style="width:15px;">Anexos</th>
            <th style="width:35px;">Número Pedido</th>
            <th class="input-mini">Usuário Responsável</th>
            <th class="input-medium">Nome Fantasia</th>
            <th class="input-medium">Funcionário</th>
            <th class="input-large">Prestador</th>
            <th class="input-mini">Tipo de exame</th>
            <th class="input-mini">Exame</th>
            <th class="input-mini">Data Emissão</th>
            <th class="input-mini">Data Agendamento</th>
            <th class="input-mini">Status</th>
            <th class="input-mini">Data de Realização</th>
            <th class="input-mini">Data Baixa</th>
            <th class="input-mini">Responsável pela Baixa</th>
            <th class="input-mini">Tipo Agendamento</th>
            <th class="input-mini">Exame Enviado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agenda as $dados): ?>
            <tr>
            
                <?php 
                //seta as variaveis como vazias
                $arquivo_exame = "";
                $arquivo_ficha = "";

                // Ajuste feito na pc-2707 bloquear imagens
                // if(!$bloqueia_anexo) {

                    if(strstr($dados['AnexoExame']['caminho_arquivo'],'https://api.rhhealth.com.br')) {
                        $arquivo_exame = $dados['AnexoExame']['caminho_arquivo'];
                    }
                    else if(strstr($dados['AnexoExame']['caminho_arquivo'],'http://api.rhhealth.com.br')) {
                        $arquivo_exame = $dados['AnexoExame']['caminho_arquivo'];
                    }
                    else {
                        //verifica se os status esta diferente de 2 (inserido pelo perfil fornecedor e em moderação)
                        // if($dados[0]['ae_status'] <> '2') {
                            //seta o caminho do arquivo para apresentar
                            $arquivo_exame = end(glob(DIR_ANEXOS.$dados['ItemPedidoExame']['codigo'].DS.'anexo_item_exame_'.$dados['ItemPedidoExame']['codigo'].'.*'));

                            if(!empty($arquivo_exame)){//verifica se existe arquivo na raiz do projeto
                                $arquivo_exame = '/files/anexos_exames/'.$dados['ItemPedidoExame']['codigo'].'/'.basename($arquivo_exame);
                            }

                        // }//fim verificacao do status como 2
                    }
                    //verifica se o status esta diferente de 2 (inserido pelo perfil fornecedor e em moderação)
                    // if($dados[0]['afc_status'] <> '2') {
                        //seta o caminho do arquivo para apresentar
                        $arquivo_ficha = end(glob(DIR_ANEXOS.$dados['ItemPedidoExame']['codigo'].DS.'anexo_ficha_clinica_'.$dados['ItemPedidoExame']['codigo'].'.*')); 
                    // }
                    
                    if(!empty($dados['AnexoExame']['codigo']) && $com_anexo_aso == 'S') {
                        if(empty($arquivo_exame)) {
                            continue;
                        }
                    }

                    if(!empty($dados['AnexoFichaClinica']['codigo']) && $com_anexo_ficha == 'S') {                    
                        if(empty($arquivo_ficha)) {                       
                            continue;
                        }
                    }
                // }//fim if anexo


                $icon = "icon-file";
                $title = "Visualizar anexo do Exame";
                $disabled = "";
                // if($dados[0]['ae_status'] == 2) {
                //     $icon = "icon-file-gray";
                //     $title = "Anexo em Moderação";
                //     $disabled = "true";
                // }

                $iconFC = "icon-file";
                $titleFC = "Anexo de Exame";
                $disabledFC = "";
                // if($dados[0]['afc_status'] == 2) {
                //     $iconFC = "icon-file-gray";
                //     $titleFC = "Anexo em Moderação";
                //     $disabledFC = "true";
                // }

                $visualiza_anexo = false;//nao pode visualizar padrao
                $visualiza_ficha_clinica = false;//nao pode visualizar padrao

                if($dados['Exame']['codigo'] == $codigo_pcd && $visualiza_av_pcd == true){// se o exame for pcd e se o perfil do usuario tiver dentro dos perfis permitidos ele pode visualizar
                    $visualiza_anexo = true;
                } 

                if($_SESSION['Auth']['Usuario']['codigo_uperfil'] == 13 || $_SESSION['Auth']['Usuario']['codigo_uperfil'] == 14 || $_SESSION['Auth']['Usuario']['codigo_uperfil'] == 45 && $dados['Exame']['codigo'] == $codigo_aso){//se o perfil for RH cliente e se for exame aso o usuario pode visualizar o anexo
                    $visualiza_anexo = true;
                } else if(in_array($usuario['Usuario']['codigo_uperfil'],$permissoes_acoes['anexo_exame'][0])) {
                    $visualiza_anexo = true;
                }

                if(in_array($usuario['Usuario']['codigo_uperfil'],$permissoes_acoes['anexo_ficha'][0])) {// se o codigo do perfil estiver dentro dos perfis que podem visualiza a ficha clinica ele libera
                    $visualiza_ficha_clinica = true;
                }

                ?>


                <td style="text-align:center;">
                    <?php if( $visualiza_anexo == true): ?>                       
                        <?php if(!empty($arquivo_exame)): ?>
							<?php if($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5) && $dados['AnexoExame']['aprovado_auditoria'] != null) || ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2 && $dados['AuditoriaExame']['libera_anexo_exame']==1)):?>
                                <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo')), $arquivo_exame, array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo do Exame')) ?>
                            <?php elseif((!empty($dados['AnexoExame']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == null || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 1 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6) && $dados['AnexoExame']['aprovado_auditoria'] == null))): ?>
								<a><i class="icon-file waiting"  title="Aguardando Auditoria de Imagem"></i></a>
                            <?php elseif((!empty($dados['AnexoExame']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2)): ?>
                                <a><i class="icon-file danger"  title="<?php echo $dados['TipoGlosas']['visualizacao_do_cliente']; //old PC-3181 "Imagem reprovada - Aguardando Ajuste" ?>"></i></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if( $visualiza_ficha_clinica == true): ?>

                        <?php if(!empty($arquivo_ficha)): ?> 
                            <?php if($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5) && $dados['AnexoFichaClinica']['aprovado_auditoria'] != null)|| ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2 && $dados['AuditoriaExame']['libera_anexo_ficha']==1)):?>
                                <?php echo $this->Html->link($this->Html->tag('i','',array('class' => 'icon-file btn-anexos visualiza_anexo','style' => 'border: 1px solid; text-decoration: none; border-radius: 100%; background-position: -20px -21px; padding: 3px; background-color: #33CCFF;')), '/files/anexos_exames/'.$dados['ItemPedidoExame']['codigo'].'/'.basename($arquivo_ficha), array('escape' => false, 'target' => '_blank', 'title' => 'Visualizar anexo da Ficha Clínica')) ?>                          
                            <?php elseif((!empty($dados['AnexoFichaClinica']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == null || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 1 || (($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 4 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 5 || $dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6) && $dados['AnexoFichaClinica']['aprovado_auditoria'] == null))): ?>
								<a><i class="icon-file waiting"  title="Ficha clínica aguardando aprovação"></i></a>
                            <?php elseif((!empty($dados['AnexoFichaClinica']['codigo'])) && ($dados['AuditoriaExame']['codigo_status_auditoria_imagem'] == 2)): ?>
								<a><i class="icon-file danger"  title="<?php echo $dados['TipoGlosas']['visualizacao_do_cliente']; //old PC-3181 "Imagem reprovada - Aguardando Ajuste" ?>"></i></a>     
                                
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>                    
                </td>

                <td><?php echo $this->Buonny->modal_pedidos_exames($this, $dados['PedidoExame']['codigo'],  'modal_agendamento');?></td>
                <td><?php echo $dados['PedidoExame']['usuario_resp'] ?></td>
                <td><?php echo $this->Buonny->leiamais($dados['ClienteUnidade']['nome_fantasia'],22) ?></td>
                <td><?php echo $buonny->documento($dados['Funcionario']['nome']) ?></td>
                <td><?php echo $this->Buonny->leiamais($dados['Fornecedor']['razao_social'],25) ?></td>
                <td><?php echo $dados['PedidoExame']['tipo_exame'] ?></td>
                <td><?php echo $dados['Exame']['descricao'] ?></td>
                <td><?php echo $dados['PedidoExame']['data_solicitacao']?></td>
                <td class="input-mini">
                <?php 
                if(empty($dados['AgendamentoExame']['data_inclusao'])) {
                    echo "Ordem de Chegada";
                } else {
                    $data_hora = explode(" ", $dados['PedidoExame']['data_agendamento']);
                    $data_hora[0] = AppModel::dbDateToDate($data_hora[0]);
                    echo $data_hora[0] . " ".substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 2, 2);
                }
                ?>
                </td>

                <td><?php echo $dados[0]['Exames_status'] ?></td>
                <td class="input-mini"><?php echo $dados['ItemPedidoExame']['data_realizacao_exame'] ?></td>
                <td class="input-mini"><?php echo $dados['ItemPedidoExameBaixa']['data_inclusao'] ?></td>
                <td><?php echo $dados['UsuarioBaixa']['apelido'] ?></td>
                <td><?php echo $dados[0]['PedidoExame_tipo_agendamento'] ?></td>
                <td style="text-align:center;">
                    <?php if($dados['ItemPedidoExame']['recebimento_enviado']): ?>
                        <span class="badge badge-empty badge-success" title="Enviado"></span>
                    <?php else: ?>
                        <span class="badge badge-empty badge-important" title="Não Enviado"></span>
                    <?php endif; ?>
                </td>
            </tr>           
        <?php endforeach; ?>
    </tbody>
</table>

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
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
        setup_mascaras(); setup_time(); setup_datepicker();
        $(".modal").css("z-index", "-1");
    });

    function atualizaLista(){
        var div = jQuery(".lista");
        bloquearDiv(div);
        div.load(baseUrl + "consultas_agendas/listagem/" + Math.random());
    }

'); ?>  


<style>
	.waiting {
		border: 1px solid;
		text-decoration: none; 
		border-radius: 100%; 
		background-position: -20px -21px; 
		padding: 3px; 
		background-color: #ffd859;
	}

	.danger {
		border: 1px solid;
		text-decoration: none; 
		border-radius: 100%; 
		background-position: -20px -21px; 
		padding: 3px; 
		background-color: #fc1414;
	}
</style>