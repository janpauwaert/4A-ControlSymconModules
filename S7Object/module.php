<?
 IPSUtils_Include   ( "IPSLogger.inc.php" ,   "IPSLibrary::app::core::IPSLogger" ) ; 
 IPSLogger_Err(__file__, "Variable mit Namen 'MeineVariable' konnte nicht gefunden werden");
class S7Object extends IPSModule
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
 		if ($this->ReceiveValues() == true) 
 		{ 
 			$this->setUpdateEvent(); 
 			
 		} 

	}

 	/** Processes sensor readings and updates the status variables 
	  * @return bool: true if successful, false on failure 
 	  */ 
 	public function ReceiveValues() 
 	{ 
 		$success = false;  

 		switch ($this->ReadPropertyInteger("InputType" )) {
		   	case 1:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_DI_PLC_IPS_Inteface','1010');
 			case 2:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AI_PLC_IPS_Interface','1010');
 				$Actid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AI_PLC_IPS_ActValue','1012');
 		   	case 3:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_DO_PLC_IPS_Interface','1010');
 			case 4:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AO_PLC_IPS_Interface','1010');
 				$Actid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AO_PLC_IPS_ActValue','1012');
 		}					

 		if (S7_RequestRead($Intid)){
			$bData	= GetValueInteger(getS7ValueId($Intid)); 
			$this-> StoreDataToIPS(str_pad(decbin($bData), 32, 0, STR_PAD_LEFT)); //
			$this->SetStatus(106); 
			
		}
		else{
			$this->SetStatus(201); 
		}
		// read actual value is type is analog
		if (($this->ReadPropertyInteger("InputType" )==2) || ($this->ReadPropertyInteger("InputType" )==4)){

			if (S7_RequestRead($Actid)){
				$this->StoreActValueToIPS(GetValueFloat(getS7ValueId($Actid)));
				$this->SetStatus(107);
				$success = true; 
 			}
 		}
 		else{
 			$success = true; 
 		}
 	} 

 	public function SendValues() 
 	{ 
 		$success = false;  
   		
   		switch ($this->ReadPropertyInteger("InputType" )) {
		   	case 1:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_DI_IPS_PLC_Interface','1010');
 			case 2:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AI_IPS_PLC_Interface','1010');
 				$Forceid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AI_IPS_PLC_ForceValue','1011');

 		  	case 3:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_DO_IPS_PLC-Inteface','1010');

 		  	case 4:
 				$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AO_IPS_PLC_Interface','1010');
 				$Forceid =  $this->setUpdateS7Connection($this->InstanceID,'S7_AO_IPS_PLC_ForceValue','1011');

 		}

		if 	(S7_WriteInteger($Intid, ReadDataFromIPS())){
			$this->SetStatus(108);
			 
		}
		else{
			$this->SetStatus(202); 
		}
	}

	public function SendForceValue()
	{
				//Write force value
		if (($this->ReadPropertyInteger("InputType" )==2) || ($this->ReadPropertyInteger("InputType" )==4)){
			if (S7_WriteReal($Forceid,ReadForceValueFromIPS())){
				$this->SetStatus(109);
				$success = true;
			}
		}
		else{
			$success = true;
		}
		return $success;
	}
 		


 	Private function StoreDataToIPS($data)
 	{
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"Inteface_PLC-IPS","Inteface_PLC-IPS","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		switch ($this->ReadPropertyInteger("InputType" )) {
		   	case 1:
				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_Off','Alarm_Object_Off',0,'xAlarm'),substr($data, 2, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_Off'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM OFF !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_ON','Alarm_Object_ON',0,'xAlarm'),substr($data, 1, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_On'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM ON !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'xUit','xUit',0,'xUit'),substr($stData, 13, 1)); 
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'xAan','xAan',0,"xAan"),substr($stData, 14, 1));
			    break;
			case 3:
				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_Off','Alarm_Object_Off',0,'xAlarm'),substr($data, 2, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_Off'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM OFF !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_ON','Alarm_Object_ON',0,'xAlarm'),substr($data, 1, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_On'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM ON !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'xUit','xUit',0,'xUit'),substr($stData, 13, 1)); 
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'xAan','xAan',0,"xAan"),substr($stData, 14, 1));
			    break;

			case 2:
				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_Low','Alarm_Object_Low',0,'xAlarm'),substr($data, 5, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_Low'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM LOW !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_High','Alarm_Object_High',0,'xAlarm'),substr($data, 4, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_High'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM HIGH !!!",IPS_GetName($this->InstanceID )));
				}
			    break;
			case 4:
				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_Low','Alarm_Object_Low',0,'xAlarm'),substr($data, 5, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_Low'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM LOW !!!",IPS_GetName($this->InstanceID )));
				}
 				SetValueBoolean($this->CreateVariableByIdent($deviceID,'Alarm_Object_High','Alarm_Object_High',0,'xAlarm'),substr($data, 4, 1));
				if (GetValueBoolean(@IPS_GetObjectIDByIdent($deviceID,'Alarm_Object_High'))){
					IPSLogger_Err(__file__, sprintf("Object : %s ALARM HIGH !!!",IPS_GetName($this->InstanceID )));
				}
			    break;
		} 
 		
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xAlarm','xAlarm',0,'xAlarm'),substr($data, 8, 1));
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xOnbAlarm',,'xOnbAlarm',0,'xOnbAlarm'),substr($stData, 9, 1)); 
 		SetValueBoolean($this->CreateVariableByIdent($deviceID,'xMode','xMode',0,'xMode'),substr($stData, 12, 1)); 
 
 	}

 	Private function StoreActValueToIPS($data)
 	{
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"ActualValue_PLC-IPS","ActualValue_PLC-IPS","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		SetValueFloat($this->CreateVariableByIdent($deviceID,'ActualValue','Actual Value',2),$stData); 
 
 	}

 	Private function ReadDataFromIPS()
 	{
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"IPS-PLC","DWord_IPS-PLC","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		$ManOn = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_aan_in_manueel","Schakel_aan_in_manueel",0));
 		if ($ManOn){
 			IPSLogger_Wrn(__file__, sprintf("Object : %s manueel AAN geschakeld",IPS_GetName($this->InstanceID )));
 		}
 		$ManOff = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_uit_in_manueel","Schakel_uit_in_manueel",0));
 		if ($ManOff){
 			IPSLogger_Wrn(__file__, sprintf("Object : %s manueel UIT geschakeld",IPS_GetName($this->InstanceID )));
 		}
 		$Man = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_Naar_Manueel","Schakel_Naar_Manueel",0));
 		if ($Man){
 			IPSLogger_Wrn(__file__, sprintf("Object : %s mode naar MANUEEL geschakeld",IPS_GetName($this->InstanceID )));
 		}

 		$Auto = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Schakel_Naar_Automatisch","Schakel_Naar_Automatisch",0));
 		if ($Auto){
			IPSLogger_Wrn(__file__, sprintf("Object : %s mode naar AUTO geschakeld",IPS_GetName($this->InstanceID )));
		}
 		$Bev = GetValueBoolean($this->CreateVariableByIdent($deviceID,"Bevestig_Alarmen","Bevestig_Alarmen",0));
 		$data = "00000000000".$bev.$Auto.$Man.$ManOff.$ManOn;
 		return bindec($data);
 		}
 	}

 	Private function ReadForceValueFromIPS()
 	{
 		$deviceID = $this->CreateInstanceByIdent($this->InstanceID,"ForceValue_IPS-PLC","ForceValue_IPS-PLC","{485D0419-BE97-4548-AA9C-C083EB82E61E}");
 		$Force = GetValueFloat($this->CreateVariableByIdent($deviceID,"ForceValue","Force Value",2));
 		return $Force;
 	}

	private function setUpdateEvent()
	{
		$variableId = $this->getUpdateS7Id(); 
  
		if ($variableId) 
		{ 
			$eventId = $this->getUpdateEventId(); 
 
			IPS_SetEventTrigger($eventId, 0, $variableId); 
			IPS_SetEventActive($eventId, true); 
			IPS_SetEventScript($eventId, "S7OBJ(" . $this->InstanceID . ");"); 
		} 

	}

	Private function setUpdateS7Connection($id, $ident, $AreaAddress)
	{
		$OBJid = $this->CreateInstanceByIdent($id,$ident,$ident,"{932076B1-B18E-4AB6-AB6D-275ED30B62DB}")


		switch ($this->ReadPropertyInteger("InputType" )) {
		   	case 1:
			    $InputType = 'Digital_Input_';
			    $Address = 0+($this->ReadPropertyInteger("Id" )*4); // 0 is start adres digital in
			    break;
			case 2:
			    $InputType = 'Analog_Input_';
			   	$Address = 560+($this->ReadPropertyInteger("Id" )*4); // 560 is start adres analog in
			    break;
			case 2:
			    $InputType = 'Digital_Output';
			   	$Address = 280+($this->ReadPropertyInteger("Id" )*4); // 280 is start adres Digital out
			    break;
			case 2:
			    $InputType = 'Analog_Output';
			   	$Address = 320+($this->ReadPropertyInteger("Id" )*4); // 320 is start adres analog out
			    break;

		}

			//IPS_SetName ( $InsID , sprintf("S7_PLC_Connection_%s_%s"),$InputType,$this->ReadPropertyInteger("Id"));  // noem de instantie volgens het type en nr
			$config = sprintf('{"DataType":1,"Area":7,"AreaAddress":%s,"Address":%s,"Bit":0,"Length":2,"Poller":0,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}',$AreaAddress, $Address);
			IPS_SetConfiguration ( $OBJid , $config) ;
			IPS_ApplyChanges ( $OBJid ) ;  // accepteer nieuwe configuratie 

		
		return $OBJid;
	}


	Private function CreateInstanceByIdent($id, $ident, $name, $moduleid = "{485D0419-BE97-4548-AA9C-C083EB82E61E}")
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
	private function getS7ValueId($ident)
	{
		return @IPS_GetVariableIDByName("Value", $ident);
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