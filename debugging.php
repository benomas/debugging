<?
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
?>