# HasOneField

Easy to manage has_one relations with familiar link/unlink feature.

## Introduction

Adds a Has-One type of field to allow you to create, link, unlink, delete, or edit DataObjects in a has_one
relationship. Based on a gridfield, but without the fluff.

* "Create" button takes you to a new page, and from there the parent is updated with the new relation when the new object is saved.
* Gridfield-style "Link Existing" field is used to search and select existing objects, and happens inline/ajax. 
* If there already is a relation, clicking on the data or the gridfield-style "Edit" icon take you to the usual edit page.
* Gridfield-style "Unlink" icon happens inline/ajax and writes the parent only. 
* Gridfield-style "Delete" icon also happens inline/ajax with the "Are you sure" prompt. 


## Requirements

* Silverstripe 3x.

## Installation

`composer install`

## Manipulations

* Access the internal gridfield using `$gridfield = $field->FieldList()->first();`.
* Set the display fields using `$gridfield->setDisplayFields($someArray);`.
