<?php

/**
 * This class is a {@link GridField} component responsible for adding an existing object to a has_one relation.
 *
 * @package  michael.caruana/hasonefield
 * @author  Michael Caruana <mikeyc7m@gmail.com>
 */
class GridFieldAddExistingHasOneAutocompleter extends GridFieldAddExistingAutocompleter
{

    /**
     * @var string $resultsFormat The title used to render the search results.
     */
    protected $resultsFormat = '#$ID | $Title';

    protected $instance = null;

    public function __construct(DataObject $instance, $targetFragment = 'before', $searchFields = null)
    {
        parent::__construct($targetFragment, $searchFields);
        $this->instance = $instance;
    }

    /**
     * Detect searchable fields and searchable relations.
     * Falls back to {@link DataObject->summaryFields()} if
     * no custom search fields are defined.
     *
     * @param string $dataClass the class name
     * @return array names of the searchable fields
     */
    public function scaffoldSearchFields($dataClass): array
    {
        $fields = parent::scaffoldSearchFields($dataClass);
        // remove any ID lookup and always allow search by exact ID...
        unset($fields['ID:StartsWith']);
        array_unshift($fields, "ID:ExactMatch");
        return $fields;
    }

    /**
     * Add the item, don't bother manipulating state.
     *
     * @param GridField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data): void
    {
        if ($actionName == 'addto') {
            if (isset($data['relationID']) && $data['relationID']) {
                $objectID = (int)$data['relationID'];
                if (empty($objectID)) {
                    return;
                }
                $dataList = $gridField->getList();
                $object = DataObject::get_by_id($dataList->dataclass(), $objectID);
                if ($object) {
                    $dataList->add($object);
                }
            }
        }
    }

    /**
     * Just return the list - adding item to the list occurs elsewhere.
     * @param GridField $gridField
     * @param SS_List $dataList
     * @return SS_List
     */
    public function getManipulatedData(GridField $gridField, SS_List $dataList): SS_List
    {
        return $dataList;
    }

    /**
     * Sets the base list instance which will be used for the autocomplete
     * search.
     *
     * @param SS_List $list
     * @return GridFieldAddExistingHasOneAutocompleter
     */
    public function setSearchList(SS_List $list): GridFieldAddExistingHasOneAutocompleter
    {
        $this->searchList = $list;
        return $this;
    }
}
