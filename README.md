# (MODX)EvolutionCMS.snippets.ddRunSnippets

Snippet runs necessary snippets with necessary params. Capabilities:

* Run a few snippets consequentially.
* Snippets results can be sent into parameters names and/or into other snippets values (can repeat this as much as you like).
* The snippet result can be returned into the chunk `tpl`, have transferring additional data through the parameter `placeholders`.
* Final result can be returned into placeholder (see the `toPlaceholder` and the `placeholderName` params).

See the documentation for a more complete picture.


## Requires

* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.2


## Documentation


### Installation

Elements → Snippets: Create a new snippet with the following data:

1. Snippet name: `ddRunSnippets`.
2. Description: `<b>2.2</b> Snippet runs necessary snippets with necessary params.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddRunSnippets_snippet.php` file from the archive.


### Parameters description

If needs to run a few snippets just set the parameters as `snipName0`, `snipName1`, etc (`snipParams` & `snipValues` respectively).

* `snipName[$i]`
	* Desctription: Snippet name to run.
	* Valid values: `stringSnippetName`
	* **Required**
	
* `snipParams[$i]`
	* Desctription: Params names to be transfered.  
		Use `+ddresultN+` (where `N` — number of the snippet) for substitution by any previous snippet execution result.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `snipValues[$i]`
	* Desctription: Parameters values to be transfered (according to names), separated by `'##'`.  
		Use `+ddresultN+` (where `N` — number of the snippet) for substitution by any previous snippet execution result.
	* Valid values: `stringSeparated`
	* Default value: —
	
* `tpl`
	* Desctription: Chunk for output results.  
		Available placeholders:
		* `[+ddresultN+]` — a snippet result (where `N` — number of the snippet).
	* Valid values: `stringChunkName`
	* Default value: —
	
* `placeholders`
	* Desctription: Additional data to be transfered into the chunk `tpl`.  
		Format: string, separated by `'::'` between a pair of key-value, and `'||'` between the pairs.
	* Valid values: `stringSeparated`
	* Default value: —
	
* `toPlaceholder`
	* Desctription: Return final result into placeholder `placeholderName`.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `placeholderName`
	* Desctription: Name of placeholder if results returns into placeholder (`toPlaceholder` == `1`).
	* Valid values: `string`
	* Default value: `'ddRunSnippets'`
	
* `num`
	* Desctription: Snippet number(s), result of which have to be returned (with returning without a template).
	* Valid values:
		* `stringCommaSeparated`
		* `'last'`
		* `'all'`
	* Default value: `'last'`
	
* `num[$i]`
	* Desctription: Snippet number.
	* Valid values: `integer`
	* Default value: —
	
* `glue`
	* Desctription: String for joining results (with returning without a template).
	* Valid values: `string`
	* Default value: —


## [Home page →](https://code.divandesign.biz/modx/ddrunsnippets)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />