<?php

/**
 * ProcessWire Pro Cache: File Merger
 *
 * Copyright (C) 2015 by Ryan Cramer
 *
 * PLEASE DO NOT DISTRIBUTE
 *
 * http://processwire.com
 * 
 * @todo the fill='url(%23b)' in this gets removed somehow: background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='800' height='57' viewBox='0 0 800 57'%3E%3Cdefs%3E%3Cpath id='a' d='M0 0v57h787.49L800 28.5 787.008 0z'/%3E%3C/defs%3E%3CclipPath id='b'%3E%3Cuse xlink:href='%23a' overflow='visible'/%3E%3C/clipPath%3E%3ClinearGradient id='c' gradientUnits='userSpaceOnUse' x1='-1205.676' y1='24.198' x2='-1204.676' y2='24.198' gradientTransform='matrix(0 -57 57 0 -979.313 -68666.563)' fill='url(%23b)' d='M0 0h800v57H0V0z'/%3E\a %3C/svg%3E");
 *
 */

class ProCacheFileMerger extends Wire {
	
	protected $prefix = 'pwpc';
	protected $ext = '';
	protected $source = '';
	protected $destination = '';
	protected $refreshSeconds = 43200;
	protected $expiredSeconds = 86400; 
	protected $hashLength = 40;
	protected $mergedFilename = '';
	protected $maxImportSizeCSS = 5; 
	
	const debug = false;  // when true, files are re-created on every merge

	/**
	 * Construct
	 * 
	 * @param array $files
	 * @param string $destination
	 * @throws WireException
	 * 
	 */
	public function __construct(array $files = array(), $destination = '') {
		$this->source = $this->wire('config')->paths->templates;
		if($destination) {
			$this->setDestination($destination);
		} else {
			$destination = $this->wire('config')->paths->assets . $this->prefix . '/';
			if(!is_dir($destination)) wireMkdir($destination);
			$this->setDestination($destination);
		}
		$src = dirname(__FILE__) . '/minify/src/';
		foreach(array('Exception', 'Minify', 'Converter', 'CSS', 'JS') as $f) {
			require_once("$src$f.php");
		}
		if(count($files)) $this->merge($files);
		return $this; 
	}

	/**
	 * Set the file extension
	 * 
	 * @param $ext
	 * @return $this
	 * 
	 */
	public function setExtension($ext) {
		$this->ext = strtolower(trim($ext, '.'));
		return $this; 
	}

	/**
	 * Set the max CSS file import size (for background images, etc.)
	 *
	 * @param int $size
	 * @return $this
	 *
	 */
	public function setMaxImportSizeCSS($size) {
		$this->maxImportSizeCSS = (int) $size;
		return $this; 
	}

	/**
	 * Get a property or API var
	 * 
	 * @param $key
	 * @return mixed|string
	 * 
	 */
	public function __get($key) {
		if($key == 'url') return $this->url();
		if($key == 'path') return $this->path();
		return parent::__get($key);
	}

	/**
	 * Set the destination for merged files
	 * 
	 * @param $destination
	 * @return $this
	 * @throws WireException
	 * 
	 */
	public function setDestination($destination) {
		if(!is_dir($destination)) throw new WireException("Please specify a destination dir that exists"); 
		if(!is_writable($destination)) throw new WireException("Destination dir is not writable");
		$this->destination = rtrim($destination, '/') . '/';
		return $this;
	}
	
	public function getDestination($url = false) {
		if(!$url) return $this->destination;
		return str_replace($this->wire('config')->paths->root, $this->wire('config')->urls->root, $this->destination);
	}

	/**
	 * Set the max age of a cached CSS/JS file
	 * 
	 * @param $seconds
	 * @return $this
	 * 
	 */
	public function setExpiration($seconds) {
		$this->expiredSeconds = (int) $seconds; 
		$this->refreshSeconds = floor($seconds / 2);
		return $this; 
	}

