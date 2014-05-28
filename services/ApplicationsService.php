<?php
namespace Craft;

/**
 * Applications service
 */
class ApplicationsService extends BaseApplicationComponent
{
	/**
	 * Returns an application by its ID.
	 *
	 * @param int $applicationId
	 * @return Applications_ApplicationModel|null
	 */
	public function getApplicationById($applicationId)
	{
		return craft()->elements->getElementById($applicationId, 'Applications_Application');
	}

	/**
	 * Saves an application.
	 *
	 * @param Applications_ApplicationModel $event
	 * @throws Exception
	 * @return bool
	 */
	public function saveApplication(Applications_ApplicationModel $application)
	{
		$isNewApplication = !$application->id;

		// Application data
		if (!$isNewApplication)
		{
			$applicationRecord = Applications_ApplicationRecord::model()->findById($application->id);

			if (!$applicationRecord)
			{
				throw new Exception(Craft::t('No application exists with the ID “{id}”', array('id' => $application->id)));
			}
		}
		else
		{
			$applicationRecord = new Applications_ApplicationRecord();
		}

		$applicationRecord->formId = $application->formId;
		$applicationRecord->submitDate  = $application->submitDate;

		$applicationRecord->validate();
		$application->addErrors($applicationRecord->getErrors());

		if (!$application->hasErrors())
		{
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
			try
			{
				// Fire an 'onBeforeSaveApplication' event
				$this->onBeforeSaveApplication(new Application($this, array(
					'application'      => $application,
					'isNewApplication' => $isNewApplication
				)));

				if (craft()->elements->saveElement($application))
				{
					// Now that we have an element ID, save it on the other stuff
					if ($isNewApplication)
					{
						$applicationRecord->id = $application->id;
					}

					$applicationRecord->save(false);

					// Fire an 'onSaveEvent' event
					$this->onSaveApplication(new Application($this, array(
						'application'      => $application,
						'isNewApplication' => $isNewApplication
					)));

					if ($transaction !== null)
					{
						$transaction->commit();
					}

					return true;
				}
			}
			catch (\Exception $e)
			{
				if ($transaction !== null)
				{
					$transaction->rollback();
				}

				throw $e;
			}
		}

		return false;
	}

	// Events

	/**
	 * Fires an 'onBeforeSaveApplication' event.
	 *
	 * @param Application $application
	 */
	public function onBeforeSaveApplication(Application $application)
	{
		$this->raiseEvent('onBeforeSaveApplication', $application);
	}

	/**
	 * Fires an 'onSaveApplication' event.
	 *
	 * @param Application $application
	 */
	public function onSaveApplication(Application $application)
	{
		$this->raiseEvent('onSaveApplication', $application);
	}
}