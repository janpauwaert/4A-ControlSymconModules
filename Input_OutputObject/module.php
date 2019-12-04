<?php
	class input_outputObject extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			
			// Create Property
			$this -> RegisterPropertyInteger ( "InputType" , 1 ) ;
			$this -> RegisterPropertyInteger ( "ID" , 0 ) ;
			$this -> RegisterPropertyInteger ( "UpdateTime" , 100 ) ;

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

			if ($this->getUpdateEventId('UpdateInterface') == false)
			{
				$eventId = IPS_CreateEvent(0);
				IPS_SetParent($eventId, $this->InstanceID);
				IPS_SetIdent($eventId, 'UpdateInterface');
				IPS_SetName($eventId, "Update values");
				IPS_SetHidden($eventId, true);
				IPS_SetPosition($eventId, 0);
			}	
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		Private function DestroyObject()
		{
			$InstanceIDs = IPS_GetChildrenIDs ($this->InstanceID);
			if ($InstanceIDs)
			{
				foreach ( $InstanceIDs as $IID ){
				if ( !IPS_EventExists ( $IID ) ){
				if (!IPS_CategoryExists($IID))
				{
					if (IPS_GetInstance($IID)['ModuleInfo']['ModuleName'] == 'Siemens Device')
					{
						$SID = IPS_GetChildrenIDs($IID);
						foreach ( $SID as $VID )
			 				IPS_DeleteVariable($VID);
						IPS_DeleteInstance($IID);
					}
				}else{
					$SID = IPS_GetChildrenIDs($IID);
					foreach ( $SID as $VID )
			 			IPS_DeleteVariable($VID);
					IPS_DeleteCategory($IID);
				}

				}

			} 
		}
	}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			//$this->setUpdateS7Connection();
			//Validate if compatible instance id was selected and set update event 
			//$this->DestroyObject();

 			if ($this->ReceiveValues() == true) 
 			{ 
 				$this->SendValues(); 
			}
		}

		/** Processes sensor readings and updates the status variables 
		* @return bool: true if successful, false on failure 
 		*/ 
 		public function ReceiveValues() 
 		{ 
 			$success = false;  
 			$poller = IPS_GetProperty($this->InstanceID, "UpdateTime" );
 			switch (IPS_GetProperty($this->InstanceID, "InputType" )) {
		   		case 1:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7DIPLCIPSInteface','S7_DI_PLC_IPS_Inteface','1010',3,0,$poller);
 					break;
 				case 2:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7AIPLCIPSInterface','S7_AI_PLC_IPS_Interface','1010',3,0,$poller);
 					$Actid =  $this->setUpdateS7Connection($this->InstanceID,'S7AIPLCIPSActValue','S7_AI_PLC_IPS_ActValue','1012',7,2,$poller);
 					break;
 		   		case 3:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7DOPLCIPSInterface','S7_DO_PLC_IPS_Interface','1010',3,0,$poller);
 					break;
 				case 4:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7AOPLCIPSInterface','S7_AO_PLC_IPS_Interface','1010',3,0,$poller);
 					$Actid =  $this->setUpdateS7Connection($this->InstanceID,'S7AOPLCIPSActValue','S7_AO_PLC_IPS_ActValue','1012',7,2,$poller);
 					break;
 			}	
 			$this->setUpdateEvent($this->getS7ValueId($Intid));				

 			//if (S7_RequestRead($Intid)){
			$bData	= GetValueInteger($this->getS7ValueId($Intid)); 
			$this-> StoreDataToIPS(str_pad(decbin($bData), 64, 0, STR_PAD_LEFT)); //
				//$this->SetStatus(106); 
			
			// read actual value is type is analog
			if (($this->ReadPropertyInteger("InputType" )==2) || ($this->ReadPropertyInteger("InputType" )==4)){

		
					$this->StoreActValueToIPS($this->getS7ValueId($Actid));
					//$this->SetStatus(107);
					$success = true; 

 			}
 			else{
 				$success = true; 
 			}
 			return $success;
 		} 

 		public function SendValues() 
 		{ 
 			$success = false;  
   		
   			switch ($this->ReadPropertyInteger("InputType" )) {
		   		case 1:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7DIIPSPLCInterface','S7_DI_IPS_PLC_Interface','1010',2,0);
 					break;
 				case 2:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7AIIPSPLCInterface','S7_AI_IPS_PLC_Interface','1010',2,0);
 					$Forceid =  $this->setUpdateS7Connection($this->InstanceID,'S7AIIPSPLCForceValue','S7_AI_IPS_PLC_ForceValue','1011',7,1);
 					break;

 		  		case 3:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7DOIPSPLCInteface','S7_DO_IPS_PLC_Inteface','1010',2,0);
 					break;

 		  		case 4:
 					$Intid =  $this->setUpdateS7Connection($this->InstanceID,'S7AOIPSPLCInterface','S7_AO_IPS_PLC_Interface','1010',2,0);
 					$Forceid =  $this->setUpdateS7Connection($this->InstanceID,'S7AOIPSPLCForceValue','S7_AO_IPS_PLC_ForceValue','1011',7,1);
 					break;

 			}
 			if (intval($this->ReadDataFromIPS())==0){
 				$success = true;
 			}
 			else{
				
				if 	(S7_WriteInteger($Intid,intval($this->ReadDataFromIPS()) )){
					//$this->SetStatus(108);
					$success = true;
				 
				}
				else{
					$this->SetStatus(202); 
				}
		
		
			}
			return $success;
		}

		public function SendForceValue()
		{
		//Write force value
			if (($this->ReadPropertyInteger("InputType" )==2) || ($this->ReadPropertyInteger("InputType" )==4)){
				if (S7_WriteReal($Forceid,ReadForceValueFromIPS())){
					//$this->SetStatus(109);
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
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"IntefacePLCIPS","Inteface_PLC-IPS");
 			switch ($this->ReadPropertyInteger("InputType" )) {
		   		case 1:
					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectOff','Alarm_Object_Off',0,'xAlarm'),substr($data, 49, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectOff'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM OFF !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM OFF !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectOn','Alarm_Object_ON',0,'xAlarm'),substr($data, 50, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectOn'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM ON !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM ON !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xUit','xUit',0,'xUit'),substr($data, 5, 1)); 
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAan','xAan',0,"xAan"),substr($data, 6, 1));
					break;
				case 3:
					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectOff','Alarm_Object_Off',0,'xAlarm'),substr($data, 50, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectOff'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM OFF !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM OFF !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectON','Alarm_Object_ON',0,'xAlarm'),substr($data, 49, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectOn'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM ON !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM ON !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xUit','xUit',0,'xUit'),substr($data, 37, 1)); 
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAan','xAan',0,"xAan"),substr($data, 38, 1));
					break;

				case 2:
					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectLow','Alarm_Object_Low',0,'xAlarm'),substr($data, 53, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectLow'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM LOW !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM LOW !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectHigh','Alarm_Object_High',0,'xAlarm'),substr($data, 52, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectHigh'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM HIGH !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM HIGH !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
					break;
				case 4:
					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectLow','Alarm_Object_Low',0,'xAlarm'),substr($data, 53, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectLow'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM LOW !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM LOW !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
 					SetValueBoolean($this->CreateVariableByIdent($CategorieID,'AlarmObjectHigh','Alarm_Object_High',0,'xAlarm'),substr($data, 52, 1));
					if (GetValueBoolean(IPS_GetStatusVariableID($CategorieID,'AlarmObjectHigh'))){
						//IPSLogger_Err(__file__, sprintf("Object : %s ALARM HIGH !!!",IPS_GetName($this->InstanceID )));
						WFC_PushNotification ( 34202 /*[Turkeije_17]*/ , '!! ALARM !!' , sprintf("Object : (%s) ALARM HIGH !!!",IPS_GetName($this->InstanceID )) , '' , 0 ) ; 
					}
					break;
			} 
 		
 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAlarm','xAlarm',0,'xAlarm'),substr($data, 32, 1));
 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xOnbAlarm','xOnbAlarm',0,'xOnbAlarm'),substr($data, 33, 1)); 
 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xMode','xMode',0,'xMode'),substr($data, 36, 1)); 
 		}

 		Private function StoreActValueToIPS($TargetID)
 		{
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"ActualValuePLCIPS","ActualValue_PLC-IPS");
			$this->CreateLinkByIdent($CategorieID,'ActualValue','Actual Value',$TargetID);
 			//SetValueFloat($this->CreateVariableByIdent($CategorieID,'ActualValue','Actual Value',2),$data); 
 
 		}

 		Private function ReadDataFromIPS()
 		{
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"IPSPLC","DWord_IPS-PLC");
 			$ManOn = GetValueBoolean($this->CreateVariableByIdent($CategorieID,"Schakelaaninmanueel","Schakel_aan_in_manueel",0));
 			if ($ManOn){
 				IPSLogger_Wrn(__file__, sprintf("Object : %s manueel AAN geschakeld",IPS_GetName($this->InstanceID )));
 			}
 			$ManOff = GetValueBoolean($this->CreateVariableByIdent($CategorieID,"Schakeluitinmanueel","Schakel_uit_in_manueel",0));
 			if ($ManOff){
 				IPSLogger_Wrn(__file__, sprintf("Object : %s manueel UIT geschakeld",IPS_GetName($this->InstanceID )));
 			}
 			$Man = GetValueBoolean($this->CreateVariableByIdent($CategorieID,"SchakelNaarManueel","Schakel_Naar_Manueel",0));
 			if ($Man){
 				IPSLogger_Wrn(__file__, sprintf("Object : %s mode naar MANUEEL geschakeld",IPS_GetName($this->InstanceID )));
 			}

 			$Auto = GetValueBoolean($this->CreateVariableByIdent($CategorieID,"SchakelNaarAutomatisch","Schakel_Naar_Automatisch",0));
 			if ($Auto){
				IPSLogger_Wrn(__file__, sprintf("Object : %s mode naar AUTO geschakeld",IPS_GetName($this->InstanceID )));
			}
 			$Bev = GetValueBoolean($this->CreateVariableByIdent($CategorieID,"BevestigAlarmen","Bevestig_Alarmen",0));
 			$data = "00000000000".$Bev.$Auto.$Man.$ManOff.$ManOn;
 			return bindec($data);
 		
 		}

 		Private function ReadForceValueFromIPS()
 		{
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"ForceValueIPSPLC","ForceValue_IPS-PLC");
 			$Force = GetValueFloat($this->CreateVariableByIdent($CategorieID,"ForceValue","Force Value",2));
 			return $Force;
 		}

		private function setUpdateEvent($variableId)
		{
				$eventId = $this->getUpdateEventId('UpdateInterface'); 
 
				IPS_SetEventTrigger($eventId, 1, $variableId); 
				IPS_SetEventActive($eventId, true); 
				IPS_SetEventScript($eventId, "S7OBJ_ReceiveValues(" . $this->InstanceID . ");"); 
		}

		Private function setUpdateS7Connection($id, $ident,$name, $AreaAddress,$DataType,$Interface,$poller=0)
		{
			$OBJid = $this->CreateInstanceByIdent($id,$ident,$name,"{932076B1-B18E-4AB6-AB6D-275ED30B62DB}");


			switch ($this->ReadPropertyInteger("InputType" )) {
		   		case 1:
					$InputType = 'Digital_Input_';
					switch ($Interface){
			    		case 0:
			    			$Address = 0+($this->ReadPropertyInteger("ID" )*6); // 0 is start adres digital in
			    			break;
			    		case 1:
							$Address = 0+($this->ReadPropertyInteger("ID" )*1); // 0 is start adres digital in
			    			break;
			    		case 2:
			    			$Address = 0+($this->ReadPropertyInteger("ID" )*1); // 0 is start adres digital in
			    			break;
					}
					break;
				case 2:
					$InputType = 'Analog_Input_';
					switch ($Interface){
			    		case 0:
			   				$Address = 732+($this->ReadPropertyInteger("ID" )*6); // 732 is start adres analog in DB 1010
			   				break;
			    		case 1:
							$Address = 16+($this->ReadPropertyInteger("ID" )*4); // 16 is start adres analog in DB1011
			    			break;
			    		case 2:
			    			$Address = 16+($this->ReadPropertyInteger("ID" )*4); // 16 is start adres analog in DB1012
			    			break;
					}
					break;
				case 3:
					$InputType = 'Digital_Output';
					switch ($Interface){
			    		case 0:
			   				$Address = 366+($this->ReadPropertyInteger("ID" )*6); // 366 is start adres Digital out in DB1010
			   				break;
			   			case 1:
			   				$Address = 8+($this->ReadPropertyInteger("ID" )*1); // 8 is start adres Digital out in DB1011
			   				break;
			   			case 2:
			   				$Address = 8+($this->ReadPropertyInteger("ID" )*1); // 8 is start adres Digital out DB 1012
							break;
					}
					break;
				case 4:
					$InputType = 'Analog_Output';
					switch ($Interface){
			    		case 0:
			   				$Address = 798+($this->ReadPropertyInteger("ID" )*6); // 798 is start adres analog out in DB1010
			   				break;
			   			case 1:
			   				$Address = 60+($this->ReadPropertyInteger("ID" )*4); // 60 is start adres analog out in DB1011
			   				break;
			   			case 2:
			   				$Address = 60+($this->ReadPropertyInteger("ID" )*4); // 60 is start adres analog out in DB1012
			   				break;
			   		}

					break;
			}

				//IPS_SetName ( $InsID , sprintf("S7_PLC_Connection_%s_%s"),$InputType,$this->ReadPropertyInteger("Id"));  // noem de instantie volgens het type en nr
				$config = sprintf('{"DataType":%s,"Area":7,"AreaAddress":%s,"Address":%s,"Bit":0,"Length":1,"Poller":%s,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}',$DataType,$AreaAddress, $Address,$poller);
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

		Private function CreateCategorieByIdent($id, $ident, $name)
		{
			$CatID = @IPS_GetObjectIDByIdent($ident,$id);
			if ($CatID === false){
				$CatID = IPS_CreateCategory () ;
				IPS_SetName ( $CatID , $name);  // noem de instantie
				IPS_SetParent ( $CatID , $id ) ;  // sorteer instantie onder dit object
				IPS_SetIdent ($CatID, $ident);
				//IPS_ApplyChanges ( $CatID ) ;  // accepteer nieuwe configuratie 
			}
			return $CatID;
		}

		Private function CreateLinkByIdent($id, $ident, $name,$targetID)
		{
			$LinkID = @IPS_GetObjectIDByIdent($ident,$id);
			if($LinkID === false){
				$LinkID = IPS_CreateLink();
				IPS_SetName($LinkID, $name);// noem de link
				IPS_SetParent ( $LinkID , $id ) ; // sorteer instantie onder dit object
				IPS_SetIdent ($LinkID, $ident);
				IPS_SetLinkTargetID($LinkID,$targetID);

			}
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
		private function getUpdateEventId($Ident)
		{
			return @IPS_GetObjectIDByIdent($Ident, $this->InstanceID);
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