	/**
	 * Set the relative source directory 
	 * 
	 * Not necessary to set this usually, as the default is /path/to/pw/site/templates/
	 * 
	 * @param $source
	 * @return $this
	 * 
	 */
	public function setSource($source) {
		$this->source = rtrim($source, '/') . '/';	
		return $this;
	}

	/**
	 * Set the file prefix  (default=pwpc)
	 * 
	 * @param $prefix
	 * @return $this
	 * 
	 */
	public function setPrefix($prefix) {
		if($prefix) $this->prefix = $this->wire('sanitizer')->name($prefix);
		return $this;
	}

	/**
	 * Given an array of filenames, determine what the target filename should be
	 * 
	 * This considers the file mtime and size, so that changes to the file should be picked up immediately.
	 * 
	 * @param array $files
	 * @return string
	 * 
	 */
	protected function getTargetFilename($files) {
		$hash = '';
		$ext = $this->ext;
		foreach($files as $file) {
			$hash .= $file . ',' . filemtime($file) . ',' . filesize($file) . ',';
			if(!$ext) $ext = pathinfo($file, PATHINFO_EXTENSION);
		}
		$hash = sha1($hash);
		return "$this->destination$this->prefix-$hash.$ext";
	}

	/**
	 * Get the URL for the merged file
	 * 
	 * @return string
	 * 
	 */
	public function url() {
		$url = str_replace($this->wire('config')->paths->root, $this->wire('config')->urls->root, $this->mergedFilename);
		return $url;
	}

	/**
	 * Get the path for the merged file
	 * 
	 * @return string
	 * 
	 */
	public function path() {
		return $this->mergedFilename;
	}

	/**
	 * Given an array of file URLs, convert them to file system paths
	 * 
	 * @param string|array $files Can be array or CSV string
	 * @return array
	 * 
	 */
	protected function fileURLsToPaths($files) {
		if(is_string($files)) {
			// convert CSV string to $files array
			$files = explode(',', $files);
			foreach($files as $key => $file) {
				if(strpos($file, '?') !== false) list($file, $unused) = explode('?', $file);
				$files[$key] = trim($file); 
				if(!strlen($file)) unset($files[$key]); 
			}
		}
		if(!is_array($files)) return array();
		$rootPath = $this->wire('config')->paths->root;
		$rootURL = $this->wire('config')->urls->root; 
		foreach($files as $key => $file) {
			if(strpos($file, '/') !== 0) $file = $this->source . $file;
			if(strpos($file, $rootPath) !== 0) {
				if($rootURL != '/' && strpos($file, $rootURL) === 0) {
					$file = substr($file, strlen($rootURL) - 1);
				}
				$file = $rootPath . ltrim($file, '/');
			}
			$files[$key] = $file;
		}
		return $files;
	}
	
	public function getMinifierJS() {
		return new MatthiasMullie\Minify\JS();
	}
	
	public function getMinifierCSS() {
		$minifier = new MatthiasMullie\Minify\CSS();
		$minifier->setMaxImportSize($this->maxImportSizeCSS);
		return $minifier;
	}
	
	/**
	 * Given a list of file URLs, merge them into a single file
	 * 
	 * To retrieve the result, call the $this->url() method or append ->url() to your merge call.
	 * 
	 * @param string|array $files Array or CSV string of file URLs
	 * @param bool $minify Also minify the files? (default=true)
	 * @return $this
	 * 
	 */
	public function merge($files, $minify = false) {
		
		$files = $this->fileURLsToPaths($files);	
		$targetFilename = $this->getTargetFilename($files);
		
		if(is_file($targetFilename) && !self::debug) {
			// use existing merge
			if(filemtime($targetFilename) < time() - $this->refreshSeconds) {
				// update mtime to prevent it from being cleaned out
				touch($targetFilename);
			}
			
		} else if($minify) {
			
			if(is_file($targetFilename)) unlink($targetFilename); 
			$minifier = null;
			
			if(substr($targetFilename, -4) == '.css') {
				$minifier = $this->getMinifierCSS();
			} else if(substr($targetFilename, -3) == '.js') {
				$minifier = $this->getMinifierJS();
			}
			
			if($minifier) {
				foreach($files as $file) {
					$minifier->add($file);
				}
				$minifier->minify($targetFilename);
				wireChmod($targetFilename);
				$this->clean();
			}

		} else {
			// create new non-minified merge
			$fp = fopen($targetFilename, "w");
			foreach($files as $file) {
				fwrite($fp, $this->getFileContents($file));
			}
			fclose($fp);
			wireChmod($targetFilename);
			$this->clean();
		}
		
		$this->mergedFilename = $targetFilename;
		
		return $this;
	}

