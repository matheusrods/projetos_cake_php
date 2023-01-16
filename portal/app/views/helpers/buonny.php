<?php
class BuonnyHelper extends AppHelper {

	var $helpers = array('Form', 'Number', 'Session', 'Html', 'Javascript', 'Text');

	function moeda($valor, $opcoes = array()) {
        // Se possuir os dois separadores ent�o deixar somente a "," que � o separador de decimal
		if (strpos($valor, '.') && strpos($valor, ',')) {
			$valor = preg_replace('/[^0-9,\-]/', '', $valor);
			if (empty($valor))
				$valor = 0;
		}

		if (isset($opcoes['nozero']) && $opcoes['nozero'] && $valor == 0)
			return '';

		if (!isset($opcoes['format']))
			$opcoes['format'] = false;

		if (isset($opcoes['edit']) && $opcoes['edit']) {
			$valor = abs(str_replace(',', '.', $valor));
			//$opcoes['thousands'] = '.';
			$opcoes['before'] = '';
		}

		$unidadeMonetaria = $opcoes['format'] ? '<span>R$ </span>' : '';

		$valor = $this->Number->format($valor, array_merge(array('thousands' => '.', 'decimals' => ',', 'zero' => '0', 'before' => $unidadeMonetaria, 'escape' => false), $opcoes));

		if (isset($opcoes['format_decimals']) && $opcoes['format_decimals']) {
			$valor .= '</span>';
		}
		return $this->output("$valor");
	}//FINAL FUNCTION moeda


	function flash($key = 'flash'){
		if ($this->Session->check('Message.flash')){
			$msg = $this->Session->read('Message.' . $key);
			$type = isset($msg['params']['type']) ? $msg['params']['type'] : MSGT_NORMAL;
			$text = $this->Html->para(null, $msg['message']);
			$this->Session->delete('Message.' . $key);
			return $this->Html->div($type, $text);
		}
	}//FINAL FUNCTION flash

