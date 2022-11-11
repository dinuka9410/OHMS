<?php 
namespace App\Traits;
use App\Models\Inv_purchase_order;
use Elibyy\TCPDF\Facades\TCPdf;

Trait perches_oder_report {


    // this function will print a pdf GRC

    public function print_Po_details($info){


        $purchase_order = $info['inv_po_products'];

        $product_with_uom = $info['product_with_uom'];
        $agreement2='Terms and Conditions';

        


        $table = ' <table border="1" cellspacing="0" cellpadding="3">

        <thead class="thead-dark" >
            <tr  >
                <th>Product Name</th>
                <th>Product Code</th>
                <th>Product Brand</th>
                <th>QTY</th>
                <th>Price</th>
                <th>Total</th>

            </tr>
        </thead>

        <tbody>';

        foreach($product_with_uom as $row){

            $table .= '<tr><td>'.$row->product_with_get->product_name.'</td><td>'.$row->product_with_get->product_code.'</td><td>'.$row->product_with_get->product_brand.'</td><td>'.$row->qty.'</td><td>Rs.'.$row->price.' /= </td><td>Rs.'.($row->price)*($row->qty).' /= </td></tr>';
        
        }
        $table .= '<tr><td></td><td></td><td></td><td></td><td> Total</td><td>Rs.'.$purchase_order->total.' /= </td></tr>';

        $table .='  

        </tbody>
        
        </table>';

        TCPdf::addPage();
        //-------- header part ---///
        TCPdf::SetFont('Helvetica','',18);
        TCPdf::cell(190,3,'Hotel Lagone Kandy',0,1,'C');
        TCPdf::SetFont('Helvetica','',10);
        TCPdf::cell(190,3,'ramanayake mawatha colombo 2 kandy ',0,1,'C');
        TCPdf::cell(190,7,'081 - 4221338 / 081 - 4221337',0,1,'C');
        TCPdf::cell(190,3,'---- Purchas Oder ----',0,1,'C');
        TCPdf::Ln();
        //------- reservation details ----- //
        TCPdf::cell(80,5,'PO Number : '.$purchase_order->po_number,0,1);
        TCPdf::cell(80,5,'Supplier : '.$purchase_order->po_with_supp->suppliers_name,0,1);
        TCPdf::cell(80,5,'Supplier Email : '.$purchase_order->po_with_supp->suppliers_email,0,1);
        TCPdf::cell(80,5,'Supplier Tel-Number : '.$purchase_order->po_with_supp->suppliers_tel,0,1);
        // --- agreement area -------//

        // ------ table for room numbers --- //
        TCPdf::cell(190,5,'',0,1,'C');
        TCPdf::writeHTMLCell(192,0,9,'',$table,0);
        TCPdf::Ln();
        TCPdf::cell(190,7,'',0,1,'C');
        TCPdf::writeHTMLCell(190,10,'','',$agreement2,0);
        

        TCPdf::setFooterCallback(function($pdf){

            // Position at 15 mm from bottom
             $pdf->SetY(-20);
             // Set font
             $pdf->SetFont('helvetica', 'I', 8);
             $pdf->Cell(0, 0,'By signing to this agreement you are agreeing to the above conditions', 0, false, '', 0, '', 0, false, 'T', 'M');
             $pdf->Ln();
             $pdf->Cell(10, 16,'Accountant : _________________________', 0, false, '', 0, '', 0, false, 'T', 'M');
             $pdf->Ln();
            });


        TCPdf::lastPage();
        TCPdf::output();
        

    }


    // this function will print a PDF with reservation room bill + additional features

   

}


?>