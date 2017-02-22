<?php

namespace App\Services;

use App\Models\Shorturl;

require_once dirname(__FILE__) . '/Node.php';

class TextRandomizer
{
    private $minDomainsMailRu = [
        'http://wapmaximus.ru/1',
        'http://wapmaximus.ru/2',
        'http://wapmaximus.ru/3',
        'http://wapmaximus.ru/4',
        'http://wapmaximus.ru/5',
        'http://wapmaximus.ru/6',
        'http://wapmaximus.ru/7',
        'http://wapmaximus.ru/8',
        'http://wapmaximus.ru/9',
        'http://wapmaximus.ru/a',
        'http://wapmaximus.ru/b',
        'http://wapmaximus.ru/c',
        'http://wapmaximus.ru/d',
        'http://wapmaximus.ru/e',
        'http://wapmaximus.ru/f',
        'http://wapmaximus.ru/g',
        'http://wapmaximus.ru/h',
        'http://wapmaximus.ru/i',
        'http://wapmaximus.ru/j',
        'http://wapmaximus.ru/k',
        'http://wapmaximus.ru/l',
        'http://wapmaximus.ru/m',
        'http://wapmaximus.ru/n',
        'http://wapmaximus.ru/o',
        'http://wapmaximus.ru/p',
        'http://wapmaximus.ru/q',
        'http://wapmaximus.ru/r',
        'http://wapmaximus.ru/s',
        'http://wapmaximus.ru/t',
        'http://wapmaximus.ru/u',
        'http://wapmaximus.ru/v',
        'http://wapmaximus.ru/w',
        'http://wapmaximus.ru/x',
        'http://wapmaximus.ru/y',
        'http://wapmaximus.ru/z',
        'http://wapmaximus.ru/10',
        'http://wapmaximus.ru/11',
        'http://wapmaximus.ru/12',
        'http://wapmaximus.ru/13',
        'http://wapmaximus.ru/14',
        'http://wapmaximus.ru/15',
        'http://wapmaximus.ru/16',
        'http://wapmaximus.ru/17',
        'http://wapmaximus.ru/18',
        'http://wapmaximus.ru/19',
        'http://wapmaximus.ru/1a',
        'http://wapmaximus.ru/1b',
        'http://wapmaximus.ru/1c',
        'http://wapmaximus.ru/1d',
        'http://wapmaximus.ru/1e',
    ];
    private $_text = '';

    private $_tree = null;
    private $_shortUrl = null;

    public function __construct($text = '', $domain = '', $forMailRu = false)
    {
        $text = (string) $text;
        if (!$forMailRu) {
            $key = 'AIzaSyB2FOhu6MpvgIpMpqYOwbaDt6po9x7-iCQ';
            $googer = new GoogleUrlApi($key);
            $shortDWName = $googer->shorten("https://docs.google.com/document/d/1-sytIIRkyse81jDKE8e-70m5uizQ5VNQv1jki9U73UI?p=" . time());
        } else {
            $shortDWName = $this->minDomainsMailRu[array_rand($this->minDomainsMailRu)];
        }
        $this->_shortUrl = $shortDWName;

        $text = str_replace('!faq_link!', '<a href="' . $shortDWName . '">' . $shortDWName . '</a>', $text);
        $text = str_replace('!dom_link!', $domain, $text);
        $this->_text = $text;
        $this->_tree = new Natty_TextRandomizer_Node;
        $preg = '/
            \\\\\\\            | # мнемонизированный слэш
            \\\\\+             | # мнемонизированный +
            \\\\\{             | # мнемонизированный {
            \\\\\}             | # мнемонизированный }
            \\\\\[             | # мнемонизированный [
            \\\\\]             | # мнемонизированный ]
            \\\\\|             | # мнемонизированный |
            \\\                | # никчемный слэш
            \[\+               | # начало разделителя
            \+                 | # возможно, конец разделителя перетановок
            \{                 | # начало перебора
            \}                 | # конец перевора
            \[                 | # начало перестановки
            \]                 | # конец перестановки
            \|                 | # разделитель вариантов
            [^\\\+\{\}\[\]\|]+   # все прочее
            /xu';
        $currentNode = $this->_tree;
        $currentNode = new Natty_TextRandomizer_Node($currentNode);
        $currentNode->setType('series');
        $currentNode = $currentNode->concat('');
        while (preg_match($preg, $text, $match)) {
            switch ($match[0]) {
                case '\\\\':
                case '\\':
                    $currentNode = $currentNode->concat('\\');
                    break;
                case '\+':
                    $currentNode = $currentNode->concat('+');
                    break;
                case '\{':
                    $currentNode = $currentNode->concat('{');
                    break;
                case '\}':
                    $currentNode = $currentNode->concat('}');
                    break;
                case '\[':
                    $currentNode = $currentNode->concat('[');
                    break;
                case '\]':
                    $currentNode = $currentNode->concat(']');
                    break;
                case '\|':
                    $currentNode = $currentNode->concat('|');
                    break;
                case '[+':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode->parent);
                    } else {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode);
                    }
                    $currentNode->isSeparator = true;
                    break;
                case '+':
                    if ($currentNode->isSeparator) {
                        $currentNode->isSeparator = false;
                        $currentNode = new Natty_TextRandomizer_Node($currentNode);
                        $currentNode->setType('series');
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('+');
                    }
                    break;
                case '{':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode->parent);
                    } else {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode);
                    }
                    $currentNode->setType('synonyms');
                    $currentNode = new Natty_TextRandomizer_Node($currentNode);
                    $currentNode->setType('series');
                    $currentNode = $currentNode->concat('');
                    break;
                case '}':
                    $is = $currentNode->parent->parent;
                    if ($is && 'synonyms' == $is->type) {
                        $currentNode = $is->parent;
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('}');
                    }
                    break;
                case '[':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode->parent);
                    } else {
                        $currentNode = new Natty_TextRandomizer_Node($currentNode);
                    }
                    $currentNode = new Natty_TextRandomizer_Node($currentNode);
                    $currentNode->setType('series');
                    $currentNode = $currentNode->concat('');
                    break;
                case ']':
                    $is = $currentNode->parent->parent;
                    if ($is && 'mixing' == $is->type && $is->parent) {
                        $currentNode = $is->parent;
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat(']');
                    }
                    break;
                case '|':
                    $is = $currentNode->parent;
                    if ($is && 'series' == $is->type) {
                        $currentNode = $is->parent;
                        $currentNode = new Natty_TextRandomizer_Node($currentNode);
                        $currentNode->setType('series');
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('|');
                    }
                    break;
                default:
                    $currentNode = $currentNode->concat($match[0]);
            }
            $text = substr($text, strlen($match[0]));

        }
    }

    public function getText()
    {
        return $this->_tree->getText();
    }

    public function numVariant()
    {
        return $this->_tree->numVariant();
    }

    /**
     * @return bool|mixed|null
     */
    public function getShortUrl()
    {
        return $this->_shortUrl;
    }

}