	function documento($numero) {
		if (strlen($numero) > 11) {
			$formatado = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $numero);
		} else {
			$formatado = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $numero);
		}
		return $formatado;
	}//FINAL FUNCTION documento

	function cep($numero) {
		if(strlen($numero) < 8) {
			$count = 8 - strlen($numero);
			$numero = (String)$numero;
			for ($i=0; $i < $count; $i++) { 
				$numero = '0'.$numero;
			}
		}
		$numero = substr($numero, 0, 5) . '-'. substr($numero, 5, 3);
		return $numero;
	}//FINAL FUNCTION cep

	function combo_booleano($name, $params=null) {
		$dfValues = array('0' => 'NÃO', '1' => 'SIM');
		if (empty($params['class'])) {
			$params['class'] = 'text-small';
		}
		if (empty($params['options'])) {
			$params['options'] = $dfValues;
		}
		return $this->Form->input($name, $params);
	}//FINAL FUNCTION combo_booleano

	function telefone($numero) {
		return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1)$2-$3", $numero);
	}//FINAL FUNCTION telefone

	function link_js($paths, $inline = true) {
		if (!is_array($paths))
			$paths = array($paths);
		$out = '';
		foreach ($paths as $path) {
			$html_script = $this->Javascript->link($path, true); 
			$out .= $this->script_timestamp($html_script, 'src', $inline);
		}
		return $this->trata_link_inline($out, $inline);
	}//FINAL FUNCTION link_js
	
	function link_css($paths, $rel = null, $htmlAttributes = array(), $inline = true) {
		if (!is_array($paths))
			$paths = array($paths);

		$out = '';
		foreach ($paths as $path) {
			$html_script = $this->Html->css($path, $rel = null, $htmlAttributes, true);
			$out .= $this->script_timestamp($html_script, 'href', $inline);
		}
		
		return $this->trata_link_inline($out, $inline);
	}//FINAL FUNCTION link_css
	
	private function script_timestamp($html_script, $attr, $inline) {
		$matches = array();
		preg_match("/{$attr}\=\"([^\ ]*)\"/", $html_script, $matches);
		$url_path = $matches[1];
		$url_path = str_replace('/portal', '', $url_path);
		$file_path = WWW_ROOT . substr(str_replace('/', DS, $url_path),1);
		$url_path_timestamped = $this->auto_version($url_path, $file_path);
		return str_replace($url_path, $url_path_timestamped, $html_script) . "\n";
	}//FINAL FUNCTION script_timestamp
	
	private function trata_link_inline($out, $inline) {
		if ($inline) {
			return $out;
		} else {
			$view =& ClassRegistry::getObject('view');
			$view->addScript($out);
		}
	}//FINAL FUNCTION trata_link_inline
	
	function auto_version($url_path, $file_path){ 
		if (file_exists($file_path)) {
			$mtime = filemtime($file_path);
			return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $url_path); 
		}
		return'';
	}//FINAL FUNCTION auto_version
	
	function class_invalidated($fields) {
		$invalidou = false;
		foreach ($fields as $model => $model_fields) {
			foreach ($model_fields as $field) {
				if (isset($this->validationErrors[$model][$field])) {
					$invalidou = true;
					break;
				}
			}
			if ($invalidou) 
				break;
		}
		return ($invalidou ? 'validation-error' : '');
	}//FINAL FUNCTION class_invalidated

	function mes_extenso($mes) {
		$meses = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
		return $meses[$mes-1];
	}//FINAL FUNCTION mes_extenso
	
	/**
	 * Função para acrescentar valores em um array preservando as keys da mesma
	 * https://stackoverflow.com/questions/3353745/how-to-insert-element-into-arrays-at-specific-position?noredirect=1&lq=1
	 *
	 * @param array $arr
	 * @param array $arr_add
	 * @param integer $line posição na linha
	 * @return void
	 */
	function array_merge_preserve_keys($arr = array(), $arr_add = array(), $line = 0){
		return array_slice($arr, 0, $line, true) +	$arr_add + array_slice($arr, $line, count($arr)-$line, true);
	}

	function input_codigo_cliente(&$view, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		
		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){ 
			return $this->combo_codigo_cliente_multi($view, $input_name = 'codigo_cliente', null, $label = false, $model, $value, true);	
		}

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		} else {
			// implementado verificação para avaliar $codigo_cliente 
			// pois algumas situações retorna array por utilizar normalizaCodigoCliente
			if (!empty($this->data[$model][$input_name])) {
				$codigo = $this->data[$model][$input_name];
				if(is_array($codigo)){
					$value = implode(',', $codigo);
					$options = array_merge($options, array('value' => $value));
				}
			}
		}	
		if ($input_name == 'codigo_cliente') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$lastValue = is_array($lastValue) ? implode(',', $lastValue) : $lastValue;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name_camelized = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').search_clientes();}) ");

		if ($input_name == 'codigo_cliente') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		$out .= $this->Javascript->codeBlock("
		jQuery(document).ready(function(){  
			
			// se estiver usando selecao multicliente
			var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 

			var options = {
				model : '{$model}',
				multicliente : multiclientesAtivo,
				inputs: {
					codigo_cliente : '{$model}CodigoCliente',
					codigo_cliente_alocacao : '{$model}CodigoClienteAlocacao',
					codigo_unidade : '{$model}CodigoUnidade',
					codigo_setor : '{$model}CodigoSetor',
					codigo_cargo : '{$model}CodigoCargo'
				},
				valores:{
					codigo_cliente : '',
					codigo_cliente_alocacao : '',
					codigo_setor : '',
					codigo_cargo : ''									
				}
			};

			// inicializa tratamento das selecoes para
			// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
			form_dinamico.init(options); 

		}); ");
		return $out;
	}//FINAL FUNCTION input_codigo_cliente

	function input_codigo_matriz(&$view, $input_name = 'codigo_matriz', $placeholder = 'PosMetas', $label = false, $force_model = 'PosMetas', $value = null) {
		$model = 'PosMetas';
		$authUsuario = $_SESSION['Auth'];

		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){
			return $this->combo_codigo_cliente_multi($view, $input_name = 'codigo_matriz', null, $label = false, 'PosMetas', $value = null, true);
		}

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		} else {
			// implementado verificação para avaliar $codigo_cliente
			// pois algumas situações retorna array por utilizar normalizaCodigoCliente
			if (!empty($this->data[$model][$input_name])) {
				$codigo = $this->data[$model][$input_name];
				if(is_array($codigo)){
					$value = implode(',', $codigo);
					$options = array_merge($options, array('value' => $value));
				}
			}
		}
		if ($input_name == 'codigo_matriz') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$lastValue = is_array($lastValue) ? implode(',', $lastValue) : $lastValue;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name_camelized = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').search_clientes();}) ");

		if ($input_name == 'codigo_matriz') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		$out .= $this->Javascript->codeBlock("
		jQuery(document).ready(function(){  
			
			// se estiver usando selecao multicliente
			var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 

			var options = {
				model : '{$model}',
				multicliente : multiclientesAtivo,
				inputs: {
					codigo_cliente : '{$model}CodigoCliente',
					codigo_cliente_alocacao : '{$model}CodigoClienteAlocacao',
					codigo_unidade : '{$model}CodigoUnidade',
					codigo_setor : '{$model}CodigoSetor',
					codigo_cargo : '{$model}CodigoCargo'
				},
				valores:{
					codigo_cliente : '',
					codigo_cliente_alocacao : '',
					codigo_setor : '',
					codigo_cargo : ''									
				}
			};

			// inicializa tratamento das selecoes para
			// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
			form_dinamico.init(options); 

		}); ");
		return $out;
	}//FINAL FUNCTION input_codigo_cliente

	function input_codigo_cliente_com_label(&$view, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		
		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){
			return $this->combo_codigo_cliente_multi($view, $input_name = 'codigo_cliente', 
						null, $label = false, $model, $value = null, true);	
		}

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => 'Cliente Matriz (*)', 'placeholder' => $placeholder));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		} else {
			// implementado verificação para avaliar $codigo_cliente 
			// pois algumas situações retorna array por utilizar normalizaCodigoCliente
			if (!empty($this->data[$model][$input_name])) {
				$codigo = $this->data[$model][$input_name];
				if(is_array($codigo)){
					$value = implode(',', $codigo);
					$options = array_merge($options, array('value' => $value));
				}
			}
		}	
		if ($input_name == 'codigo_cliente') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$lastValue = is_array($lastValue) ? implode(',', $lastValue) : $lastValue;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name_camelized = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').search_clientes();}) ");

		if ($input_name == 'codigo_cliente') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		$out .= $this->Javascript->codeBlock("
		jQuery(document).ready(function(){  
			
			// se estiver usando selecao multicliente
			var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 

			var options = {
				model : '{$model}',
				multicliente : multiclientesAtivo,
				inputs: {
					codigo_cliente : '{$model}CodigoCliente',
					codigo_cliente_alocacao : '{$model}CodigoClienteAlocacao',
					codigo_unidade : '{$model}CodigoUnidade',
					codigo_setor : '{$model}CodigoSetor',
					codigo_cargo : '{$model}CodigoCargo'
				},
				valores:{
					codigo_cliente : '',
					codigo_cliente_alocacao : '',
					codigo_setor : '',
					codigo_cargo : ''									
				}
			};

			// inicializa tratamento das selecoes para
			// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
			form_dinamico.init(options); 

		}); ");
		return $out;
	}//FINAL FUNCTION input_codigo_cliente

	function input_codigo_cliente3(&$view, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		
		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){
			return $this->combo_codigo_cliente_multi($view, $input_name = 'codigo_cliente', 
						null, $label = false, $model, $value = null, true);	
		}

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => 'Cliente', 'placeholder' => $placeholder));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		} else {
			// implementado verificação para avaliar $codigo_cliente 
			// pois algumas situações retorna array por utilizar normalizaCodigoCliente
			if (!empty($this->data[$model][$input_name])) {
				$codigo = $this->data[$model][$input_name];
				if(is_array($codigo)){
					$value = implode(',', $codigo);
					$options = array_merge($options, array('value' => $value));
				}
			}
		}	
		if ($input_name == 'codigo_cliente') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$lastValue = is_array($lastValue) ? implode(',', $lastValue) : $lastValue;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name_camelized = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').search_clientes();}) ");

		if ($input_name == 'codigo_cliente') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		$out .= $this->Javascript->codeBlock("
		jQuery(document).ready(function(){  
			
			// se estiver usando selecao multicliente
			var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 

			var options = {
				model : '{$model}',
				multicliente : multiclientesAtivo,
				inputs: {
					codigo_cliente : '{$model}CodigoCliente',
					codigo_cliente_alocacao : '{$model}CodigoClienteAlocacao',
					codigo_unidade : '{$model}CodigoUnidade',
					codigo_setor : '{$model}CodigoSetor',
					codigo_cargo : '{$model}CodigoCargo'
				},
				valores:{
					codigo_cliente : '',
					codigo_cliente_alocacao : '',
					codigo_setor : '',
					codigo_cargo : ''									
				}
			};

			// inicializa tratamento das selecoes para
			// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
			form_dinamico.init(options); 

		}); ");
		return $out;
	}//FINAL FUNCTION input_codigo_cliente

	function combo_codigo_cliente_multi(&$view, $input_name = 'codigo_cliente', $placeholder = 'Multi-Clientes', $label = false, $force_model = null, $value , $fullajax = false) {
			
			$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
			$usuario = $this->Session->read('Auth.Usuario'); // recupera sessao do usuário atual
		
			// inicializar Multi-Clientes
			//$label = true;
			//$placeholder = 'Multi-Clientes';
			$label = (($label != false) ? $label : 'Multi-Clientes');
			$type = 'select';
			$class = ($fullajax) ? 'input-xlarge ajax-multiclientes': 'input-xlarge';
			
			// Valida quem 
			$clientes = array();
			
			$clientes = $usuario['multicliente'];
			// definir valor para Todos
			$clientes_valor_todos = is_array($clientes) ? implode(',', array_keys( $clientes)) : array();
			$clientes_count_todos = 'Todos ('.count($clientes) .')';
			$todos = array( $clientes_valor_todos =>$clientes_count_todos);
			
			$clientes = $this->array_merge_preserve_keys($clientes, $todos);

			// sempre sera [codigo_cliente] da sessão desde que b_request esteja 
			// avaliando o post e trocando na session
			// se valores da option Todos for igual ao codigo_cliente	
			$selected = (implode(',', array_values( $usuario['codigo_cliente']))  == $clientes_valor_todos) ? $clientes_valor_todos : $usuario['codigo_cliente'];
			
			if (isset($value) && !is_null($value)) {
				$selected = $value;
			}

			$options = array(
				'title' => $label, 
				'label' => $label,
				'class' => $class,
				'type' => $type,
				'selected' => $selected,
				'value'=> $value,
				'options'=>$clientes
			);
			
			if ($input_name == 'codigo_cliente') {
				if (empty($this->data[$model][$input_name])) {
					$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
					$view->BForm->data[$model][$input_name] = $lastValue;
					$view->BForm->data['Last'][$input_name] = $lastValue;
				}
			}
	
			$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
			
			$input_name_camelized = Inflector::camelize($input_name);
			
			if (!empty($usuario['codigo_cliente'])){

				if ($input_name == 'codigo_cliente') {
					$out .= $this->Javascript->link('form_dinamico');
					$out .= $this->Javascript->codeBlock("
					jQuery(document).ready(function(){  
						
						// se estiver usando selecao multicliente
						var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 
			
						var options = {
							model : '{$model}',
							multicliente : multiclientesAtivo,
							inputs: {
								codigo_matriz : '{$model}CodigoMatriz',
								codigo_cliente : '{$model}CodigoCliente',
								codigo_unidade : '{$model}CodigoUnidade',
								codigo_setor : '{$model}CodigoSetor',
								codigo_cargo : '{$model}CodigoCargo'
							},
							valores:{
								codigo_cliente : '',
								codigo_cliente_alocacao : '',
								codigo_setor : '',
								codigo_cargo : ''									
							}
						};
			
						// inicializa tratamento das selecoes para
						// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
						form_dinamico.init(options); 
			
					}); ");		
				} 
			}
			return $out;
	}

	function input_codigo_prestador(&$view, $input_name = 'codigo_prestador', $placeholder = 'Prestador', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name = Inflector::camelize($input_name);		
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name}').search_prestadores();}) ");
		return $out;
	}//FINAL FUNCTION input_codigo_prestador

	function input_codigo_fornecedor(&$view, $input_name = 'codigo_fornecedor', $placeholder = 'Prestador', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		
		$input_desc = $input_name.'Codigo';
		$placeholderDescricao = 'Prestador';

		//$authUsuario = $_SESSION['Auth'];
		
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		$options_descricao = array('title' => 'Prestador', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true);
		
		if ($label) { 
			$options = array_merge($options, array('label' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => $placeholderDescricao));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => $placeholderDescricao));

		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		if ($input_name == 'codigo_fornecedor') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_descricao);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$input_name_camelized}').search_fornecedor({$input_name_camelized},{$input_desc_camelized});}) ");

		if ($input_name == 'codigo_fornecedor') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		return $out;
	}//FINAL FUNCTION input_codigo_fornecedor

	/**
	 * [input_codigo_fornecedor2 description]
	 * 
	 * 
	 * helper para colocar o input codigo do fornecedor mas sem o nome do mesmo
	 * 
	 * @param  [type]  &$view       [description]
	 * @param  string  $input_name  [description]
	 * @param  string  $placeholder [description]
	 * @param  boolean $label       [description]
	 * @param  [type]  $force_model [description]
	 * @param  [type]  $value       [description]
	 * @param  [type]  $readonly    [description]
	 * @return [type]               [description]
	 */
	function input_codigo_fornecedor2(&$view, $input_name = 'codigo_fornecedor', $placeholder = 'Fornecedor', $label = false, $force_model = null, $value = null, $readonly = null) 
	{
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		
		$input_desc = $input_name.'Codigo';
		$placeholderDescricao = 'Fornecedor';

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		if($readonly) {
			$options['readonly'] = true;
		}

		// $options_descricao = array('title' => 'Fornecedor', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true);		
		if ($label) { 
			$options = array_merge($options, array('label' => $placeholder));
			// $options_descricao = array_merge($options_descricao, array('label' => $placeholderDescricao));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			// $options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => $placeholderDescricao));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		if ($input_name == 'codigo_fornecedor') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);

		$options_desc = array('type' => 'hidden');
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_desc);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$input_name_camelized}').search_fornecedor({$input_name_camelized},{$input_desc_camelized});}) ");

		if ($input_name == 'codigo_fornecedor') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}

		// debug($out);exit;
		return $out;

	}//FINAL FUNCTION input_codigo_fornecedor2
	
	function input_codigo_cnae(&$view, $input_name = 'cnae', $placeholder = '', $label = 'CNAE', $force_model = 'Cliente', $value = '', $value_descricao = '') {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		$input_desc = $input_name."Descricao";
		$placeholderDescricao = 'Ramo de Atividade';

		//$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		$options_descricao = array('title' => 'Ramo de Atividade', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true, 'label' => 'Ramo de Atividade');

		if ($label) {
			$options = array_merge($options, array('label' => $label));
			$options_descricao = array_merge($options_descricao, array('label' => $placeholderDescricao));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => $placeholderDescricao));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		
		if ($value_descricao) {
			$options_descricao = array_merge($options_descricao, array('value' => $value_descricao));
		}		
		
		if ($input_name == 'cnae') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_descricao);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){
			$('#{$input_name_camelized}').search_cnae({$input_name_camelized},{$input_desc_camelized});
		}) ");

		if ($input_name == 'cnae') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
				$('#{$model}{$input_name_camelized}').blur(function() { 
					$('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) 
				});
			}) ");
		}
		
		return $out;
	}//FINAL FUNCTION input_codigo_cnae	

	function input_codigo_cid (&$view, $input_name = 'codigo_cid10', $placeholder = '', $label = 'CID10', $force_model = 'Cid', $value = '', $value_descricao = '') {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		//$input_desc = $input_name."Descricao";
		$input_desc = "descricao";
		$placeholderDescricao = 'Descrição';

		//$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini', 'type' => 'text');
		$options_descricao = array('title' => 'Descrição', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true, 'label' => 'Descrição');

		if ($label) {
			$options = array_merge($options, array('label' => $label));
			$options_descricao = array_merge($options_descricao, array('label' => $placeholderDescricao));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => $placeholderDescricao));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		
		if ($value_descricao) {
			$options_descricao = array_merge($options_descricao, array('value' => $value_descricao));
		}		
		
		if ($input_name == 'codigo_cid10') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_descricao);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);
		
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){
			$('#{$input_name_camelized}').search_cid({$input_name_camelized},{$input_desc_camelized});
		}) ");

		if ($input_name == 'codigo_cid10') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
				$('#{$model}{$input_name_camelized}').blur(function() { 
					$('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) 
				});
			}) ");
		}
		
		return $out;
	}//FINAL FUNCTION input_codigo_cid	
	
	function input_codigo_credenciado(&$view, $input_name = 'codigo_proposta_credenciamento', $placeholder = 'Credenciado', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		
		$input_desc = $input_name . 'RazaoSocial';
		$placeholderUnidade = 'Razão Social';

		//$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		$options_descricao = array('title' => 'Credenciado', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true);
		
		if ($label) { 
			$options = array_merge($options, array('label' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => $placeholderUnidade));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => $placeholderUnidade));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		if ($input_name == 'codigo_proposta_credenciamento') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_descricao);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$input_name_camelized}').search_credenciado({$input_name_camelized},{$input_desc_camelized});}) ");

		if ($input_name == 'codigo_proposta_credenciamento') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		return $out;
	}//FINAL FUNCTION input_codigo_credenciado	

	function input_codigo_cliente2(&$view, $options, $model=null) {
		
		$authUsuario = $_SESSION['Auth'];

		$default_options = array(
			'model' => key($view->BForm->fieldset),
			'input_name' => 'codigo_cliente',
			'placeholder' => null,
			'label' => false, 
			'title' => 'Cliente',
			'class' => 'input-mini just-number',
			'checklogin' => true,
            'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden'),
		);
		
		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){
			$input_name = (!empty($options['input_name']) ? $options['input_name'] : 'codigo_cliente');
			$label = (!empty($options['label']) ? $options['label'] : false) ;
			return $this->combo_codigo_cliente_multi($view, $input_name, null, $label, $force_model = null, $value = null, true);	
		}

		$options = array_merge($default_options, $options);

		if(!is_null($model)) {
			$options['model'] = $model;
		}
		
		$authUsuario = $_SESSION['Auth'];
		if (!empty($options['name_display']))
			$out = "<div style='float:left'>";
		else
			$out = "";
		$out .= $view->BForm->input($options['model'].'.'.$options['input_name'], $options);
		if (!empty($options['name_display'])) {
			$default_options = array(
				'readonly' => true,
				'class' => 'input-xlarge',
				'label' => $options['label'],
				'placeholder' => $options['placeholder'],
			);
			$options_name_display = array_merge($default_options, $options['name_display']);
			$out .= $view->BForm->input($options['model'].'.'.$options['input_name'].'_name', $options_name_display);
			$input_name = Inflector::camelize($options['input_name']);
			$out .= $this->Javascript->codeBlock("$(document).on('blur', '#{$options['model']}{$input_name}', function() {
				var div_group = $('#{$options['model']}{$input_name}').parent().parent();
				var codigo_cliente = $('#{$options['model']}{$input_name}').val();
				if (codigo_cliente) {
					$.ajax({
						url:baseUrl + 'clientes/buscar/' + codigo_cliente + '/' + Math.random(),
						dataType: 'json',
						beforeSend: function() {
							bloquearDiv(div_group);
						},
						success: function(data) {
							if (data.sucesso) {
								var input_name_display = $('#{$options['model']}{$input_name}Name').val(data.dados.razao_social);
							} else {
								var input_name_display = $('#{$options['model']}{$input_name}Name').val('');
							}
						},
						complete: function() {
							div_group.unblock();
						}
					});
				}
			})");
		}
		if (!$options['checklogin'] || empty($authUsuario['Usuario']['codigo_cliente'])) {
			$input_name = Inflector::camelize($options['input_name']);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$options['model']}{$input_name}').search_clientes();}) ");
		}
		if (!empty($options['name_display']))
			$out .= "</div>";

		return $out;
	}//FINAL FUNCTION input_codigo_cliente2

	function input_codigo_endereco_regiao(&$view, $filiais, $empty = 'Selecione', $input_name = 'codigo_endereco_regiao', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$value = (!empty($authUsuario['Usuario']['codigo_filial']) ? $authUsuario['Usuario']['codigo_filial'] : '');
		$options = array('options' => $filiais, 'class' => 'input-medium', 'empty' => $empty, 'value' => $value, 'type' => (empty($authUsuario['Usuario']['codigo_filial']) ? 'select' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $label));
		} else {
			$options = array_merge($options, array('label' => false));
		}
		$out = '';
		if(empty($authUsuario['Usuario']['codigo_filial'])) {
			$out .= $view->BForm->input($model.'.codigo_endereco_regiao', $options);
		}
		return $out;
	}//FINAL FUNCTION input_codigo_endereco_regiao

	function input_codigo_corretora(&$view, $input_name = 'codigo_corretora', $placeholder = 'Corretora', $label = false, $force_model = null, $value = null, $search = true, $input = 'input-large') {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];		
		$optionsCodigo = array('type' => 'hidden');

		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => $input, 'type' => (empty($authUsuario['Usuario']['codigo_corretora']) ? 'text' : 'hidden') );
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder));
		}
		if ($value) {
			$optionsCodigo = array_merge($optionsCodigo, array('value' => $value));
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_visual, $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.Inflector::camelize($input_visual);
		$input_name = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_corretora'])){
			if($search){
				$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
					$('{$localizador_input_visual}').search_corretoras('".$model.Inflector::camelize($input_name)."','".$model.Inflector::camelize($input_visual)."');
				}) ");
			}
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){
				$('{$localizador_input_visual}').autocomplete_corretoras('{$localizador_input_name}');
				$('{$localizador_input_name}').change(function() {
					validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','corretora');
				});
				$(document).on('keyup', '{$localizador_input_visual}', function(e) {
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						$('{$localizador_input_name}').val('');
						validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','corretora');
					}
				});
			}) ");
		}
		return $out;
	}//FINAL FUNCTION input_codigo_corretora

	function input_embarcador_transportador(&$view, $embarcadores, $transportadores, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null, $display_codigo_cliente = true) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "";
		if($display_codigo_cliente)
			$out .= $this->input_codigo_cliente($view, $input_name, $placeholder, $label, $model, $value);
		if ($label) {
			$out .= $view->BForm->input($model.'.codigo_embarcador', array('label' => 'Embarcador', 'options' => $embarcadores, 'empty' => 'Embarcador'));
			$out .= $view->BForm->input($model.'.codigo_transportador', array('label' => 'Transportador', 'options' => $transportadores, 'empty' => 'Transportador'));
		} else {
			$out .= $view->BForm->input($model.'.codigo_embarcador', array('label' => false, 'placeholder' => 'Embarcador', 'options' => $embarcadores, 'empty' => 'Embarcador', 'class' => 'input-xlarge'));
			$out .= $view->BForm->input($model.'.codigo_transportador', array('label' => false, 'placeholder' => 'Transportador', 'options' => $transportadores, 'empty' => 'Transportador', 'class' => 'input-xlarge'));
		}
		$input_name = Inflector::camelize($input_name);
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function() {
			
			jQuery('#{$model}{$input_name}').blur(function() {
				var value = jQuery('#{$model}{$input_name}').val();
				jQuery('#{$model}CodigoTransportador').html('').append('<option value=\'\'>Transportador</option>').addClass('ui-autocomplete-loading');
				jQuery('#{$model}CodigoEmbarcador').html('').append('<option value=\'\'>Embarcador</option>').addClass('ui-autocomplete-loading');
				if (value != '') {
					jQuery.ajax({
						url:baseUrl + 'embarcadores_transportadores/listar_por_cliente/' + value + '/' + Math.random(),
						dataType: 'json',
						success: function(data) {
							if (data) {
								var clientes;

								if (data.tipo == 'T') {
									jQuery('#{$model}CodigoTransportador').html('').append('<option value=\"'+data.codigo+'\">'+data.razao_social+'</option>');
									clientes = jQuery('#{$model}CodigoEmbarcador');
								} else {
									jQuery('#{$model}CodigoEmbarcador').html('').append('<option value=\"'+data.codigo+'\">'+data.razao_social+'</option>');
									clientes = jQuery('#{$model}CodigoTransportador');
								}
								clientes.html('').append('<option value=\'\'>'+(data.tipo == 'T' ? 'Embarcador' : 'Transportador')+'</option>');
								for (var i = 0; i < data.clientes.length; i++) { 
									clientes.append('<option value=\''+data.clientes[i].codigo+'\'>'+data.clientes[i].razao_social+'</option>');
								}
							}
						},
						complete: function() {
							jQuery('#{$model}CodigoTransportador').removeClass('ui-autocomplete-loading');
							jQuery('#{$model}CodigoEmbarcador').removeClass('ui-autocomplete-loading');
						}
					})
				}
				jQuery('#{$model}CodigoTransportador').removeClass('ui-autocomplete-loading');
				jQuery('#{$model}CodigoEmbarcador').removeClass('ui-autocomplete-loading');
			})
		})");
		return $out;
	}//FINAL FUNCTION input_embarcador_transportador
	
	function input_codigo_cliente_base(&$view, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $multipla_utilizacao = false) {
		$input = $this->input_codigo_cliente($view, $input_name, $placeholder, $label, $force_model);
		if (!isset($_SESSION['Auth']['Usuario']['restringe_base_cnpj']) || empty($_SESSION['Auth']['Usuario']['restringe_base_cnpj'])) {
			if (!empty($label)) {
				$ckeckbox = $view->BForm->input(($force_model != null ? $force_model.'.' : '').($multipla_utilizacao?$input_name.'_':'')."base_cnpj", array('type'=>'checkbox', 'label'=>'Buscar pela Base CNPJ','before'=>'<br/><br/>'));
			} else {
				$ckeckbox = $view->BForm->input(($force_model != null ? $force_model.'.' : '').($multipla_utilizacao?$input_name.'_':'')."base_cnpj", array('type'=>'checkbox', 'label'=>'Buscar pela Base CNPJ'));
			}
		} else {
			$ckeckbox = $view->BForm->hidden(($force_model != null ? $force_model.'.' : '').($multipla_utilizacao?$input_name.'_':'')."base_cnpj");
		}
		return $input.$ckeckbox;
	}//FINAL FUNCTION input_codigo_cliente_base

	function input_cliente_tipo(&$view, $tipo_empresa = 0, $clientes_tipos, $force_model = null){
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		if($tipo_empresa == 1)
		{
			$placeholder    = 'Embarcador';
			$input_portal   = 'codigo_embarcador';
			$input_monitora = 'cliente_embarcador';
		}
		elseif($tipo_empresa == 4)
		{
			$placeholder    = 'Transportador';
			$input_portal   = 'codigo_transportador';
			$input_monitora = 'cliente_transportador';
		}
		else
		{
			$placeholder    = 'Cliente';
			$input_portal   = 'codigo_cliente';
			$input_monitora = 'cliente_tipo';
		}

		$out = $this->input_codigo_cliente($view, $input_portal, $placeholder, false, $force_model);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_monitora, array('class' => 'input-large', 'options' => $clientes_tipos, 'label' => false, 'empty' => $placeholder));

		$input_monitora = Inflector::camelize($input_monitora);
		$input_portal   = Inflector::camelize($input_portal);

		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ init_combo_events_base_cnpj_por_tipo_empresa({$tipo_empresa}, '#{$model}{$input_monitora}', '#{$model}{$input_portal}'); });");

		return $out;
	}//FINAL FUNCTION input_cliente_tipo

	function input_cliente_usuario_cliente_monitora(&$view, $usuarios, $force_model = null, $label = false) {
		$authUsuario = $_SESSION['Auth'];
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$placeholder    = 'Cliente';
		$input_portal   = 'codigo_cliente';
		$input_usuario_portal = 'codigo_usuario';
		$input_monitora = 'cliente_tipo';

		$out = $this->input_codigo_cliente($view, $input_portal, $placeholder, $label, $force_model);
		$label_usuario = $label ? 'Usuário' : false;
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_usuario_portal, array('class' => 'input-large', 'options' => $usuarios, 'label' => $label_usuario, 'empty' => $placeholder, 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'select' : 'hidden')));
		$out .= $view->BForm->hidden(($force_model != null ? $force_model.'.' : '').$input_monitora);
		$input_usuario_portal = Inflector::camelize($input_usuario_portal);
		$input_portal   = Inflector::camelize($input_portal);
		$input_monitora = Inflector::camelize($input_monitora);
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				init_combo_usuarios_cliente_monitora('#{$model}{$input_usuario_portal}', '#{$model}{$input_portal}'); 
				init_input_cliente_monitora('#{$model}{$input_monitora}', '#{$model}{$input_usuario_portal}');
			});"
		);
		if (!empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ jQuery('#{$model}{$input_usuario_portal}').change(); });");	

		return $out;
	}//FINAL FUNCTION input_cliente_usuario_cliente_monitora

	function input_cliente_usuario_cliente(&$view, $usuarios, $force_model = null, $label = false, $placeholder = 'Cliente') {
		$authUsuario = $_SESSION['Auth'];
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$input_portal   = 'codigo_cliente';
		$input_usuario_portal = 'codigo_usuario';

		$out = $this->input_codigo_cliente($view, $input_portal, $placeholder, $label, $force_model);
		$label_usuario = $label ? 'Usuário' : false;
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_usuario_portal, array('class' => 'input-large', 'options' => $usuarios, 'label' => $label_usuario, 'empty' => $label_usuario, 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'select' : 'hidden')));
		$input_usuario_portal = Inflector::camelize($input_usuario_portal);
		$input_portal   = Inflector::camelize($input_portal);
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				init_combo_usuarios_cliente('#{$model}{$input_usuario_portal}', '#{$model}{$input_portal}'); 
			});"
		);
		if (!empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ jQuery('#{$model}{$input_usuario_portal}').change(); });");	

		return $out;
	}//FINAL FUNCTION input_cliente_usuario_cliente

	function input_grupo_empresas(&$view,$grupos_empresas,$empresas){
		$model = key($view->BForm->fieldset);
		$out  = $view->BForm->input('grupo_empresa', array('legend' => false, 'class' => 'input-small grupo_empresas', 'options' => $grupos_empresas, 'type' => 'radio', 'default' => '1', 'label' => array('class' => 'radio inline')));
		$out .= $view->BForm->input('empresa', array('label' => false, 'placeholder' => 'Empresa', 'class' => 'input-large lista_empresas', 'options' => $empresas, 'empty' => 'Todas empresas'));
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				jQuery('.grupo_empresas').change(function(){
					$('.lista_empresas').css('color','#000'); // change font-color to black
					$('.lista_empresas option:selected').text('Aguarde, carregando...');

					jQuery.ajax({
						'url': baseUrl + 'lojas_naveg/listar/' + jQuery(this).val() + '/' + Math.random(),
						'success': function(data) {
							jQuery('.lista_empresas').html(data);
							$('.lista_empresas').css('color','#555555'); // return default font-color
						}
					});
				});
			});
			");
		return $out;
	}//FINAL FUNCTION input_grupo_empresas

	function input_produto_servico(&$view,$produtos,$servicos = array(), $label = null){
		$model = key($view->BForm->fieldset);
		if($label){
			if($label == true){
				$label_produto = 'Produto';
				$label_servico = 'Serviço';
			}
			else{
				$label_produto = false;
				$label_servico = false;
			}
		}
		else{
			$label_produto = false;
			$label_servico = false;
		}

		$out = $view->BForm->input('codigo_produto', array('label' => $label_produto, 'options' => $produtos, 'empty' => 'Selecione um produto', 'class' => 'input-large bselect2 produto'));
		$out .= $view->BForm->input('codigo_servico', array('label' => $label_servico, 'options' => $servicos, 'empty' => 'Selecione um serviço', 'class' => 'input-large bselect2 servico'));
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				jQuery('.bselect2').select2();
				jQuery('.produto').change(function(){
					$('.servico option:selected').text('Aguarde, carregando...');
					jQuery.ajax({
						'url': baseUrl + 'produtos_servicos/servicos_por_produto/' + jQuery(this).val() + '/' + Math.random(),
						'success': function(data) {
							jQuery('.servico').html(data);
						}
					});
				});
			});
			");
		$out .= (empty($servicos) ? $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				jQuery('.produto').change();
			});
			") : "");
		return $out;
	}//FINAL FUNCTION input_produto_servico

	function codigo_sm($codigo_sm) {
		return $this->Html->link( $codigo_sm, 'javascript:void(0)', array( 'onclick' => "consulta_sm('{$codigo_sm}')" ));
	}//FINAL FUNCTION codigo_sm

	function codigo_sinistro($codigo_sinistro){
		return $this->Html->link($codigo_sinistro, 'javascript:void(0)', array( 'onclick' => "consulta_sinistro('{$codigo_sinistro}')",'title'=>'Visualizar Sinistro'));
	}//FINAL FUNCTION codigo_sinistro

	function codigo_loadplan($codigo_loadplan) {
		return $this->Html->link( $codigo_loadplan, 'javascript:void(0)', array( 'onclick' => "consulta_loadplan('{$codigo_loadplan}')" ));
	}//FINAL FUNCTION codigo_loadplan

	function codigo_ficha_scorecard($codigo_ficha_scorecard){
		return $this->Html->link( $codigo_ficha_scorecard, 'javascript:void(0)', array( 'onclick' => "consulta_ficha_scorecard('{$codigo_ficha_scorecard}')" ));
	}//FINAL FUNCTION codigo_ficha_scorecard

	function posicao_geografica($descricao, $latitude, $longitude, $placa = '') {
		$latitude_longitude = 'latitude:'.number_format($latitude, 4, '.', '') . ' longitude:'.number_format($longitude, 4, '.', '');
		$out = "<span title='{$latitude_longitude}'>".$this->Html->link($descricao, 'javascript:void(0)', array('onclick' => "mapa_coordenadas({$latitude}, {$longitude}, '{$placa}')"))."</span>";
		return $out;
	}//FINAL FUNCTION posicao_geografica

	function placa($placa, $data_inicial, $data_final, $codigo_cliente = null) {
		return $this->Html->link($placa, 'javascript:void(0)', array( 'onclick' => "eventos_logisticos_sm('{$placa}', '{$data_inicial}', '{$data_final}', '{$codigo_cliente}')" ));
	}//FINAL FUNCTION placa

	function evento($texto_evento, $codigo_espa) {
		return $this->Html->link($texto_evento, 'javascript:void(0)', array( 'onclick' => "detalhes_evento('{$codigo_espa}')" ));
	}//FINAL FUNCTION evento

	function truncate($text, $length = 100){
		return "<span title='{$text}'>{$this->Text->truncate($text, $length)}</span>";
	}//FINAL FUNCTION truncate

	function input_periodo(&$view, $force_model = null, $data_inicial = 'data_inicial', $data_final = 'data_final', $label = false, $periodo=  null, $unico_label = false) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		if($label){
			if ($unico_label!=false) {
				$out = $view->BForm->input($model.'.'.$data_inicial, array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' =>$unico_label, 'div' => array('style' => 'width:280px'), 'after'=>'&nbsp; até &nbsp;'.$view->BForm->input($model.'.'.$data_final, array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' => false, 'div' => false))));
				//$out .= ;
			} else {
				$out = $view->BForm->input($model.'.'.$data_inicial, array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' => ($label ? 'Data Inicial' : false), 'div' => array('style' => 'width:122px')));
				$out .= $view->BForm->input($model.'.'.$data_final, array('placeholder' => false, 'class' => 'input-small data', 'type' => 'text', 'label' => ($label ? 'Data Final' : false), 'div' => array('style' => 'width:122px')));
			}
		} else {
			$out = $view->BForm->input($model.'.'.$data_inicial, array('placeholder' => 'Início', 'class' => 'input-small data', 'type' => 'text', 'label' => ($label ? 'Data Inicial' : false), 'div' => array('style' => 'width:122px')));
			$out .= $view->BForm->input($model.'.'.$data_final, array('placeholder' => 'Fim', 'class' => 'input-small data', 'type' => 'text', 'label' => ($label ? 'Data Final' : false), 'div' => array('style' => 'width:122px')));
		}
		$data_inicial = Inflector::camelize($data_inicial);
		$data_final   = Inflector::camelize($data_final);

		$bloco_js = "jQuery(document).ready(function(){
			setup_datepicker();
			function verifica_data() {
				var data_inicial = jQuery('#{$model}{$data_inicial}').val();
				var data_final = jQuery('#{$model}{$data_final}').val();
				if (data_inicial != '' && data_final != '') {	

					ano_inicial = data_inicial.substr(6, 4);          
					mes_inicial = data_inicial.substr(4, 2);            
					hoje_inicial = data_inicial.substr(0, 2);           

					ano_final = data_final.substr(6, 4);
					mes_final = data_final.substr(4, 2);
					dia_final = data_final.substr(0, 2);

					var data_final_convertida = new Date(Date.parse(ano_final + ',' + mes_final + '/' + dia_final));
					var data_inicial_convertida = new Date(Date.parse(ano_inicial + ',' + mes_inicial +'/' + hoje_inicial));

					var um_dia = 1000 * 60 * 60 * 24;

					var diferenca_datas = parseInt(data_final_convertida.getTime() - data_inicial_convertida.getTime());

					var dias_diferenca = Math.round((diferenca_datas / um_dia));

					data_inicial = data_inicial.replace(/(\d{2})\/(\d{2})\/(\d{4})(\w*)/g, \"$3$2$1$4\");
					data_final = data_final.replace(/(\d{2})\/(\d{2})\/(\d{4})(\w*)/g, \"$3$2$1$4\");

					if ($.trim(data_final) < $.trim(data_inicial)) {
						alert('Data Final menor que Data Inicial');
					}";			

					$bloco_js_periodo = "else if (dias_diferenca > {$periodo}) {
						alert('O periodo deve ser menor ou igual a {$periodo} dias');
						jQuery('#{$model}{$data_final}').val('');
					}";

					$bloco_final_js = "
				}
			}
			jQuery('#{$model}{$data_inicial}').change(function() {verifica_data()});
			jQuery('#{$model}{$data_final}').change(function() {verifica_data()});
		});";
		
		if($periodo)
			$bloco_js .= $bloco_js_periodo.$bloco_final_js;
		else
			$bloco_js .= $bloco_final_js;
		
		$out .= $view->Javascript->codeBlock($bloco_js, false);	
		return $out;
	}//FINAL FUNCTION input_periodo
	
	function options($lista,$label = null){
		$retorno = "<option value=''>{$label}</option>";

		if(is_array($lista)){
			if(count($lista) > 1){
				foreach ($lista as $key => $value) {
					$retorno .= '<option value='.$key.'>'.$value.'</option>';
				}
			}else{
				foreach ($lista as $key => $value) {
					$retorno .= '<option value='.$key.' selected="selected">'.$value.'</option>';
				}
			}	
		}
		
		return $retorno;
	}//FINAL FUNCTION options

	function input_referencia(&$view, $localizador_input_codigo_cliente, $force_model = null, $input_name = 'refe_codigo', $input_number = false, $placeholder = 'Alvo', $label = false, $opcao_adicionar_novo = false, $localizador_input_codigo_cliente2 = 0, $value = null) {
		$model = ($force_model != null ? str_replace('.','',$force_model) : key($view->BForm->fieldset));
		$optionsCodigo = array('type' => 'hidden');

		if(!is_null($value) && is_numeric($value)){
			$optionsCodigo['value'] = $value;
			$this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
			$referencia = $this->TRefeReferencia->carregar($value);
		}		
		
		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-large referencia', 'type' => 'text');
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder));
		}
		if(isset($referencia) && is_array($referencia)){
			$optionsVisual['value'] = !empty($referencia['TRefeReferencia']['refe_descricao']) ? $referencia['TRefeReferencia']['refe_descricao'] : null;
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name : $input_number.'.'.$input_name), $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_visual : $input_number.'.'.$input_visual), $optionsVisual);
		$out .= '</div>';

		$localizador_input_name = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name);		
		$localizador_input_visual = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_visual);
		$opcao_adicionar_novo = ($opcao_adicionar_novo ? 'true' : 'false');
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
			$('{$localizador_input_visual}').search_referencias('{$localizador_input_codigo_cliente}', '{$localizador_input_name}', ".($localizador_input_codigo_cliente2 == null ? '0' : " '{$localizador_input_codigo_cliente2}'").");
			$('{$localizador_input_visual}').autocomplete_referencias('{$localizador_input_codigo_cliente}', '{$localizador_input_name}', {$opcao_adicionar_novo}" . ($localizador_input_codigo_cliente2 == null ? '' : ", '{$localizador_input_codigo_cliente2}'"). ");
			$('{$localizador_input_name}').change(function() {
				validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','referencia');
			});
			$(document).on('keyup', '{$localizador_input_visual}', function(e) {
				if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8 || e.which == 46 ) {
					$('{$localizador_input_name}').val('').change();
				}
			});
		}) ");		
		return $out;
	}//FINAL FUNCTION input_referencia

	function input_codigo_endereco_cidade(&$view, $input_name = 'codigo_endereco_cidade', $placeholder = 'Cidade', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$optionsCodigo = array('type' => 'hidden');

		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-large endereco_cidade_visual', 'type' => (empty($authUsuario['Usuario']['codigo_corretora']) ? 'text' : 'hidden'));
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder));
		}
		if ($value) {
			$optionsCodigo = array_merge($optionsCodigo, array('value' => $value));
		}
		$out = '<div class="control-group input text endereco_cidades">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_visual, $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.Inflector::camelize($input_visual);
		$input_name = Inflector::camelize($input_name);
		$out .= $this->Javascript->codeBlock("
			function testa_cidade(codigo, visual) {
				if($(visual).val() != '' && $(codigo).val() != '') {
					$('#campo_validacao').remove();
					if(!$('#campo_validacao').length) {
						$(visual).after('<span id=campo_validacao></span>');
					}
					$('#campo_validacao').css(
						'background-image','url('+baseUrl+'/img/icon-check.png)'
					);
				}else if($(visual).val() != '' && $(codigo).val() == '') {
					$('#campo_validacao').remove();
					if(!$('#campo_validacao').length) {
						$(visual).after('<span id=campo_validacao></span>');
					}
					$('#campo_validacao').css(
						'background-image','url('+baseUrl+'/img/icon-error.png)'
					).attr({'title':'Cidade inexistente'});
				}else if($(visual).val() == '' && $(codigo).val() == '') {
					if($('#campo_validacao').length) {
						$('#campo_validacao').remove();
					}
				}
				if($('#campo_validacao').length) {
					$('#campo_validacao').css({
						'width':'22px',
						'height':'24px',
						'display':'inline-block',
						'background-position':'6px 2px',
						'background-repeat':'no-repeat',
						'vertical-align':'text-top'
					});
				}
			}


			jQuery(document).ready(function(){
				$('{$localizador_input_visual}').autocomplete_cidades('{$localizador_input_name}', '#SinistroEstado');
				testa_cidade('{$localizador_input_name}','{$localizador_input_visual}');
				$('{$localizador_input_name}').change(function() {
					testa_cidade('{$localizador_input_name}','{$localizador_input_visual}');
					validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','cidade');
				});

				$(document).on('keyup', '{$localizador_input_visual}', function(e) {
					testa_cidade('{$localizador_input_name}','{$localizador_input_visual}');
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						$('{$localizador_input_name}').val('');
						validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','cidade');
					}
				});
			}) ");
		return $out;
	}//FINAL FUNCTION input_codigo_endereco_cidade

	function input_codigo_artigo_criminal(&$view, $input_name = 'codigo_artigo_criminal', $placeholder = 'Artigo Criminal', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$optionsCodigo = array('type' => 'hidden');

		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-large', 'type' => (empty($authUsuario['Usuario']['codigo_corretora']) ? 'text' : 'hidden') );
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder));
		}
		if ($value) {
			$optionsCodigo = array_merge($optionsCodigo, array('value' => $value));
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_visual, $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.Inflector::camelize($input_visual);
		$input_name = Inflector::camelize($input_name);
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){
			$('{$localizador_input_visual}').autocomplete_artigos_criminais('{$localizador_input_name}');
			// $('{$localizador_input_name}').change(function() {
			// 	validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','artigos_criminais');
			// });
			$(document).on('keyup', '{$localizador_input_visual}', function(e) {
				if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
					$('{$localizador_input_name}').val('');
					// validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','artigos_criminais');
				}
			});
		}) ");
		return $out;
	}//FINAL FUNCTION input_codigo_artigo_criminal

	function input_escolta(&$view, $force_model = null, $input_name = 'eesc_codigo', $input_number = false, $placeholder = 'Empresa Escolta', $label = false) {
		$model = ($force_model != null ? str_replace('.', '', $force_model) : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$optionsCodigo = array('type' => 'hidden');
		
		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-xlarge', 'type' => 'text');
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder, 'div' => false));
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name : $input_number.'.'.$input_name), $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_visual : $input_number.'.'.$input_visual), $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_visual);
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
			$('{$localizador_input_visual}').search_escoltas('{$localizador_input_name}');
			$('{$localizador_input_visual}').autocomplete_escoltas('{$localizador_input_name}');
			$('{$localizador_input_name}').change(function() {
				validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','escolta');
			});
			$(document).on('keyup', '{$localizador_input_visual}', function(e) {
				if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
					$('{$localizador_input_name}').val('');
					validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','escolta');
				}
			});
		}) ");
		return $out;
	}//FINAL FUNCTION input_escolta
	
	function status_viagem($posicao) {
		$vest_estatus = $posicao['vest_estatus'];
		$viag_status_viagem = $posicao['viag_status_viagem'];
		$viag_data_inicio = $posicao['viag_data_inicio'];
		$viag_data_fim = $posicao['viag_data_fim'];
		if ($vest_estatus == '2')
			return 'Cancelado';
		elseif ((empty($vest_estatus) || $vest_estatus == '1') && $viag_status_viagem == 'N' AND empty($viag_data_inicio) AND empty($viag_data_fim))
			return 'Agendado';
		elseif (($viag_status_viagem == 'N' || $viag_status_viagem == 'V') AND !empty($viag_data_inicio) AND empty($viag_data_fim))
			return 'Em Trânsito';
		elseif ($viag_status_viagem == 'D')
			return 'Entregando';
		elseif ($viag_status_viagem == 'L' AND empty($viag_data_fim))
			return 'Logístico';
		return 'Sem viagem';
	}//FINAL FUNCTION status_viagem
	
	function status_viagem_cor($posicao) {
		$cores = array('Cancelado'=>'red', 'Agendado'=>'green', 'Em Trânsito'=>'yellow', 'Entregando'=>'blue', 'Logístico'=>'darkseagreen', 'Sem viagem'=>'orange');
		return $cores[$this->status_viagem($posicao)];
	}//FINAL FUNCTION status_viagem_cor
	
	function combo_estado_cidade(&$view, $estado_field, $cidade_field, $estado_options, $cidade_options, $label = true) {
		$label_estado = 'Estado';
		$label_cidade = 'Cidade';
		if($label == false){
			$label_estado = false;
			$label_cidade = false;
		}
		$estado_id = Inflector::camelize(str_replace('.', '_', $estado_field));
		$cidade_id = Inflector::camelize(str_replace('.', '_', $cidade_field));
		$out = $view->BForm->input($estado_field, array('label' => $label_estado, 'class' => 'input-mini estado', 'empty' => 'Estado', 'options'=>$estado_options));
		$out .= $view->BForm->input($cidade_field, array('label' => $label_cidade, 'class' => 'input-large cidade', 'empty' => 'Cidade', 'options'=>$cidade_options));
		$out .= $view->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$estado_id}').change(function() { buscar_cidade(this, '#{$cidade_id}'); }); });");
		return $out;
	}//FINAL FUNCTION combo_estado_cidade

	function combo_cep_endereco(&$view, $cep_field, $endereco_field, $enderecos, $label = true) {
		$label_cep = 'CEP';
		$label_endereco = 'Endereço logradouro / Bairro / Cidade / Estado';
		if($label == false){
			$label_cep = false;
			$label_endereco = false;
		}
		$cep_id = Inflector::camelize(str_replace('.', '_', $cep_field));
		
		$out = $view->BForm->input($cep_field, array('class' => 'evt-endereco-cep input-mini formata-cep', 'label' => $label_cep));
		$out .= $view->BForm->input($endereco_field, array('label' => $label_endereco, 'class' => 'input-xxlarge evt-endereco-codigo', 'options' => $enderecos, 'empty' => 'Selecione um endereço..'));
		$out .= $view->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$cep_id}').search_ceps();}) ");
		
		return $out;
	}//FINAL FUNCTION combo_cep_endereco
	
	function input_rota(&$view, $localizador_input_codigo_cliente, $force_model = null, $input_name = 'rota_codigo', $input_number = false, $placeholder = 'Rota', $label = false) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$optionsCodigo = array('type' => 'hidden');
		
		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-large', 'type' => 'text');
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder, 'div' => false));
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name : $input_number.'.'.$input_name), $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_visual : $input_number.'.'.$input_visual), $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_visual);
		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
			$('{$localizador_input_visual}').search_rotas('{$localizador_input_codigo_cliente}', '{$localizador_input_name}');
			$('{$localizador_input_name}').change(function(){
				bloquearDiv($('#itinerario'));
				$('section#destino').load(baseUrl + 'solicitacoes_monitoramento/incluir_sm_destino/embarcador:' + $('#RecebsmEmbarcador').val() + '/cliente:' + $('#RecebsmCodigoCliente') .val() + '/' + $(this).val());
			});
		}) ");
		return $out;
	}//FINAL FUNCTION input_rota

	//Variavel div, foi colocada para exibição do erro validate (ficar vemelho), pois o mesmo é necessario para colocar a classe 'error'
	function input_rota_emb_transp(&$view, $localizador_input_codigo_embarcador, $localizador_input_codigo_transportador, $force_model = null, $input_name = 'rota_codigo', $input_number = false, $placeholder = 'Rota', $label = false, $validar_itinerario = false, $div = false) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$optionsCodigo = array('type' => 'hidden');
		
		$input_visual = $input_name.'_visual';
		$optionsVisual = array('title' => $placeholder, 'class' => 'input-large', 'type' => 'text', 'autocomplete'=>'off');
		if ($label) {
			$optionsVisual = array_merge($optionsVisual, array('label' => $placeholder));
		} else {
			$optionsVisual = array_merge($optionsVisual, array('label' => false, 'placeholder' => $placeholder, 'div' => $div));
		}
		$out = '<div class="control-group input text">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name : $input_number.'.'.$input_name), $optionsCodigo);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_visual : $input_number.'.'.$input_visual), $optionsVisual);
		$out .= '</div>';
		$localizador_input_name = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_visual);
		if ($validar_itinerario) {
			$txt_itinerario = "
			var itinerario_preenchido = false;
			$.each($('.referencia'), function(){
				itinerario_preenchido = itinerario_preenchido || ($(this).val()!='');
			});


			if (itinerario_preenchido) {
				var validado = (valida_itinerario_rota(this)=='1'?true:false);
			} else {
				validado = true;
			}
			";
		} else {
			$txt_itinerario = "var validado = true;";
		}

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
			$('{$localizador_input_visual}').search_rotas_emb_transp('{$localizador_input_codigo_embarcador}','{$localizador_input_codigo_transportador}', '{$localizador_input_name}');
			$('{$localizador_input_visual}').autocomplete_rotas_emb_transp('{$localizador_input_codigo_embarcador}','{$localizador_input_codigo_transportador}', '{$localizador_input_name}');
			$('{$localizador_input_name}').change(function() {
				validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','rota');
				if ($(this).val()!='') {
					".$txt_itinerario."
					if (validado && itinerario_preenchido) {
						$(this).removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();                
					}else if (validado) {
						$(this).removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();                
						bloquearDiv($('#itinerario'));
						$('section#destino').load(baseUrl + 'solicitacoes_monitoramento/incluir_sm_destino/embarcador:' + $('#RecebsmEmbarcador').val() + '/cliente:' + $('#RecebsmCodigoCliente') .val() + '/' + $(this).val());
					} else {
						$(this).removeClass('form-error').parent().removeClass('error').find('#lbl-error').remove();                
						$(this).addClass('form-error').parent().addClass('error').append('<div id=\'lbl-error\' class=\'help-block error-message\'><br><br>Pontos da rota estão divergentes com o Itinerário.</div>');
						validar_campo_autocomplete('{$localizador_input_name}','{$localizador_input_visual}','rota');
					}
				}
			});
			$(document).on('keyup', '{$localizador_input_visual}', function(e) {
				if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8 || e.which == 46 ) {
					$('{$localizador_input_name}').val('').change();
				}
			});
		}) ");
		return $out;
	}//FINAL FUNCTION input_rota_emb_transp

	function input_cep_endereco(&$view, $fields = array(), $enderecos = array(), $label = true, $input_number = false, $force_model = null){
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset)); 

		$cep_field = (isset($fields['cep_field']) ? $fields['cep_field'] : 'endereco_cep');
		$endereco_field = (isset($fields['endereco_field']) ? $fields['endereco_field'] : 'codigo_endereco');
		$numero_field = (isset($fields['numero_field']) ? $fields['numero_field'] : 'numero');
		$complemento_field = (isset($fields['complemento_field']) ? $fields['complemento_field'] : 'complemento');

		$label_cep = 'CEP';
		$label_endereco = 'Endereço';
		$label_numero = 'Número';
		$label_complemento = 'Complemento';
		
		$options_cep = array('title' => $label_cep, 'class' => ' input-mini');
		$options_endereco = array('title' => $label_endereco, 'class' => 'input-xxlarge codigo_endereco', 'options' => $enderecos, 'empty' => 'Selecione um endereço..');
		$options_numero = array('title' => $label_numero, 'class' => 'input-mini just-number', 'size' => 10, 'maxlength'=> 7);
		$options_complemento = array('title' => $label_complemento, 'class' => 'input-small complemento');

		if($label){
			$options_cep = array_merge($options_cep, array('label' => $label_cep, 'class'=>'input-mini formata-cep'));
			$options_endereco = array_merge($options_endereco, array('label' => $label_endereco));
			$options_numero = array_merge($options_numero, array('label' => $label_numero));
			$options_complemento = array_merge($options_complemento, array('label' => $label_complemento));
		}else{
			$options_cep = array_merge($options_cep, array('label' => false, 'placeholder' => $label_cep));
			$options_endereco = array_merge($options_endereco, array('label' => false, 'placeholder' => $label_endereco));
			$options_numero = array_merge($options_numero, array('label' => false, 'placeholder' => $label_numero));
			$options_complemento = array_merge($options_complemento, array('label' => false, 'placeholder' => $label_complemento));
		}

		$out = '<div class="row-fluid inline">';
		$out .= $view->BForm->hidden(($force_model != null ? $force_model.'.' : '').($input_number === false ? 'codigo' : $input_number.'.codigo'));
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $cep_field : $input_number.'.'.$cep_field), $options_cep);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $endereco_field : $input_number.'.'.$endereco_field), $options_endereco);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $numero_field : $input_number.'.'.$numero_field), $options_numero);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $complemento_field : $input_number.'.'.$complemento_field), $options_complemento);
		$out .= '</div>';
		$cep_id = str_replace('.','',$model.($input_number === false ? '' : $input_number).Inflector::camelize(str_replace('.', '_', $cep_field)));
		$out .= $view->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$cep_id}').search_enderecos();}) ");
		$out .= $view->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$cep_id}').search_ceps();}) ");
		if(  count($enderecos) == 0 ){
			$out .= $view->Javascript->codeBlock("jQuery(document).ready(function(){ 
				if($('#{$cep_id}').val() != ''){
					$('#{$cep_id}').blur();
				}
			}) ");
		}
		return $out;
	}//FINAL FUNCTION input_cep_endereco

	function input_cliente_razao_social(&$view,$force_model = null, $label = false, $placeholder = 'Cliente') {
		$authUsuario = $_SESSION['Auth'];
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$input_portal   = 'codigo_cliente';
		$input_razao_social = 'codigo_usuario';

		$out = $this->input_codigo_cliente($view, $input_portal, $placeholder, $label, $force_model);
		$label_razaosocial = $label ? 'Razão Social' : false;
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_razao_social, array('class' => 'input-large', 'options' => $usuarios, 'label' => $label_usuario, 'empty' => $label_usuario, 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'select' : 'hidden')));
		$input_usuario_portal = Inflector::camelize($input_usuario_portal);
		$input_portal   = Inflector::camelize($input_portal);
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				init_combo_usuarios_cliente('#{$model}{$input_usuario_portal}', '#{$model}{$input_portal}'); 
			});"
		);
		if (!empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ jQuery('#{$model}{$input_usuario_portal}').change(); });");	

		return $out;
	}//FINAL FUNCTION input_cliente_razao_social

	function input_referencia_endereco(&$view, $force_model = null, $input_name = 'refe_codigo', $input_number = false){
		$model = ($force_model != null ? str_replace('.','',$force_model) : key($view->BForm->fieldset));
		$input_visual = $input_name.'_visual';

		$out = '<div id="'.$input_name.'_endereco_detalhes'.($input_number === false ? '' : $input_number).'" class="control-group" style="display:none;">';
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name.'_endereco' : $input_number.'.'.$input_name.'_endereco'), array('label' => 'Endereço', 'class' => 'input-xlarge', 'readonly' => true));
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name.'_cidade' : $input_number.'.'.$input_name.'_cidade'), array('label' => 'Cidade', 'readonly' => true));
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').($input_number === false ? $input_name.'_estado' : $input_number.'.'.$input_name.'_estado'), array('label' => 'Estado', 'class' => 'input-mini', 'readonly' => true));
		$out .= '</div>';
		$localizador_input_div = '#'.$input_name.'_endereco_detalhes'.($input_number === false ? '' : $input_number);
		$localizador_input_name = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name);
		$localizador_input_visual = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_visual);
		$localizador_input_endereco = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name.'_endereco');
		$localizador_input_cidade = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name.'_cidade');
		$localizador_input_estado = '#'.$model.($input_number === false ? '' : $input_number).Inflector::camelize($input_name.'_estado');
		
		$out .= $this->Javascript->codeBlock("
			$(document).ready(function(){
				if($('{$localizador_input_name}').val() != ''){
					preenche_endereco_{$input_name}".($input_number === false ? '' : $input_number)."();
				}

				$('{$localizador_input_visual}').change(function(){
					$('{$localizador_input_name}').change();
				});
				$('{$localizador_input_name}').change(function(){
					if($(this).val() != ''){
						preenche_endereco_{$input_name}".($input_number === false ? '' : $input_number)."();
					}else{
						$('{$localizador_input_div}').hide();
						$('{$localizador_input_div}').closest('thead').hide();
					}
				});

				function preenche_endereco_{$input_name}".($input_number === false ? '' : $input_number)."(){
					$.ajax({
						url: baseUrl + 'referencias/busca_endereco/' + $('{$localizador_input_name}').val(),
						dataType: 'json',
						success: function(data){
							if(data.TRefeReferencia.refe_bairro_empresa_terceiro != '' && data.TRefeReferencia.refe_bairro_empresa_terceiro != null){
								$('{$localizador_input_endereco}').val(data.TRefeReferencia.refe_endereco_empresa_terceiro+', '+data.TRefeReferencia.refe_bairro_empresa_terceiro);
							}else{
								$('{$localizador_input_endereco}').val(data.TRefeReferencia.refe_endereco_empresa_terceiro);
							}
							$('{$localizador_input_cidade}').val(data.TCidaCidade.cida_descricao);
							$('{$localizador_input_estado}').val(data.TEstaEstado.esta_sigla);
							$('{$localizador_input_div}').show();
							$('{$localizador_input_div}').closest('thead').show();
						}
					});
				}
			});
			");

		return $out;
	}//FINAL FUNCTION input_referencia_endereco

	function input_alvos_bandeiras_regioes(&$view, $options){
		$model = (!empty($options['force_model']) ? str_replace('.','',$options['force_model']) : key($view->BForm->fieldset));
		$exibe_label = (isset($options['exibe_label']) && $options['exibe_label']==false ? false : true);
		$exibe_classes = (isset($options['exibe_classes']) && $options['exibe_classes']==false ? false : true);
		$exibe_veiculo = (isset($options['exibe_veiculo']) && $options['exibe_veiculo']==false ? false : true);
		$exibe_transportador = (isset($options['exibe_transportador']) && $options['exibe_transportador']==false ? false : true);
		$exibe_bandeira = (isset($options['exibe_bandeira']) && $options['exibe_bandeira']==false ? false : true);
		$exibe_regiao = (isset($options['exibe_regiao']) && $options['exibe_regiao']==false ? false : true);
		$exibe_loja = (isset($options['exibe_loja']) && $options['exibe_loja']==false ? false : true);
		$somente_cd = (isset($options['somente_cd']) ? true : false);
		$input_codigo_cliente = $model.Inflector::camelize($options['input_codigo_cliente']);
		$out = $view->BForm->input($model.'.cd_id', array('label' => ($exibe_label?'CD':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-cd', 'options'=> $options['cds'], 'style' => 'display:none'));
		//$out .= $view->BForm->input($model.'.bandeira_id', array('label' => ($exibe_label?'Bandeira':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-bandeira', 'options'=> $options['bandeiras'], 'style' => 'display:none'));
		//$out .= $view->BForm->input($model.'.regiao_id', array('class' => 'input-medium multiselect-regiao', 'multiple' => 'multiple', 'label' => ($exibe_label?'Região':false), 'title' => 'Região', 'options'=> $options['regioes'], 'style' => 'display:none'));
		//$out .= $view->BForm->input($model.'.loja_id', array('class' => 'input-medium multiselect-loja', 'multiple' => 'multiple', 'label' => ($exibe_label?'Loja':false), 'title' => 'Loja', 'options'=> $options['lojas'], 'style' => 'display:none'));
		if ($exibe_bandeira) $out .= $view->BForm->input($model.'.bandeira_id', array('label' => ($exibe_label?'Bandeira':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-bandeira', 'options'=> $options['bandeiras'], 'style' => 'display:none'));
			if ($exibe_regiao) $out .= $view->BForm->input($model.'.regiao_id', array('label' => ($exibe_label?'Região':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-regiao', 'options'=> $options['regioes'], 'style' => 'display:none'));
				if ($exibe_loja) $out .= $view->BForm->input($model.'.loja_id', array('label' => ($exibe_label?'Loja':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-loja', 'options'=> $options['lojas'], 'style' => 'display:none'));
					if ($exibe_classes) $out .= $view->BForm->input($model.'.cref_codigo', array('label' => ($exibe_label?'Classe Alvos':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-classe-alvo', 'options'=> $options['classes_referencia'], 'style' => 'display:none'));
						if ($exibe_veiculo) $out .= $view->BForm->input($model.'.tvei_codigo', array('label' => ($exibe_label?'Tipo Veículo':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-tipo-veiculo', 'options'=>$options['tipos_veiculo'], 'style' => 'display:none'));
							if ($exibe_transportador) $out .= $view->BForm->input($model.'.transportador_id', array('label' => ($exibe_label?'Transportador':false), 'multiple' => 'multiple', 'class' => 'input-medium multiselect-transportador', 'options'=>$options['transportadores'], 'style' => 'display:none'));

								$out .= $this->Javascript->codeBlock("
									$(document).ready(function(){
										$('.multiselect-cd').multiselect({
											maxHeight: 300,
											nonSelectedText: 'CD',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-bandeira').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Bandeira',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-regiao').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Região',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-loja').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Loja',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-tipo-veiculo').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Tipo Veículo',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-classe-alvo').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Classe Alvos',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});
										$('.multiselect-transportador').multiselect({
											maxHeight: 300,
											nonSelectedText: 'Transportador',
											numberDisplayed: 1,
											includeSelectAllOption: true
										});

										jQuery('#{$input_codigo_cliente}').change(function(){
											var div = jQuery('{$options['div']}');
											bloquearDiv(div);
											hash = '". urlencode(Comum::encriptarLink("{$model}|{$options['input_codigo_cliente']}|{$options['div']}|{$exibe_label}|{$exibe_classes}|{$exibe_veiculo}|{$exibe_transportador}|{$exibe_bandeira}|{$exibe_regiao}|{$exibe_loja}|{$somente_cd}")) ."';
											jQuery.ajax({
												'url': baseUrl + 'relatorios_sm/render_alvos_bandeiras_regioes_checkbox/' + jQuery('#{$input_codigo_cliente}').val() + '?hash=' + hash,
												'success': function(data) {
													jQuery(div).html(data).change();
													jQuery(div).unblock();
												}
											});
										});
									});");
		return $out;
	}//FINAL FUNCTION input_alvos_bandeiras_regioes

	function input_validade_checklist(&$view, $regras_aceite_sm, $force_model = null, $input_name = 'racs_validade_checklist', $label = 'Regra Aceite SM', $empty = 'Selecione',$seleciona_item_obrigatorio = false) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$regras_aceite_sm = array();
		$select_options = array('class' => 'input-xxlarge', 'options' => $regras_aceite_sm, 'label' => $label);
		if ($seleciona_item_obrigatorio && is_array($regras_aceite_sm) && count($regras_aceite_sm)>0) {
			// $select_options['selected'] = (isset($regras_aceite_sm[0]) ? $regras_aceite_sm[0] : '');
			$selected = (!empty($this->data[$model][$input_name]) ? $this->data[$model][$input_name] : NULL);		
			$select_options['selected'] = $selected; 
		} else {
			$select_options['empty'] = $empty;
		}
		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $select_options);
		$input_camelize  = $model.Inflector::camelize($input_name);
		$input_codigo_cliente = $model.Inflector::camelize('codigo_cliente');
		$input_qtde_dias_camelize = $model.Inflector::camelize('checklist_dias_validos');	

		$rac_selecionada = (!empty($this->data[$model][$input_name]) ? $this->data[$model][$input_name] : 0 );
		$qtde_dias       = (!empty($this->data[$model]['checklist_dias_validos']) ? $this->data[$model]['checklist_dias_validos'] : 0 );		

		$js_input = "
		jQuery('#{$input_codigo_cliente}').blur(function() {
			var codigo_cliente = $(this).val();
			var div_group = 
			$.ajax({
				url:baseUrl + 'regras_aceite_sm/list_validade/' + codigo_cliente + '/' + Math.random(),
				dataType: 'json',
				beforeSend: function() {
				//bloquearDiv(div_group);
				},
				success: function(data) {
					if (data) {

						";
						if (!$seleciona_item_obrigatorio) {
							$js_input .= "
							$('#{$input_camelize}').empty().append('<option>Selecione</option>');
							";
						} else {

							$js_input .= "
							$('#{$input_camelize}').empty();
							if (jQuery.isEmptyObject(data)) {
								$('#{$input_camelize}').empty().append('<option value=\'\'>Selecione</option>');
							}
							";			
						}
						$js_input .= "							
						$.each(data, function(i, item) {
							$('#{$input_camelize}').append('<option value='+i+' for='+item.racs_validade_checklist+'>'+item.descricao+'</option>');
						});
						";
						if ($seleciona_item_obrigatorio) {
							$js_input .= "
							var qtd_reg = ($('#{$input_camelize}').prop('length'));
							$('#{$input_camelize} option:first').prop('selected',true);
							$('#{$input_camelize}').change();
							";
						}
						$js_input .= "
						$('#{$input_camelize} option[value=".$rac_selecionada."]').attr('selected','selected');
						$('#{$input_qtde_dias_camelize}').val(".$qtde_dias.");
						$('#{$input_camelize}').change();			
						";
						$js_input .= "							
					}
				},
				complete: function() {

				}
			});
		});
		";
		$out .= $this->Javascript->codeBlock($js_input);
		$js_input2 = "		
		$('#{$input_camelize}').change(function() {
			var qtd_dias = $(\"option:selected\", this).attr('for');
			var div = $('#{$input_qtde_dias_camelize}').parent();
			if( qtd_dias ){
				$('#{$input_qtde_dias_camelize}').val(qtd_dias);
				div.show();
			} else {
				$('#{$input_qtde_dias_camelize}').val('');
				div.hide();
			}
		});";		
		$out .= $this->Javascript->codeBlock($js_input2);
		return $out;
	}//FINAL FUNCTION input_validade_checklist

	function input_codigo_cliente_dados(&$view, $fields_id,$input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name = Inflector::camelize($input_name);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name}').search_clientes();}) ");
		if($fields_id['razao_social']){
			//Campos Obrigatorios(Ex: razao_social => id_do_campo)
			$razao_social = $fields_id['razao_social'];
			//Campos Opcionais
			$gestor = isset($fields_id['gestor']) ? $fields_id['gestor'] : '';
			$cnpj = isset($fields_id['cnpj']) ? $fields_id['cnpj'] : '';

			$out .= $this->Javascript->codeBlock("
				jQuery(document).ready(function(){
					setup_mascaras();
					var razao_social = $('#{$razao_social}');
					var gestor = $('#{$gestor}');
					var cnpj = $('#{$cnpj}');

					var	codigo_cliente = $('#{$model}{$input_name}');            

					function clienteInvalido() {
						var div1 = '<div id=\'codigo-cliente-div\' style=\'color:#b94a48\' class=\'help-block error-message\'>Código inválido</div>'; 
						var div2 = document.createElement('div');
						razao_social.after(div1, div2); 
					}

					codigo_cliente.blur(function(){						
						if(codigo_cliente.val() != ''){
							$.ajax({
								url: baseUrl + 'clientes/buscar/' + codigo_cliente.val(),
								cache: false,
								type: 'post',
								dataType: 'json',
								beforeSend: function(){
									codigo_cliente.addClass('ui-autocomplete-loading');
								},
								success: function(data){					
									if(data.sucesso != false){									
										razao_social.val(data.dados.razao_social);						
										gestor.val(data.dados.codigo_gestor);
										cnpj.val(data.dados.codigo_documento);
										cnpj.focus();
										$('#codigo-cliente-div').remove();						
									}else{
										clienteInvalido();
									}
								},
								complete: function(){
									codigo_cliente.removeClass('ui-autocomplete-loading');
								},				
							});  
						}			
					});
				})"
			);
		}
		return $out;
	}//FINAL FUNCTION input_codigo_cliente_dados

	public function exportacao_relatorio_email( &$view, $model, $action, $tile = 'Exportar para Excel' ) {		
		$out = $this->Html->link('<i class="icon-envelope"></i>',
			array('controller' => 'relatorios_emails', 'action' => 'incluir', $model, $action ), 
			array('escape' => false, 'title' =>'Agendar Email', 
				'onclick' => "return open_dialog(this, 'Relatório por Email', 460)"));
		return $out;
	}//FINAL FUNCTION exportacao_relatorio_email

	function input_codigo_risco(&$view, $input_name = 'codigo_risco', $placeholder = 'Risco', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		$input_desc = 'nome_agente';

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		$options_descricao = array('title' => $placeholder, 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true);

		if ($label) { 
			$options = array_merge($options, array('label' => $label));
			$options_descricao = array_merge($options_descricao, array('label' => 'Nome Agente'));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
			$options_descricao = array_merge($options_descricao, array('label' => false, 'placeholder' => 'Nome Agente'));

		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$out .= $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_desc, $options_descricao);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);

		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				$('#{$input_name_camelized}').search_risco({$input_name_camelized},{$input_desc_camelized});}) 
				");
		return $out;
	}//FINAL FUNCTION input_codigo_risco

	function input_codigo_cbo(&$view, $input_name = 'codigo_cbo', $placeholder = 'CBO', $label = false, $force_model = null, $value = null, $input_desc = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		if(!isset($input_desc))
			$input_desc = 'descricao_cbo1';

		$options = array('title' => $placeholder, 'class' => 'input-small just-number', 'type' => 'text');

		if ($label) { 
			$options = array_merge($options, array('label' => $label));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_desc_camelized = $model.Inflector::camelize($input_desc);
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				$('#{$input_name_camelized}').search_cbo({$input_name_camelized},{$input_desc_camelized});}) 
				");
		return $out;
	}//FINAL FUNCTION input_codigo_cbo

	function input_codigo_medico(&$view, $input_name = 'codigo_medico', $placeholder = 'Médicos', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');

		if ($label) { 
			$options = array_merge($options, array('label' => $label));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);

		$input_name_camelized = $model.Inflector::camelize($input_name);

		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				$('#{$input_name_camelized}').search_medico({$input_name_camelized});}) 
				");
		return $out;
	}//FINAL FUNCTION input_codigo_medico

	function input_codigo_medico_readonly(&$view, $input_name = 'codigo_medico', $placeholder = 'Médicos', $label = false, $force_model = null, $value = null, $input_crm_display = null, $input_uf_display = null, $input_nome_display = null, $input_cpf_display = null) {

		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		$options = array('title' => $placeholder, 'class' => 'input-small just-number', 'type' => 'text');

		if ($label) { 
			$options = array_merge($options, array('label' => $label));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}
		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_crm_display_camelized = $model.Inflector::camelize($input_crm_display);
		$input_uf_display_camelized = $model.Inflector::camelize($input_uf_display);
		$input_nome_display_camelized = $model.Inflector::camelize($input_nome_display);
		$input_cpf_display_camelized = $model.Inflector::camelize($input_cpf_display);

		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				$('#{$input_name_camelized}').search_medico_readonly({$input_name_camelized}, {$input_crm_display_camelized}, {$input_uf_display_camelized}, {$input_nome_display_camelized}, {$input_cpf_display_camelized});
			});
			");

		return $out;
	}//FINAL FUNCTION input_codigo_medico_readonly	

	function input_codigo_grupo_exposicao(&$view, $input_name = 'codigo_grupo_exposicao', $placeholder = 'Grupo de Exposição', $label = false, $force_model = null, $value = null, $codigo_cliente) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		$input_desc = $input_name.'Código';
		$placeholderDescricao = 'Fornecedor';

		//$authUsuario = $_SESSION['Auth'];
		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => 'text');
		$options_descricao = array('title' => 'Fornecedor', 'class' => 'input-xlarge name', 'type' => 'text', 'readonly' => true);

		if ($label) { 
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => false, 'placeholder' => $placeholder));		 
		}

		if ($value) {
			$options = array_merge($options, array('value' => $value));
		}

		if ($input_name == 'codigo_fornecedor') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);

		$input_name_camelized = $model.Inflector::camelize($input_name);
		$input_name_camelized = $model.Inflector::camelize($input_name);

		$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ 
			$('#{$input_name_camelized}').search_grupo_exposicao({$input_name_camelized}, {$codigo_cliente});
		}) ");

		return $out;
	}//FINAL FUNCTION input_codigo_grupo_exposicao

	function leiaMais($texto , $ate = 100 ,$mais = '.. '){
		if(strlen($texto) <=  $ate ){
			return $texto;
		}
		$novo_texto = substr($texto, 0, $ate);
		$novo_texto .= $mais;

		$novo_texto = '<span title = "'.$texto.'">'.$novo_texto.'</span>';

		return $novo_texto;
	}//FINAL FUNCTION leiaMais

	function retorna_aviso_se_data_menor($date = '') {
		$return = '';
		if(!empty($date)) {	
			$date1  = DateTime::createFromFormat('d/m/Y', $date);
			$date2  = DateTime::createFromFormat('d/m/Y', date('d/m/Y'));
			if($date1 >= $date2) {
				$return = $date1->format('d/m/Y');
			} else {
				$return = '<span style="font-family:Lucida Grande,Lucida Sans,Arial,sans-serif;color:red;font-weight:bold;font-size:1.0em">'.$date1->format('d/m/Y').'</span>';
			}
		}
		return $return;
	}//FINAL FUNCTION retorna_aviso_se_data_menor	

	function valida_atendimento_de_servicos($quant_atendidas, $quant_total) {
		if($quant_atendidas == $quant_total) {
			$return = '<span class="badge-empty badge badge-success" data-toggle="tooltip" title="Atende todos os serviços"></span>';
		} else if($quant_atendidas < $quant_total && $quant_atendidas > 0) {
			$return = '<span class="badge-empty badge badge-transito" data-toggle="tooltip" title="Atende parcialmente os serviços"></span>';
		} else {
			$return = '<span class="badge-empty badge badge-important" data-toggle="tooltip" title="Não atende nenhum serviço"></span>';
		}
		return $return;
	}//FINAL FUNCTION valida_atendimento_de_servicos

	function input_grupo_economico(&$view, $force_model = null, $unidades, $setores, $cargos, $nomefuncionario = null, $cpfFuncionario = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid'>".$this->input_codigo_cliente($view, 'codigo_cliente', 'Cliente', null, $force_model)."</div>";
		$out .= "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_cliente_alocacao', array('label' => false, 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Selecione a Unidade'));
		$out .= $view->BForm->input('codigo_setor', array('label' => false, 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= $view->BForm->input('codigo_cargo', array('label' => false, 'class' => 'input-xlarge','options' => $cargos, 'empty' => 'Selecione o Cargo'));
		$out .= $view->BForm->hidden('codigo_funcionario', array('label' => false, 'div' => array('class' => 'input-xlarge control-group input')));
		if($nomefuncionario == null)
		{
			$out .= $view->BForm->input('nome_funcionario', array('label' => false, 'placeholder' => 'Nome do Funcionário', 'div' => array('class' => 'input-large control-group input')));
		}
		if($cpfFuncionario == null)
		{
			$out .= $view->BForm->input('cpf_funcionario', array('label' => false, 'class' => 'input-medium', 'placeholder' => 'CPF do Funcionário', 'div' => array('class' => 'input-medium control-group input')));
		}
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				(function($) {
					$.fn.autocomplete_funcionario_holding = function(localizador_input_codigo_cliente, localizador_input_codigo) {
						return this.each(function() {
							var input_codigo = $(localizador_input_codigo);
							$(this).autocomplete({
								source: function (request, response) {
									var codigo_cliente = $(localizador_input_codigo_cliente).val();
									
									input_codigo.val('').change();
									if (codigo_cliente != '') {
										$.ajax({
											url: baseUrl + 'clientes_funcionarios/autocomplete_funcionario/codigo:' + codigo_cliente +'/'+ Math.random(), 
											data: request,
											dataType: 'json',
											success: function (data) {
												if (data.length === 0)
													data = [{label: 'Nenhum', value: 0}];
												response(data);

											},
											error: function () {
												response([]);
											}
										});
									} else {
										response([]);
									}
								},
								minLength: 3,
								focus: function(){return false;},
								delay: 500,
								select: function( event, ui ){
									var name 	= $(this);
									input_codigo.val(ui.item.value).change();
									name.val(ui.item.label);
									return false;
								}
							}).data( 'uiAutocomplete' )._renderItem = function( ul, item) {
								var li = $( '<li></li>' ).data( 'item.autocomplete', item );
								if (item.value == 0) {
									li.append( 'Nenhum item encontrado <br />' );
								} else {
									li.append( '<a>'+ item.label + '</a>' )
								}
								return li.appendTo( ul );
							};
						});
					}    
				
				})(jQuery);
				
						
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoClienteAlocacao');
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Todos'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
					
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Selecione o Setor'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaCargos(codigo_matriz) {
				// 	var input = $('#{$model}CodigoCargo');
					
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Selecione o Cargo'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'cargos/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 				carregaCargos(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });
				
				$('#{$model}NomeFuncionario').autocomplete_funcionario_holding('#{$model}CodigoCliente', '#{$model}CodigoFuncionario', false);

				$(this).change(function (){					
					validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
				});

				$(this).keyup(function (e){
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						//$(localizador_input_codigo).val(\"\");
						validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
					}
				});

				validador = function(codigo, visual) {

					if($(visual).val() != \"\" && $(codigo).val() != \"\") {
						$(\"#campo_validacao\").remove();
						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
						);
					}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
						$(\"#campo_validacao\").remove();

						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
						).attr({\"title\":\"Cidade inexistente\"});

					}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
						if($(\"#campo_validacao\").length) {
							$(\"#campo_validacao\").remove();
						}
					}

					if($(\"#campo_validacao\").length) {
						$(\"#campo_validacao\").css({
							\"width\":\"22px\",
							\"height\":\"24px\",
							\"display\":\"inline-block\",
							\"background-position\":\"6px 2px\",
							\"background-repeat\":\"no-repeat\",
							\"vertical-align\":\"text-top\"
						});
					}
				} //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_grupo_economico

	function input_grupo_economico3(&$view, $force_model = null, $unidades, $setores) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid'>".$this->input_codigo_matriz($view, 'codigo_matriz', 'Cliente', null, $force_model)."</div>";
		$out .= "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_cliente', array('label' => 'Unidades', 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Selecione a Unidade'));
		$out .= $view->BForm->input('codigo_setor', array('label' => 'Setores', 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				(function($) {
					$.fn.autocomplete_funcionario_holding = function(localizador_input_codigo_cliente, localizador_input_codigo) {
						return this.each(function() {
							var input_codigo = $(localizador_input_codigo);
							$(this).autocomplete({
								source: function (request, response) {
									var codigo_cliente = $(localizador_input_codigo_cliente).val();
									
									input_codigo.val('').change();
									if (codigo_cliente != '') {
										$.ajax({
											url: baseUrl + 'clientes_funcionarios/autocomplete_funcionario/codigo:' + codigo_cliente +'/'+ Math.random(), 
											data: request,
											dataType: 'json',
											success: function (data) {
												if (data.length === 0)
													data = [{label: 'Nenhum', value: 0}];
												response(data);

											},
											error: function () {
												response([]);
											}
										});
									} else {
										response([]);
									}
								},
								minLength: 3,
								focus: function(){return false;},
								delay: 500,
								select: function( event, ui ){
									var name 	= $(this);
									input_codigo.val(ui.item.value).change();
									name.val(ui.item.label);
									return false;
								}
							}).data( 'uiAutocomplete' )._renderItem = function( ul, item) {
								var li = $( '<li></li>' ).data( 'item.autocomplete', item );
								if (item.value == 0) {
									li.append( 'Nenhum item encontrado <br />' );
								} else {
									li.append( '<a>'+ item.label + '</a>' )
								}
								return li.appendTo( ul );
							};
						});
					}    
				
				})(jQuery);
				
						
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoClienteAlocacao');
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Todos'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
					
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Selecione o Setor'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaCargos(codigo_matriz) {
				// 	var input = $('#{$model}CodigoCargo');
					
				// 	input.html('');
				// 	input.append($('<option />').val('').text('Selecione o Cargo'));

				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'cargos/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 				carregaCargos(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });
				
				$('#{$model}NomeFuncionario').autocomplete_funcionario_holding('#{$model}CodigoCliente', '#{$model}CodigoFuncionario', false);

				$(this).change(function (){					
					validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
				});

				$(this).keyup(function (e){
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						//$(localizador_input_codigo).val(\"\");
						validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
					}
				});

				validador = function(codigo, visual) {

					if($(visual).val() != \"\" && $(codigo).val() != \"\") {
						$(\"#campo_validacao\").remove();
						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
						);
					}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
						$(\"#campo_validacao\").remove();

						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
						).attr({\"title\":\"Cidade inexistente\"});

					}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
						if($(\"#campo_validacao\").length) {
							$(\"#campo_validacao\").remove();
						}
					}

					if($(\"#campo_validacao\").length) {
						$(\"#campo_validacao\").css({
							\"width\":\"22px\",
							\"height\":\"24px\",
							\"display\":\"inline-block\",
							\"background-position\":\"6px 2px\",
							\"background-repeat\":\"no-repeat\",
							\"vertical-align\":\"text-top\"
						});
					}
				} //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_grupo_economico

	/**
	 * [input_grupo_economico_cidade_estado description]
	 * 
	 * metodo para desennhar na tela os combos de codigo_cliente, unidade,setores, cargos, nome funcionario, cidade unidade, estado unidade, cidade credenciado, estado credenciado, matricula
	 * 
	 * 
	 * @param  [type] &$view           [description]
	 * @param  [type] $force_model     [description]
	 * @param  [type] $unidades        [description]
	 * @param  [type] $setores         [description]
	 * @param  [type] $cargos          [description]
	 * @param  [type] $nomefuncionario [description]
	 * @return [type]                  [description]
	 */
	function input_grupo_economico_cidade_estado(&$view, $force_model = null, $unidades, $setores, $cargos, $nomefuncionario = null, $cidade_unidade = null, $estado_unidade = null, $cidade_credenciado = null, $estado_credenciado = null,$nao_exibir_cidade = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));

		//



		$out = "<div class='row-fluid'>".$this->input_codigo_cliente($view, 'codigo_cliente', 'Cliente', null, $force_model)."</div>";
		$out .= "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_cliente_alocacao', array('label' => false, 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		$out .= $view->BForm->input('codigo_setor', array('label' => false, 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= $view->BForm->input('codigo_cargo', array('label' => false, 'class' => 'input-xlarge','options' => $cargos, 'empty' => 'Selecione o Cargo'));
		$out .= $view->BForm->hidden('codigo_funcionario', array('label' => false, 'div' => array('class' => 'input-xlarge control-group input')));
		if($nomefuncionario == null)
		{
			$out .= $view->BForm->input('nome_funcionario', array('label' => false, 'placeholder' => 'Nome do Funcionário', 'div' => array('class' => 'input-xlarge control-group input')));
		}
		$out .= "</div>";

		$out .= "<div class='row-fluid inline'>";
		if(is_null($nao_exibir_cidade)) {
			
			$out .= $view->BForm->input('codigo_estado_unidade', array('label' => false, 'class' => 'input-large','options' => $estado_unidade, 'empty' => 'Sel. Estado (Unidade)'));
			$out .= $view->BForm->input('codigo_cidade_unidade', array('label' => false, 'class' => 'input-large','options' => $cidade_unidade, 'empty' => 'Sel. Cidade (Unidade)'));

			$out .= $view->BForm->input('codigo_estado_fornecedor', array('label' => false, 'class' => 'input-large','options' => $estado_credenciado, 'empty' => 'Sel. Estado (Credenciado)'));
			$out .= $view->BForm->input('codigo_cidade_fornecedor', array('label' => false, 'class' => 'input-large','options' => $cidade_credenciado, 'empty' => 'Sel. Cidade (Credenciado)'));
		}
		
		$out .= $view->BForm->input('matricula', array('label' => false, 'placeholder' => 'Matricula', 'class' => 'input-small control-group input'));

		$out .= "</div>";


		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				function carregaUnidades(codigo_matriz) {
					var input = $('#{$model}CodigoClienteAlocacao');

					input.html('');
					input.append($('<option />').val('').text('Todos'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaSetores(codigo_matriz) {
					var input = $('#{$model}CodigoSetor');

					input.html('');
					input.append($('<option />').val('').text('Selecione o Setor'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaCargos(codigo_matriz) {
					var input = $('#{$model}CodigoCargo');

					input.html('');
					input.append($('<option />').val('').text('Selecione o Cargo'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'cargos/por_cliente/' + codigo_matriz + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaCidadeUnidade(codigo_cliente,uf) {
					var input = $('#{$model}CodigoCidadeUnidade');

					input.html('');
					input.append($('<option />').val('').text('Sel. Cidade (Unidade)'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'consulta_pedidos_exames/get_combo_cidade_unidade/' + codigo_cliente + '/' + uf + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaEstadoUnidade(codigo_cliente) {
					var input = $('#{$model}CodigoEstadoUnidade');

					input.html('');
					input.append($('<option />').val('').text('Sel. Estado (Unidade)'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'consulta_pedidos_exames/get_combo_estado_unidade/' + codigo_cliente + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaCidadeFornecedor(codigo_cliente,uf) {
					var input = $('#{$model}CodigoCidadeFornecedor');

					input.html('');
					input.append($('<option />').val('').text('Sel. Cidade (Fornecedor)'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'consulta_pedidos_exames/get_combo_cidade_fornecedor/' + codigo_cliente + '/' + uf + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				function carregaEstadoFornecedor(codigo_cliente) {
					var input = $('#{$model}CodigoEstadoFornecedor');

					input.html('');
					input.append($('<option />').val('').text('Sel. Estado (Unidade)'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'consulta_pedidos_exames/get_combo_estado_fornecedor/' + codigo_cliente + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				jQuery('#{$model}CodigoCliente').change(function(){
					var div = $(this).parent();
					bloquearDiv(div);
					$.ajax({
						'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
						'success': function(data) {
							if (data != null) {
								carregaUnidades(data);
								carregaSetores(data);
								carregaCargos(data);
								
								carregaEstadoUnidade(data);								
								carregaEstadoFornecedor(data);
							}
							div.unblock();
						}
					});
				});

				jQuery('#{$model}CodigoEstadoUnidade').change(function(){
					var div = $(this).parent();
					bloquearDiv(div);

					carregaCidadeUnidade(jQuery('#{$model}CodigoCliente').val(), jQuery('#{$model}CodigoEstadoUnidade').val());
							
					div.unblock();
				});

				jQuery('#{$model}CodigoEstadoFornecedor').change(function(){
					var div = $(this).parent();
					bloquearDiv(div);
					
					carregaCidadeFornecedor(jQuery('#{$model}CodigoCliente').val(), jQuery('#{$model}CodigoEstadoFornecedor').val());					
							
					div.unblock();
				});

				$('#{$model}NomeFuncionario').autocomplete_funcionario('#{$model}CodigoCliente', '#{$model}CodigoFuncionario', false);

				$(this).change(function (){
					validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
				});

				$(this).keyup(function (e){
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						$(localizador_input_codigo).val(\"\");
						validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
					}
				});

				validador = function(codigo, visual) {

					if($(visual).val() != \"\" && $(codigo).val() != \"\") {
						$(\"#campo_validacao\").remove();
						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
						);
					}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
						$(\"#campo_validacao\").remove();

						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
						).attr({\"title\":\"Cidade inexistente\"});

					}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
						if($(\"#campo_validacao\").length) {
							$(\"#campo_validacao\").remove();
						}
					}

					if($(\"#campo_validacao\").length) {
						$(\"#campo_validacao\").css({
							\"width\":\"22px\",
							\"height\":\"24px\",
							\"display\":\"inline-block\",
							\"background-position\":\"6px 2px\",
							\"background-repeat\":\"no-repeat\",
							\"vertical-align\":\"text-top\"
						});
					}
				} //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_grupo_economico

	function input_consulta_versao(&$view, $force_model = null, $unidades) {

		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		
		$out = $this->input_codigo_cliente($view, 'codigo_cliente', 'Consulta', 'Consulta', $force_model);

		$out .= $view->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidade', 
																	 'class' => 'input-xlarge',
																	 'options' => $unidades, 
																	 'empty' => 'Todos')
									);

		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoClienteAlocacao');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				input.html('');
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
					
				// 	carregaUnidades(jQuery('#{$model}CodigoCliente').val());

				// 	/*$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});*/
				// });

				// validador = function(codigo, visual) {

				// 	if($(visual).val() != \"\" && $(codigo).val() != \"\") {
				// 		$(\"#campo_validacao\").remove();
				// 		if(!$(\"#campo_validacao\").length) {
				// 			$(visual).after(\"<span id=campo_validacao></span>\");
				// 		}

				// 		$(\"#campo_validacao\").css(
				// 			\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
				// 		);
				// 	}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
				// 		$(\"#campo_validacao\").remove();
						
				// 		if(!$(\"#campo_validacao\").length) {
				// 			$(visual).after(\"<span id=campo_validacao></span>\");
				// 		}
						
				// 		$(\"#campo_validacao\").css(
				// 			\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
				// 		).attr({\"title\":\"Cidade inexistente\"});

				// 	}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
				// 		if($(\"#campo_validacao\").length) {
				// 			$(\"#campo_validacao\").remove();
				// 		}
				// 	}

				// 	if($(\"#campo_validacao\").length) {
				// 		$(\"#campo_validacao\").css({
				// 			\"width\":\"22px\",
				// 			\"height\":\"24px\",
				// 			\"display\":\"inline-block\",
				// 			\"background-position\":\"6px 2px\",
				// 			\"background-repeat\":\"no-repeat\",
				// 			\"vertical-align\":\"text-top\"
				// 		});
				// 	}
				// } //fim validador
				
			}) 
			");
		return $out;
	}//FINAL FUNCTION input_consulta_versao

	function input_posicao_exames(&$view, $force_model = null, $unidades, $setores, $exames) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_unidade', array('label' => false, 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		$out .= $view->BForm->input('codigo_setor', array('label' => false, 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= $view->BForm->input('codigo_exame', array('label' => false, 'class' => 'input-xlarge','options' => $exames, 'empty' => 'Selecione o Exame'));


		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoUnidade');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });

			}) 
			");
		return $out;
	}//FINAL FUNCTION input_posicao_exames

	function input_unid_setor_cargo(&$view, $force_model = null, $unidades, $setores, $cargos) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		
		$out .= $view->BForm->input('codigo_unidade', array('label' => false, 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		
		$out .= $view->BForm->input('codigo_setor', array('label' => false, 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= $view->BForm->input('codigo_cargo', array('label' => false, 'class' => 'input-xlarge','options' => $cargos, 'empty' => 'Selecione o Cargo'));
		
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
			// function carregaUnidades(codigo_matriz) {
			// 	var input = $('#{$model}CodigoUnidade');
			// 	bloquearDiv(input.parent());
			// 	$.ajax({
			// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
			// 		'dataType': 'json',
			// 		'success': function(result) {
			// 			if (result != null) {
			// 				input.empty();
			// 				input.append($('<option></option>').attr('value', '').text('Todos'));
			// 				$.each(result, function() {
			// 				    input.append($('<option />').val(this.codigo).text(this.descricao));
			// 				});
			// 			}
			// 			input.val(".(!empty($this->data['DashboardRelatorio']['codigo_unidade']) ? $this->data['DashboardRelatorio']['codigo_unidade'] : '' ).");
			// 			input.parent().unblock();
			// 		}
			// 	});
			// }

			// function carregaSetores(codigo_matriz) {
			// 	var input = $('#{$model}CodigoSetor');
			// 	bloquearDiv(input.parent());
			// 	$.ajax({
			// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
			// 		'dataType': 'json',
			// 		'success': function(result) {
			// 			if (result != null) {
			// 				input.empty();
			// 				input.append($('<option></option>').attr('value', '').text('Selecione o Setor'));
			// 				$.each(result, function() {
			// 				    input.append($('<option />').val(this.codigo).text(this.descricao));
			// 				});
			// 			}
			// 			input.val(".(!empty($this->data['DashboardRelatorio']['codigo_setor']) ? $this->data['DashboardRelatorio']['codigo_setor'] : '').");
			// 			input.parent().unblock();
			// 		}
			// 	});
			// }

			// function carregaCargos(codigo_matriz) {
			// 	var input = $('#{$model}CodigoCargo');
			// 	bloquearDiv(input.parent());
			// 	$.ajax({
			// 		'url': baseUrl + 'cargos/por_cliente/' + codigo_matriz + '/' + Math.random(),
			// 		'dataType': 'json',
			// 		'success': function(result) {
			// 			if (result != null) {
			// 				input.empty();
			// 				input.append($('<option></option>').attr('value', '').text('Selecione o Cargo'));
			// 				$.each(result, function() {
			// 				    input.append($('<option />').val(this.codigo).text(this.descricao));
			// 				});
			// 			}
			// 			input.val(".(!empty($this->data['DashboardRelatorio']['codigo_cargo']) ? $this->data['DashboardRelatorio']['codigo_cargo'] : '').");
			// 			input.parent().unblock();
			// 		}
			// 	});
			// }
			
			// jQuery('#{$model}CodigoCliente').change(function(){
			// 	var div = $(this).parent();
			// 	$.ajax({
			// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
			// 		'success': function(data) {
			// 			if (data != null) {
			// 				carregaUnidades(data);
			// 				carregaSetores(data);
			// 				carregaCargos(data);
			// 			}
			// 		}
			// 	});
			// });
			
			jQuery(document).ready(function(){ 
				$('#{$model}CodigoCliente').trigger('change');
			}); 
		");
		return $out;
	}

	function input_setor_cargo(&$view, $force_model = null, $unidade_id ) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div>";
		
		// Objeto do cliente setor
		$ClienteSetorCargo = new ClienteSetorCargo;
		// Seleciona os dados referentes ao cliente

		$options = array(

				'joins'  => array( 
									array(
										
										'table' => 'setores',
										'alias' => 'Setores',
										'conditions' => 'ClienteSetorCargo.codigo_setor = Setores.codigo'
									),
									array(
										
										'table' => 'cargos',
										'alias' => 'Cargos',
										'conditions' => 'ClienteSetorCargo.codigo_cargo = Cargos.codigo'
									)
								),

				'fields' => array( 	'ClienteSetorCargo.codigo_cargo', 
									'ClienteSetorCargo.ativo',
									'ClienteSetorCargo.codigo',
									'Cargos.descricao',
									'ClienteSetorCargo.codigo_setor',
									'Setores.descricao' ),

				'conditions' => array(
					'ClienteSetorCargo.codigo_cliente' => $unidade_id,
					'(ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)', 
				),

				'order' => array('Setores.descricao','Cargos.descricao'),
			);

		//pr( $ClienteSetorCargo->find( 'sql', $options ) );
		$res = $ClienteSetorCargo->find( 'all', $options );		

		$setores = array();
		$cargos = array();
		$bonds = array();
		if( $res ){
			// Popula itens para os selects
			$cargo = null;
			foreach( $res as $row ){
				if( $cargo != $row['ClienteSetorCargo']['codigo_cargo'] ){
					$cargo = $row['ClienteSetorCargo']['codigo_cargo'];
					$cargos[ $cargo ] = $row['Cargos']['descricao'];
				}
				$setores[ $row['ClienteSetorCargo']['codigo_setor'] ] = $row['Setores']['descricao'] ;
				//$bonds[ $row['ClienteSetorCargo']['codigo_setor'] ][] = $cargo;
			}
		}	
		// Imprime os selects	
		$out .= $view->BForm->input($model.'.codigo_setor', array('label' => false, 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));
		$out .= $view->BForm->input($model.'.codigo_cargo', array('label' => false, 'class' => 'input-xlarge','options' => $cargos, 'empty' => 'Selecione o Cargo'));

		$out .= "</div>";
		// Javascript!!
		$out .= $this->Javascript->codeBlock("
			
		");

		return $out;
	}


	/**
	 * Chama a moda para exibir os exames
	 *
	 *
	*/
	public function modal_pedidos_exames(&$view,$codigo_pedido,$nome='pedido')
	{
		
		$out = "";

		//pega os exames dos pedidos
		$out .= '<a href="javascript:void(0);" onclick="modalPedidoExame(\'' . $codigo_pedido . '\', 1);">' . $codigo_pedido . '</a>';

		// $out .= '<div id="modal_agendamento_'.$codigo_pedido.'"></div>';
		
		$out .= '<div class="modal fade" id="modal_agendamento_'. $codigo_pedido . '" data-backdrop="static"></div>';

		$out .= $this->Javascript->codeBlock('
					jQuery(document).ready(function() {
						setup_mascaras(); setup_time(); setup_datepicker();
						$(".modal").css("z-index", "-1");
					});
					
					function modalPedidoExame(codigo_pedido,mostra) {
						
						if(mostra) {
							
							var div = jQuery("div#modal_agendamento_"+codigo_pedido);
							bloquearDiv(div);
							div.load(baseUrl + "pedidos_exames/modal_pedidos_exames/" + codigo_pedido + "/" + Math.random());
					
							$("#modal_agendamento_"+codigo_pedido).css("z-index", "1050");
							$("#modal_agendamento_"+codigo_pedido).modal("show");

						} else {
							$(".modal").css("z-index", "-1");
							$("#modal_agendamento_"+codigo_pedido).modal("hide");
						}

					}
						
				');

		return $out;

	}//fim modal_pedidos_exames
	
	/**
     * 
     * Encontrar um valor por chave informada
     * https://stackoverflow.com/questions/3975585/search-for-a-key-in-an-array-recursively
     *
     * @param array $array
     * @param [type] $needle
     * @return void
     */
    public function recursiveFind(array $array, $needle) {
		if(!is_array($array)){
			return false;
		}
        $iterator = new RecursiveArrayIterator($array);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $return = array();
        foreach ($recursive as $key => $value) {
          if ($key === $needle) {
            $return[] = $value;
          }
        } 
        return $return;
	}
	
	public function dump( $dados = null, $class = '' ) {
		$this->log($class.' :: '.print_r($dados, true), 'debug');
	}

	/**
	 * verifica se usuario da sessao é um multicliente
	 *
	 * @return bool
	 */
	public function seUsuarioForMulticliente(){
		$usuario = $this->Session->read('Auth.Usuario'); // recupera sessao do usuário atual
		return (isset($usuario['multicliente']));
	}

	function input_nome_funcionario_sem_label(&$view, $force_model = null, $nomefuncionario = null, $codigo_cliente = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		$out .= $view->BForm->hidden('codigo_funcionario', array('label' => false, 'div' => array('class' => 'input-xlarge control-group input')));
		$out .= $view->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente, 'div' => array('class' => 'input-xlarge control-group input')));
		if($nomefuncionario == null)
		{
			$out .= $view->BForm->input('nome_funcionario', array('label' => false, 'placeholder' => 'Nome do Funcionário', 'div' => array('class' => 'input-xlarge control-group input')));
		}
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				jQuery('#{$model}CodigoCliente').change(function(){
					var div = $(this).parent();
					bloquearDiv(div);
					$.ajax({
						'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
						'success': function(data) {
							if (data != null) {
								carregaUnidades(data);
								carregaSetores(data);
								carregaCargos(data);
							}
							div.unblock();
						}
					});
				});

				$('#{$model}NomeFuncionario').autocomplete_funcionario('#{$model}CodigoCliente', '#{$model}CodigoFuncionario', false);

				$(this).change(function (){
					validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
				});

				$(this).keyup(function (e){
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						$(localizador_input_codigo).val(\"\");
						validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
					}
				});

				validador = function(codigo, visual) {

					if($(visual).val() != \"\" && $(codigo).val() != \"\") {
						$(\"#campo_validacao\").remove();
						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
						);
					}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
						$(\"#campo_validacao\").remove();

						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
						).attr({\"title\":\"Funcionário inexistente\"});

					}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
						if($(\"#campo_validacao\").length) {
							$(\"#campo_validacao\").remove();
						}
					}

					if($(\"#campo_validacao\").length) {
						$(\"#campo_validacao\").css({
							\"width\":\"22px\",
							\"height\":\"24px\",
							\"display\":\"inline-block\",
							\"background-position\":\"6px 2px\",
							\"background-repeat\":\"no-repeat\",
							\"vertical-align\":\"text-top\"
						});
					}
				} //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_grupo_economico


	function input_nome_funcionario_com_label(&$view, $force_model = null, $nomefuncionario = null, $codigo_cliente = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		$out .= $view->BForm->hidden('codigo_funcionario', array('label' => false, 'div' => array('class' => 'input-xlarge control-group input')));
		$out .= $view->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente, 'div' => array('class' => 'input-xlarge control-group input')));
		if($nomefuncionario == null)
		{
			$out .= $view->BForm->input('nome_funcionario', array('label' => 'Funcionário', 'placeholder' => 'Nome do Funcionário', 'div' => array('class' => 'input-xlarge control-group input')));
		}
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				jQuery('#{$model}CodigoCliente').change(function(){
					var div = $(this).parent();
					bloquearDiv(div);
					$.ajax({
						'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
						'success': function(data) {
							if (data != null) {
								carregaUnidades(data);
								carregaSetores(data);
								carregaCargos(data);
							}
							div.unblock();
						}
					});
				});

				$('#{$model}NomeFuncionario').autocomplete_funcionario('#{$model}CodigoCliente', '#{$model}CodigoFuncionario', false);

				$(this).change(function (){
					validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
				});

				$(this).keyup(function (e){
					if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || e.which == 8) {
						$(localizador_input_codigo).val(\"\");
						validador('#{$model}CodigoFuncionario','#{$model}NomeFuncionario');
					}
				});

				validador = function(codigo, visual) {

					if($(visual).val() != \"\" && $(codigo).val() != \"\") {
						$(\"#campo_validacao\").remove();
						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
						);
					}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
						$(\"#campo_validacao\").remove();

						if(!$(\"#campo_validacao\").length) {
							$(visual).after(\"<span id=campo_validacao></span>\");
						}

						$(\"#campo_validacao\").css(
							\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
						).attr({\"title\":\"Funcionário inexistente\"});

					}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
						if($(\"#campo_validacao\").length) {
							$(\"#campo_validacao\").remove();
						}
					}

					if($(\"#campo_validacao\").length) {
						$(\"#campo_validacao\").css({
							\"width\":\"22px\",
							\"height\":\"24px\",
							\"display\":\"inline-block\",
							\"background-position\":\"6px 2px\",
							\"background-repeat\":\"no-repeat\",
							\"vertical-align\":\"text-top\"
						});
					}
				} //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_nome_funcionario_com_label

	function input_unidades(&$view, $force_model = null, $unidades) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_unidade', array('label' => false, 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoUnidade');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });

			}) 
			");
		return $out;
	}//FINAL FUNCTION input_posicao_exames

	function input_unidades_tela_digitalizacao(&$view, $force_model = null, $unidades) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_cliente_alocacao', array('label' => 'Cliente Unidade (*)', 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoUnidade');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });

			}) 
			");
		return $out;
	}//FINAL FUNCTION input_posicao_exames

	function input_unidades_exames_faturar(&$view, $force_model = null, $unidades) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		// $out = "<div class='row-fluid inline'>";
		$out = $view->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidade', 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Todos'));
		// $out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){ 
				// COMENTADO POIS O JAVASCRIPT USADO É DO INPUT_CODIGO_CLIENTE
				// function carregaUnidades(codigo_matriz) {
				// 	var input = $('#{$model}CodigoUnidade');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos_clientes/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// function carregaSetores(codigo_matriz) {
				// 	var input = $('#{$model}CodigoSetor');
				// 	bloquearDiv(input.parent());
				// 	$.ajax({
				// 		'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
				// 		'dataType': 'json',
				// 		'success': function(result) {
				// 			if (result != null) {
				// 				$.each(result, function() {
				// 					input.append($('<option />').val(this.codigo).text(this.descricao));
				// 				});
				// 			}
				// 			input.parent().unblock();
				// 		}
				// 	});
				// }

				// jQuery('#{$model}CodigoCliente').change(function(){
				// 	var div = $(this).parent();
				// 	bloquearDiv(div);
				// 	$.ajax({
				// 		'url': baseUrl + 'grupos_economicos/por_cliente/' + jQuery('#{$model}CodigoCliente').val() + '/' + Math.random(),
				// 		'success': function(data) {
				// 			if (data != null) {
				// 				carregaUnidades(data);
				// 				carregaSetores(data);
				// 			}
				// 			div.unblock();
				// 		}
				// 	});
				// });

			}) 
			");
		return $out;
	}//FINAL FUNCTION input_posicao_exames

	function input_codigo_cliente_matriz(&$view, $input_name = 'codigo_cliente', $placeholder = 'Cliente', $label = false, $force_model = null, $value = null) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$authUsuario = $_SESSION['Auth'];
		
		// verifica se ha dados validos para ser um usuario multi empresa
		if(isset($authUsuario['Usuario']['multicliente'])){
			return $this->combo_codigo_cliente_multi($view, $input_name = 'codigo_cliente', 
						null, $label = false, $model, $value = null, true);	
		}

		$options = array('title' => $placeholder, 'class' => 'input-mini just-number', 'type' => (empty($authUsuario['Usuario']['codigo_cliente']) ? 'text' : 'hidden') );
		if ($label) {
			$options = array_merge($options, array('label' => $placeholder));
		} else {
			$options = array_merge($options, array('label' => 'Código', 'placeholder' => $placeholder));
		}
		
		if ($value) {
			$options = array_merge($options, array('value' => $value));
		} else {
			// implementado verificação para avaliar $codigo_cliente 
			// pois algumas situações retorna array por utilizar normalizaCodigoCliente
			if (!empty($this->data[$model][$input_name])) {
				$codigo = $this->data[$model][$input_name];
				if(is_array($codigo)){
					$value = implode(',', $codigo);
					$options = array_merge($options, array('value' => $value));
				}
			}
		}	
		if ($input_name == 'codigo_cliente') {
			if (empty($this->data[$model][$input_name])) {
				$lastValue = isset($_SESSION['Last'][$input_name]) ? $_SESSION['Last'][$input_name] : NULL;
				$lastValue = is_array($lastValue) ? implode(',', $lastValue) : $lastValue;
				$view->BForm->data[$model][$input_name] = $lastValue;
				$view->BForm->data['Last'][$input_name] = $lastValue;
			}
		}

		$out = $view->BForm->input(($force_model != null ? $force_model.'.' : '').$input_name, $options);
		$input_name_camelized = Inflector::camelize($input_name);
		if (empty($authUsuario['Usuario']['codigo_cliente']))
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').search_clientes();}) ");

		if ($input_name == 'codigo_cliente') {
			$out .= $view->BForm->hidden('Last.'.$input_name);
			$out .= $this->Javascript->codeBlock("jQuery(document).ready(function(){ $('#{$model}{$input_name_camelized}').blur(function() { $('#Last{$input_name_camelized}').val( $('#{$model}{$input_name_camelized}').val() ) });}) ");
		}
		$out .= $this->Javascript->link('form_dinamico');
		$out .= $this->Javascript->codeBlock("
		jQuery(document).ready(function(){  
			
			// se estiver usando selecao multicliente
			var multiclientesAtivo = jQuery('.ajax-multiclientes').length; 

			var options = {
				model : '{$model}',
				multicliente : multiclientesAtivo,
				inputs: {
					codigo_cliente : '{$model}CodigoCliente',
					codigo_cliente_alocacao : '{$model}CodigoClienteAlocacao',
					codigo_unidade : '{$model}CodigoUnidade',
					codigo_setor : '{$model}CodigoSetor',
					codigo_cargo : '{$model}CodigoCargo'
				},
				valores:{
					codigo_cliente : '',
					codigo_cliente_alocacao : '',
					codigo_setor : '',
					codigo_cargo : ''									
				}
			};

			// inicializa tratamento das selecoes para
			// codigo_cliente, codigo_cliente_alocacao, codigo_setor, codigo_cargo
			form_dinamico.init(options); 

		}); ");
		return $out;
	}//FINAL FUNCTION input_codigo_cliente


	function input_grupo_economico_setor(&$view, $force_model = null, $setores) {
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		$out = "<div class='row-fluid'>".$this->input_codigo_cliente($view, 'codigo_cliente', 'Cliente', null, $force_model)."</div>";
		$out .= "<div class='row-fluid inline'>";
		$out .= $view->BForm->input('codigo_setor', array('label' => "Setor", 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));

		$out .= "</div>";
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				
				carregaSetores = function(codigo_matriz) {
					var input = $('#{$model}CodigoSetor');
					
					input.html('');
					input.append($('<option />').val('').text('Selecione o Setor'));

					bloquearDiv(input.parent());
					$.ajax({
						'url': baseUrl + 'setores/por_cliente/' + codigo_matriz + '/' + Math.random(),
						'dataType': 'json',
						'success': function(result) {
							if (result != null) {
								$.each(result, function() {
									input.append($('<option />').val(this.codigo).text(this.descricao));
								});
							}
							input.parent().unblock();
						}
					});
				}

				
				// validador = function(codigo, visual) {

				// 	if($(visual).val() != \"\" && $(codigo).val() != \"\") {
				// 		$(\"#campo_validacao\").remove();
				// 		if(!$(\"#campo_validacao\").length) {
				// 			$(visual).after(\"<span id=campo_validacao></span>\");
				// 		}

				// 		$(\"#campo_validacao\").css(
				// 			\"background-image\",\"url(\"+baseUrl+\"/img/icon-check.png)\"
				// 		);
				// 	}else if($(visual).val() != \"\" && $(codigo).val() == \"\") {
				// 		$(\"#campo_validacao\").remove();

				// 		if(!$(\"#campo_validacao\").length) {
				// 			$(visual).after(\"<span id=campo_validacao></span>\");
				// 		}

				// 		$(\"#campo_validacao\").css(
				// 			\"background-image\",\"url(\"+baseUrl+\"/img/icon-error.png)\"
				// 		).attr({\"title\":\"Cidade inexistente\"});

				// 	}else if($(visual).val() == \"\" && $(codigo).val() == \"\") {
				// 		if($(\"#campo_validacao\").length) {
				// 			$(\"#campo_validacao\").remove();
				// 		}
				// 	}

				// 	if($(\"#campo_validacao\").length) {
				// 		$(\"#campo_validacao\").css({
				// 			\"width\":\"22px\",
				// 			\"height\":\"24px\",
				// 			\"display\":\"inline-block\",
				// 			\"background-position\":\"6px 2px\",
				// 			\"background-repeat\":\"no-repeat\",
				// 			\"vertical-align\":\"text-top\"
				// 		});
				// 	}
				// } //fim validador

			}) 
			");

		return $out;
	}//FINAL FUNCTION input_grupo_economico_setor

	function input_ge_unidades_cargos_setores(&$view, $force_model = null, $unidades, $setores, $cargos = null, $nomefuncionario = null) {
		
		$model = ($force_model != null ? $force_model : key($view->BForm->fieldset));
		
		$out = "<div class='row-fluid inline'>".$this->input_codigo_cliente($view, 'codigo_cliente', 'Cliente', 'Cliente', $force_model);		
		
		$out .= $view->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Selecione a Unidade'));
		
		$out .= $view->BForm->input('codigo_setor', array('label' => 'Setor', 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor'));		
		
		$out .= "</div>";
		
		$out .= $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
			}) 
		");

		return $out;
	}//FINAL FUNCTION input_grupo_economico_unidades_cargos_setores

}//FINAL FUNCTION BuonnyHelper	

?>
