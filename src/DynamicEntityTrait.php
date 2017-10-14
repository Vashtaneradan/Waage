<?php
namespace Kanti;

/**
 * Trait AbstractEntity
 *
 * @package AUS\AusUtility\Extbase\DomainObject
 * @author anders und sehr GmbH <m.hoelzle@andersundsehr.com>
 */
trait DynamicEntityTrait
{

    /**
     * Dynamic getter and setter
     *
     * @param string $methodName
     * @param array $params
     *
     * @return mixed|null
     * @throws \RunTimeException
     */
    public function __call($methodName, $params)
    {
        $methodPrefix = substr($methodName, 0, 3);
        $attributeName = lcfirst(substr($methodName, 3));
        $attributePlName = $attributeName . 's';

        if ($attributeName !== '') {
            if ($methodPrefix == 'set') {
                $value = $params[0];
                if (!property_exists($this, $attributeName)) {
                    throw new \RunTimeException('method not implemented', 1503411617);
                }
                $this->{$attributeName} = $value;
                return $this;
            } elseif ($methodPrefix == 'get') {
                if (!property_exists($this, $attributeName)) {
                    throw new \RunTimeException('method not implemented', 1503411618);
                }
                return $this->{$attributeName};
            } elseif ($methodPrefix == 'add') {
                if (!property_exists($this, $attributePlName)) {
                    throw new \RunTimeException('method not implemented', 1503411619);
                }
                $this->{$attributePlName}->attach($params[0]);
                return $this; //fluent interface
            } elseif (substr($methodName, 0, 6) == 'remove') {
                if (!property_exists($this, $attributePlName)) {
                    throw new \RunTimeException('method not implemented', 1503411620);
                }
                $this->{$attributePlName}->detach($params[0]);
                return $this; //fluent interface
            }
            throw new \RunTimeException('method not implemented', 1503411616);
        }
        throw new \RunTimeException('method not implemented', 1503411616);
    }
}