	/**
	 * Get the contents of a file
	 * 
	 * Same as PHP file_get_contents() except that it updates CSS files to convert
	 * relative paths to absolute paths where appropriate. 
	 * 
	 * @param $file
	 * @return string
	 * 
	 */
	public function ___getFileContents($file) {
		if(is_file($file)) {
			$out = file_get_contents($file) . "\n";
		} else {
			$this->error("File does not exist: $file"); 
			$out = '';
		}
	
		if($this->ext == 'css' || strtolower(substr($file, -4)) == '.css') {
			$this->updateCSS($out, $file);
		}
		
		return $out; 
	}

	/**
	 * Clean up the merged file cache, removing old/expired files
	 * 
	 * @return int Number of files cleaned out
	 * 
	 */
	public function clean() {
		static $cleaned = false;
		if($cleaned) return; // don't allow this to run more than once per request
		$numRemoved = 0;
		foreach($this->getFiles() as $file) {
			if(filemtime($file) >= time() - $this->expiredSeconds) continue;
			if(@unlink($file)) $numRemoved++;
		}
		$cleaned = true;
		return $numRemoved; 
	}

	/**
	 * Clear out all cached files
	 * 
	 * @param string $ext Optional extension to clear
	 * @return int Number of files removed
	 * 
	 */
	public function clear($ext = '') {
		$numRemoved = 0;
		foreach($this->getFiles($ext) as $file) {
			if(unlink($file)) {
				$numRemoved++;
			} else {
				$this->warning("Error removing " . $file); 
			}
		}
		return $numRemoved; 
	}

	/**
	 * Get all files in the merge cache (both css and js)
	 * 
	 * @param string $ext Optionally specify an extension
	 * @return array
	 * 
	 */
	public function getFiles($ext = '') {
		$files = array();
		$prefix = $this->prefix . '-';
		$expectedLength = $this->hashLength + strlen($prefix); 
		foreach(new DirectoryIterator($this->destination) as $file) {
			if($file->isDot() || $file->isDir()) continue;
			$basename = $file->getBasename("." . $file->getExtension());
			if(strlen($basename) != $expectedLength) continue;
			if(strpos($basename, $prefix) !== 0) continue;
			if($ext && $file->getExtension() != $ext) continue; 
			$files[$file->getBasename()] = $file->getPathname();
		}
		return $files;
	}

	/**
	 * Get the number of files in the merge cache
	 * 
	 * @param string $ext Optionally specify a file extension
	 * @return int
	 * 
	 */
	public function getNumFiles($ext = '') {
		return count($this->getFiles($ext));
	}

