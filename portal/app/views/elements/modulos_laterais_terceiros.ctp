<nav class="scoop-navbar">

    <div class="scoop-inner-navbar">

        <ul class="scoop-item scoop-right-item">

            <div class="scoop-navigatio-lavel">Módulos</div>

            <?php

                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_admin'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/sistema/modulo_sistema');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_financeiro'))) {
                   echo $this->element('menu_lateral/modulos_terceiros/financeiro/modulo_financeiro');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_covid'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/covid/modulo_covid');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_comercial'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/comercial/modulo_comercial');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_contas_medicas'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/contas_medicas/modulo_contas_medicas');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_credenciamento'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/credenciamento/modulo_credenciamento');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_gestao_documentos'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/gestao_doc/modulo_gestao_doc');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_saude'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/saude/modulo_saude');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_seguranca'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/seguranca/modulo_seguranca');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_mapeamento_risco'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/mapeamento_risco/modulo_mapeamento_risco');
                }
                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_e_social'))) {
                    echo $this->element('menu_lateral/modulos_terceiros/e_social/modulo_e_social');
                }


            //Configuração de menus POS
            if (!empty($authUsuario['Usuario']['codigo_empresa'])) {
                App::import('Controller', 'Clientes');


                $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];

                if ($authUsuario['Usuario']['codigo_uperfil'] == 1 || $authUsuario['Usuario']['codigo_uperfil'] == 50 && $authUsuario['Usuario']['admin'] == 1) {
                    if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_plano_de_acao'))) {
                        echo $this->element('menu_lateral/modulos_terceiros/plano_de_acao/modulo_plano_de_acao');
                    }

                    if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_walk_talk'))) {
                        echo $this->element('menu_lateral/modulos_terceiros/safety_walk_talk/modulo_safety_walk_talk');
                    }

                    if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_observador_ehs'))) {
                        echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/modulo_observador_ehs');
                    }
                } elseif($authUsuario['Usuario']['codigo_uperfil'] != 1 || $authUsuario['Usuario']['admin'] != 1 && !empty($codigo_cliente)) {

                    if(!empty($codigo_cliente)) {
                        $assinaturas = ClientesController::getClienteAssinatura($codigo_cliente);

                        if (!empty($assinaturas)) {

                            if (in_array("PLANO_DE_ACAO", $assinaturas)) {
                                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_plano_de_acao'))) {
                                    echo $this->element('menu_lateral/modulos_terceiros/plano_de_acao/modulo_plano_de_acao');
                                }
                            }

                            if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
                                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_walk_talk'))) {
                                    echo $this->element('menu_lateral/modulos_terceiros/safety_walk_talk/modulo_safety_walk_talk');
                                }
                            }

                            if (in_array("OBSERVADOR_EHS", $assinaturas)) {
                                if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_observador_ehs'))) {
                                    echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/modulo_observador_ehs');
                                }
                            }
                        }
                    }
                    else {
                        if (in_array("PLANO_DE_ACAO", $assinaturas)) {
                            if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_plano_de_acao'))) {
                                echo $this->element('menu_lateral/modulos_terceiros/plano_de_acao/modulo_plano_de_acao');
                            }
                        }

                        if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
                            if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_walk_talk'))) {
                                echo $this->element('menu_lateral/modulos_terceiros/safety_walk_talk/modulo_safety_walk_talk');
                            }
                        }

                        if (in_array("OBSERVADOR_EHS", $assinaturas)) {
                            if ($this->BMenu->permiteMenu(array('controller'=>'painel','action'=>'modulo_observador_ehs'))) {
                                echo $this->element('menu_lateral/modulos_terceiros/observador_ehs/modulo_observador_ehs');
                            }
                        }
                    }
                }
            }
            ?>


            <!-- Menus de usuário-->
            <div class="scoop-navigatio-lavel" style="margin-top: 20px;">Configurações</div>

            <ul class="scoop-item scoop-right-item" item-border="true" item-border-style="solid" subitem-border="true">

                <?php if($authUsuario['Usuario']['codigo_cliente']) : ?>
                    <li class="">
                        <a href="/portal/usuarios_multi_cliente/selecionar_cliente">
                            <span class="scoop-micon"><i class="fas fa-user"></i></span>
                            <span class="scoop-mtext">Acessar Cliente</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="">
                    <a href="/portal/usuarios/minhas_configuracoes">
                        <span class="scoop-micon"><i class="fas fa-user"></i></span>
                        <span class="scoop-mtext">Minhas Configurações</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>

                <?php if (!empty($authUsuario['Usuario']['codigo_cliente'])): ?>
                    <li class="">
                        <a href="/portal/usuarios/trocar_senha">
                            <span class="scoop-micon"><i class="fas fa-user"></i></span>
                            <span class="scoop-mtext">Trocar Senha</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>

                    <?php if (isset($authUsuario['Usuario']['admin']) && $authUsuario['Usuario']['admin'] == 1): ?>

                        <li class="">
                            <a href="/portal/usuarios/index/minha_configuracao">
                                <span class="scoop-micon"><i class="fas fa-user"></i></span>
                                <span class="scoop-mtext">Gerenciar Usuarios</span>
                                <span class="scoop-mcaret"></span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/portal/uperfis/index">
                                <span class="scoop-micon"><i class="fas fa-user"></i></span>
                                <span class="scoop-mtext">Gerenciar Perfis</span>
                                <span class="scoop-mcaret"></span>
                            </a>
                        </li>
                    <?php endif ?>
                <?php endif ?>

                <li class="">
                    <a href="/portal/processamentos/index">
                        <span class="scoop-micon"><i class="fas fa-user"></i></span>
                        <span class="scoop-mtext">Processamentos</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>

                <li class="">
                    <a href="/portal/usuarios/logout">
                        <span class="scoop-micon"><i class="fas fa-power-off"></i></span>
                        <span class="scoop-mtext">Sair</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>
            </ul>
        </ul>
    </div>
            <?php
    if(isset($mensagem['MensagemDeAcesso']['mensagem'])):
		if(isset($_SERVER['HTTPS'])):
			$msg = str_replace('http', 'https',$mensagem['MensagemDeAcesso']['mensagem']);
		else:
			$msg = str_replace('https', 'http',$mensagem['MensagemDeAcesso']['mensagem']);
		endif;
		   $msg = str_replace("\r","",str_replace("\n","",str_replace("'", "\'", $msg)));
		   echo $this->Javascript->codeBlock("
				$(document).ready(function() { 
					$('#lnkAjuda').animate({'font-size': '3.5em'},1000).animate({'font-size':'13px'},500);
					$('div.page-title').append('<div style=\"margin: 50px auto\">{$msg}</div>');
				});
		  ");
	?>
<?php endif ?>
</nav>
