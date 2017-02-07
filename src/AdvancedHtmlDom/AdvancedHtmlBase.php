<?php

namespace Deimos\AdvancedHtmlDom;

class AdvancedHtmlBase
{
    public $doc;
    public $dom;
    public $node;
    public $is_text = false;

    public function text()
    {
        return $this->node->nodeValue;
    }

    public function html()
    {
        return $this->doc->dom->saveHTML($this->node);
    }

    public function __toString()
    {
        return $this->html();
    }

    public function remove()
    {
        $this->node->parentNode->removeChild($this->node);

        return $this;
    }

    public function str()
    {
        return new Str($this->text);
    }

    public function match($re)
    {
        $str = new Str($this->text);

        return $str->match($re);
    }

    public function scan($re)
    {
        $str = new Str($this->text);

        return $str->scan($re);
    }

    public function clean($str)
    {
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    public function trim($str)
    {
        return trim($str);
    }

    public function find($css, $index = null)
    {
        $xpath = CSS::xpath_for($css);
        if (!isset($this->doc) || !isset($this->doc->xpath))
        {
            return null;
        }
        if (null === $index)
        {
            return new AHTMLNodeList($this->doc->xpath->query($xpath, $this->node), $this->doc);
        }
        else
        {
            $nl = $this->doc->xpath->query($xpath, $this->node);
            if ($index < 0)
            {
                $index = $nl->length + $index;
            }
            $node = $nl->item($index);

            return $node ? new AHTMLNode($node, $this->doc) : null;
        }
    }

    // magic methods
    public function __call($key, $args)
    {
        $key = strtolower(str_replace('_', '', $key));
        switch ($key)
        {
            case 'innertext':
                return ($this->is_text || !$this->children->length) ? $this->text() : $this->find('./text()|./*')->outertext;
            case 'plaintext':
                return $this->text();
            case 'outertext':
            case 'html':
            case 'save':
                return $this->html();
            case 'innerhtml':
                $ret = '';
                foreach ($this->node->childNodes as $child)
                {
                    $ret .= $this->doc->dom->saveHTML($child);
                }

                return $ret;

            case 'tag':
                return $this->node->nodeName;
            case 'next':
                return $this->at('./following-sibling::*[1]|./following-sibling::text()[1]|./following-sibling::comment()[1]');

            case 'index':
                return $this->search('./preceding-sibling::*')->length + 1;

            /*
            DOMNode::insertBefore — Adds a new child
            */

            // simple-html-dom junk methods
            case 'clear':
                return;

            // search functions
            case 'at':
            case 'getelementbytagname':
                return $this->find($args[0], 0);

            case 'search':
            case 'getelementsbytagname':
                return isset($args[1]) ? $this->find($args[0], $args[1]) : $this->find($args[0]);

            case 'getelementbyid':
                return $this->find('#' . $args[0], 0);
            case 'getelementsbyid':
                return isset($args[1]) ? $this->find('#' . $args[0], $args[1]) : $this->find('#' . $args[0]);

            // attributes
            case 'hasattribute':
                return !$this->is_text && $this->node->getAttribute($args[0]);
            case 'getattribute':
                $arg = $args[0];

                return $this->$arg;
            case 'setattribute':
                $arg0 = $args[0];
                $arg1 = $args[1];

                return $this->$arg0 = $arg1;
            case 'removeattribute':
                $arg = $args[0];

                return $this->$arg = null;
            case 'getattribute':
                return $this->node->getAttribute($args[0]);
            case 'setattribute':
                return $this->$args[0] = $args[1];
            case 'removeattribute':
                return $this->$args[0] = null;


            // wrap
            case 'wrap':
                return $this->replace('<' . $args[0] . '>' . $this . '</' . $args[0] . '>');
            case 'unwrap':
                return $this->parent->replace($this);

            case 'str':
                return new Str($this->text);

            // heirarchy
            case 'firstchild':
                return $this->at('> *');
            case 'lastchild':
                return $this->at('> *:last');
            case 'nextsibling':
                return $this->at('+ *');
            case 'prevsibling':
                return $this->at('./preceding-sibling::*[1]');
            case 'parent':
                return $this->at('./..');
            case 'children':
            case 'childnodes':
                $nl = $this->search('./*');

                return isset($args[0]) ? $nl[$args[0]] : $nl;
            case 'child': // including text/comment nodes
                $nl = $this->search('./*|./text()|./comment()');

                return isset($args[0]) ? $nl[$args[0]] : $nl;

        }

        // $doc->spans[x]
        if (preg_match(TAGS_REGEX, $key, $m))
        {
            return $this->find($m[1]);
        }
        if (preg_match(TAG_REGEX, $key, $m))
        {
            return $this->find($m[1], 0);
        }

        if (preg_match('/(clean|trim|str)(.*)/', $key, $m) && isset($m[2]))
        {
            $arg1 = $m[1];
            $arg2 = $m[2];

            return $this->$arg1($this->$arg2);
        }

        if (!preg_match(ATTRIBUTE_REGEX, $key, $m))
        {
            trigger_error('Unknown method or property: ' . $key, E_USER_WARNING);
        }
        if (!$this->node || $this->is_text)
        {
            return null;
        }

        return $this->node->getAttribute($key);
    }

    public function __get($key)
    {
        return $this->$key();
    }
}