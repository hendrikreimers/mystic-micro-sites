<?php
declare(strict_types=1);

namespace Template\ViewHelper;

use Exception;
use \JShrink\Minifier;
use Models\DOM\DOMNode;

/**
 * Class InlineJsViewHelper
 *
 * This class provides functionality to load a JS file and returns it's content as inline script tag
 */
class InlineJsFileViewHelper extends BaseViewHelper implements ViewHelperInterface {

  /**
   * @var string Path to inline JS files
   */
  protected string $filePath;

  /**
   * @var string path to caching data folder
   */
  protected string $cachePath;

  /**
   * Constructor
   * Initialize the paths
   *
   * @param DOMNode $currentNode
   */
  public function __construct(DOMNode $currentNode) {
    parent::__construct($currentNode);

    $this->filePath = implode(DIRECTORY_SEPARATOR, [BASE_PATH, 'Resources', 'Public', 'Javascript']);
    $this->cachePath = implode(DIRECTORY_SEPARATOR, [BASE_PATH, 'data']);
  }

  /**
   * Registers allowed and required Arguments for this ViewHelper
   *
   * @return void
   */
  public function registerArguments(): void {
    $this->registerArgument('files', true, 'String or Array of /Resources/Private/InlineJavascript/ js file(s).', true);
    $this->registerArgument('minify', false, 'Minification enabled', false);
    $this->registerArgument('cache', false, 'Caching of minified JS enabler', false);
  }

  /**
   * Rendering method
   * Loads the JS file from the static path and returns that content as inline JS script tag.
   *
   * @return string The rendered content
   * @throws Exception
   */
  public function render(): string {
    // Minification enabled?
    $minify = (bool)$this->getArgumentValue('minify');
    $cache = (bool)$this->getArgumentValue('cache');

    $fileList = trim($this->getArgumentValue('files') ?: '');
    if ( str_starts_with($fileList, '[') && str_ends_with($fileList, ']') ) {
      $fileList = json_decode(str_replace('\'', '"', $fileList), true);
    } else $fileList = [$fileList];

    // Load contents
    $content = '';
    foreach ( $fileList as $fileName ) {
      $file = $this->filePath . DIRECTORY_SEPARATOR . $fileName;
      $cacheFile = $this->cachePath . DIRECTORY_SEPARATOR . 'cache_' . $fileName;

      if ( !file_exists($file) ) continue;

      if ( !$cache ) {
        $fileContent = $this->getFileContent($file, $minify);
      } else {
        if ( $this->isCacheFileValid($fileName) ) {
          $fileContent = $this->getFileContent($cacheFile, false);
        } else {
          $fileContent = $this->getFileContent($file, $minify);
          $this->createCacheFile($fileName, $fileContent);
        }
      }

      // Append to resulting content
      $content .= $fileContent;
    }

    // Return script tag with file content
    return '<script type="text/javascript">' . PHP_EOL . $content . PHP_EOL . '</script>';
  }

  /**
   * Returns file content with optional minification
   *
   * @param string $file
   * @param bool $minify
   * @return string|false
   * @throws Exception
   */
  private function getFileContent(string $file, bool $minify): string|false {
    if ( !file_exists($file) ) return false;

    // Get file content
    $fileContent = file_get_contents($file);

    // Minify if enabled
    if ( $minify ) {
      return Minifier::minify($fileContent);
    } else return $fileContent;
  }

  /**
   * Creates a cache file
   *
   * @param string $fileName
   * @param string $content
   * @return void
   */
  private function createCacheFile(string $fileName, string $content): void {
    $cacheFile = $this->cachePath . DIRECTORY_SEPARATOR . 'cache_' . $fileName;

    file_put_contents($cacheFile, $content);
  }

  /**
   * Checks if caching file exists and is valid
   *
   * @param string $fileName
   * @return bool
   */
  private function isCacheFileValid(string $fileName): bool {
    $file = $this->filePath . DIRECTORY_SEPARATOR . $fileName;
    $cacheFile = $this->cachePath . DIRECTORY_SEPARATOR . 'cache_' . $fileName;

    if ( !file_exists($cacheFile) )
      return false;

    if ( file_exists($cacheFile) && file_exists($file) ) {
      // Get the creation and modification times of the cache file
      $cacheFileCreationTime = filectime($cacheFile);
      $cacheFileModificationTime = filemtime($cacheFile);
      $cacheLastModificationTime = max($cacheFileCreationTime, $cacheFileModificationTime);

      // Get the creation and modification times of the cache file
      $fileCreationTime = filectime($file);
      $fileModificationTime = filemtime($file);
      $lastModificationTime = max($fileCreationTime, $fileModificationTime);

      // Cache file valid if it's newer than the original file
      if ($cacheLastModificationTime >= $lastModificationTime) {
        return true;
      }
    }

    return false;
  }
}
