<?php

namespace modmore\Commerce\Tests\Modules;

use modmore\Commerce\Events\PDFWriter;
use modmore\Commerce\PDF\Writer\WriterInterface;
use modmore\Commerce\PDFCrowdWriter\Modules\PDFCrowdWriter;
use modmore\Commerce\PDFCrowdWriter\Writer;
use modmore\Commerce\Dispatcher\EventDispatcher;

class PDFCrowdWriterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Commerce $commerce */
    public $commerce;
    /** @var \modmore\Commerce\Adapter\AdapterInterface $adapter */
    public $adapter;

    public function setUp()
    {
        global $commerce;
        $this->commerce = $commerce;
        $this->adapter = $this->commerce->adapter;
    }

    public function testModuleRegistering()
    {
        $dispatcher = new EventDispatcher();
        $module = new PDFCrowdWriter($this->commerce);

        $module->initialize($dispatcher);
        self::assertCount(2, $dispatcher->getListeners());


        $event = new PDFWriter();

        // No config = no writer
        $module->getPDFWriter($event);
        $writers = $event->getWriters();
        self::assertEmpty($writers);

        // No apikey  = no writer
        $module->setConfiguration(['username' => 'foobar']);
        $module->getPDFWriter($event);
        $writers = $event->getWriters();
        self::assertEmpty($writers);

        // No username  = no writer
        $module->setConfiguration(['apikey' => 'blabla']);
        $module->getPDFWriter($event);
        $writers = $event->getWriters();
        self::assertEmpty($writers);

        // gotcha!
        $module->setConfiguration(['username' => 'foobar', 'apikey' => 'blabla']);
        $module->getPDFWriter($event);
        $writers = $event->getWriters();
        self::assertCount(1, $writers);
        $writer = reset($writers);
        self::assertInstanceOf(WriterInterface::class, $writer);
        self::assertInstanceOf(Writer::class, $writer);
    }
}
