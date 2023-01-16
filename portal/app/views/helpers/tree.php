<?php 
class TreeHelper extends FormHelper{
	
	function listElement($data, $modelName, $displayData, $attributes, $level = 1){
		$output = array();
		$name = isset($attributes['name']) ? $attributes['name'] : 'id';
		$class = isset($attributes['class']) ? $attributes['class'] : '';
		$displayModel = $displayData['model'];
		$displayField = $displayData['field'];
		
		foreach ($data as $key=>$val){
			$item = $this->Html->tag('span',$val[$displayModel][$displayField]);
			
			$inner = '';
			if(isset($val['children'][0]))
				$inner = $this->listElement($val['children'], $modelName, $displayData, $attributes, $level+1);
			    
			$editar = $this->Html->link('Editar', array('action'=>'edit', $val[$displayModel]['id']), array('title'=>'Editar', 'class'=>'editar')); 
			$excluir = $this->Html->link('Excluir', array('action'=>'delete', $val[$displayModel]['id']), array('title'=>'Excluir', 'class'=>'excluir'), sprintf('Deseja realmente excluir a categoria %s?',  $val[$displayModel][$displayField]));
			$actions = $this->Html->tag('span' , $editar.' '.$excluir, array('class'=>'actions'));

			$output[] = $this->Html->tag('li', $item.' '.$actions."\n".$inner);
		}
		return $this->Html->tag('ul', implode("\n", $output) , array('class'=> $class));
	}

	function select($fieldName, $list = array(), $selected = null, $options){
		$empty = false;
		if(isset($options['empty']))
			$empty = $options['empty'];
		
		$attributes = $options;
		
		$model = $attributes['model'];
		$field = $attributes['field'];
		unset($attributes['model'], $attributes['field']);

		$attributes = $this->_initInputField($fieldName, array_merge((array)$attributes, array('secure' => false)));
		
		if(!isset($selected))
			$selected = $attributes['value'];
		
		$select = array();
		$select[] = sprintf($this->Html->tags['selectstart'], $attributes['name'], $this->_parseAttributes($attributes, array('name', 'value')));
		
		if($empty)
			$select[] = sprintf($this->Html->tags['selectoption'], '', null, $empty);
		
		$select = array_merge($select, $this->__selectOptions($list,$selected, $model, $field));

		$select[] = $this->Html->tags['selectend'];
		return $this->output(implode("\n", $select));
	}
	
	function __selectOptions($elements = array(), $selected = null, $model = null, $field = null, $level = 0){
		$select = array();
		
		foreach($elements as $idx => $element) {
			$name = $element[$model]['id'];
			$title = str_repeat('-', $level*2).' '.Inflector::humanize(strtolower($element[$model][$field]));
			
			$htmlOptions = array();
			if(!empty($selected) && ($name == $selected))
				$htmlOptions['selected'] = 'selected';
			
			$select[] = sprintf($this->Html->tags['selectoption'],$name, $this->_parseAttributes($htmlOptions), $title);
			
			if(isset($element['children'][0]))
				$select[] = implode("\n", $this->__selectOptions($element['children'], $selected, $model, $field, $level+1));
		}
		return $select;
	}
	
	function itensDynatree($objetos, $parent_id = null) {
	    $items = ($parent_id == null ? "[" : "");
	    foreach ($objetos as $objeto) {
	        $chave = (empty($parent_id) ? "" : $parent_id.".").$objeto['ObjetoAcl']['id'];
	        $aco_string = trim($objeto['ObjetoAcl']['aco_string']);
	        $selected = ((isset($this->data['Permissao'][$aco_string]) && $this->data['Permissao'][$aco_string]) ? 1 : 0);
	        if (empty($aco_string)) 
	            $input = "";
	        else
	            $input = '<span style="display:none">'.$aco_string.'</span>'; //<input type="hidden" value="'.$selected.'" id="key'.str_replace('.','_',$chave).'" name="data[Permissao]['.$aco_string.']" aco_string="'.$aco_string.'">';
	        $item = "{";
	        $item.= "title:'".$objeto['ObjetoAcl']['descricao'].$input."'";
	        $item.= ',key:"key'.$chave.'"';
	        //if (empty($aco_string)) {
	        //    $item.= ',unselectable:true';
	        //} else {
    	        if ($selected) {
    	            $item.= ',select:true';
    	        }
    	    //}
	        if (!empty($objeto['children'])) {
	            $item.= ', children:['.$this->itensDynatree($objeto['children'], $chave).']';
	        }
	        $item .= "}";
	        $items.= $item .",";
	    }
	    if (substr($items, strlen($items)-1,1) == ',')
	        $items = substr($items, 0, strlen($items)-1);
	    $items .= ($parent_id == null ? "]" : "");
	    return $items;
	}

