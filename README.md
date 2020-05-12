# (MODX)EvolutionCMS.snippets.ddRunSnippets

Snippet runs necessary snippets with necessary params.
Result can be returned into chunk `tpl`, with additional data into `placeholders` param.


## Documentation


### Installation

Elements → Snippets: Create a new snippet with the following data:

1. Snippet name: `ddRunSnippets`.
2. Description: `<b>1.0</b> Snippet description.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddRunSnippets_snippet.php` file from the archive.


### Parameters description

* `name`
	* Desctription: Snippet name to run.
	* Valid values: `stringSnippetName`
	* **Required**
	
* `parameters`
	* Desctription: Params names to be transfered.
	* Valid values: `stringCommaSeparated`
	* Default value: —
	
* `values`
	* Desctription: Parameters values to be transfered (according to names), separated by `'##'`.
	* Valid values: `stringSeparated`
	* Default value: —
	
* `tpl`
	* Desctription: Chunk for output results.  
		Available placeholders:
		* `[+wrapper+]`
	* Valid values: `stringChunkName`
	* Default value: —
	
* `placeholders`
	* Desctription: Additional data to be transfered into the chunk `tpl`.  
		Format: string, separated by `'::'` between a pair of key-value, and `'||'` between the pairs.
	* Valid values: `stringSeparated`
	* Default value: —


## [Home page →](https://code.divandesign.biz/modx/ddrunsnippets)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />