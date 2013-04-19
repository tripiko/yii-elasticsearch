<?php
/**
 * Represents an Elastic Search document element
 * @author  Charles Pick
 * @author  Stratos Gerakakis
 * @package packages.elasticSearch
 */
class AElasticSearchDocumentElement extends CAttributeCollection {

    /**
     * The name of this document
     * @var string
     */
    protected $_name;

    /**
     * Constructor.
     *
     * @param array $data the intial data. Default is null, meaning no initialization.
     */
    public function __construct($data = null) {
        parent::__construct($data);
    }

    /**
     * Adds an item into the map.
     * If the item is an array, it will be converted to an instance of AElasticSearchCriteria
     *
     * @param mixed $key   key
     * @param mixed $value value
     */
    public function add($key, $value) {
        if (is_array($value) && count($value)) {
            if (is_string(array_shift(array_keys($value)))) {
                $value = new AElasticSearchDocumentElement($value);
            }
            else if (is_array(array_shift(array_values($value)))) {
                foreach ($value as $i => $item) {
                    if (is_array($item)) {
                        $value[$i] = new AElasticSearchDocumentElement($item);
                        $value[$i]->setName($i);
                    }
                }
            }
        }
        if ($value instanceof AElasticSearchDocumentElement) {
            $value->setName($key);
        }
        if ($this->caseSensitive)
            parent::add($key, $value);
        else
            parent::add(strtolower($key), $value);
    }

    /**
     * Gets the name of this document
     * @return string the document name
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Sets the name of this document, this will not be persisted!
     *
     * @param string $name the document name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * Gets the attributes to show for CDetailView widgets
     * @return array the attributes to show in a CDetailView widget
     */
    public function detailViewAttributes() {
        $attributes = array();
        foreach (array_keys($this->toArray()) as $attribute) {
            if (!is_array($this->{$attribute}) && !is_object($this->{$attribute})) {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @return array the list of items in array
     */
    public function toArray() {
        $data = array();
        foreach (parent::toArray() as $key => $value) {
            if ($value instanceof AElasticSearchDocumentElement) {
                $value = $value->toArray();
            }
            $data[$key] = $value;
        }

        return $data;
    }
}