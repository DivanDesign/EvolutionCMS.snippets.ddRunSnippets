<?php
/**
 * ddRunSnippets
 * @version 2.2 (2012-04-09)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddrunsnippets
 * 
 * @copyright 2011–2012 DD Group {@link https://DivanDesign.biz }
 */

//Подключаем modx.ddTools
require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

$resArr = array();
$result = '';

//Все параметры сниппета (скопируем, чтобы в глобал не срать)
$ddParams = $params;

$i = isset($ddParams['snipName']) ? '' : 0;

while(isset($ddParams['snipName'.$i])){
	//Если имена параметров и их значения заданы
	if (isset($ddParams['snipParams'.$i]) && isset($ddParams['snipValues'.$i])){
		//Подставляем в названия параметров и в значения (смотря куда надо) результат работы предыдущего сниппета
		$ddParams['snipParams'.$i] = ddTools::parseText([
			'text' => $ddParams['snipParams'.$i],
			'data' => $resArr,
			'placeholderPrefix' => '+',
			'placeholderSuffix' => '+'
		]);//str_replace('+ddresult+', $resArr, $ddParams['snipParams'.$i]);
		$ddParams['snipValues'.$i] = ddTools::parseText([
			'text' => $ddParams['snipValues'.$i],
			'data' => $resArr,
			'placeholderPrefix' => '+',
			'placeholderSuffix' => '+'
		]);//str_replace('+ddresult+', $resArr, $ddParams['snipValues'.$i]);
	
		//Разбиваем на массивы
		$ddParams['snipParams'.$i] = explode(',', $ddParams['snipParams'.$i]);
		$ddParams['snipValues'.$i] = explode('##', $ddParams['snipValues'.$i]);
		
		//Если размеры массивов параметров и значений совпадают
		if (count($ddParams['snipParams'.$i]) == count($ddParams['snipValues'.$i])) $snipParamsArr = array_combine($ddParams['snipParams'.$i], $ddParams['snipValues'.$i]);
	}
	
	//Если что-то передали
	if ($snipParamsArr){
		$resArr['ddresult'.$i] = $modx->runSnippet($ddParams['snipName'.$i], $snipParamsArr);
	}else{
		$resArr['ddresult'.$i] = $modx->runSnippet($ddParams['snipName'.$i]);
	}
	
	if ($i === '') $i = 0; else $i++;
}

$num = isset($num) ? explode(',', $num) : array('last');
$glue = isset($glue) ? $glue : '';

//Если задан шаблон для вывода
if (isset($tpl) && $tpl != ''){
	//Если есть дополнительные данные
	if (isset($placeholders)){
		//Разбиваем их
		$resArr = array_merge($resArr, ddTools::explodeAssoc($placeholders));
	}
	
	$result .= $modx->parseChunk($tpl, $resArr, '[+','+]');
//Если шаблон не задан
}else{
	//Если надо вывести всё
	if (in_array('all', $num)){
		$result .= implode($glue, $resArr);
	}else{
		//Перебираем массив номеров результатов сниппетов
		foreach ($num as $n){
			//Если номер не номер или такого элемента нет
			if (!is_numeric($n) || !$resArr['ddresult'.$n]){
				//Тупо выводим последний
				$n = $i - 1;
			}
		
			//Выводим нужный
			$result .= $resArr['ddresult'.$n];
		}
	}
}

//Если надо, выводим в плэйсхолдер
if (isset($toPlaceholder) && $toPlaceholder == '1'){
	$modx->setPlaceholder(isset($placeholderName) ? $placeholderName : 'ddRunSnippets', $result);
}else{
	return $result;
}
?>