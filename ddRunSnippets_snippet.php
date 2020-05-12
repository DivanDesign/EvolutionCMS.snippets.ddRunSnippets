<?php
/**
 * ddRunSnippets
 * @version 2.1 (2012-03-18)
 * 
 * Запускает необходимые сниппеты с необходимыми параметрами.
 * Если нужно запустить несколько сниппетов, просто указывайте параметры как snipName0, snipName1 и т.д. (snipParams и snipValues соответственно).
 * 
 * @param snipName {string} - Имя сниппета, который нужно вызвать.
 * @param snipParams {comma separated string} - Имена параметров, которые нужно передать, разделённые запятыми. Используйте +ddresultN+ (где N - номер сниппета) для подстановки результата выполнения любого предыдущего сниппета.
 * @param snipValues {separated string} - Значения параметров, которые нужно передать (в соответствии с именами), разделённые через '##'. Используйте +ddresultN+ (где N - номер сниппета) для подстановки результата выполнения предыдущего сниппета.
 * @param tpl {string: chunkName} - Чанк для вывода результатов. Доступные плэйсхолдеры: [+ddresultN+] (где N - номер сниппета).
 * @param placeholders {separated string} - Дополнительные данные, которые необходимо передать в чанк «tpl». Формат: строка, разделённая '::' между парой ключ-значение и '||' между парами.
 * @param toPlaceholder {0; 1} - Возвращать ли результат в плэйсхолдер «placeholderName». По умолчанию: 0.
 * @param placeholderName {string} - Имя плэйсхолдера при возврате через 'toPlaceholder'. По умолчанию: 'ddRunSnippets'.
 * 
 * @copyright 2011–2012, DivanDesign
 * http://www.DivanDesign.ru
 */

//Подключаем modx.ddTools
require_once $modx->config['base_path'].'assets/snippets/ddTools/modx.ddtools.class.php';

$result = array();

//Все параметры сниппета (скопируем, чтобы в глобал не срать)
$ddParams = $params;

$i = isset($ddParams['snipName']) ? '' : 0;

while(isset($ddParams['snipName'.$i])){
	//Если имена параметров и их значения заданы
	if (isset($ddParams['snipParams'.$i]) && isset($ddParams['snipValues'.$i])){
		//Подставляем в названия параметров и в значения (смотря куда надо) результат работы предыдущего сниппета
		$ddParams['snipParams'.$i] = ddTools::parseText($ddParams['snipParams'.$i], $result, '+', '+');//str_replace('+ddresult+', $result, $ddParams['snipParams'.$i]);
		$ddParams['snipValues'.$i] = ddTools::parseText($ddParams['snipValues'.$i], $result, '+', '+');//str_replace('+ddresult+', $result, $ddParams['snipValues'.$i]);
		
		//Разбиваем на массивы
		$ddParams['snipParams'.$i] = explode(',', $ddParams['snipParams'.$i]);
		$ddParams['snipValues'.$i] = explode('##', $ddParams['snipValues'.$i]);
		
		//Если размеры массивов параметров и значений совпадают
		if (count($ddParams['snipParams'.$i]) == count($ddParams['snipValues'.$i])) $snipParamsArr = array_combine($ddParams['snipParams'.$i], $ddParams['snipValues'.$i]);
	}
	
	//Если что-то передали
	if ($snipParamsArr){
		$result['ddresult'.$i] = $modx->runSnippet($ddParams['snipName'.$i], $snipParamsArr);
	}else{
		$result['ddresult'.$i] = $modx->runSnippet($ddParams['snipName'.$i]);
	}
	
	if ($i === '') $i = 0; else $i++;
}

//Если задан шаблон для вывода
if (isset($tpl) && $tpl != ''){
	//Если есть дополнительные данные
	if (isset($placeholders)){
		//Разбиваем по парам
		$placeholders = explode('||', $placeholders);
		foreach ($placeholders as $val){
			//Разбиваем на ключ-значение
			$val = explode('::', $val);
			$result[$val[0]] = $val[1];
		}
	}
	
	$result = $modx->parseChunk($tpl, $result, '[+','+]');
}else{
	//Если шаблон не задан, просто вернём последний результат
	$result = $result['ddresult'.($i - 1)];
}

//Если надо, выводим в плэйсхолдер
if (isset($toPlaceholder) && $toPlaceholder == '1'){
	$modx->setPlaceholder(isset($placeholderName) ? $placeholderName : 'ddRunSnippets', $result);
}else{
	return $result;
}
?>