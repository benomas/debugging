Pasos para habilitar

-Agregar la carpeta debugg en la carpeta htdocs, www, o cualquiera donde se procesen archivos php

-A�adimos en php.ini la siguiente linea:
	auto_prepend_file = "{ruta donde colocaron la carpeta}debugg/debugging.php"

En mi caso, tengo mi carpeta "debugg" en �C:/www� y agrego la siguiente l�nea a php.ini

auto_prepend_file = "C:/www/debugg/debugging.php"	
	

Reiniciar apache para que se apliquen los cambios y despu�s simplemente mandamos llamar la funci�n debugg($variable,$nombre_var), desde cualquier l�nea de c�digo php.
Los par�metros $variable  y $nombre_var son ambos opcionales, no requiere ninguna libreria, ni plugin, es php y javascript puro

Se aceptan sugerencias y aportaciones

Saludos 


Modificaci�n de Jes�s parte 2
Otra linea