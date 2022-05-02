<?php

/**
 * This class is a {@link GridField} responsible for managing a has_one relation.
 *
 * @package  michael.caruana/hasonefield
 * @author  Michael Caruana <mikeyc7m@gmail.com>
 */
class GridFieldHasOneField extends GridField
{

    protected $record;
    protected $parent;
    protected $relation;
    private $displayFields;

    public function __construct($parent, $name, $title = '')
    {
        $this->relation = $name;
        $this->record = $parent->{$name}();
        $this->parent = $parent;

        $list = $this->setupList();
        $config = $this->setupConfig($list);
        parent::__construct($name, $title, $list, $config);
    }

    /**
     * Returns a customised gridfield list with just the related has_one record, if any.
     * @return SS_List
     */
    private function setupList(): SS_List
    {
        $list = GridFieldRelationHasOneList::create($this->record, $this->relation, $this->parent);
        return $list->filter('ID', $this->parent->{$this->relation . 'ID'});
    }

    /**
     * Returns a customised gridfield config of either find/create, or list/edit/remove
     * @param $list SS_List The related DataObject, expressed as a list of one/none.
     * @return GridFieldConfig
     */
    private function setupConfig(SS_List $list): GridFieldConfig
    {
        $config = GridFieldConfig::create();
        if ($list->count()) {
            $fieldsConfig = new GridFieldDataColumns();
            if ($this->displayFields) {
                $fieldsConfig->setDisplayFields($this->displayFields);
            }
            $config->addComponent($fieldsConfig);
            $config->addComponent(new GridFieldTitleHeader());
            $config->addComponent(new GridFieldEditButton());
            $config->addComponent(new GridFieldDeleteHasOneAction($this->parent, false));
            $config->addComponent(new GridFieldDeleteHasOneAction($this->parent, true));
            $config->addComponent(new GridFieldDetailForm());
        } else {
            $config->addComponents(new GridFieldButtonRow('before'));
            $config->addComponents($add = new GridFieldAddNewButton('buttons-before-left'));
            $add->setButtonName('Create');
            $config->addComponents(new GridFieldAddExistingHasOneAutocompleter($this->parent, 'buttons-before-left'));
            $config->addComponent(new GridFieldDetailForm());
        }
        return $config;
    }

    /**
     * @return DataObject The related object to edit, if any.
     */
    public function getRecord(): ?DataObject
    {
        return $this->record;
    }


    /**
     * Returns the Gridfield as HTML, but refreshes the list/config first.
     * @param array $properties Any custom properties
     * @return HTMLText The gridfield as HTML.
     */
    public function FieldHolder($properties = array()): HTMLText
    {
        $this->setList($this->setupList());
        $this->setConfig($this->setupConfig($this->list));
        Requirements::css(basename(dirname(__DIR__)) . '/css/GridfieldHasOne.css', 'screen');
        return parent::FieldHolder($properties);
    }

    /**
     * Resets the gridfield config with a new list of fields to display
     * @param $fields array of field name => title
     */
    public function setDisplayFields(array $fields): void
    {
        $this->displayFields = $fields;
        $config = $this->setupConfig($this->list);
        $this->setConfig($config);
    }
}
