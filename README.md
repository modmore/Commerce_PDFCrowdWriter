# PDFCrowd PDF Writer for Commerce

[PDFCrowd v2](https://pdfcrowd.com/) implementation for Commerce, allowing you to generate PDFs for invoices.

Requires [Commerce 1.0+](https://www.modmore.com/commerce/)

## Install as a package

A package is available in _packages on GitHub and from the modmore.com package provider.

## Building from source

To run the module from source, for example if you'd like to contribute a change, you'll need to take a few steps.

1. Clone the repository (or better yet, a clone of your own fork)

2. Copy config.core.sample.php to config.core.php, and if needed adjust it so that it includes your MODX site's config.core.php. Make sure you have [Commerce](https://www.modmore.com/commerce/) installed as well, of course.

3. From the browser open `_bootstrap/index.php`, this will set up the necessary settings and will make the module known to Commerce.

4. Enable the "PDF Writer: PDFCrowd" module under Configuration > Modules and enter your credentials.
