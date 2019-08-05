[![Build Status](https://api.travis-ci.com/dmitry-kuchura/pdf-to-html-php.svg?branch=master)](https://travis-ci.org/mgufrone/pdf-to-html)

# PDF to HTML PHP Class

A simple class for converting PDF files into HTML documents. This package was forked from the [original maintainer](https://github.com/mgufrone/pdf-to-html). As it has since been abandoned, I've decided to migrate the package and port it so that it can be used in php 7.2+ environments.

## Installation

```
composer require dmitry-kuchura/pdf-to-html-php
```

Or add this package to your `composer.json`

```json
{
  "dmitry-kuchura/pdf-to-html-php": "^2.0.1"
}
```

## Requirements

1. You **must** install the `poppler-utils` package on your system. You must also make sure that the user who owns `poppler-utils` aligns with the your `Nginx` user, otherwise you will not be able to access this package.

2. Before instantiating the `Pdf` class, you will need to tell the library about the location of your binaries. Without this, the default fallback will be used (which is likely incorrect for most people) and you will receive a generic error. You may do this by using the `Config::set` method of this class.

> Note: The `Config` method is the same repository implementation that Laravel uses.

```php
\Gswits\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');

\Gswits\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');
```

## Usage

Having setup your poll-utils package and provided the location to the library, you can proceed with the following:

> WARNING! If you're not working in an environment that automatically loads the vendor list from composer, you will need to manually do so yourself by adding `include /vendor/autoload.php` at the top of your file. If you're in Laravel, you do not need this.

### Usage:

```php
namespace App\Http\Controllers;

use Kuchura\PdfToHtml\Config;
use Kuchura\PdfToHtml\Pdf;

class PdfController
{
    public function pdf()
    {
        Config::set('pdftohtml.bin', '/usr/bin/pdftohtml');
        Config::set('pdfinfo.bin', '/usr/bin/pdfinfo');

        $pdf = new Pdf(public_path() . '/test.pdf');

        $html = $pdf->html();
    }
}
```

### Passing options to getDOM

By default `getDom()` will extract all of the images contained in the pdf. If you do not wish to maintain the images, you can specify this property prior to calling `\$pdf->html() to generate your HTML document.

```php
$pdfDom = $pdf->getDom(['ignoreImages' => true]);
```

### Available Options

Additionally, you may pass several arguments to the `Pdf` constructor. These arguments are passed as flags to the underlying `pdftohtml` binary. You can [view the man page for a full list of options](https://www.mankier.com/1/pdftohtml)

- singlePage, default: false
- imageJpeg, default: false
- ignoreImages, default: false
- zoom, default: 1.5
- noFrames, default: true

## Usage note for OS/X Users

Thanks to @kaleidoscopique for giving a try and make it run on OS/X for this package

**1. Install brew**

Brew is a famous package manager on OS/X : http://brew.sh/ (aptitude style).

**2. Install poppler**

```bash
brew install poppler
```

**3. Verify the path of pdfinfo and pdftohtml**

```bash
$ which pdfinfo
/usr/local/bin/pdfinfo

$ which pdftohtml
/usr/local/bin/pdfinfo
```

## Feedback & Contribute

Send me an issue for improvement or any buggy thing. I love to help and solve another people problems. Thanks :+1:
