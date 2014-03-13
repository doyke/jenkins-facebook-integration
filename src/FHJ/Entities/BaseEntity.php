<?php

namespace FHJ\Entities;

/**
 * BaseEntity
 * @package FHJ\Entities
 */
class BaseEntity {

    protected function checkInt($value, $fieldName) {
        if (!is_int($value)) {
            throw new InvalidArgumentException(sprintf('The field "%s" must be of type int',
                $fieldName));
        }
    }
    
    protected function checkBoolean($value, $fieldName) {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(sprintf('The field "%s" must be of type bool',
                $fieldName));
        }
    }

}