	/**
	 * Adjustments to CSS file contents
	 * 
	 * Converts relative paths to absolute
	 * 
	 * @param $out
	 * @param $file
	 * 
	 */
	public function updateCSS(&$out, $file) {
		if(strpos($out, 'url(') === false) return;

		$urls = array();
		$dirname = dirname($file) . '/';
		$cssRootURL = str_replace($this->wire('config')->paths->root, '/', $dirname);
		$siteRootURL = $this->wire('config')->urls->root;
		if($siteRootURL != '/') $cssRootURL = $siteRootURL . ltrim($cssRootURL, '/');
		
		preg_match_all('!\burl\(([^)]+)\)!', $out, $matches);

		foreach($matches[1] as $key => $url) {
			// skip over absolute, possibly external URLs.
			$url = trim($url, ' \'"');
			if(strpos($url, '//') === 0 || strpos($url, ':') !== false) continue;
			
			if(strpos($url, '../') === 0) {
				$cssRootParts = explode('/', trim($cssRootURL, '/'));
				while(strpos($url, '../') === 0) {
					array_pop($cssRootParts);
					$url = substr($url, 3);
				}
				$url = '/' . implode('/', $cssRootParts) . '/' . ltrim($url, '/');
			} else if(strpos($url, './') === 0) {
				$url = substr($url, 2);
				$url = $cssRootURL . ltrim($url, '/');
			} else {
				$url = $cssRootURL . ltrim($url, '/');
			}

			$url = '/' . ltrim($url, '/');
			$out = str_replace($matches[0][$key], "url($url)", $out);
			
			if(self::debug) $urls[] = $url;
		}
		
		if(self::debug) {
			$out = "/* PWPC " . wireDate('Y-m-d H:i:s') . "\n" . implode("\n", $urls) . "\n*/" . $out;
		}
	}

	/**
	 * Given contents of an HTML document merge and minify <link rel='stylesheet'> tags 
	 * 
	 * If any of the <link> tags have an href attribute that contains "?NoMinify" it will be skipped. 
	 * 
	 * @param string $html HTML document markup
	 * @param bool $minify Minfiy also? (default=true)
	 * @return string Updated HTML markup
	 * 
	 */
	public function mergeCSSFilesInMarkup($html, $minify = true) {
		
		$pos = stripos($html, '</head>');
		$head = substr($html, 0, $pos);
		$filesByGroup = array();
		$groups = array();

		$this->findAndRemovePlaceholders($head);
		$re = '!<link\s[^>]*?href\s*=\s*["\']?(/[^/][^"\'\s]+?\.css(?:\?[^"\'\s]+)?)["\'\s][^>]*>!i';
		if(!preg_match_all($re, $head, $matches)) return $html;
		
		$timer = $this->wire('config')->debug ? Debug::timer() : null;
		
		foreach($matches[0] as $key => $fullMatch) {
			// skip references that have "?NoMinify" in them	
			if(stripos($fullMatch, '?NoMinify')) {
				$fullReplacement = str_ireplace(array('?NoMinify=1', '?NoMinify'), '', $fullMatch); 
				$html = str_replace($fullMatch, $fullReplacement, $html);
				continue; 
			}
			// skip link tags that don't contain a rel='stylesheet'
			if(stripos($fullMatch, 'stylesheet') === false) continue;

			$file = $matches[1][$key];
			$group = str_replace($file, '{url}', $fullMatch); // remove file from the picture
			$group = str_replace("'", '"', $group); // normalize quotes to double
			
			$pos = strpos($file, '?');
			if($pos !== false) $file = substr($file, 0, $pos); // remove nocache variables, if present
			
			if(strpos($group, "\t") !== false) $group = str_replace("\t", " ", $group); // remove tabs
			while(strpos($group, '  ') !== false) $group = str_replace('  ', ' ', $group); // remove redundant whitespaace

			$hash = str_replace(array(' />', '/>', ' >'), '>', strtolower($group)); // tail doesn't matter for hash
			$hash = str_replace(array('"', ' '), '', $hash); // quotes and whitespace on't need to matter for the hash
			if(strpos($hash, 'text/css')) $hash = str_replace('type=text/css', '', $hash); // not needed in hash
			$hash = str_split($hash); // convert to array to generate hash
			sort($hash); // make order of attributes not matter for our group hash
			$hash = implode('', $hash); // convert sorted parts back to group hash
			
			if(isset($groups[$hash])) {
				$group = $groups[$hash];
			} else {
				$groups[$hash] = $group;
			}
			
			if(!isset($filesByGroup[$group])) $filesByGroup[$group] = array();
			$filesByGroup[$group][$fullMatch] = $file;
		}

		foreach($filesByGroup as $group => $files) {
			$mergedFile = $this->merge(array_values($files), $minify)->url();
			$mergedLink = '';
			$n = 0;
			foreach($files as $fullMatch => $file) {
				if(!$n) {
					// replace first <link> tag in the group with merged version
					$mergedLink = str_replace('{url}', $mergedFile, $group);
					$html = str_replace($fullMatch, $mergedLink, $html);
				} else {
					// replace all other <link> tags with blank
					$html = str_replace($fullMatch, '', $html); 
				}
				$n++;
			}
			if($mergedLink) {
				// remove whitespace that was created 
				$re = '/' . preg_quote($mergedLink, '/') . '\s*([\r\n]{1,2}[ \t]*)/s'; 
				$debugTimer = $timer ? "<!--PWPC" . Debug::timer($timer) . "-->": '';
				$html = preg_replace($re, $mergedLink . $debugTimer . '$1', $html);
			}
		}
		
		return $html;
	}

