<? 
/*
* Revisamos si se esta ejecutando php desde ambiente local, si es asi permitimos acceso a las definiciones de las funciones para debuggeo
* 
*/
$current_ip=gethostbyname(trim(`hostname`));
$regex='/\.[0-9]*?$/';
$sub_red=preg_replace($regex,'',$current_ip);
if	(	$sub_red =='10.0.1')
{
/********************************************************************************
 * paquete de funciones para debuguear
 * Creado por: Beny
 *******************************************************************************/
function var_printer($var)
{
	$print_buffer='';
	$altern='style="background-color:WHITE;"';
	$normal='style="background-color:WHITE;"';
	$other_type=TRUE;
	if(is_scalar($var))
	{
		if(is_bool($var))
		{
			if($var)
				$print_buffer.="TRUE";
			else
				$print_buffer.="FALSE";
		}
		else
			$print_buffer.=$var;
			
		$other_type=FALSE;
	}
	if(is_array($var))
	{
		$GLOBALS["noEscalar_count"]++;
		if(empty($var))
			$print_buffer.='Array(empty)';
		else
		{
			$fil_cont=1;
			$table_id='array_'.$GLOBALS["noEscalar_count"];
			$print_buffer.='Array <span   onclick="showNoEscalar(this,\''.$table_id.'\')"  class="button_no_escalar show_no_escalar">+</span> <table class="no_escalar_hidden" id="'.$table_id.'" style="border-style:dashed;border-width:1px; padding-left:0px; background-color:#F9C5A0;">';
			foreach($var AS $campo=>$valor)
			{
				if($fil_cont++%2==0)
					$fil_style =$altern;
				else
					$fil_style = $normal;
				$print_buffer.='<tr '.$fil_style.'>';
					$print_buffer.='<td style="font-weight:bold;">'.$campo.'</td><td> = </td>';
					$print_buffer.='<td >';
					$print_buffer.=	'<div class="">'.var_printer($valor).'</div></td>';
				$print_buffer.='</tr>';
			}
			$print_buffer.='</table>';
		}
		$other_type=FALSE;
	}
	if(is_object($var))
	{
		$GLOBALS["noEscalar_count"]++;
		if(empty($var))
			$print_buffer.='Object(empty)';
		else
		{
			if(get_class($var)==='SimpleXMLElement')
			{
				$out_var=array();
				foreach ( (array) $var as $index_var => $node_var )
					$out_var[$index_var] = ( is_object ( $node_var ) ) ? xml2array ( $node_var ) : $node_var;
				$var=$out_var;
				$out_var=array();
			}
			
			$fil_cont=1;
			$table_id='object_'.$GLOBALS["noEscalar_count"];
			$print_buffer.='Object <span   onclick="showNoEscalar(this,\''.$table_id.'\')"  class="button_no_escalar show_no_escalar">+</span> <table class="no_escalar_hidden" id="'.$table_id.'" style="border-style:dashed;border-width:1px; padding-left:0px; background-color:#F9C5A0;">';
			foreach($var AS $campo=>$valor)
			{
				if($fil_cont++%2==0)
					$fil_style =$altern;
				else
					$fil_style = $normal;
				$print_buffer.='<tr '.$fil_style.'>';
					$print_buffer.='<td style="text-decoration: underline; font-weight:bold;">'.$campo.'</td><td> = </td>';
					$print_buffer.='<td >';
					$print_buffer.=	'<div class="">'.var_printer($valor).'</div></td>';
				$print_buffer.='</tr>';
			}
			$print_buffer.='</table>';
		}
		$other_type=FALSE;
	}
	if($other_type)
	{
		if($var==NULL)
			$print_buffer.="NULL";
		else
		{
			$print_buffer.="Resource";
		}
	}
	return $print_buffer;
	
}

/********************************************************************************
 * recibe 2 parametros opcionales
 *	-$debugin_var:			nombre de la variable objetivo
 *	-$debuggin_var_name: 	alias para la variable objetivo
 * regresa
 * 	-Si esta definido $debuggin_var y es de tipo "escalar", "objeto" o "array". Imprime en pantalla un html con su representacion, navegable por niveles mediante javascript
 *	-Si esta definido $debuggin_var_name, utiliza este valor como alias para $debuggin_var
 * 	-Imprime el numero de linea donde se genero la llamada a debugg
 * 	-Imprime la ruta y nombre del archivo donde se genero la llamada a debugg
 * la ejecucion continua
 * Creado por: Beny
 *******************************************************************************/

function debugg($debugin_var='',$debuggin_var_name='')
{	
	$ruta= dirname (__FILE__);
	if(empty($GLOBALS["debugg_step"]))
	{
		$GLOBALS["debugg_step"] = 1;
		$GLOBALS["noEscalar_count"] = 1;
	}
	else
	{
		$GLOBALS["debugg_step"] ++;
	}
	
	$objet_trace = debug_backtrace();
	$linea=$objet_trace[0]['line'];
	$archivo=$objet_trace[0]['file'];
	$function_launcher = $objet_trace[1]['function'];
	$id_debugg  = 'id_debugg_'.$GLOBALS["debugg_step"];
	if($GLOBALS["debugg_step"]==1)
	{
	?>	
		<style>
			
			.button_debugg
			{
				width:25px;
				height:25px;
				margin-top:10px;
			}
			
			.button_debugg:hover
			{
				cursor:pointer;
			}
			
			.button_no_escalar
			{
				width:25px;
				height:25px;
			}
			
			.button_no_escalar:hover
			{
				cursor:pointer;
			}
			
			
			.show
			{
				background-color:#F9C5A0;
				background-image: url("/debugg/mas.png");
				
			}
			
			.hide
			{
				background-color:WHITE;
				background-image: url("/debugg/menos.png");
			}
			
			.show_no_escalar
			{
				background-color:#F9D9A0;
				padding-left:3px;
				padding-right:3px;
			}
			
			.hide_no_escalar
			{
				background-color:#FCEACA;
				padding-left:5px;
				padding-right:5px;
			}
			
			.debugg_content
			{
				background-color:#F9C5A0; 
				border-style:dashed ; 
				border-width:1px; 
				padding:5px; 
				box-shadow: 3px 4px 3px #AAAAAA; 
				margin: -25px 26px 28px 28px; 
				z-index: 1000; 
			}
			
			.hide_debugg_content
			{
				display:none;
			}
			
			.no_escalar_hidden
			{
				display:none;
			}
			.no_escalar_visible
			{
				
			}
		</style>
		<script>
			
			function hasClass( elem, klass ) 
			{
				return (" " + elem.className + " " ).indexOf( " "+klass+" " ) > -1;
			}
			
			function addClass(elem, klass)
			{
				elem.className = elem.className + " " + klass;
			}
			
			function removeClass(elem, klass)
			{
				if (hasClass(elem,klass)) 
				{
					var remplace = new RegExp('(\\s|^)'+klass+'(\\s|$)');
						elem.className=elem.className.replace(remplace,' ');
				}
			}
			
			function showDebugg(button_id,div_id)
			{
				var currentElement = document.getElementById(div_id);
				var currentButton = button_id;
				if(hasClass(currentElement,'debugg_content'))
				{
					removeClass(currentElement,'debugg_content');
					addClass(currentElement,'hide_debugg_content');
					removeClass(currentButton,'hide');
					addClass(currentButton,'show');
				}
				else
				{
					removeClass(currentElement,'hide_debugg_content');
					addClass(currentElement,'debugg_content');
					removeClass(currentButton,'show');
					addClass(currentButton,'hide');
				}
			}
			
			function showNoEscalar(button_id,div_id)
			{
				var currentElement = document.getElementById(div_id);
				var currentButton = button_id;
				if(hasClass(currentElement,'no_escalar_visible'))
				{
					removeClass(currentElement,'no_escalar_visible');
					addClass(currentElement,'no_escalar_hidden');
					removeClass(currentButton,'hide_no_escalar');
					addClass(currentButton,'show_no_escalar');
					currentButton.innerHTML = "+";
				}
				else
				{
					removeClass(currentElement,'no_escalar_hidden');
					addClass(currentElement,'no_escalar_visible');
					removeClass(currentButton,'show_no_escalar');
					addClass(currentButton,'hide_no_escalar');
					currentButton.innerHTML = "-";
				}
			}


		</script>
		<?
	}
	?>
		<div onclick="showDebugg(this,'<?=$id_debugg ?>')" class="button_debugg show">
		</div>
		<div id="<?=$id_debugg?>" class="hide_debugg_content"> Debugging <br>
	<?
	
	if(!empty($function_launcher))
	{
		$function_launcher = "<br> Function -> <b>" . $function_launcher. "</b>";
	}
	
	if(!empty($linea))
	{
		$linea = "<br> Line -> <b>" . $linea. "</b>";
	}
	
	if(!empty($archivo))
	{
		$archivo = "<br> File -> <b>" . $archivo ."</b>";
	}
	
	if(!empty($debugin_var) || is_bool($debugin_var))
	{
		if(!empty($debuggin_var_name))
		{
			echo "<b>".$debuggin_var_name."</b> <pan style='padding-left:15px;'>";
			
			?>
				<script>
					/*var debug_objet<?=$GLOBALS["debugg_step"]?> = <?=json_encode($debugin_var)?>;
					var debug_objet_name<?=$GLOBALS["debugg_step"]?> = <?=$debuggin_var_name?>;*/
				</script>
			<?
			echo var_printer($debugin_var);
			echo "</span><br>";
			echo $function_launcher.$linea.$archivo;
		}
		else
		{
			echo "<pan style='padding-left:15px;'>";
			
			?>
				<script>
					/*var debug_objet<?=$GLOBALS["debugg_step"]?> = <?=json_encode($debugin_var)?>;*/
				</script>
			<?
			echo var_printer($debugin_var);
			echo "</span><br>";
		}
		echo " <b>Launch info:</b> ".$function_launcher.$linea.$archivo;
	}	
	else
	{
		echo " <b>Launch info:</b> ".$function_launcher.$linea.$archivo;
	}
		echo '</div>';
		
	//die();
}

/********************************************************************************
 * recibe 2 parametros opcionales
 *	-$debugin_var:			nombre de la variable objetivo
 *	-$debuggin_var_name: 	alias para la variable objetivo
 * regresa
 * 	-Si esta definido $debuggin_var hace un print_r
 *	-Si esta definido $debuggin_var_name, utiliza este valor como alias para $debuggin_var
 * 	-Imprime el numero de linea donde se genero la llamada a debugg
 * 	-Imprime la ruta y nombre del archivo donde se genero la llamada a debugg
 * la ejecucion se detiene
 * Creado por: Beny
 *******************************************************************************/

function break_point($debugin_var='',$debuggin_var_name='')
{	
	$objet_trace = debug_backtrace();
	$linea=$objet_trace[0]['line'];
	$archivo=$objet_trace[0]['file'];
	$function_launcher = $objet_trace[1]['function'];
	
	echo '<div style="background-color:#F9C5A0; border-style:dashed ; border-width:1px; padding:5px; box-shadow: 3px 4px 3px #AAAAAA; margin: 20px; z-index: 1000;">';
	echo 'Break point';
	
	
	if(!empty($function_launcher))
	{
		$function_launcher = "<br> Function -> <b>" . $function_launcher. "</b>";
	}
	
	if(!empty($linea))
	{
		$linea = "<br> Line -> <b>" . $linea. "</b>";
	}
	
	if(!empty($archivo))
	{
		$archivo = "<br> File -> <b>" . $archivo ."</b>";
	}
	
	if(!empty($debugin_var))
	{
		if(!empty($debuggin_var_name))
		{
			echo " <b>".$debuggin_var_name."</b> Value=<br>{<br><pan style='padding-left:15px;'>";
			print_r($debugin_var);
			echo "</span><br>}<br>";
			echo $function_launcher.$linea.$archivo;
		}
		else
		{
			echo " <b>objet</b> Value=<br>{<br><pan style='padding-left:15px;'>";
			print_r($debugin_var);
			echo "</span><br>}<br>";
		}
	}	
	
	echo " <b>Launch info:</b> ".$function_launcher.$linea.$archivo;
	echo '</div>';
	die();
}

/********************************************************************************
 * alias de backtrace
 * Creado por: Beny
 *******************************************************************************/
 
function debugg_trace()
{
	$objet_trace = debug_backtrace();
	print_r($objet_trace);
	die();
}


 /********************************************************************************
 * recibe 1 parametro opcional
 *	-$forceOrigin:			para indicar si se permite recargar las variables post/get desde cualquier funcion, 
 *  						o unicamente desde la funcion orignal que los cargo
 * regresa
 *  -nada
 * guarda los valores get/post en session para ser accedidos despues por get_history_incoming
 * Creado por: Beny
 *******************************************************************************/
 
function listen_incoming($forceOrigin=FALSE)
{
	$socket='default';
	if($forceOrigin)
	{
		$objet_trace = debug_backtrace();
		$linea=$objet_trace[0]['line'];
		$archivo=$objet_trace[0]['file'];
		$function_launcher = $objet_trace[1]['function'];
		$socket=$archivo.$function_launcher;
	}
	
	if(!isset($_SESSION))	
		session_start();
	if(!isset($_SESSION['listen_count']))
		$_SESSION['listen_count']=0;
	
	$_SESSION['socket'][$_SESSION['listen_count']]=$socket;
	$_SESSION['listen_get'][$_SESSION['listen_count']]=$_GET;
	$_SESSION['listen_post'][$_SESSION['listen_count']]=$_POST;
	$_SESSION['listen_count']++;
}


 /********************************************************************************
 * recibe 3 parametros opcionales
 *	-$listenNumber			indica la secuencia de guardado que se quiere recargar, el default es 0
 *	-$method				para indicar a que variable se quiere recargar get/post default es ambas
 *	-$forceOrigin:			para indicar si se permite recargar las variables post/get desde cualquier funcion, 
 *  						o unicamente desde la funcion orignal que los cargo
 * regresa
 *  -nada
 * recarga los valores get/post de session guardados por listen_incoming
 * Creado por: Beny
 *******************************************************************************/

function get_history_incoming($listenNumber=0,$method='both',$forceOrigin=FALSE)
{
	$socket='default';
	if($forceOrigin)
	{
		$objet_trace = debug_backtrace();
		$linea=$objet_trace[0]['line'];
		$archivo=$objet_trace[0]['file'];
		$function_launcher = $objet_trace[1]['function'];
		$socket=$archivo.$function_launcher;
	}
	
	
	if(!isset($_SESSION[$listenNumber]))
		return FALSE;
	if( $_SESSION['socket'][$_SESSION[$listenNumber]]===$socket )
	{
		if(strcasecmp( $method,'get')==0 )
		{
			$_GET=$_SESSION['listen_get'][$listenNumber];
		}
		if(strcasecmp( $method,'post')==0 )
		{
			$_POST=$_SESSION['listen_post'][$listenNumber];
		}
		if(strcasecmp( $method,'both')==0 )
		{
			$_GET=$_SESSION['listen_get'][$listenNumber];
			$_POST=$_SESSION['listen_post'][$listenNumber];
		}
	}
}

 /********************************************************************************
 * sin parametros
 * regresa
 *  -nada
 * guarda los valores get/post en sesion para ser recargados despues por reload_last_incoming
 * Creado por: Beny
 *******************************************************************************/
function save_last_incoming()
{	
	if(!isset($_SESSION))	
		session_start();
	clear_incoming();
	
	$_SESSION['listen_count']=0;
	$_SESSION['listen_get'][$_SESSION['listen_count']]=$_GET;
	$_SESSION['listen_post'][$_SESSION['listen_count']]=$_POST;
	$_SESSION['listen_count']++;
}

/********************************************************************************
 * alias para save_last_incoming
 * Creado por: Beny
 *******************************************************************************/
 
function debcarga()
{
	save_last_incoming();
}

 /********************************************************************************
 * sin parametros
 * regresa
 *  -nada
 * recarga los valores get/post guardados por save_last_incoming
 * Creado por: Beny
 *******************************************************************************/
function reload_last_incoming()
{
	$_GET=$_SESSION['listen_get'][($_SESSION['listen_count']-1)];
	$_POST=$_SESSION['listen_post'][($_SESSION['listen_count']-1)];
}

/********************************************************************************
 * alias para reload_last_incoming
 * Creado por: Beny
 *******************************************************************************/
 
function debrecarga()
{
	reload_last_incoming();
}

/********************************************************************************
 * alias para reload_last_incoming
 * Creado por: Beny
 *******************************************************************************/
 
function form_cache($save=FALSE)
{	
	if(!isset($_SESSION['listen_count']))
	{
		save_last_incoming();
		return TRUE;
	}
	if($save)
	{
		save_last_incoming();
		return TRUE;
	}
	else
		reload_last_incoming();
	return false;
}

 /********************************************************************************
 * sin parametros
 * regresa
 *  -nada
 * borra los datos de session de incoming
 * Creado por: Beny
 *******************************************************************************/
 
function clear_incoming()
{
	if(isset($_SESSION['listen_count']))
		unset($_SESSION['listen_count']);
	if(isset($_SESSION['listen_get']))
		unset($_SESSION['listen_get']);
	if(isset($_SESSION['listen_post']))
		unset($_SESSION['listen_post']);	
}

function xml2array($contents, $get_attributes=1, $priority = 'tag') 
{ 
    if(!$contents) return array(); 

    if(!function_exists('xml_parser_create')) 
	{ 
        //print "'xml_parser_create()' function not found!"; 
        return array(); 
    } 

    //Get the XML parser of PHP - PHP must have this module for the parser to work 
    $parser = xml_parser_create(''); 
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, trim($contents), $xml_values); 
    xml_parser_free($parser); 

    if(!$xml_values) return;//Hmm... 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array; //Refference 

    //Go through the tags. 
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
    foreach($xml_values as $data) 
	{ 
        unset($attributes,$value);//Remove existing values, or there will be trouble 

        //This command will extract these variables into the foreach scope 
        // tag(string), type(string), level(int), attributes(array). 
        extract($data);//We could use the array by itself, but this cooler. 

        $result = array(); 
        $attributes_data = array(); 
         
        if(isset($value)) 
		{ 
            if($priority == 'tag') $result = $value; 
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
        } 

        //Set the attributes too. 
        if(isset($attributes) and $get_attributes) 
		{ 
            foreach($attributes as $attr => $val) 
			{ 
                if($priority == 'tag') $attributes_data[$attr] = $val; 
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
            } 
        } 

        //See tag status and do the needed. 
        if($type == "open") {//The starting of the tag '<tag>' 
            $parent[$level-1] = &$current; 
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) 
			{ //Insert New tag 
                $current[$tag] = $result; 
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 

                $current = &$current[$tag]; 

            } 
			else 
			{ //There was another element with the same tag name 

                if(isset($current[$tag][0])) 
				{//If there is a 0th element it is already an array 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                    $repeated_tag_index[$tag.'_'.$level]++; 
                } else 
				{//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2; 
                     
                    if(isset($current[$tag.'_attr'])) 
					{ //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                        unset($current[$tag.'_attr']); 
                    } 

                } 
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                $current = &$current[$tag][$last_item_index]; 
            } 

        } 
		elseif($type == "complete") 
		{ //Tags that ends in 1 line '<tag />' 
            //See if the key is already taken. 
            if(!isset($current[$tag])) 
			{ //New Key 
                $current[$tag] = $result; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

            } 
			else 
			{ //If taken, put all things inside a list(array) 
                if(isset($current[$tag][0]) and is_array($current[$tag])) 
				{//If it is already an array... 

                    // ...push the new element into that array. 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                     
                    if($priority == 'tag' and $get_attributes and $attributes_data) 
					{ 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; 

                } else { //If it is not an array... 
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $get_attributes) 
					{ 
                        if(isset($current[$tag.'_attr'])) 
						{ //The attribute of the last(0th) tag must be moved as well
                             
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
                         
                        if($attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                } 
            } 

        } elseif($type == 'close') { //End of tag '</tag>' 
            $current = &$parent[$level-1]; 
        } 
    } 
     
    return($xml_array); 
}
}
?>