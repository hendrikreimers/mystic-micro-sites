<?php
declare(strict_types=1);

namespace Services;

/**
 * FileService
 * Reads and writes file content
 *
 */
class FileService {

  /**
   * Returns content of a file
   *
   * @param string $filename filename
   * @param string $dataFolder target folder
   * @param bool $removeLinebreaks remove linke breaks in the file (useful if you added it using saveFileContent function)
   * @return false|string
   */
  public static function getFileContent(string $filename, string $dataFolder = 'data', bool $removeLinebreaks = true): false|string {
    $file = $dataFolder . DIRECTORY_SEPARATOR . $filename;

    if ( !file_exists($file) ) {
      return false;
    }

    if ( $removeLinebreaks ) {
      return str_replace("\n", '', file_get_contents($file));
    } else return file_get_contents($file);
  }

  /**
   * Writes content to a file
   *
   * @param string $filename Filename
   * @param string $data File content
   * @param string $dataFolder target folder
   * @param bool $addLinebreaks split string at each 120 chars and add a line break
   * @param bool $overwrite overwrite existing file
   * @return bool
   */
  public static function saveFileContent(string $filename, string $data, string $dataFolder = 'data', bool $addLinebreaks = true, bool $overwrite = false): bool {
    $file = $dataFolder . DIRECTORY_SEPARATOR . $filename;

    if ( file_exists($file) && $overwrite === false ) {
      return false;
    }

    if ( $addLinebreaks ) {
      $result = file_put_contents($file, implode("\n", str_split($data, 120)));
    } else $result = file_put_contents($file, $data);

    return $result !== false;
  }

  /**
   * Generates a unique filename and takes care that the filename not exists already as file
   *
   * @param string $directory
   * @param string $extension
   * @param int $length
   * @return string
   * @throws \Random\RandomException
   */
  public static function generateUniqueFilename(string $dataFolder = 'data', string $extension = 'enc', int $length = 8): string {
    do {
      $filename = self::generateRandomString($length) . '.' . $extension;
      $filePath = $dataFolder . DIRECTORY_SEPARATOR . $filename;
    } while (file_exists($filePath));

    return $filename;
  }

  /**
   * Returns list of encrypted file list
   *
   * @param string $dataFolder
   * @param string $extensionPattern
   * @return array
   */
  public static function getEncryptionFileList(string $dataFolder = 'data', string $extensionPattern = '*.enc'): array {
    $path = $dataFolder . DIRECTORY_SEPARATOR . $extensionPattern;
    $fileList = glob($path);

    $result = [];
    if ( $fileList && sizeof($fileList) > 0 ) {
      foreach ( $fileList as $file ) {
        $result[] = basename($file, '.enc');
      }
    }

    return $result;
  }

  /**
   * Generates a random string used for filename
   *
   * @param int $length
   * @return string
   * @throws \Random\RandomException
   */
  protected static function generateRandomString(int $length): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
  }

}
