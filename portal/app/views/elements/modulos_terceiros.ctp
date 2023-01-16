<div class="navbar navbar-fixed-top">
  <div class="navbar-inner<?php echo (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO ? ' enviroment-colorx' : '').(isset($dbprod) && $dbprod ? ' dbprod-xcolor' : '') ?>">
    <div class="container">
      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <?php echo $this->Html->link(
      				$this->Html->image('logo-ithealth-small.png', 
      						array('alt' => 'logo do IT Health', 'title' => 'logo do IT Health')
      				),
      				array('controller'=>'usuarios','action'=>'inicio'), 
      				array('escape' => false, 'class' => 'brand logo', 'title'=>'IT Health')
      			 ); 
      ?>
      <?php echo $this->element('menu_usuario') ?>
      <div class="nav-collapse">
        <ul class="nav">
			<?php $this->Session->read('modulo_selecionado') ?>
			<?php $menu_modulos = ''; ?>
			<?php $menu_modulos .= $this->BMenu->link('Sistema',array('controller'=>'painel','action'=>'modulo_admin'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::ADMIN?'active':'') , 'title' =>'Modulo Sistema' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Financeiro',array('controller'=>'painel','action'=>'modulo_financeiro'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::FINANCEIRO?'active':'') , 'title' =>'Modulo Financeiro' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Covid',array('controller'=>'painel','action'=>'modulo_covid'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::COVID?'active':'') , 'title' =>'Modulo Covid' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Comercial',array('controller'=>'painel','action'=>'modulo_comercial'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::COMERCIAL?'active':'') , 'title' =>'Modulo Comercial' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Contas Médicas',array('controller'=>'painel','action'=>'modulo_contas_medicas'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::CONTASMEDICAS?'active':'') , 'title' =>'Modulo Contas Médicas' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Credenciamento',array('controller'=>'painel','action'=>'modulo_credenciamento'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::CREDENCIAMENTO?'active':'') , 'title' =>'Modulo Credenciamento' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Gestão Doc.',array('controller'=>'painel','action'=>'modulo_gestao_documentos'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::GESTAODOCUMENTOS?'active':'') , 'title' =>'Modulo Gestão Documentos' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Saúde',array('controller'=>'painel','action'=>'modulo_saude'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::SAUDE?'active':'') , 'title' =>'Modulo Saude' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Segurança',array('controller'=>'painel','action'=>'modulo_seguranca'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::SEGURANCA?'active':'') , 'title' =>'Módulo Segurança' )); ?>
			<?php $menu_modulos .= $this->BMenu->link('Mapeamento Risco',array('controller'=>'painel','action'=>'modulo_mapeamento_risco'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::MAPEAMENTORISCO?'active':'') , 'title' =>'Módulo Mapeamento Risco' )); ?>

			<?php $menu_modulos .= $this->BMenu->link('eSocial',array('controller'=>'painel','action'=>'modulo_e_social'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::ESOCIAL?'active':'') , 'title' =>'Módulo eSocial' )); ?>

            <?php
            //Configuração de menus POS
            if (!empty($authUsuario['Usuario']['codigo_empresa'])) {
                App::import('Controller', 'Clientes');

                $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
//                echo 'cliente';
//                pr($authUsuario['Usuario']);

                if ($authUsuario['Usuario']['codigo_uperfil'] == 1 || $authUsuario['Usuario']['admin'] == 1 && !empty($codigo_cliente)) {

                    $assinaturas = ClientesController::getClienteAssinatura($codigo_cliente);

                    if (!empty($assinaturas)) {

                        if (in_array("PLANO_DE_ACAO", $assinaturas)) {
                            $menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
                        }

                       if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
                           $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));
                       }

                       if (in_array("OBSERVADOR_EHS", $assinaturas)) {
                           $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));
                       }
                    }
                } elseif($authUsuario['Usuario']['codigo_uperfil'] != 1 || $authUsuario['Usuario']['admin'] != 1 && !empty($codigo_cliente)) {

                	if(!empty($codigo_cliente)) {
	                    $assinaturas = ClientesController::getClienteAssinatura($codigo_cliente);

	                    if (!empty($assinaturas)) {

	                        if (in_array("PLANO_DE_ACAO", $assinaturas)) {
	                            $menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
	                        }

	                        if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
	                           $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));
	                        }

	                        if (in_array("OBSERVADOR_EHS", $assinaturas)) {
	                           $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));
	                        }
	                    }
					}
					else {

						if (!empty($assinaturas)) {
							if (in_array("PLANO_DE_ACAO", $assinaturas)) {
								$menu_modulos .= $this->BMenu->link('Plano de Ação',array('controller'=>'painel','action'=>'modulo_plano_de_acao'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::PLANO_DE_ACAO?'active':'') , 'title' =>'Módulo Plano de Ação' ));
							}
	
							if (in_array("SAFETY_WALK_TALK", $assinaturas)) {
							   $menu_modulos .= $this->BMenu->link('Walk & Talk',array('controller'=>'painel','action'=>'modulo_walk_talk'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::WALK_TALK?'active':'') , 'title' =>'Módulo Walk & Talk' ));
							}
	
							if (in_array("OBSERVADOR_EHS", $assinaturas)) {
							   $menu_modulos .= $this->BMenu->link('Obs',array('controller'=>'painel','action'=>'modulo_observador_ehs'), array('wrapper'=>'li', 'wrapper_class' => ($modulo_selecionado==Modulo::OBSERVADOR_EHS?'active':'') , 'title' =>'Módulo Observador EHS' ));
							}
						}						
					}
                }
            }
            ?>

            <?php echo $menu_modulos;?>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
