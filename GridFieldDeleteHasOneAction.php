<?php

/**
 * This class is a {@link GridField} component that adds a delete/unlink action for HasOne objects.
 * 
 * @package  michael.caruana/hasonefield
 * @author  Michael Caruana <mikeyc7m@gmail.com>
 */
class GridFieldDeleteHasOneAction extends GridFieldDeleteAction
{
    protected $removeRelation = false;
    protected $instance = null;

    public function __construct(DataObject $instance, bool $removeRelation = false)
    {
        $this->removeRelation = $removeRelation;
        $this->instance = $instance;
    }

    /**
     * Add the delete/unlink buttons to a GridField row
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return string|null - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName): ?string
    {
        if ($this->removeRelation) {
            if (!$record->canEdit()) {
                return null;
            }

            $field = GridField_FormAction::create(
                $gridField,
                'UnlinkRelation' . $record->ID,
                false,
                "unlinkrelation",
                array('RecordID' => $record->ID)
            )
                ->addExtraClass('gridfield-button-unlink')
                ->setAttribute('title', _t('GridAction.UnlinkRelation', "Unlink"))
                ->setAttribute('data-icon', 'chain--minus')
                ->setDescription(_t('GridAction.UnlinkRelation', 'Unlink'));
        } else {
            if (!$record->canDelete()) {
                return null;
            }

            $field = GridField_FormAction::create(
                $gridField,
                'DeleteRecord' . $record->ID,
                false,
                "deleterecord",
                array('RecordID' => $record->ID)
            )
                ->addExtraClass('gridfield-button-delete')
                ->setAttribute('title', _t('GridAction.Delete', "Delete"))
                ->setAttribute('data-icon', 'cross-circle')
                ->setDescription(_t('GridAction.DELETE_DESCRIPTION', 'Delete'));
        }
        return $field->Field();
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param GridField $gridField
     * @param string $actionName
     * @param mixed $arguments
     * @param array $data - form data
     * @return void
     * @throws ValidationException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data): void
    {
        if ($actionName == 'deleterecord' || $actionName == 'unlinkrelation') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }
            if ($actionName == 'deleterecord') {
                if (!$item->canDelete()) {
                    throw new ValidationException(
                        _t('GridFieldAction_Delete.DeletePermissionsFailure', "No delete permissions"), 0
                    );
                }
                $item->delete();
            } else {
                if (!$this->instance->canEdit()) {
                    throw new ValidationException(
                        _t('GridFieldAction_Delete.EditPermissionsFailure', "No permission to unlink record"), 0
                    );
                }
            }
            $gridField->getList()->remove($item);
        }
    }
}
