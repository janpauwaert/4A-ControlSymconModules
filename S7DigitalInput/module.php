<?

class S7DigitalInput extends IPSModule
{
	public function Create()
	{
		// Never delete this line
		parent::Create();

		$this->RegisterPropertyInteger('sensorInstanceId', 0);

		// Create variable profiles
		if (@IPS_GetVariableProfile('xAan') == false)
		{
			IPS_CreateVariableProfile('xAan', 0);
			IPS_SetVariableProfileIcon('xAan', 'Flag');
			IPS_SetVariableProfileAssociation("xAan",false,"Uit");
			IPS_SetVariableProfileAssociation("xAan",true,"Aan",0x00ff00);
		}

		if (@IPS_GetVariableProfile('xUit') == false)
		{
			IPS_CreateVariableProfile('xUit', 0);
			IPS_SetVariableProfileIcon('xUit', 'Flag');
			IPS_SetVariableProfileAssociation("xUit",false,"Uit");
			IPS_SetVariableProfileAssociation("xUit",true,"Aan",0x00ff00);

		}

		if (@IPS_GetVariableProfile('xMode') == false)
		{
			IPS_CreateVariableProfile('xUit', 0);
			IPS_SetVariableProfileIcon('xUit', 'Gear');
			IPS_SetVariableProfileAssociation("xUit",false,"Automatisch");
			IPS_SetVariableProfileAssociation("xUit",true,"Manueel");
		}

		if (@IPS_GetVariableProfile('xOnbAlarm') == false)
		{
			IPS_CreateVariableProfile('xOnbAlarm', 0);
			IPS_SetVariableProfileIcon('xOnbAlarm', 'Warning');
			IPS_SetVariableProfileAssociation("xOnbAlarm",false,"");
			IPS_SetVariableProfileAssociation("xOnbAlarm",true,"",0xff0000);
		}

		if (@IPS_GetVariableProfile('xAlarm') == false)
		{
			IPS_CreateVariableProfile('xAlarm', 0);
			IPS_SetVariableProfileIcon('xAlarm', 'Warning');
			IPS_SetVariableProfileAssociation("xAlarm",false,"");
			IPS_SetVariableProfileAssociation("xAlarm",true,"",0xff0000);
		}






		// Create status variables
		$this->registerVariableBoolean('xAan', 'Input staat aan', 'Aan', 0);
		$this->registerVariableBoolean('xUit', 'Input staat Uit', 'Uit', 1);
		$this->registerVariableBoolean('xMode', 'Manuele mode als 1', 'Mode', 2);
		$this->registerVariableBoolean('xOnbAlarm', 'Er zijn onbevestigde alarmen', 'Onbevestig alarm', 3);
		$this->registerVariableBoolean('xAlarm', 'Er is een Alarm', 'Alarm', 4);


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

	}

	
}