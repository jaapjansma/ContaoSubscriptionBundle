<?php
/**
 * Copyright (C) 2022  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Edeveloper\ContaoSubscriptionBundle\Backend;

use Contao\DataContainer;
use Contao\Environment;
use Haste\Util\StringUtil;
use Contao\System;
use Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;

class InvoiceModule {

  protected $documentTpl = 'subscription_invoice';

  public function printDocument(DataContainer $dc) {
    $invoice = InvoiceModel::findByPk($dc->id);
    $ids[] = $dc->id;
    $pdf = $this->createPdf($ids);
    $pdf->Output($this->prepareFileName($invoice->document_number) . '.pdf', 'D');
  }

  /**
   * @param $ids
   *
   * @return \Mpdf\Mpdf
   * @throws \Mpdf\MpdfException
   */
  protected function createPdf($ids) {
    $pdf        = $this->generatePDF();
    foreach($ids as $id) {
      $invoice = InvoiceModel::findByPk($id);
      $pdf->AddPage('P', '', '1', '', '', '10', '10', '25', '20');
      /** @var \Contao\FrontendTemplate|\stdClass $objTemplate */
      $objTemplate = new \Contao\FrontendTemplate($this->documentTpl);
      $objTemplate->invoice = $invoice;
      $pdf->writeHTML($objTemplate->parse());
    }
    return $pdf;
  }

  /**
   * Prepare file name
   *
   * @param string $strName   File name
   *
   * @return string Sanitized file name
   */
  protected function prepareFileName($strName)
  {
    // Replace simple tokens
    $strName = $this->sanitizeFileName(StringUtil::recursiveReplaceTokensAndTags($strName, StringUtil::NO_TAGS | StringUtil::NO_BREAKS | StringUtil::NO_ENTITIES));
    return $strName;
  }

  /**
   * Sanitize file name
   *
   * @param string $strName              File name
   * @param bool   $blnPreserveUppercase Preserve uppercase (true by default)
   *
   * @return string Sanitized file name
   */
  protected function sanitizeFileName($strName, $blnPreserveUppercase = true)
  {
    return standardize(ampersand($strName, false), $blnPreserveUppercase);
  }

  /**
   * Generate the pdf document
   *
   * @return \Mpdf\Mpdf
   */
  protected function generatePDF()
  {
    // Get the project directory
    $projectDir = System::getContainer()->getParameter('kernel.project_dir');

    // Include TCPDF config
    if (file_exists($projectDir.'/system/config/tcpdf.php')) {
      require_once $projectDir.'/system/config/tcpdf.php';
    } elseif (file_exists($projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php')) {
      require_once $projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php';
    } elseif (file_exists($projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php')) {
      require_once $projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php';
    }

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    // Create new PDF document
    $pdf = new \Mpdf\Mpdf([
      'fontDir' => $fontDirs,
      'fontdata' => $fontData,
      'format' => \defined('PDF_PAGE_FORMAT') ? PDF_PAGE_FORMAT : 'A4',
      'orientation' => \defined('PDF_PAGE_ORIENTATION') ? PDF_PAGE_ORIENTATION : 'P',
      'margin_left' => \defined('PDF_MARGIN_LEFT') ? PDF_MARGIN_LEFT : 15,
      'margin_right' => \defined('PDF_MARGIN_RIGHT') ? PDF_MARGIN_RIGHT : 15,
      'margin_top' => \defined('PDF_MARGIN_TOP') ? PDF_MARGIN_TOP : 10,
      'margin_bottom' => \defined('PDF_MARGIN_BOTTOM') ? PDF_MARGIN_BOTTOM : 10,
      'default_font_size' => \defined('PDF_FONT_SIZE_MAIN') ? PDF_FONT_SIZE_MAIN : 12,
      'default_font' => \defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'freeserif',
    ]);

    // Set document information
    $pdf->SetCreator(\defined('PDF_CREATOR') ? PDF_CREATOR : 'Contao Open Source CMS');
    $pdf->SetAuthor(\defined('PDF_AUTHOR') ? PDF_AUTHOR : Environment::get('url'));
    return $pdf;
  }

}