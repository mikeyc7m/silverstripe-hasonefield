<?php

/**
 * HasOneField is a tidy way to display & manipulate a has_one relationship. Includes Create, Edit, Unlink, Delete, and
 * a search feature to Link to existing items.
 *
 * Example Usage: $field = new HasOneField($this, 'MyRelation', 'My Related Field');
 *
 * @author Michael Caruana
 */
class HasOneField extends CompositeField
{
    public function __construct($parent, $relation, $title = '')
    {
        parent::__construct(GridFieldHasOneField::create($parent, $relation, $title));
        $this->setName($relation . '_HasOneHolder')
            ->setLegend(CompositeField::name_to_label($relation))
            ->setTag('fieldset')
            ->addExtraClass('gridfield-has-one');
    }
}
