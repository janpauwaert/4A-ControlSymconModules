<?

class S7DigitalInput extends IPSModule
{
	public function Create()
	{
		// Never delete this line
		parent::Create();

		$this->RegisterPropertyInteger('sensorInstanceId', 0);

		// Create variable profiles
		if (@IPS_GetVariableProfile('DigitalInput') == false)
		{
			IPS_CreateVariableProfile('DigitalInput', 1);
			IPS_SetVariableProfileIcon('DigitalInput', 'Flag');

		}

		// Create status variables
		$this->registerVariableFloat('xAan', 'Input staat aan', 'Aan', 0);
		$this->registerVariableFloat('xUit', 'Input staat Uit', 'Uit', 1);
		$this->registerVariableFloat('xMode', 'Manuele mode als 1', 'Mode', 2);
		$this->registerVariableFloat('xOnbAlarm', 'Er zijn onbevestigde alarmen', 'Onbevestig alarm', 3);
		$this->registerVariableFloat('xAlarm', 'Er is een Alarm', 'Alarm', 4);


		// Create event
		if ($this->getUpdateEventId() == false)
		{
			$eventId = IPS_CreateEvent(0);
			IPS_SetParent($eventId, $this->InstanceID);
			IPS_SetIdent($eventId, 'updateEvent');
			IPS_SetName($eventId, "Update values");
			IPS_SetHidden($eventId, true);
			IPS_SetPosition($eventId, 5);
		}
	}

	public function ApplyChanges()
	{
		// Never delete this line
		parent::ApplyChanges();

		if ($sensorId = $this->ReadPropertyInteger('sensorInstanceId'))
		{
			// Validate if compatible instance id was selected and set update event
			if ($this->ProcessValues() == true)
			{
				$this->setUpdateEvent();
			}
		}
	}

	
}