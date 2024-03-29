<?php
namespace ddRunSnippets;

class Cache {
	private string $cacheDir = '';
	private string $contentPrefix = '<?php die("Unauthorized access."); ?>';
	private int $contentPrefixLen = 37;
	
	/**
	 * __construct
	 * @version 1.0 (2023-05-03)
	 */
	public function __construct(){
		$this->cacheDir =
			//path to `assets`
			dirname(
				__DIR__,
				3
			) .
			'/cache/ddRunSnippets'
		;
		
		if (!is_dir($this->cacheDir)){
			\DDTools\FilesTools::createDir([
				'path' => $this->cacheDir
			]);
		}
	}
	
	/**
	 * getCache
	 * @version 1.0 (2023-05-03)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. @required
	 * @param $params->docId {integer} — Document ID related to cache. @required
	 * @param $params->name {string} — Unique cache name for the document. @required
	 * @param $params->data {string|array|stdClass} — Data to save. @required
	 * 
	 * @return {void}
	 */
	public function createCache($params) :void {
		$params = (object) $params;
		
		//str|obj|arr
		$dataType =
			is_object($params->data) ?
			'obj' :
			(
				is_array($params->data) ?
				'arr' :
				//All other types are considered as string (because of this we don't use the gettype function)
				'str'
			)
		;
		
		if ($dataType != 'str'){
			$params->data = \DDTools\ObjectTools::convertType([
				'object' => $params->data,
				'type' => 'stringJsonAuto'
			]);
		}
		
		//Save cache file
		file_put_contents(
			//Cache file path
			$this->buildCacheFilePath($params),
			//Cache content
			(
				$this->contentPrefix .
				$dataType .
				$params->data
			)
		);
	}
	
	/**
	 * getCache
	 * @version 1.0 (2023-05-03)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. @required
	 * @param $params->docId {integer} — Document ID related to cache. @required
	 * @param $params->name {string} — Unique cache name for the document. @required
	 * 
	 * @return {null|string|array|stdClass} — `null` means cache is not exist.
	 */
	public function getCache($params){
		$result = null;
		
		$filePath = $this->buildCacheFilePath($params);
		
		if (is_file($filePath)){
			//Cut PHP-code prefix
			$result = substr(
				file_get_contents($filePath),
				$this->contentPrefixLen
			);
			
			//str|obj|arr
			$dataType = substr(
				$result,
				0,
				3
			);
			
			//Cut dataType
			$result = substr(
				$result,
				3
			);
			
			if ($dataType != 'str'){
				$result = \DDTools\ObjectTools::convertType([
					'object' => $result,
					'type' =>
						$dataType == 'obj' ?
						'objectStdClass' :
						'objectArray'
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * clearCache
	 * @version 1.0 (2023-05-03)
	 * 
	 * @param Clear cache files for specified document or every documents.
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. Default: —.
	 * @param $params->docId {integer|null} — Document ID related to cache. Default: null (cache of all docs will be cleared).
	 * 
	 * @return {void}
	 */
	public function clearCache($params) :void {
		\DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'docId' => null
				],
				$params
			]
		]);
		
		//Clear all cache
		if (empty($params->docId)){
			\DDTools\FilesTools::removeDir($this->cacheDir);
		//Clear cache for specified documents
		}else{
			$files = glob(
				$this->cacheDir .
				'/doc' . $params->docId . '-*.php'
			);
			
			foreach (
				$files as
				$filepath
			){
				unlink($filepath);
			}
		}
	}
	
	/**
	 * buildCacheFilePath
	 * @version 1.0 (2023-05-03)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used. @required
	 * @param $params->docId {integer} — Document ID related to cache. @required
	 * @param $params->name {string} — Unique cache name for the document. @required
	 * 
	 * @return {string}
	 */
	private function buildCacheFilePath($params) :string {
		$params = (object) $params;
		
		return
			$this->cacheDir .
			'/doc' . $params->docId . '-' . $params->name . '.php'
		;
	}
}