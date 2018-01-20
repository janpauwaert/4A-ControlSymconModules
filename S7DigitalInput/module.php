<?

class S7DigitalInput extends IPSModule
{
	public function Create()
	{
		// Never delete this line
		parent::Create();

		$this -> RegisterPropertyInteger ( "InputType" , 1 ) ;
		$this -> RegisterPropertyInteger ( "Id" , 1 ) ;

		// Create variable profiles
		if (@IPS_GetVariableProfile('xAan') == false)
		{
			IPS_CreateVariableProfile('xAan', 0);
			IPS_SetVariableProfileIcon('xAan', 'Flag');
			IPS_SetVariableProfileAssociation("xAan",false,"Uit",'flag',0xa0a0a0);
			IPS_SetVariableProfileAssociation("xAan",true,"Aan",'Flag',0x00ff00);
		}

		if (@IPS_GetVariableProfile('xUit') == false)
		{
			IPS_CreateVariableProfile('xUit', 0);
			IPS_SetVariableProfileIcon('xUit', 'Flag');
			IPS_SetVariableProfileAssociation("xUit",false,"Uit",'Flag',0xa0a0a0);
			IPS_SetVariableProfileAssociation("xUit",true,"Aan",'Flag',0x00ff00);

		}

		if (@IPS_GetVariableProfile('xMode') == false)
		{
			IPS_CreateVariableProfile('xMode', 0);
			IPS_SetVariableProfileIcon('xMode', 'Gear');
			IPS_SetVariableProfileAssociation("xMode",false,"Automatisch",'Gear',0x00ff00);
			IPS_SetVariableProfileAssociation("xMode",true,"Manueel",'Gear',0x0000ff);
		}

		if (@IPS_GetVariableProfile('xOnbAlarm') == false)
		{
			IPS_CreateVariableProfile('xOnbAlarm', 0);
			IPS_SetVariableProfileIcon('xOnbAlarm', 'Warning');
			IPS_SetVariableProfileAssociation("xOnbAlarm",false,"OK",'',0xa0a0a0);
			IPS_SetVariableProfileAssociation("xOnbAlarm",true,"Onb Alarm",'Warning',0xff0000);
		}

		if (@IPS_GetVariableProfile('xAlarm') == false)
		{
			IPS_CreateVariableProfile('xAlarm', 0);
			IPS_SetVariableProfileIcon('xAlarm', 'Warning');
			IPS_SetVariableProfileAssociation("xAlarm",false,"OK",'',0xa0a0a0);
			IPS_SetVariableProfileAssociation("xAlarm",true,"Alarm",'Warning',0xff0000);
		}


		// create s7 input or output instance

		$InsID = IPS_CreateInstance ( "{932076B1-B18E-4AB6-AB6D-275ED30B62DB}" ) ;

		IPS_SetName ( $InsID , "S7_Input" .  $this -> ReadPropertyInteger ( "Id" ));  // noem de instantie
 		IPS_SetParent ( $InsID , 0 ) ;  // sorteer instantie onder object met objectID "12345"
 		$config = sprintf('{"DataType":1,"Area":7,"AreaAddress":1000,"Address":%s,"Bit":0,"Length":0,"Poller":100,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}', $this -> ReadPropertyInteger ( "Id" )*2);
		IPS_SetConfiguration ( $InsID , $config) ;
		IPS_ApplyChanges ( $InsID ) ;  // accepteer nieuwe configuratie 


		// Create status variables
		$this->registerVariableBoolean('xAan', 'Input Aan', 'xAan', 0);
		$this->registerVariableBoolean('xUit', 'Input Uit', 'xUit', 1);
		$this->registerVariableBoolean('xMode', 'Mode', 'xMode', 2);
		$this->registerVariableBoolean('xOnbAlarm', 'Onbevestigde Alarm', 'xOnbAlarm', 3);
		$this->registerVariableBoolean('xAlarm', 'Alarm', 'xAlarm', 4);


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

	/** Returns object id for update event
	* @return int
	*/
	private function getUpdateEventId()
	{
		return @IPS_GetObjectIDByIdent('updateEvent', $this->InstanceID);
	}
	
}