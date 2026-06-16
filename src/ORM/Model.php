<?php

namespace Zenwalker\CommerceML\ORM;

use ArrayObject;
use SimpleXMLElement;
use Zenwalker\CommerceML\CommerceML;

use function array_slice;

/**
 * Class Model
 *
 * @package Zenwalker\CommerceML\ORM
 * @property string id
 * @property string name
 * @property string value
 */
abstract class Model extends ArrayObject
{

    public CommerceML $owner;

    public ?SimpleXMLElement $xml = null;
    private bool $namespaceRegistered = false;

    /**
     * Model constructor.
     *
     * @param CommerceML $owner
     * @param \SimpleXMLElement|null $xml
     */
    public function __construct(CommerceML $owner, ?SimpleXMLElement $xml = null)
    {
        $this->owner = $owner;

        $this->xml = $xml ?: $this->loadXml();
        $this->init();
        parent::__construct();
    }

    /**
     * @return array
     */
    public function propertyAliases(): array
    {
        return [
            'Ид' => 'id',
            'Наименование' => 'name',
            'Значение' => 'value',
        ];
    }

    /**
     * @return string
     */
    public function getClearId(): string
    {
        return explode('#', $this->id)[0];
    }

    /**
     * @return string
     */
    public function getIdSuffix(): string
    {
        return (string)array_slice(explode('#', $this->id), 1)[0];
    }

    /**
     * @param $name
     * @return mixed|null|\SimpleXMLElement|string
     */
    public function __get($name)
    {
        if (method_exists($this, $method = 'get' . ucfirst($name))) {
            return $this->$method();
        }
        if ($this->xml) {
            $attributes = $this->xml;
            if (isset($attributes[$name])) {
                return trim((string)$attributes[$name]);
            }
            if ($value = $this->xml->{$name}) {
                return $value;
            }
            if (($value = $this->getPropertyAlias($name)) !== null) {
                return $value;
            }
        }
        return null;
    }

    public function __set($name, $value)
    {
    }

    public function __isset($name)
    {
    }

    public function loadXml(): ?SimpleXMLElement
    {
        $this->registerNamespace();

        return null;
    }

    public function init(): void
    {
        $this->registerNamespace();
    }

    /**
     * Лучше использовать данный метод, вместо стандартного xpath у SimpleXMLElement,
     * т.к. есть проблемы с неймспейсами xmlns
     *
     * Для каждого элемента необходимо указывать наймспейс "c", например:
     * //c:Свойство/c:ВариантыЗначений/c:Справочник[c:ИдЗначения = ':параметр']
     *
     * @param string $path
     * @param array $args - Аргументы задаём в бинд стиле ['параметр'=>'значение'] без двоеточия
     * @return \SimpleXMLElement[]
     */
    public function xpath(string $path, array $args = []): array
    {
        $this->registerNamespace();
        if (!$this->namespaceRegistered) {
            $path = str_replace('c:', '', $path);
        }
        if (!empty($args)) {
            foreach ($args as $ka => $kv) {
                $replace = (str_contains($kv, "'") ? ("concat('" . str_replace("'", "',\"'\",'", $kv) . "')") : "'" . $kv . "'");
                $path = str_replace(':' . $ka, $replace, $path);
            }
        }
        return $this->xml->xpath($path);
    }

    /**
     * @param $name
     * @return null|string
     */
    protected function getPropertyAlias($name): ?string
    {
        $attributes = $this->xml;
        $aliases = $this->propertyAliases();
        while ($idx = array_search($name, $aliases, true)) {
            if (isset($attributes[$idx])) {
                return trim((string)$attributes[$idx]);
            }
            if (isset($this->xml->{$idx})) {
                return trim((string)$this->xml->{$idx});
            }
            unset($aliases[$idx]);
        }
        return null;
    }

    protected function registerNamespace(): void
    {
        if ($this->xml && !$this->namespaceRegistered && ($namespaces = $this->xml->getNamespaces())) {
            $this->namespaceRegistered = true;
            foreach ($namespaces as $namespace) {
                $this->xml->registerXPathNamespace('c', $namespace);
            }
        }
    }
}
