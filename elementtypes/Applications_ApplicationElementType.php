<?php
namespace Craft;

/**
 * Applications - Application element type
 */
class Applications_ApplicationElementType extends BaseElementType
{
    /**
     * Returns the element type name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Applications');
    }

    /**
     * Returns whether this element type has content.
     *
     * @return bool
     */
    public function hasContent()
    {
        return true;
    }

    /**
     * Returns whether this element type has titles.
     *
     * @return bool
     */
    public function hasTitles()
    {
        return false;
    }

    /**
     * Returns whether this element type can have statuses.
     *
     * @return bool
     */
    public function hasStatuses()
    {
        return true;
    }

    /**
     * Returns all of the possible statuses that elements of this type may have.
     *
     * @return array|null
     */
    public function getStatuses()
    {
        return array(
            ApplicationsApplicationStatus::Approved => Craft::t('Approved'),
            ApplicationsApplicationStatus::Denied   => Craft::t('Denied'),
            ApplicationsApplicationStatus::Pending  => Craft::t('Pending'),
        );
    }

    /**
     * Returns this element type's sources.
     *
     * @param string|null $context
     * @return array|false
     */
    public function getSources($context = null)
    {
        $sources = array(
            '*' => array(
                'label' => Craft::t('All Applications'),
            )
        );

        foreach (craft()->applications_forms->getAllForms() as $form)
        {
            $key = 'form:'.$form->id;

            $sources[$key] = array(
                'label'    => $form->name,
                'criteria' => array(
                    'formId' => $form->id
                )
            );
        }

        return $sources;
    }

    /**
     * Returns the attributes that can be shown/sorted by in table views.
     *
     * @param string|null $source
     * @return array
     */
    public function defineTableAttributes($source = null)
    {
        return array(
            'name' => Craft::t('Applicant'),
            'email' => Craft::t('Email'),
            'phone' => Craft::t('Phone'),
            'status' => Craft::t('Status'),
            'submitDate' => Craft::t('Submit Date')
        );
    }

    /**
	 * Defines which model attributes should be searchable.
	 *
	 * @return array
	 */
	public function defineSearchableAttributes()
	{
		return array(
            'name',
            'email',
            'phone',
            'status',
            'submitDate'
        );
	}

    /**
     * Returns the table view HTML for a given attribute.
     *
     * @param BaseElementModel $element
     * @param string $attribute
     * @return string
     */
    public function getTableAttributeHtml(BaseElementModel $element, $attribute)
    {
        switch ($attribute)
        {
            case 'submitDate':
            {
                $date = $element->$attribute;

                if ($date)
                {
                    return $date->localeDate();
                }
                else
                {
                    return '';
                }
            }

            default:
            {
                return parent::getTableAttributeHtml($element, $attribute);
            }
        }
    }

    /**
     * Defines any custom element criteria attributes for this element type.
     *
     * @return array
     */
    public function defineCriteriaAttributes()
    {
        return array(
            'form'              => AttributeType::Mixed,
            'formId'            => AttributeType::Mixed,
            'name'     => AttributeType::String,
            'email'    => AttributeType::Email,
            'phone'    => AttributeType::String,
            'status' => array(
                AttributeType::Enum,
                'values' => array(
                    ApplicationsApplicationStatus::Approved,
                    ApplicationsApplicationStatus::Denied,
                    ApplicationsApplicationStatus::Pending
                ),
                'default' => ApplicationsApplicationStatus::Pending
            ),
            'submitDate'       => AttributeType::Mixed,
            'order'            => array(
                AttributeType::String,
                'default' => 'applications.submitDate asc'
            ),
        );
    }

    /**
     * Modifies an element query targeting elements of this type.
     *
     * @param DbCommand $query
     * @param ElementCriteriaModel $criteria
     * @return mixed
     */
    public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
    {
        $query
            // you must add the columns here when adding a new field
            ->addSelect('applications.formId, applications.name,
            applications.email, applications.status,
            applications.phone, applications.submitDate,')
            ->join('applications applications', 'applications.id = elements.id');

        if ($criteria->formId)
        {
            $query->andWhere(DbHelper::parseParam('applications.formId', $criteria->formId, $query->params));
        }

        if ($criteria->form)
        {
            $query->join('applications_forms applications_forms', 'applications_forms.id = applications.formId');
            $query->andWhere(DbHelper::parseParam('applications_forms.handle', $criteria->form, $query->params));
        }

        if ($criteria->submitDate)
        {
            $query->andWhere(DbHelper::parseDateParam('entries.submitDate', $criteria->submitDate, $query->params));
        }

    }

    /**
     * Populates an element model based on a query result.
     *
     * @param array $row
     * @return array
     */
    public function populateElementModel($row)
    {
        return Applications_ApplicationModel::populateModel($row);
    }

    /**
     * Returns the HTML for an editor HUD for the given element.
     *
     * @param BaseElementModel $element
     * @return string
     */
    public function getEditorHtml(BaseElementModel $element)
    {
        // Start/End Dates
        $html = craft()->templates->render('applications/_edit', array(
            'element' => $element,
        ));

        // Everything else
        $html .= parent::getEditorHtml($element);

        return $html;
    }
}
