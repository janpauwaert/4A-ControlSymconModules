<?
 IPSUtils_Include   ( "IPSLogger.inc.php" ,   "IPSLibrary::app::core::IPSLogger" ) ; 

class S7DigitalInput extends IPSModule
{
	public function Create()
	{
		// Never delete this line
		parent::Create();

		// Create Property
		$this -> RegisterPropertyInteger ( "InputType" , 1 ) ;
		$this -> RegisterPropertyInteger ( "Id" , 0 ) ;

		// Create variable profiles
		if (@IPS_GetVariableProfile('xAan') == false)
		{
			IPS_CreateVariableProfile('xAan', 0);
			IPS_SetVariableProfileIcon('xAan', 'Flag');
			IPS_SetVariableProfileAssociation("xAan",false,"Uit",'Flag',0xa0a0a0);
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

		// create s7 input instance
		IPSLogger_Dbg ( __file__ ,   $this->ReadPropertyInteger("InputType" ) ) ; 
		if ($this->ReadPropertyInteger("InputType" ) == 1)
		{
			$InsID = IPS_CreateInstance ( "{932076B1-B18E-4AB6-AB6D-275ED30B62DB}" ) ;
			IPS_SetName ( $InsID , "S7_PLC_Connection");  // noem de instantie
 			IPS_SetParent ( $InsID , $this->InstanceID ) ;  // sorteer instantie onder dit object
 			IPS_ApplyChanges ( $InsID ) ;  // accepteer nieuwe configuratie 
		}

	}

	public function ApplyChanges()
	{
		// Never delete this line
		parent::ApplyChanges();

		// Validate if compatible instance id was selected and set update event 
 		if ($this->ProcessValues() == true) 
 		{ 
 			$this->setUpdateEvent(); 
 			$this->setUpdateS7Connection();
 		} 

	}

	/** Processes sensor readings and updates the status variables
	  * @return bool: true if successful, false on failure
	  */
	public function ProcessValues()
	{
		$success = false;
		IPSLogger_Dbg ( __file__ ,   'process' ) ; 

		// Sleep for two seconds to make sure all variables of the sensor instance have been updated
		//IPS_Sleep(2000);

		$variableId 				= $this->getUpdateS7Id(); 

		if ($variableId)
		{
			$bData				= GetValueInteger($variableId);
			$stData				= str_pad(bindec($bData), 8, 0, STR_PAD_LEFT);

			for ($i = 1; $i <= 9; $i++) {
    			switch ($i) {
    				case 0: //alarm
				        SetValueBoolean($this->GetIDForIdent('xAlarm'),substr($stData, 0, 1));
				        break;
				    case 1: //onbevesdtigde alarm
				        eSetValueBoolean($this->GetIDForIdent('xOnbAlarm'),substr($stData, 1, 1));
				        break;
				    case 4: //mode
				        SetValueBoolean($this->GetIDForIdent('xMode'),substr($stData, 4, 1));
				        break;
				    case 5: //mode
				        SetValueBoolean($this->GetIDForIdent('xUit'),substr($stData, 5, 1));
				        break;
					case 6: //uit
				        SetValueBoolean($this->GetIDForIdent('xAan'),substr($stData, 6, 1));
				        break;
				}
			}
			$success = true;
			$this->SetStatus(102);
		}
		else
		{
			// Incompatible instance
			$this->setStatus(200);
		}

		return $success;
	}

	/** Sets the source variable and action of the trigger event 
	*/ 


	private function setUpdateEvent()
	{
		$variableId = $this->getUpdateS7Id(); 
  
		if ($variableId) 
		{ 
			$eventId = $this->getUpdateEventId(); 
 
			IPS_SetEventTrigger($eventId, 0, $variableId); 
			IPS_SetEventActive($eventId, true); 
			IPS_SetEventScript($eventId, "S7OBJ_ProcessValues(" . $this->InstanceID . ");"); 
		} 

	}

	Private function setUpdateS7Connection()
	{
		$InsID = $this->getUpdateS7Id(); 
  
		if ($InsID) 
		{ 
			switch ($this->ReadPropertyInteger("InputType" )) {
    			case 1:
			        $InputType = 'Digital_Input_';
			        $Address = 0+($this->ReadPropertyInteger("Id" )*2);
			        break;
			    case 2:
			        $InputType = 'Digital_Output_';
			        $Address = 100+($this->ReadPropertyInteger("Id" )*2);
			        break;
			    case 3:
			        $InputType = 'Analog_Input_';
			        $Address = 200+($this->ReadPropertyInteger("Id" )*2);
			        break;
			    case 4:
			        $InputType = 'Analog_Output_';
			        $Address = 230+($this->ReadPropertyInteger("Id" )*2);
			        break;
			    }

			//IPS_SetName ( $InsID , sprintf("S7_PLC_Connection_%s_%s"),$InputType,$this->ReadPropertyInteger("Id"));  // noem de instantie volgens het type en nr
			$config = sprintf('{"DataType":1,"Area":7,"AreaAddress":1000,"Address":%s,"Bit":0,"Length":0,"Poller":100,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}', $Address);
			IPS_SetConfiguration ( $InsID , $config) ;
			IPS_ApplyChanges ( $InsID ) ;  // accepteer nieuwe configuratie 
		}
	}

	/** Returns object id for update event
	* @return int
	*/
	private function getUpdateEventId()
	{
		return @IPS_GetObjectIDByIdent('updateEvent', $this->InstanceID);
	}

	/** Returns object id for S7_PLC_Connection
	* @return int
	*/
	private function getUpdateS7Id()
	{
		return @IPS_GetObjectIDByIdent('S7_PLC_Connection', $this->InstanceID);
	}



}