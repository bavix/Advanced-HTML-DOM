<?php

namespace Bavix\AdvancedHtmlDom;

use Bavix\AdvancedHtmlDom\CacheSystem\InterfaceCache;

class AdvancedHtmlDom extends AdvancedHtmlBase
{

    /**
     * @var
     */
    public $xpath;

    /**
     * @var
     */
    public $root;

    /**
     * @var InterfaceCache
     */
    protected $cache;

    /**
     * AdvancedHtmlDom constructor.
     *
     * @param null $html
     * @param bool $is_xml
     */
    public function __construct($html = null, $is_xml = false)
    {
        $this->doc = $this;
        if ($html) {
            $this->load($html, $is_xml);
        }
    }

    /**
     * @param      $html
     * @param bool $is_xml
     */
    public function load($html, $is_xml = false)
    {
        $this->dom = new \DOMDocument();
        if ($is_xml) {
            $html = \preg_replace('/xmlns=".*?"/ ', '', $html);
        }

        $this->dom->loadHTML($html, LIBXML_NOERROR);
        $this->xpath = new \DOMXPath($this->dom);
        //$this->root = new AHTMLNode($this->dom->documentElement, $this->doc);
        $this->root = $this->at('body');
    }

    /**
     * @inheritdoc
     */
    public function __destruct()
    {
        $this->xpath = $this->root = null;
        unset($this->xpath, $this->root);
        parent::__destruct();
    }

    /**
     * @param InterfaceCache $cache
     */
    public function setCache(InterfaceCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param      $file
     * @param bool $is_xml
     *
     * @deprecated loadFile
     */
    public function load_file($file, $is_xml = false)
    {
        $this->loadFile($file, $is_xml);
    }

    /**
     * @param string $file
     * @param bool $is_xml
     *
     * @return $this
     */
    public function loadFile($file, $is_xml = false)
    {
        $this->load($this->cache($file), $is_xml);

        return $this;
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function cache($url)
    {
        if (!$this->cache) {
            return \file_get_contents($url);
        }

        return $this->cache->get($url);
    }

    // special cases

    /**
     * @return mixed
     */
    public function text()
    {
        return $this->root->text;
    }

    /**
     * @return mixed
     */
    public function title()
    {
        return $this->at('title')->text();
    }

}
