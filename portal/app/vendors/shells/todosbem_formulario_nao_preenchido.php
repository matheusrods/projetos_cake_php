<?php
class TodosbemFormularioNaoPreenchidoShell extends Shell {
    var $uses = array('UsuariosQuestionario');
   
	function verifica_formularios_nao_preenchidos() {
        $this->UsuariosQuestionario->verifica_formulario_pendente_de_preenchimento();
	}
}
?>