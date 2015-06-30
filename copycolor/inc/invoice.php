<?php

require_once(get_stylesheet_directory().'/inc/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
	public function Header() {
		$this->setJPEGQuality(90);
		$this->Image(get_stylesheet_directory_uri().'/images/invoice-logo.png', 165, 10, 35, 0, 'PNG', '');
 
	}
	public function Footer() {
		$this->SetY(-15);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 8);
		$this->Cell(0, 10, 'Copy Color - ' . get_theme_mod('company_address') . ' - ' . get_theme_mod('company_phone') . ' - ' . get_theme_mod('company_email'), 0, false, 'C');
	}
	public function CreateTextBox($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'R') {
		$this->SetXY($x+20, $y); // 20 = margin left
		$this->SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
		$this->Cell($width, $height, $textval, 0, false, $align);
	}
}

function writeInvoice($id) {

    $order = get_order($id);

    $uploadfolder =  url_to_path(WP_CONTENT_URL . '/uploads/invoices');
    if (!is_dir($uploadfolder) && !mkdir($uploadfolder, 0777)) {
        echo 'Error creating folder, please try again';
        return NULL;
    }

    // create a PDF object
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
    // set document (meta) information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Copy Color');
    $pdf->SetTitle('Invoice n° ' . $id);
    $pdf->SetSubject('Invoice');
    $pdf->SetKeywords('TCPDF, PDF, invoice');
 
// add a page
$pdf->AddPage();
 
// create address box
$pdf->CreateTextBox("Client's details: ", 0, 55, 170, 10, 10, 'B', 'R');
$pdf->CreateTextBox('' . $order->phone, 0, 60, 170, 10, 10, '', 'R');
$pdf->CreateTextBox('' . $order->mail, 0, 65, 170, 10, 10, '', 'R');
 
// invoice title / number
$pdf->CreateTextBox('Order n° ' . $id, 0, 90, 170, 20, 16, '', 'C');
 
// date, order ref
$pdf->CreateTextBox('Date: ' . $order->date, 0, 105, 0, 10, 10, '', 'R');

// list headers
$pdf->CreateTextBox('Service', 0, 120, 45, 10, 10, 'B', 'C');
$pdf->CreateTextBox('Product n°', 45, 120, 25, 10, 10, 'B', 'C');
$pdf->CreateTextBox('Pages', 70, 120, 25, 10, 10, 'B', 'C');
$pdf->CreateTextBox('Quantity', 95, 120, 25, 10, 10, 'B', 'C');
$pdf->CreateTextBox('Price', 120, 120, 25, 10, 10, 'B', 'R');
$pdf->CreateTextBox('Amount', 145, 120, 25, 10, 10, 'B', 'R');
 
$pdf->Line(20, 129, 195, 129);

$currY = 128;
$total = 0;

$sub_cart = getSubCart();
if ($sub_cart != NULL) {
    foreach ($sub_cart as $pid => $sub_cart_product) {
        $sub_product = get_sub_product($pid);
        $unit_price = $sub_product->price;
        for ($i = 0; $i < getDesignNumber($pid); $i++) {
            $quantity = getSublimationQuantity($pid, $i);
            if ($quantity > 0) {
                $sub_price = $quantity * $unit_price;
                $pdf->CreateTextBox('Sublimation', 0, $currY, 45, 10, 10, '', 'C');
                $pdf->CreateTextBox($pid, 45, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox('-', 70, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox($quantity, 95, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox($unit_price . 'NIS', 120, $currY, 25, 10, 10, '', 'R');
                $pdf->CreateTextBox($sub_price . 'NIS', 145, $currY, 25, 10, 10, '', 'R');
	            $currY = $currY+5;
	            $total = $total+$sub_price;
            }
        }  
    }
}
$print_cart = getPrintCart();
if ($print_cart != NULL) {
    foreach ($print_cart as $cid => $print_cart_cat) {
        $cat = get_print_category($cid);
        for ($i = 0; $i < getUploadNumber($cid); $i++) {
            $pid = getPid($cid, $i);
            $print_product = get_print_product($pid);
            $unit_price = $print_product->price;
            $quantity = getPrintQuantity($cid, $i);
            if ($quantity > 0) {
                $pagesno = getNumPages($cid, $i);
                $sub_price = $pagesno * $quantity * $unit_price;
                $pdf->CreateTextBox('Print', 0, $currY, 45, 10, 10, '', 'C');
                $pdf->CreateTextBox($pid, 45, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox($pagesno, 70, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox($quantity, 95, $currY, 25, 10, 10, '', 'C');
                $pdf->CreateTextBox($unit_price . 'NIS', 120, $currY, 25, 10, 10, '', 'R');
                $pdf->CreateTextBox($sub_price . 'NIS', 145, $currY, 25, 10, 10, '', 'R');
	            $currY = $currY+5;
	            $total = $total+$sub_price;
            }
        }  
    }
}
$pdf->Line(20, $currY+4, 195, $currY+4);

// output the total row
$pdf->CreateTextBox('Total :', 20, $currY+5, 130, 10, 10, 'B', 'R');
$pdf->CreateTextBox(number_format($total, 2, '.', ''). 'NIS', 145, $currY+5, 25, 10, 10, 'B', 'R');
 
// some payment instructions or information
$pdf->setXY(20, $currY+30);
$pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
$pdf->MultiCell(175, 10, 'You can contact us to know when your order will be ready. We retain the right to change the total price if the number of pages of one or more files you uploaded is different from the actual number of pages in this file.', 0, 'L', 0, 1, '', '', true, null, true);
 
//Close and output PDF document
$pdf->Output($uploadfolder . '/invoice-number-' . $id . '.pdf', 'F');
}

?>