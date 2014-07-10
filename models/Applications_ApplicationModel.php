<?php
namespace Craft;

/**
 * Applications - Application model
 */
class Applications_ApplicationModel extends BaseElementModel
{
    protected $elementType = 'Applications_Application';

    /**
     * Use the element's applicant name as its string representation.
     *
     * @return string
     */
    function __toString()
    {
        return $this->applicantName;
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(
            parent::defineAttributes(), array(
                'formId' => AttributeType::Number,
                'applicantName' => AttributeType::String,
                'applicantEmail' => AttributeType::Email,
                'applicantPhone' => AttributeType::String,
                'applicationStatus' => array(
                    AttributeType::Enum,
                    'values' => array(
                        ApplicationsApplicationStatus::Approved,
                        ApplicationsApplicationStatus::Denied,
                        ApplicationsApplicationStatus::Pending
                    ),
                    'default' => ApplicationsApplicationStatus::Pending
                ),
                'submitDate'  => AttributeType::DateTime,
            )
        );
    }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * Returns the element's CP edit URL.
     *
     * @return string|false
     */
    public function getCpEditUrl()
    {
        $form = $this->getForm();

        if ($form)
        {
            return UrlHelper::getCpUrl('applications/'.$form->handle.'/'.$this->id);
        }
    }

    /**
     * Returns the field layout used by this element.
     *
     * @return FieldLayoutModel|null
     */
    public function getFieldLayout()
    {
        $form = $this->getForm();

        if ($form)
        {
            return $form->getFieldLayout();
        }
    }

    /**
     * Returns the applicaiton's form.
     *
     * @return Applicaitons_FormModel|null
     */
    public function getForm()
    {
        if ($this->formId)
        {
            return craft()->applications_forms->getFormById($this->formId);
        }
    }

}
