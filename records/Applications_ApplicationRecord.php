<?php
namespace Craft;

/**
 * Applications - Application record
 */
class Applications_ApplicationRecord extends BaseRecord
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'applications';
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'name' => array(
                AttributeType::String,
                'required' => true
            ),
            'email' => array(
                AttributeType::Email,
                'required' => true
            ),
            'phone' => array(
                AttributeType::String,
                'required' => true
            ),
            'status' => array(
                AttributeType::Enum,
                'values' => array(
                    ApplicationsApplicationStatus::Approved,
                    ApplicationsApplicationStatus::Denied,
                    ApplicationsApplicationStatus::Pending
                ),
                'default' => ApplicationsApplicationStatus::Pending
            ),
            'submitDate' => array(
                AttributeType::DateTime,
                'required' => true
            ),
        );
    }

    /**
     * @return array
     */
    public function defineRelations()
    {
        return array(
            'element' => array(
                static::BELONGS_TO,
                'ElementRecord',
                'id',
                'required' => true,
                'onDelete' => static::CASCADE
            ),
            'form'    => array(
                static::BELONGS_TO,
                'Applications_FormRecord',
                'required' => true,
                'onDelete' => static::CASCADE
            ),
        );
    }
}