	/**
	 * Given contents of an HTML document merge and minify <script src='...'></script> tags
	 *
	 * If any of the <script> tags have a src attribute that contains "?NoMinify" it will be skipped.
	 *
	 * @param string $html HTML document markup
	 * @param bool $head Focus on document <head>? If false, it will focus on document <body>
	 * @param bool $minify Minfiy also? (default=true)
	 * @return string Updated HTML markup
	 *
	 */
	public function mergeJSFilesInMarkup($html, $head = true, $minify = true) {

		if($head) {
			$pos = stripos($html, '</head>');
			$content = $pos ? substr($html, 0, $pos) : $html;
		} else {
			$pos = stripos($html, '<body'); 
			$content = $pos ? substr($html, $pos) : $html; 
		}
		
		$filesByGroup = array();
		$groups = array();
		$timer = Debug::timer();

		$this->findAndRemovePlaceholders($content);
		$re = '!<script[^>]*?\s*src\s*=\s*["\']?(/[^/][^"\'\s>]+?\.js(?:\?[^"\'\s]+)?)["\'\s][^>]*>.*?<\s*/script\s*>!is';
		if(!preg_match_all($re, $content, $matches)) return $html;
		

		foreach($matches[0] as $key => $fullMatch) {
			// skip references that have "?NoMinify" in them	
			if(stripos($fullMatch, '?NoMinify')) {
				$fullReplacement = str_ireplace(array('?NoMinify=1', '?NoMinify'), '', $fullMatch);
				$html = str_replace($fullMatch, $fullReplacement, $html);
				continue;
			}

			$file = $matches[1][$key];
			$group = str_replace($file, '{url}', $fullMatch); // remove file from the picture
			$group = str_replace("'", '"', $group); // normalize quotes to double

			$pos = strpos($file, '?');
			if($pos !== false) $file = substr($file, 0, $pos); // remove nocache variables, if present

			if(strpos($group, "\t") !== false) $group = str_replace("\t", " ", $group); // remove tabs
			while(strpos($group, '  ') !== false) $group = str_replace('  ', ' ', $group); // remove redundant whitespace

			$hash = str_replace(array('"', ' '), '', $group); // quotes and whitespace don't need to matter for the hash
			if(strpos($hash, 'text/javascript')) $hash = str_replace('type=text/javascript', '', $hash); // not needed in hash
			$hash = str_split($hash); // convert to array to generate hash
			sort($hash); // make order of attributes not matter for our group hash
			$hash = implode('', $hash); // convert sorted parts back to group hash

			if(isset($groups[$hash])) {
				$group = $groups[$hash];
			} else {
				$groups[$hash] = $group;
			}

			if(!isset($filesByGroup[$group])) $filesByGroup[$group] = array();
			$filesByGroup[$group][$fullMatch] = $file;
		}

		foreach($filesByGroup as $group => $files) {
			$mergedFile = $this->merge(array_values($files), $minify)->url();
			$mergedScript = '';
			$n = 0;
			foreach($files as $fullMatch => $file) {
				if(!$n) {
					// replace first <script src> tag in the group with merged version
					$mergedScript = str_replace('{url}', $mergedFile, $group);
					$html = str_replace($fullMatch, $mergedScript, $html);
				} else {
					// replace all other <script src> tags with blank
					$html = str_replace($fullMatch, '', $html);
				}
				$n++;
			}
			if($mergedScript) {
				// remove whitespace that was created 
				$re = '/' . preg_quote($mergedScript, '/') . '\s*([\r\n]{1,2}[ \t]*)/s'; 
				$debugTimer = $timer ? "<!--PWPC" . Debug::timer($timer) . "-->" : '';
				$html = preg_replace($re, '$1' . $mergedScript . $debugTimer, $html);
			}
		}

		return $html;
	}

