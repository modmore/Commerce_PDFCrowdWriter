<?php
namespace modmore\Commerce\PDFCrowdWriter\Modules;
use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Widgets\Form\PasswordField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\PDFWriter;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce\PDFCrowdWriter\Writer;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class PDFCrowdWriter extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_pdfcrowdwriter:default');
        return $this->adapter->lexicon('commerce_pdfcrowdwriter');
    }

    public function getAuthor()
    {
        return 'modmore';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_pdfcrowdwriter.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_pdfcrowdwriter:default');

        $dispatcher->addListener(\Commerce::EVENT_GET_PDF_WRITER, array($this, 'getPDFWriter'));
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);
    }

    public function getPDFWriter(PDFWriter $event)
    {
        $username = $this->getConfig('username', '');
        $apikey = $this->getConfig('apikey', '');
        if (empty($username) || empty($apikey)) {
            return;
        }
        $converter = $this->getConfig('converter', '20.10');
        $instance = new Writer($username, $apikey);
        $instance->setConverterVersion($converter);
        $event->addWriter($instance);
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[username]',
            'label' => $this->adapter->lexicon('commerce_pdfcrowdwriter.username'),
            'description' => $this->adapter->lexicon('commerce_pdfcrowdwriter.username.desc'),
            'value' => $module->getProperty('username', '')
        ]);

        $fields[] = new PasswordField($this->commerce, [
            'name' => 'properties[apikey]',
            'label' => $this->adapter->lexicon('commerce_pdfcrowdwriter.apikey'),
            'description' => $this->adapter->lexicon('commerce_pdfcrowdwriter.apikey.desc'),
            'value' => $module->getProperty('apikey', '')
        ]);
        $fields[] = new SelectField($this->commerce, [
            'name' => 'properties[converter]',
            'label' => $this->adapter->lexicon('commerce_pdfcrowdwriter.converter'),
            'description' => $this->adapter->lexicon('commerce_pdfcrowdwriter.converter.desc'),
            'value' => $module->getProperty('converter', 'latest'),
            'options' => [
                [
                    'value' => 'latest',
                    'label' => 'latest',
                ],
                [
                    'value' => '20.10',
                    'label' => '20.10',
                ],
                [
                    'value' => '18.10',
                    'label' => '18.10',
                ],
            ]
        ]);
        //latest|20.10|18.10

        return $fields;
    }


    public function addLibrariesToAbout(PageEvent $event)
    {
        $lockFile = dirname(dirname(__DIR__)) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_pdfcrowdwriter'),
                'introduction' => '', // Could add information about how libraries are used, if you'd like
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }
}
