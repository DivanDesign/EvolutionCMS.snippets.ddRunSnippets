<?php
/**
 * ddRunSnippets
 * @version 4.0 (2023-03-30)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddrunsnippets
 * 
 * @copyright 2011–2023 Ronef {@link https://Ronef.ru }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => $params
]);
?>