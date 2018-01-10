<?php

namespace Application\Model;

class AbstractModel
{

    public function __call($name, $arguments)
    {
        if (preg_match('/^(g|s)et(\w+)$/', $name, $matches)) {
            $property = lcfirst($matches[2]);
            if ($matches[1] == 's') {
                return $this->__set($property, $arguments[0]);
            } else {
                return $this->__get($property);
            }
        } else {
            throw new \Exception('Invalid method ' . $name);
        }
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        $property = '_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method();
        } else if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Exception('Invalid property specified : ' . $property);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        $property = '_' . $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new \Exception('Invalid property specified : ' . $property);
        }
    }

    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            $property = '_' . $key;
            if (property_exists($this, $property)) $this->$property = $value;
        }
    }

    public function toArray()
    {
        $vars = get_object_vars($this);
        $result = [];
        foreach ($vars as $key => $value) {
            $key = str_replace('_', '', $key);
            $result[$key] = $value;
        }
        return $result;
    }
}