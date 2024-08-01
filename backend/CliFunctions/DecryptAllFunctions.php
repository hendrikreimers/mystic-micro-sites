<?php
declare(strict_types=1);

use Models\SiteLayoutModel;
use Services\EncryptionService;
use Services\FileService;
use Template\TemplateEngine;
use Utility\ObjectUtility;

/**
 * Renders the decrypted file content with the template engine as HTML
 *
 * @param string $decryptedData
 * @return string
 */
function renderTemplate(string $decryptedData): string {
  // Load template
  $templatePath = join(DIRECTORY_SEPARATOR, ['.', 'Resources', 'Private', 'Templates']);
  $template = new TemplateEngine($templatePath . DIRECTORY_SEPARATOR . 'MicroSite.html');

  // Force type
  $siteLayout = SiteLayoutModel::fromArray(ObjectUtility::objectToArray(json_decode($decryptedData)));

  // Assign variables
  $template->view->assignMultiple([
    'bgColor' => $siteLayout->bgColor,
    'textColor' => $siteLayout->textColor,
    'fontFamily' => $siteLayout->fontFamily->value,
    'elements' => ObjectUtility::objectToArray($siteLayout->elements),
    'baseUrl' => BASE_URL
  ]);

  // Render output
  return $template->render();
}

/**
 * Decrypts a file with given masterPassword
 *
 * @param string $file
 * @param string $MASTER_PASSWORD
 * @param EncryptionService $encryptionService
 * @return string|false
 * @throws Exception
 */
function decryptFile(string $file, string $MASTER_PASSWORD, EncryptionService &$encryptionService): string | false {
  $fileContent = FileService::getFileContent($file.'.enc');

  if ( !$fileContent ) {
    return false;
  }

  // Decrypt
  $decryptedData = $encryptionService->decryptData($fileContent, $MASTER_PASSWORD, SECRET_KEY);

  return $decryptedData;
}
