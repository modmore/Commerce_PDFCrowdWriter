<?php
namespace modmore\Commerce\PDFCrowdWriter;

use modmore\Commerce\PDF\Exception\InvalidOutputException;
use modmore\Commerce\PDF\Exception\MissingSourceException;
use modmore\Commerce\PDF\Exception\RenderException;
use modmore\Commerce\PDF\Writer\FromHtmlWriterInterface;
use modmore\Commerce\PDF\Writer\WriterInterface;
use Pdfcrowd\Error;

final class Writer implements WriterInterface, FromHtmlWriterInterface
{
    /** @var resource */
    private $target;
    /** @var string */
    private $source;

    /** @var \Pdfcrowd\HtmlToPdfClient */
    private $client;

    public function __construct($username, $apiKey)
    {
        $this->client = new \Pdfcrowd\HtmlToPdfClient($username, $apiKey);
    }

    /**
     * @param string $html
     * @return void
     */
    public function setSourceHtml($html)
    {
        $this->source = $html;
    }

    /**
     * @param string $file
     * @return void
     * @throws InvalidOutputException
     */
    public function setOutputFile($file)
    {
        $this->target = fopen($file, 'wb+');
        if (!$this->target) {
            throw new InvalidOutputException('Could not open target stream.');
        }
    }

    /**
     * @param array $options
     * @return string
     * @throws InvalidOutputException
     * @throws MissingSourceException
     * @throws RenderException
     */
    public function render(array $options = [])
    {
        if ($this->source === null) {
            throw new MissingSourceException('Source HTML string not provided');
        }

        if (!$this->target) {
            throw new InvalidOutputException('Could not open target stream.');
        }

        try {
            $binary = $this->client->convertString($this->source);
        } catch (Error $e) {
            throw new RenderException('Failed generating PDF: ' . $e->getMessage(), $e->getCode(), $e);
        }
        fwrite($this->target, $binary);
        fclose($this->target);

        return $binary;
    }

    public function setConverterVersion($version): void
    {
        try {
            $this->client->setConverterVersion($version);
        } catch (Error $e) {
            // quietly let it use the default
        }
    }
}
