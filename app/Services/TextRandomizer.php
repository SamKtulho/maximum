<?php

namespace App\Services;

require_once dirname(__FILE__) . '/Node.php';
require_once dirname(__FILE__) . '/GoogleUrlApi.php';


class TextRandomizer
{
    private $_text = '';

    private $_tree = null;

    public function __construct($text = '', $domain = '')
    {
        $text = (string) $text;
        $key = 'AIzaSyB2FOhu6MpvgIpMpqYOwbaDt6po9x7-iCQ';
        $googer = new GoogleURLAPI($key);
        $shortDWName = $googer->shorten("https://docs.google.com/document/d/1-sytIIRkyse81jDKE8e-70m5uizQ5VNQv1jki9U73UI?p=" . time());

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
}