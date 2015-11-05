<?php

namespace app\model\service;


class FileFilterIterator extends \FilterIterator {


    /**
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     * @since 5.1.0
     */
    public function accept () {
        $current = $this->getInnerIterator()->current();
        if (strpos($current, '\.'))
            return false;

        return true;
    }
}