</div>
<div class="subnav subnav-fixed">
  <div class="container">  		
		<?php if ($modulo_selecionado==Modulo::FINANCEIRO): ?>
			<?php echo $this->element('menu_terceiros_financeiro'); ?>
		<?php elseif ($modulo_selecionado==Modulo::COMERCIAL): ?>
			<?php echo $this->element('menu_terceiros_comercial'); ?>
		<?php elseif ($modulo_selecionado==Modulo::CREDENCIAMENTO): ?>
			<?php echo $this->element('menu_credenciado'); ?>
		<?php elseif ($modulo_selecionado==Modulo::SAUDE): ?>
			<?php echo $this->element('menu_terceiros_saude'); ?>
		<?php elseif ($modulo_selecionado==Modulo::SEGURANCA): ?>
			<?php echo $this->element('menu_terceiros_seguranca'); ?>
		<?php elseif ($modulo_selecionado==Modulo::MAPEAMENTORISCO): ?>
			<?php echo $this->element('menu_mapeamento_risco'); ?>
		<?php elseif ($modulo_selecionado==Modulo::ESOCIAL): ?>
			<?php echo $this->element('menu_e_social'); ?>
		<?php elseif ($modulo_selecionado==Modulo::COVID): ?>
			<?php echo $this->element('menu_covid'); ?>
		<?php elseif ($modulo_selecionado==Modulo::GESTAODOCUMENTOS): ?>
			<?php echo $this->element('menu_gestao_documentos'); ?>
		<?php elseif ($modulo_selecionado==Modulo::PLANO_DE_ACAO): ?>
			<?php echo $this->element('menu_plano_de_acao'); ?>
		<?php elseif ($modulo_selecionado==Modulo::WALK_TALK): ?>
			<?php echo $this->element('menu_walk_talk'); ?>
		<?php elseif ($modulo_selecionado==Modulo::OBSERVADOR_EHS): ?>
			<?php echo $this->element('menu_observador_ehs'); ?>
		<?php elseif ($modulo_selecionado==Modulo::CONTASMEDICAS): ?>
			<?php echo $this->element('menu_contas_medicas'); ?>

		<?php endif;?>
	<span style="position: absolute; float: right; right: 40px" class="pull-right margin-top-12 ajuda"><a href="https://rhhealth1.freshdesk.com/support/solutions/32000023075" target="_blank">Help</a></span>
	</div>
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