	/**
	 * Locates sections like conditional comments, replaces them with placeholders, and returns them
	 * 
	 * @param $html
	 * @return array Sections that were removed, indexed by placeholder name
	 * 
	 */
	protected function findAndRemovePlaceholders(&$html) {
		$placeholders = array();
		if(!strpos($html, '<!--[') || !strpos($html, ']-->')) return array();
		if(!preg_match_all('/(<!--\[.+?\]-->)/s', $html, $matches)) return array();
		foreach($matches[1] as $key => $value) {
			$placeholder = "<!--" . md5($value) . "-->";
			$placeholders[$placeholder] = $value;
		}
		$html = str_replace(array_values($placeholders), array_keys($placeholders), $html);
		return $placeholders;	
	}

	/**
	 * Restores placeholders that were found and removed by findAndRemovePlaceholders() method
	 * 
	 * @param $html
	 * @param array $placeholders
	 * 
	 */
	protected function restorePlaceholders(&$html, array $placeholders) {
		$html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
	}

	/**
	 * Minify inline JS in the given HTML string
	 * 
	 * To skip a particular <script> place a "NoMinify" comment somewhere within it.
	 * 
	 * @param string $out Modified directly
	 * @return int Number of minifications that took place
	 * 
	 */
	public function minifyInlineJS(&$out) {
		if(stripos($out, '<script') == false) return 0;
		if(!preg_match_all('!(<script[^>]*>\s*)([^<].+?)</script>!is', $out, $matches)) return 0;
		$n = 0;
		foreach($matches[1] as $key => $tag) {
			if(stripos($tag, 'src')) continue;
			$code = $matches[2][$key];
			if(strpos($code, 'NoMinify') !== false) continue; 
			$minifier = $this->getMinifierJS();
			$minifier->add($code);
			$minCode = $minifier->minify();
			if($minCode && strlen($minCode) < strlen($code)) {
				$out = str_replace($tag . $code, trim($tag) . $minCode, $out);
				$n++;
			}
		}
		return $n;
	}

	/**
	 * Minify inline CSS in the given HTML string
	 * 
	 * To skip a particular inline <style> section, place a "NoMinify" CSS comment somewhere in it.
	 *
	 * @param string $out Modified directly
	 * @return int Number of minifications that took place
	 *
	 */
	public function minifyInlineCSS(&$out) {
		if(stripos($out, '<style') == false) return 0;
		if(!preg_match_all('!(<style[^>]*>\s*)([^<].+?)</style>!is', $out, $matches)) return 0;
		$n = 0;
		foreach($matches[1] as $key => $tag) {
			$code = $matches[2][$key];
			if(strpos($code, 'NoMinify') !== false) continue;
			$minifier = $this->getMinifierCSS();
			$minifier->setMaxImportSize(0); // don't encode images in inline CSS
			$minifier->add($code);
			$minCode = $minifier->minify();
			if($minCode && strlen($minCode) < strlen($code)) {
				$out = str_replace($tag . $code, trim($tag) . $minCode, $out);
				$n++;
			}
		}
		return $n;
	}

	/**
	 * Typecasting to a string reveals the URL to a merged file
	 * 
	 * @return string
	 * 
	 */
	public function __toString() {
		return $this->url();
	}
	
}