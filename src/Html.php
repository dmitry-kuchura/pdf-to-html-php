<?php

namespace Gswits\PdfToHtml;

use DOMNode;
use DOMXPath;
use DOMDocument;
use PHPHtmlParser\Dom;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class Html extends Dom
{

    protected $filesystem;

    protected $contents, $total_pages, $current_page, $pdf_file, $locked = false;

    protected $default_options = [
        'singlePage' => true,
        'noFrames'   => false,
    ];

    public function __construct($pdf_file, $options = [])
    {
        $this->filesystem = new Filesystem();

        $options = array_merge($this->default_options, $options);

        $this->getContents($pdf_file, $options);

        return $this;
    }


    /**
     * @param $pdf_file
     * @param array $options
     */
    private function getContents($pdf_file, $options)
    {
        $this->locked = true;
        $info = new Pdf($pdf_file);
        $pdf = new Base($pdf_file, $options);
        $pages = $info->getPages();

        $outputdir = $this->setOutputDirectory($pdf);

        $pdf->generate();
        $fileinfo = pathinfo($pdf_file);
        $base_path = $pdf->outputDir.'/'.$fileinfo['filename'];
        $contents = [];
        for ($i = 1; $i <= $pages; $i++) {
            $content = file_get_contents($base_path.'-'.$i.'.html');
            $content = str_replace("Â", "", $content);
            if ($this->inlineCss()) {
                $dom = new DOMDocument();
                $dom->loadHTML($content);
                $xpath = new DOMXPath($dom);
                foreach ($xpath->query('//comment()') as $comment) {
                    $comment->parentNode->removeChild($comment);
                }
                $body = $xpath->query('//body')->item(0);
                $content = $body instanceof DOMNode ? $dom->saveHTML($body) : 'something failed';
            }
            file_put_contents($base_path.'-'.$i.'.html', $content);
            $contents[ $i ] = file_get_contents($base_path.'-'.$i.'.html');
        }
        $this->contents = $contents;
        $this->goToPage(1);
    }

    /**
     * Set the output directory for our temporary file storage.
     *
     * @return self
     */
    public function setOutputDirectory(Base &$pdf): string
    {
        $outputDir = Config::get('pdftohtml.output', dirname(__FILE__).'/../output/'.uniqid());

        $this->makeDirectoryIfNotExists($outputDir);

        $pdf->setOutputDirectory($outputDir);

        return $outputDir;
    }

    /**
     * Create the temporary output directory if it does not exist in the file system.
     *
     * @param string $outputDir
     * @return void
     */
    public function makeDirectoryIfNotExists(string $outputDir): void
    {
        if (!$this->filesystem->exists($outputDir)) {
            $this->filesystem->mkdir($outputDir, 0777, true);
        }
    }

    public function goToPage($page = 1)
    {
        if ($page > count($this->contents))
            throw new \Exception("You're asking to go to page {$page} but max page of this document is ".count($this->contents));
        $this->current_page = $page;

        return $this->load($this->contents[ $page ]);
    }

    public function raw($page = 1)
    {
        return $this->contents[ $page ];
    }

    public function getTotalPages()
    {
        return count($this->contents);
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function inlineCss()
    {
        return Config::get('pdftohtml.inlineCss', true);
    }
}
