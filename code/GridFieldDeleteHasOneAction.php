<?php

/**
 * This class is a {@link GridField} component that adds a delete action for HasOne objects.
 */
class GridFieldDeleteHasOneAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    protected $removeRelation = false;
    protected $instance = null;

    public function __construct(DataObject $instance, bool $removeRelation = false)
    {
        $this->removeRelation = $removeRelation;
        $this->instance = $instance;
    }

    /**
     * Add a column 'Delete'
     *
     * @param GridField $gridField
     * @param array $columns
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    /**
     * Return any special attributes that will be used for FormField::create_tag()
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    /**
     * Add the title
     *
     * @param GridField $gridField
     * @param string $columnName
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    /**
     * Which columns are handled by this component
     *
     * @param GridField $gridField
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return array('Actions');
    }

    /**
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return string - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($this->removeRelation) {
            if (!$record->canEdit()) {
                return;
            }

            $field = GridField_FormAction::create($gridField, 'UnlinkRelation' . $record->ID, false,
                "unlinkrelation", array('RecordID' => $record->ID))
                ->addExtraClass('gridfield-button-unlink')
                ->setAttribute('title', _t('GridAction.UnlinkRelation', "Unlink"))
                ->setAttribute('data-icon', 'chain--minus')
                ->setDescription(_t('GridAction.UnlinkRelation', 'Unlink'));
        } else {
            if (!$record->canDelete()) {
                return;
            }

            $field = GridField_FormAction::create($gridField, 'DeleteRecord' . $record->ID, false, "deleterecord",
                array('RecordID' => $record->ID))
                ->addExtraClass('gridfield-button-delete')
                ->setAttribute('title', _t('GridAction.Delete', "Delete"))
                ->setAttribute('data-icon', 'cross-circle')
                ->setDescription(_t('GridAction.DELETE_DESCRIPTION', 'Delete'));
        }
        return $field->Field();
    }

    /**
     * Which GridField actions are this component handling
     *
     * @param GridField $gridField
     * @return array
     */
    public function getActions($gridField)
    {
        return array('deleterecord', 'unlinkrelation');
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param GridField $gridField
     * @param string $actionName
     * @param mixed $arguments
     * @param array $data - form data
     * @return void
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'deleterecord' || $actionName == 'unlinkrelation') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }
            if ($actionName == 'deleterecord') {
                if (!$item->canDelete()) {
                    throw new ValidationException(
                        _t('GridFieldAction_Delete.DeletePermissionsFailure', "No delete permissions"), 0);
                }
                $item->delete();
            } else {
                if (!$this->instance->canEdit()) {
                    throw new ValidationException(
                        _t('GridFieldAction_Delete.EditPermissionsFailure', "No permission to unlink record"), 0);
                }
            }
            $gridField->getList()->remove($item);
        }
    }
}
