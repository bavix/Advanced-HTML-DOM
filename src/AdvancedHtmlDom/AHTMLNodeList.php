<?php

namespace Deimos\AdvancedHtmlDom;

class AHTMLNodeList implements \Iterator, \Countable, \ArrayAccess
{
    private $nodeList;
    private $doc;
    private $counter = 0;

    public function __construct($nodeList, $doc)
    {
        $this->nodeList = $nodeList;
        $this->doc      = $doc;
    }

    /*
    abstract public boolean offsetExists ( mixed $offset )
    abstract public mixed offsetGet ( mixed $offset )
    abstract public void offsetSet ( mixed $offset , mixed $value )
    abstract public void offsetUnset ( mixed $offset )
    */

    public function offsetExists($offset)
    {
        return 0 <= $offset && $offset < $this->nodeList->length;
    }

    public function offsetGet($offset)
    {
        return new AHTMLNode($this->nodeList->item($offset), $this->doc);
    }

    public function offsetSet($offset, $value)
    {
        trigger_error('offsetSet not implemented', E_USER_WARNING);
    }

    public function offsetUnset($offset)
    {
        trigger_error('offsetUnset not implemented', E_USER_WARNING);
    }

    public function count()
    {
        return $this->nodeList->length;
    }

    public function rewind()
    {
        $this->counter = 0;
    }

    public function current()
    {
        return new AHTMLNode($this->nodeList->item($this->counter), $this->doc);
    }

    public function key()
    {
        return $this->counter;
    }

    public function next()
    {
        $this->counter++;
    }

    public function valid()
    {
        return $this->counter < $this->nodeList->length;
    }

    public function last()
    {
        return ($this->nodeList->length > 0) ? new AHTMLNode($this->nodeList->item($this->nodeList->length - 1), $this->doc) : null;
    }

    public function remove()
    {
        foreach ($this as $node)
        {
            $node->remove();
        }

        return $this;
    }

    public function map($c)
    {
        $ret = array();
        foreach ($this as $node)
        {
            $ret[] = $c($node);
        }

        return $ret;
    }


    //math methods
    public function doMath($nl, $op = 'plus')
    {
        $paths       = array();
        $other_paths = array();

        foreach ($this as $node)
        {
            $paths[] = $node->node->getNodePath();
        }
        foreach ($nl as $node)
        {
            $other_paths[] = $node->node->getNodePath();
        }
        switch ($op)
        {
            case 'plus':
                $new_paths = array_unique(array_merge($paths, $other_paths));
                break;
            case 'minus':
                $new_paths = array_diff($paths, $other_paths);
                break;
            case 'intersect':
                $new_paths = array_intersect($paths, $other_paths);
                break;
        }

        return new AHTMLNodeList($this->doc->xpath->query(implode('|', $new_paths)), $this->doc);
    }

    public function minus($nl)
    {
        return $this->doMath($nl, 'minus');
    }

    public function plus($nl)
    {
        return $this->doMath($nl, 'plus');
    }

    public function intersect($nl)
    {
        return $this->doMath($nl, 'intersect');
    }


    // magic methods
    public function __call($key, $values)
    {
        $key = strtolower(str_replace('_', '', $key));
        switch ($key)
        {
            case 'to_a':
                $retval = array();
                foreach ($this as $node)
                {
                    $retval[] = new AHTMLNode($this->nodeList->item($this->counter), $this->doc);
                }

                return $retval;
        }
        // otherwise

        $retval = array();

        /*
            if(preg_match(TAGS_REGEX, $key, $m)) return $this->find($m[1]);
            if(preg_match(TAG_REGEX, $key, $m)) return $this->find($m[1], 0);
        */

        if (preg_match(ATTRIBUTES_REGEX, $key, $m) || preg_match('/^((clean|trim|str).*)s$/', $key, $m))
        {
            foreach ($this as $node)
            {
                $arg      = $m[1];
                $retval[] = $node->$arg;
            }

            return $retval;
        }

        if (preg_match(ATTRIBUTE_REGEX, $key, $m))
        {
            foreach ($this as $node)
            {
                $arg      = $m[1];
                $retval[] = $node->$arg;
            }

            return implode('', $retval);
        }

        // what now?
        foreach ($this as $node)
        {
            $retval[] = isset($values[0]) ? $node->$key($values[0]) : $node->$key();
        }

        return implode('', $retval);
    }

    public function __get($key)
    {
        return $this->$key();
    }

    public function __set($name, $value)
    {
        throw new \InvalidArgumentException();
    }

    public function __isset($name)
    {
        return true;
    }

    public function __toString()
    {
        return (string)$this->html();
    }

    public function length()
    {
        return $this->nodeList->length;
    }
}