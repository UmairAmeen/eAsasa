<?php

namespace App;

class MyPDF extends MyTCPDF
{
    /*protected $logo;*/
    protected $title;
    protected $header;
    protected $footer;
    protected $footerPosition;
    protected $background;

    public function __construct(
        /* $logo = 'images/wood_castle_png.png',*/
        $title = 'INVOICE',
        $background = false,
        $footerPosition = 260,
        $header = 'invoices.print.default_header',
        $footer = 'invoices.print.default_footer',
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    )
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->title = $title;
        $this->footerPosition = intval(session()->get('settings.misc.footer_position') * 3);
        $this->header = $header;
        $this->footer = $footer;
        $this->background = $background;
        /*$this->logo = $logo;*/
    }

    public function Header()
    {
        if ($this->background) {
            $bMargin = $this->getBreakMargin();     // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;// disable auto-page-break
            $this->SetAutoPageBreak(false, 0);      // set bacground image
            $this->Image($this->background, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        } else {
            // set document information
            /*$this->SetCreator(PDF_CREATOR); $this->SetTitle('Invoice '); $this->SetSubject('Invoice Print'); $this->SetKeywords('TCPDF, PDF, example, test, guide'); $this->setColor('#CCCCCC');*/
            $this->SetAutoPageBreak($this->AutoPageBreak, $this->getBreakMargin());// restore auto-page-break status
            $this->setPageMark();// set the starting point for the page content
            // set default header data
            $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
            // set header and footer fonts
            $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            // set margins
            $this->SetMargins(5, 5, 5);
            $this->SetHeaderMargin(5);
            $this->SetFooterMargin(5);
            // set auto page breaks
            $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            // set image scale factor
            $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
            // set font
            $this->SetFont('dejavusans', '', 11);
            // $logo = $this->logo;
            $header = view($this->header)->render();
            $header = str_replace(strtolower('{title}'), strtoupper($this->title), $header);
            $this->writeHTMLCell(0, 0, '', '', $header, 0, 1, 0, true, '', true);
        }
    }

    public function Footer()
    {
        if($this->background == false) {
            $this->SetY($this->footerPosition);// Position at 90 mm from bottom
            $this->SetFont('dejavusans', 'I', 8);// Set font
            $html = view($this->footer)->render();
            $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        }
    }
}
