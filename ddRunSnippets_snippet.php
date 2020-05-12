<?php
/**
 * ddRunSnippets
 * @version 1.0 (2011-12-12)
 * 
 * Запускает необходимый сниппет с необходимыми параметрами.
 * 
 * @param name {string} - Имя сниппета, который нужно вызвать.
 * @param parameters {comma separated string} - Имена параметров, которые нужно передать.
 * @param values {separated string} - Значения параметров, которые нужно передать (в соответствии с именами), разделённые через '##'.
 * @param tpl {string} - Шаблон внешней обёртки. Доступные плэйсхолдеры: [+wrapper+].
 * @param placeholders {separated string} - Дополнительные данные, которые необходимо передать в чанк «tpl». Формат: строка, разделённая '::' между парой ключ-значение и '||' между парами.
 * 
 * @copyright 2011, DivanDesign
 * http://www.DivanDesign.ru
 */

if (isset($name)){
	//Если имена параметров и их значения заданы
	if (isset($parameters) && isset($values)){
		//Разбиваем на массивы
		$parameters = explode(',', $parameters);
		$values = explode('##', $values);
		
		//Если размеры массивов параметров и значений совпадают
		if (count($parameters) == count($values)) $paramsArr = array_combine($parameters, $values);
	}
	
	//Если что-то передали
	if ($paramsArr){
		$result = $modx->runSnippet($name, $paramsArr);
	}else{
		$result = $modx->runSnippet($name);
	}
	
	//Если хоть что-то получили
	if ($result != ''){
		//Если задан шаблон для вывода
		if (isset($tpl)){
			$res = array();
			
			//Элемент массива 'wrapper' должен находиться самым первым, иначе дополнительные переданные плэйсхолдеры в тексте не найдутся!
			$res['wrapper'] = $result;
			
			//Если есть дополнительные данные
			if (isset($placeholders)){
				//Разбиваем по парам
				$placeholders = explode('||', $placeholders);
				foreach ($placeholders as $val){
					//Разбиваем на ключ-значение
					$val = explode('::', $val);
					$res[$val[0]] = $val[1];
				}
			}
			$result = $modx->parseChunk($tpl, $res, '[+','+]');
		}
		
		return $result;
	}
}
?>