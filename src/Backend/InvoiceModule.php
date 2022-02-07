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

use Contao\Controller;
use Contao\DataContainer;
use Edeveloper\ContaoSubscriptionBundle\Helper\PdfHelper;
use Edeveloper\ContaoSubscriptionBundle\Model\SubscriptionModel;
use Contao\System;
use Edeveloper\ContaoSubscriptionBundle\Model\InvoiceModel;

class InvoiceModule {

  protected $invoiceDocumentTpl = 'subscription_invoice';

  /**
   * Print an invoice.
   *
   * @param \Contao\DataContainer $dc
   *
   * @return void
   * @throws \Mpdf\MpdfException
   */
  public function printDocument(DataContainer $dc) {
    $invoice = InvoiceModel::findByPk($dc->id);
    $pdfHelper = new PdfHelper();
    $pdf = $pdfHelper->generatePDF();
    $pdf->AddPage('P', '', '1', '', '', '10', '10', '25', '20');
    /** @var \Contao\FrontendTemplate|\stdClass $objTemplate */
    $objTemplate = new \Contao\FrontendTemplate($this->invoiceDocumentTpl);
    $objTemplate->invoice = $invoice;
    $pdf->writeHTML($objTemplate->parse());
    $pdfHelper->DownloadPdf($pdf, $invoice->document_number);
  }

  /**
   * Mark an invoice as paid
   *
   * @param \Contao\DataContainer $dc
   *
   * @return void
   */
  public function markAsPaid(DataContainer $dc) {
    // Call onload_callback (e.g. to check permissions)
    $invoice = InvoiceModel::findByPk($dc->id);
    $previousPaidStatus = $invoice->paid;
    $invoice->paid = '1';
    $invoice->save();
    if (!$previousPaidStatus && $invoice->paid && $invoice->subscription) {
      $subsciption = SubscriptionModel::findByPk($invoice->subscription);
      $subsciption->prolong();
    }

    $url = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'invoices']);
    Controller::redirect($url);
  }

}