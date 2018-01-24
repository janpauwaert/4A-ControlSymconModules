<?
 IPSUtils_Include   ( "IPSLogger.inc.php" ,   "IPSLibrary::app::core::IPSLogger" ) ; 

class S7Input extends IPSModule
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

		// Create event

		if ($this->getUpdateEventId() == false)
		{
			$eventId = IPS_CreateEvent(0);
			IPS_SetParent($eventId, $this->InstanceID);
			IPS_SetIdent($eventId, 'updateEvent');
			IPS_SetName($eventId, "Update values");
			IPS_SetHidden($eventId, true);
			IPS_SetPosition($eventId, 0);
		}		


	}

	public function ApplyChanges()
	{
		// Never delete this line
		parent::ApplyChanges();


		//$this->setUpdateS7Connection();
		// Validate if compatible instance id was selected and set update event 
 		if ($this->ProcessValues() == true) 
 		{ 
 			$this->setUpdateEvent(); 
 			
 		} 

	}

 	/** Processes sensor readings and updates the status variables 
	  * @return bool: true if successful, false on failure 
 	  */ 
 	public function ProcessValues() 
 	{ 
 		$success = false;  
  
 		// Sleep for two seconds to make sure all variables of the sensor instance have been updated 
 		//IPS_Sleep(2000); 
  
 		$variableId =  $this->getValueInteger($this->CreateInstanceByIdent($this->InstanceID,"S7_PLC_Connection","S7_PLC_Connection","{932076B1-B18E-4AB6-AB6D-275ED30B62DB}"),; 
 		//IPSLogger_Dbg ( __file__ ,  $variableId ); 
 		if ($variableId) 
 		{ 
 			$bData	= GetValueInteger($variableId); 
			$this-> StoreDataPLC_IPS(str_pad(decbin($bData), 16, 0, STR_PAD_LEFT)); //

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

 	Private function StoreDataPLC_IPS($data)
 	{
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"PLC-IPS","Byte_PLC-IPS","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_Off','Alarm_Object_Off',0,'xAlarm'),substr($data, 2, 1));
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_ON','Alarm_Object_ON',0,'xAlarm'),substr($data, 1, 1));
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xAlarm','xAlarm',0,'xAlarm'),substr($data, 8, 1));
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xOnbAlarm',,'xOnbAlarm',0,'xOnbAlarm'),substr($stData, 9, 1)); 
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xMode','xMode',0,'xMode'),substr($stData, 12, 1)); 
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xUit','xUit',0,'xUit'),substr($stData, 13, 1)); 
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xAan','xAan',0,"xAan"),substr($stData, 14, 1)); 
 	}

 	Private function StoreDataIPS_PLC()
 	{
 		// $SendData = 
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"IPS-PLC","Byte_IPS-PLC","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		$ManOn = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_aan_in_manueel","Schakel_aan_in_manueel",0));
 		$ManOff = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_uit_in_manueel","Schakel_uit_in_manueel",0));
 		$Man = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_Naar_Manueel","Schakel_Naar_Manueel",0));
 		$Auto = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_Naar_Automatisch","Schakel_Naar_Automatisch",0));
 		$Bev = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Bevestig_Alarmen","Bevestig_Alarmen",0));
 		$date = "00000000000".$bev.$Auto.$Man.$ManOff.$ManOn;
 		return $data;
 	}

	private function setUpdateEvent()
	{
		$variableId = $this->getUpdateS7Id(); 
  
		if ($variableId) 
		{ 
			$eventId = $this->getUpdateEventId(); 
 
			IPS_SetEventTrigger($eventId, 0, $variableId); 
			IPS_SetEventActive($eventId, true); 
			IPS_SetEventScript($eventId, "S7DI(" . $this->InstanceID . ");"); 
		} 

	}

	Private function setUpdateS7Connection()
	{
		if ($this->getUpdateS7Id()==false)
		{
				if ($this->ReadPropertyInteger("InputType" ) == 1)
				{
					$InsID = IPS_CreateInstance ( "{932076B1-B18E-4AB6-AB6D-275ED30B62DB}" ) ;
					IPS_SetName ( $InsID , "S7_PLC_Connection");  // noem de instantie
		 			IPS_SetParent ( $InsID , $this->InstanceID ) ;  // sorteer instantie onder dit object
				
		  
					switch ($this->ReadPropertyInteger("InputType" )) {
				    	case 1:
						    $InputType = 'Digital_Input_';
						    $Address = 0+($this->ReadPropertyInteger("Id" )*6);
						    break;
						case 2:
						    $InputType = 'Analog_Input_';
						    $Address = 200+($this->ReadPropertyInteger("Id" )*6);
						    break;
					}

					//IPS_SetName ( $InsID , sprintf("S7_PLC_Connection_%s_%s"),$InputType,$this->ReadPropertyInteger("Id"));  // noem de instantie volgens het type en nr
					$config = sprintf('{"DataType":1,"Area":7,"AreaAddress":1010,"Address":%s,"Bit":0,"Length":0,"Poller":100,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}', $Address);
					IPS_SetConfiguration ( $InsID , $config) ;
					IPS_ApplyChanges ( $InsID ) ;  // accepteer nieuwe configuratie 
				}

		}
	}


	Private function CreateInstanceByIdent($id, $ident, $name, $moduleid)
	{
		$iid = @IPS_GetObjectIDByIdent($ident,$id);
		if ($iid === false){
			$iid = IPS_CreateInstance ( $moduleid ) ;
			IPS_SetName ( $iid , $name);  // noem de instantie
			IPS_SetParent ( $iid , $id ) ;  // sorteer instantie onder dit object
			IPS_SetIdent ($iid, $ident);
			IPS_ApplyChanges ( $iid ) ;  // accepteer nieuwe configuratie 
		}
		return $iid;
	}

	Private function CreateVariableByIdent($id, $ident, $name, $type, $profile = "")
	{
		$vid = @IPS_GetObjectIDByIdent($ident,$id);
		if ($vid === false){
			$vid = IPS_CreateVariable ( $type ) ;
			IPS_SetName ( $vid , $name);  // noem de instantie
			IPS_SetParent ( $vid , $id ) ;  // sorteer instantie onder dit object
			IPS_SetIdent ($vid, $ident);
			if ($profile != ""){
				IPS_SetVariableCustomProfile($vid, $profile);
			}
		}
		return $vid;
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
		return @IPS_GetVariableIDByName("Value", @IPS_GetInstanceIDByName('S7_PLC_Connection',$this->InstanceID));
	}

	private function getUpdatePLC_IPS()
	{
		return @IPS_GetInstanceIDByName("PLC-IPS",$this->InstanceID);
	}

	private function getUpdateIPS_PLC()
	{
		return @IPS_GetInstanceIDByName("IPS-PLC",$this->InstanceID);
	}




}