	function itensDynatreeLog($objetos, $permissoes, $parent_id = null) {
	    $items = ($parent_id == null ? "[" : "");
	    foreach ($objetos as $objeto) {
	        $chave = (empty($parent_id) ? "" : $parent_id.".").$objeto['ObjetoAcl']['id'];
	        $aco_string = trim($objeto['ObjetoAcl']['aco_string']);
	        $selected = ((isset($permissoes['Permissao'][$aco_string]) && $permissoes['Permissao'][$aco_string]) ? 1 : 0);
	        if (empty($aco_string)) 
	            $input = "";
	        else
	            $input = '<span style="display:none">'.$aco_string.'</span>'; //<input type="hidden" value="'.$selected.'" id="key'.str_replace('.','_',$chave).'" name="data[Permissao]['.$aco_string.']" aco_string="'.$aco_string.'">';
	        $item = "{";
	        $item.= "title:'".$objeto['ObjetoAcl']['descricao'].$input."'";
	        $item.= ',key:"key'.$chave.'"';
	        //if (empty($aco_string)) {
	        //    $item.= ',unselectable:true';
	        //} else {
    	        if ($selected) {
    	            $item.= ',select:true';
    	        }
    	    //}
	        if (!empty($objeto['children'])) {
	            $item.= ', children:['.$this->itensDynatreeLog($objeto['children'], $permissoes, $chave).']';
	        }
	        $item .= "}";
	        $items.= $item .",";
	    }
	    if (substr($items, strlen($items)-1,1) == ',')
	        $items = substr($items, 0, strlen($items)-1);
	    $items .= ($parent_id == null ? "]" : "");
	    return $items;
	}

	function itensTextLog($objetos, $permissoes, $parent_id = null) {
        foreach ($objetos as $key => $objeto) {
            $chave = (empty($parent_id) ? "" : $parent_id.".").$objeto['ObjetoAcl']['id'];
            $aco_string = trim($objeto['ObjetoAcl']['aco_string']);
            $selected = ((isset($permissoes['Permissao'][$aco_string]) && $permissoes['Permissao'][$aco_string]) ? 1 : 0);
            
            if ($selected) {
                $objetos[$key]['ObjetoAcl']['selecionado'] = true;
            }

            if (!empty($objeto['children'])) {
                $objetos[$key]['children'] = $this->itensTextLog($objeto['children'], $permissoes, $chave);
            }
        }
	    
	    return $objetos;
	}

    function itensConverteArray($objetos)
    {
        $return = array();
		foreach ($objetos as $key => $objeto) {
			if(isset($objeto['ObjetoAcl']['selecionado']) && $objeto['ObjetoAcl']['selecionado']){
                $return[] = $objeto['ObjetoAcl']['descricao'];
			}

			if (!empty($objeto['children'])) {
				foreach ($objeto['children'] as $key => $objeto2) {
					if (isset($objeto2['ObjetoAcl']['selecionado']) && $objeto2['ObjetoAcl']['selecionado']) {
						$return[] = $objeto['ObjetoAcl']['descricao'] . ' > ' . $objeto2['ObjetoAcl']['descricao'];
					}
					
					if (!empty($objeto2['children'])) {
						foreach ($objeto2['children'] as $key => $objeto3) {
							if (isset($objeto3['ObjetoAcl']['selecionado']) && $objeto3['ObjetoAcl']['selecionado']) {
								$return[] = $objeto['ObjetoAcl']['descricao'] . ' > ' . $objeto2['ObjetoAcl']['descricao'] . ' > ' . $objeto3['ObjetoAcl']['descricao'];
							}

							if (!empty($objeto3['children'])) {
								foreach ($objeto3['children'] as $key => $objeto4) {
									if (isset($objeto4['ObjetoAcl']['selecionado']) && $objeto4['ObjetoAcl']['selecionado']) {
										$return[] = $objeto['ObjetoAcl']['descricao'] . ' > ' . $objeto2['ObjetoAcl']['descricao'] . ' > ' . $objeto3['ObjetoAcl']['descricao'] . ' > ' . $objeto4['ObjetoAcl']['descricao'];
									}
								}
							}
						}
					}
				}
			}
		}

        return $return;
    }
}
?>