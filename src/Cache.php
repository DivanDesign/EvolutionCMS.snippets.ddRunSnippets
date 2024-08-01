<?php
namespace ddRunSnippets;

class Cache {
	private static ?string $cacheDir = null;
	private static string $contentPrefix = '<?php die("Unauthorized access."); ?>';
	private static int $contentPrefixLen = 37;
	
	/**
	 * initStatic
	 * @version 2.0 (2024-08-01)
	 * 
	 * @desc Static “constructor”.
	 * 
	 * @return {void}
	 */
	private static function initStatic(): void {
		if (is_null(static::$cacheDir)){
			static::$cacheDir =
				//path to `assets`
				dirname(
					__DIR__,
					3
				)
				. '/cache/ddCache'
			;
			
			if (!is_dir(static::$cacheDir)){
				\DDTools\FilesTools::createDir([
					'path' => static::$cacheDir,
				]);
			}
		}
	}
	
	/**
	 * create
	 * @version 2.1.2 (2024-08-01)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->name {string} — Unique cache name for the document.
	 * @param $params->data {string|array|stdClass} — Data to save.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {void}
	 */
	public static function create($params): void {
		static::initStatic();
		
		$params = (object) $params;
		
		//str|obj|arr
		$dataType =
			is_object($params->data)
			? 'obj'
			: (
				is_array($params->data)
				? 'arr'
				//All other types are considered as string (because of this we don't use the gettype function)
				: 'str'
			)
		;
		
		if ($dataType != 'str'){
			$params->data = \DDTools\ObjectTools::convertType([
				'object' => $params->data,
				'type' => 'stringJsonAuto',
			]);
		}
		
		//Save cache file
		file_put_contents(
			//Cache file path
			static::buildCacheFilePath($params),
			//Cache content
			(
				static::$contentPrefix
				. $dataType
				. $params->data
			)
		);
	}
	
	/**
	 * get
	 * @version 2.1.2 (2024-08-01)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->name {string} — Unique cache name for the document.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {null|string|array|stdClass} — `null` means cache is not exist.
	 */
	public static function get($params){
		static::initStatic();
		
		$result = null;
		
		$filePath = static::buildCacheFilePath($params);
		
		if (is_file($filePath)){
			//Cut PHP-code prefix
			$result = substr(
				file_get_contents($filePath),
				static::$contentPrefixLen
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
						$dataType == 'obj'
						? 'objectStdClass'
						: 'objectArray'
					,
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * clear
	 * @version 2.1.2 (2024-08-01)
	 * 
	 * @param Clear cache files for specified document or every documents.
	 * 
	 * @param [$params] {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used.
	 * @param $params->resourceId {integer|null} — Document ID related to cache. Default: null (cache of all docs will be cleared).
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {void}
	 */
	public static function clear($params = []): void {
		static::initStatic();
		
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'resourceId' => null,
					'prefix' => 'doc',
				],
				$params,
			],
		]);
		
		//Clear all cache
		if (empty($params->resourceId)){
			\DDTools\FilesTools::removeDir(static::$cacheDir);
		//Clear cache for specified documents
		}else{
			$files = glob(
				static::$cacheDir
				. '/' . $params->prefix . $params->resourceId . '-*.php'
			);
			
			foreach (
				$files
				as $filepath
			){
				unlink($filepath);
			}
		}
	}
	
	/**
	 * buildCacheFilePath
	 * @version 2.1.1 (2024-08-01)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Parameters, the pass-by-name style is used.
	 * @param $params->resourceId {integer} — Document ID related to cache.
	 * @param $params->name {string} — Unique cache name for the document.
	 * @param [$params->prefix='doc'] {string} — Cache prefix.
	 * 
	 * @return {string}
	 */
	private static function buildCacheFilePath($params): string {
		$params = \DDTools\ObjectTools::extend([
			'objects' => [
				(object) [
					'prefix' => 'doc',
				],
				$params,
			],
		]);
		
		return
			static::$cacheDir
			. '/' . $params->prefix . $params->resourceId . '-' . $params->name . '.php'
		;
	}
}