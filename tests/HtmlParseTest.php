<?php
use Gswits\PdfToHtml\Html,
Gswits\PdfToHtml\Config;
class HtmlParseTest extends PHPUnit_Framework_TestCase
{
  public function testConvertAndCatch()
  {
    Config::set('pdfinfo.bin', '/usr/bin/pdfinfo');
    Config::set('pdftohtml.bin', '/usr/bin/pdftohtml');
    $pdf = dirname(__FILE__).'/source/test.pdf';
    $html = new Html($pdf);
    $this->assertInstanceOf('PHPHtmlParser\\Dom', $html);
  }
}
