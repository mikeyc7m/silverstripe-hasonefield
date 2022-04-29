<?php

class GridFieldRelationHasOneList extends DataList
{

    protected $record;
    protected $name;
    protected $parent;

    /**
     * @param $record DataObject The related object.
     * @param $name String The name of the field relation, minus the "ID" bit.
     * @param $parent DataObject The parent object.
     */
    public function __construct($record, string $name, DataObject $parent)
    {
        $this->record = $record;
        $this->name = $name;
        $this->parent = $parent;
        parent::__construct($record->ClassName);
    }

    /**
     * @param DataObject $item The DataObject to add to the has_one relationship. Does not create.
     * @throws ValidationException
     */
    public function add($item): void
    {
        if ($item && $item->ID) {
            $this->parent->{$this->name . "ID"} = $item->ID;
            $this->parent->write();
        }
    }

    /**
     * @param DataObject $item The DataObject to remove from the has_one relationship. Does not delete.
     * @throws ValidationException
     */
    public function remove($item): void
    {
        if ($item && $item->ID) {
            $this->parent->{$this->name . "ID"} = 0;
            $this->parent->write();
        }
    }
}
