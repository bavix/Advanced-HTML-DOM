<?php

namespace Bavix\AdvancedHtmlDom;

use Bavix\AdvancedHtmlDom\CacheSystem\InterfaceCache;

$attributes = [
    'href', 'src', 'id', 'class', 'name',
    'text', 'height', 'width', 'content',
    'value', 'title', 'alt',
    'placeholder',
];

$tags = [
    'a', 'abbr', 'address', 'area', 'article', 'aside',
    'audio', 'b', 'base', 'blockquote', 'body', 'br',
    'button', 'canvas', 'caption', 'cite', 'code', 'col',
    'colgroup', 'data', 'datalist', 'dd',
    'detail', 'dialog', 'div', 'dl', 'dt', 'em',
    'embed', 'fieldset', 'figcaption', 'figure', 'footer', 'form',
    'font', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'image',
    'input', 'label', 'legend', 'li', 'map', 'mark', 'menu', 'meta',
    'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'p',
    'param', 'pre', 'script', 'section', 'select', 'small', 'source', 'span',
    'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td',
    'textarea', 'tfoot', 'th', 'thead', 'title', 'tr',
    'track', 'u', 'ul', 'var', 'video',
];

$tags = \implode('|', $tags);
$attributes = \implode('|', $attributes);

/**
 * TAG_REGEX
 */
\define('TAG_REGEX', '/^(' . $tags . ')$/');

/**
 * TAGS_REGEX
 */
\define('TAGS_REGEX', '/^(' . $tags . ')e?s$/');

/**
 * ATTRIBUTE_REGEX
 */
\define('ATTRIBUTE_REGEX', '/^(' . $attributes . '|data-[\w\-]+)$/');

/**
 * ATTRIBUTES_REGEX
 */
\define('ATTRIBUTES_REGEX', '/^(' . $attributes . '|data-[\w\-]+)e?s$/');

/**
 * @param string $html
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 *
 * @deprecated strGetHtml
 */
function str_get_html($html, InterfaceCache $cache = null)
{
    return strGetHtml($html, $cache);
}

/**
 * @param string $html
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 */
function strGetHtml($html, InterfaceCache $cache = null)
{
    $adv = new AdvancedHtmlDom($html);

    if ($cache) {
        $adv->setCache($cache);
    }

    return $adv;
}

/**
 * @param string $url
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 *
 * @deprecated fileGetHtml
 */
function file_get_html($url, InterfaceCache $cache = null)
{
    return fileGetHtml($url, $cache);
}

/**
 * @param string $url
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 */
function fileGetHtml($url, InterfaceCache $cache = null)
{
    if ($cache) {
        return strGetHtml($cache->get($url));
    }

    return strGetHtml(file_get_contents($url));
}

/**
 * @param string $html
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 *
 * @deprecated strGetXml
 */
function str_get_xml($html, InterfaceCache $cache = null)
{
    return strGetXml($html, $cache);
}

/**
 * @param string $html
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 */
function strGetXml($html, InterfaceCache $cache = null)
{
    $adv = new AdvancedHtmlDom($html, true);

    if ($cache) {
        $adv->setCache($cache);
    }

    return $adv;
}

/**
 * @param string $url
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 *
 * @deprecated fileGetXml
 */
function file_get_xml($url, InterfaceCache $cache = null)
{
    return fileGetXml($url, $cache);
}

/**
 * @param string $url
 * @param InterfaceCache $cache
 *
 * @return AdvancedHtmlDom
 */
function fileGetXml($url, InterfaceCache $cache = null)
{
    if ($cache) {
        return strGetXml($cache->get($url));
    }

    return strGetXml(file_get_contents($url));
}
