<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Application\Form;

use Zend\Form\Element\Select as ZendSelect;

class Select extends ZendSelect
{

    /**
     * Provide default input rules for this element
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = [
            'name' => $this->getName(),
            'required' => false,
        ];

        if ($this->useHiddenElement() && $this->isMultiple()) {
            $unselectedValue = $this->getUnselectedValue();

            $spec['allow_empty'] = true;
            $spec['continue_if_empty'] = true;
            $spec['filters'] = [[
                'name'    => 'Callback',
                'options' => [
                    'callback' => function ($value) use ($unselectedValue) {
                        if ($value === $unselectedValue) {
                            $value = [];
                        }
                        return $value;
                    }
                ]
            ]];
        }

        if ($validator = $this->getValidator()) {
            $spec['validators'] = [
                $validator,
            ];
        }

        return $spec;
    }

    /**
     * Get only the values from the options attribute
     *
     * @return array
     */
    protected function getValueOptionsValues()
    {
        $values  = [];
        $options = $this->getValueOptions();
        foreach ($options as $key => $optionSpec) {
            if (is_array($optionSpec) && array_key_exists('options', $optionSpec)) {
                foreach ($optionSpec['options'] as $nestedKey => $nestedOptionSpec) {
                    $values[] = $this->getOptionValue($nestedKey, $nestedOptionSpec);
                }
                continue;
            }

            $values[] = $this->getOptionValue($key, $optionSpec);
        }
        return $values;
    }

    protected function getOptionValue($key, $optionSpec)
    {
        return is_array($optionSpec) ? $optionSpec['value'] : $key;
    }

    /**
     * Element has the multiple attribute
     *
     * @return bool
     */
    public function isMultiple()
    {
        return isset($this->attributes['multiple'])
            && ($this->attributes['multiple'] === true || $this->attributes['multiple'] === 'multiple');
    }
}
