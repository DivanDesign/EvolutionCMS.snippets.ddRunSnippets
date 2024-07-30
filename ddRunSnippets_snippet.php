<?php
/**
 * ddRunSnippets
 * @version 4.2 (2024-07-30)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddrunsnippets
 * 
 * @copyright 2011–2024 Ronef {@link https://Ronef.me }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => $params,
]);
?>