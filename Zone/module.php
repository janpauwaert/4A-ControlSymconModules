<?php
	class Zone extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			// Create Property
			$this -> RegisterPropertyInteger ( "DBNummer" , 3001 ) ;
			$this -> RegisterPropertyInteger ( "StartAdress" , 0 ) ;
			$this -> RegisterPropertyInteger ( "UpdateTime" , 100 ) ;

			// Create variable profiles
			if (@IPS_GetVariableProfile('Closed') == false)
			{
				IPS_CreateVariableProfile('Closed', 0);
				IPS_SetVariableProfileIcon('Closed', 'Door');
				IPS_SetVariableProfileAssociation("Closed",false,"Open",'Door',0xff0000);
				IPS_SetVariableProfileAssociation("Closed",true,"Gesloten",'Door',0x00ff00);
			}

			if (@IPS_GetVariableProfile('Toegankelijk') == false)
			{
				IPS_CreateVariableProfile('Toegankelijk', 0);
				IPS_SetVariableProfileIcon('Toegankelijk', 'Door');
				IPS_SetVariableProfileAssociation("Toegankelijk",false,"Niet Toegankelijk",'Door',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Toegankelijk",true,"Toegankelijk",'Door',0x00ff00);
			}

			if (@IPS_GetVariableProfile('Heating') == false)
			{
				IPS_CreateVariableProfile('Heating', 0);
				IPS_SetVariableProfileIcon('Heating', 'Radiator');
				IPS_SetVariableProfileAssociation("Heating",false,"Niet Verwarmd",'Radiator',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Heating",true,"Verwarmd",'Radiator',0xff0000);
			}

			if (@IPS_GetVariableProfile('Heated') == false)
			{
				IPS_CreateVariableProfile('Heated', 0);
				IPS_SetVariableProfileIcon('Heated', 'Radiator');
				IPS_SetVariableProfileAssociation("Heated",false,"Verwarming uit",'Radiator',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Heated",true,"Verwarming aan",'Radiator',0x00ff00);
			}

			if (@IPS_GetVariableProfile('Cooling') == false)
			{
				IPS_CreateVariableProfile('Cooling', 0);
				IPS_SetVariableProfileIcon('Cooling', 'SnowFlake');
				IPS_SetVariableProfileAssociation("Cooling",false,"Niet Gekoeld",'SnowFlake',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Cooling",true,"Gekoeld",'SnowFlake',0x0000ff);
			}
			
			if (@IPS_GetVariableProfile('Cooled') == false)
			{
				IPS_CreateVariableProfile('Cooled', 0);
				IPS_SetVariableProfileIcon('Cooled', 'SnowFlake');
				IPS_SetVariableProfileAssociation("Cooled",false,"Koeling uit",'SnowFlake',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Cooled",true,"Koeling aan",'SnowFlake',0x00ff00);
			}


			if (@IPS_GetVariableProfile('Aanvraag') == false)
			{
				IPS_CreateVariableProfile('Aanvraag', 0);
				IPS_SetVariableProfileIcon('Aanvraag', 'Key');
				IPS_SetVariableProfileAssociation("Aanvraag",false,"False",'Flag',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Aanvraag",true,"True",'OK',0x00ff00);
			}

			if (@IPS_GetVariableProfile('Verlicht') == false)
			{
				IPS_CreateVariableProfile('Verlicht', 0);
				IPS_SetVariableProfileIcon('Verlicht', 'Light');
				IPS_SetVariableProfileAssociation("Verlicht",false,"False",'Light',0xa0a0a0);
				IPS_SetVariableProfileAssociation("Verlicht",true,"True",'Light',0x00ff00);
			}

			if (@IPS_GetVariableProfile('xAlarm') == false)
			{
				IPS_CreateVariableProfile('xAlarm', 0);
				IPS_SetVariableProfileIcon('xAlarm', 'Warning');
				IPS_SetVariableProfileAssociation("xAlarm",false,"OK",'',0xa0a0a0);
				IPS_SetVariableProfileAssociation("xAlarm",true,"Alarm",'Warning',0xff0000);
			}

			if (@IPS_GetVariableProfile('sfeer') == false)
			{
				IPS_CreateVariableProfile('sfeer', 1);
				IPS_SetVariableProfileIcon('sfeer', 'Sofa');
				IPS_SetVariableProfileAssociation("sfeer",0,"Uit",'Close',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",1,"1",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",2,"2",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",3,"3",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",4,"4",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",5,"5",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",6,"6",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",7,"7",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",8,"8",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",9,"9",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("sfeer",100,"Orientatie",'Stars',0xa0a0a0);
			}

			if (@IPS_GetVariableProfile('InputType') == false)
			{
				IPS_CreateVariableProfile('InputType', 1);
				IPS_SetVariableProfileIcon('InputType', 'Sofa');
				IPS_SetVariableProfileAssociation("InputType",0,"Not Defined",'Close',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",1,"Drukknop",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",2,"Lamp",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",3,"WCD",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",4,"Verwarmin",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",5,"Temperatuur",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",6,"Buitendeur",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",7,"Binnendeur",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",8,"Trap",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",9,"Raam",'Sofa',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",10,"Deurslot",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",11,"Tagreader",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",12,"Lichtgordijn",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",13,"Pir sensor",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",14,"Vermogen meting",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",15,"Stroom meting",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",16,"Virt Time",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",17,"Virt Termostaat",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",18,"Virt Bool",'Stars',0xa0a0a0);
				IPS_SetVariableProfileAssociation("InputType",19,"Geofence",'Stars',0xa0a0a0);
			}

			// Create event

			if ($this->getUpdateEventId('UpdateZone') == false)
			{
				$eventId = IPS_CreateEvent(0);
				IPS_SetParent($eventId, $this->InstanceID);
				IPS_SetIdent($eventId, 'UpdateZone');
				IPS_SetName($eventId, "Update values");
				//IPS_SetHidden($eventId, true);
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

			//$this->DestroyObject();

 			if ($this->ReceiveValues() == true)
 			{
 				//$this->SendValues();
			}

		}

		 public function ReceiveValues()
 		{
 			$success = false;
 			$poller = IPS_GetProperty($this->InstanceID, "UpdateTime" );
			$DBnummer = IPS_GetProperty($this->InstanceID, "DBNummer" );
			$StartAdress = IPS_GetProperty($this->InstanceID, "StartAdress" );
			$Stringid =  $this->setUpdateS7Connection($this->InstanceID,'Data','Zone_Data',$DBnummer,$StartAdress,$poller);
			if ($Stringid) {
				$this->setUpdateEvent($this->getS7ValueId($Stringid));
			}

			$StringData	= GetValueString($this->getS7ValueId($Stringid));

			$this->SendDebug("4A-Control Zone","Zone:".$Stringid."| Value:".$StringData,1);
			if ($StringData){
				$this-> StoreDataToIPS($StringData); //
				//$this->SetStatus(106);
			}

 			$success = true;
 			return $success;
 		}

		Private function StoreDataToIPS($data)
 		{
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"ZoneInfo","Zone_Info");
			$s_ZoneID = substr($data,6,40);
			$Bools = str_pad(decbin(substr($data,48,2)), 16, 0, STR_PAD_LEFT);


			SetValueString($this->CreateVariableByIdent($CategorieID,'ZoneID','Zone_ID',3),$s_ZoneID);

 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xToegankelijk','xToegankelijk',0,'Toegankelijk'),substr($Bools, 15, 1));
 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAfgesloten','xAfgesloten',0,"Closed"),substr($Bools, 14, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVergrendeld','xVergrendeld',0,"~Lock"),substr($Bools, 13, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAanvraagHerwapenen','xAanvraagHerwapenen',0,"Aanvraag"),substr($Bools, 12, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAanvraagToegang','xAanvraagToegang',0,"Aanvraag"),substr($Bools, 11, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xPersoonAanwezig','xPersoonAanwezig',0,"~Presence"),substr($Bools, 10, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVerlicht','xVerlicht',0,"Verlicht"),substr($Bools, 9, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xPaniek','xPaniek',0,"xAlarm"),substr($Bools, 8, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xInbraak','xInbraak',0,"xAlarm"),substr($Bools, 7, 1));
			SetValueInteger($this->CreateVariableByIdent($CategorieID,'iSfeer','iSfeer',1,"sfeer"),$this->String2Hex2dec(substr($data, 54, 2)));
			SetValueInteger($this->CreateVariableByIdent($CategorieID,'iLastInput','iLastInput',1,"InputType"),$this->String2Hex2dec(substr($data, 56, 2)));

			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"ZoneKlimaat","Zone_Klimaat");
			$Bools = str_pad(decbin(substr($data,58,2)), 16, 0, STR_PAD_LEFT);

			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVerwarmd','xVerwarmd',0,"Heating"),substr($Bools, 7, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVerwarmen','xVerwarmen',0,"Heated"),substr($Bools, 6, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xGekoeld','xGekoeld',0,"Cooling"),substr($Bools, 5, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xKoelen','xKoelen',0,"Cooled"),substr($Bools, 4, 1));
			SetValueFloat($this->CreateVariableByIdent($CategorieID,'rPvTemperatuur','rPvTemperatuur',2,"~Temperature"),$this->StringTo32Float(substr($data, 60, 4)));
			SetValueFloat($this->CreateVariableByIdent($CategorieID,'rSPTemperatuur','rSPTemperatuur',2,"~Temperature"),$this->StringTo32Float(substr($data, 64, 4)));

			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"PLC_IPSYMCON","PLC_IPSYMCON");
			$Bools = str_pad(decbin(substr($data,68,1)), 16, 0, STR_PAD_LEFT);

			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xLockable','xLockable',0),substr($Bools, 7, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xManModeReq','xManModeReq',0),substr($Bools, 6, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAutoModeReq','xAutoModeReq',0),substr($Bools, 5, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xOKVoorAuto','xOKVoorAuto',0),substr($Bools, 4, 1));

			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"IPSYMCON_plc","IPSYMCON_plc");
			$Bools = str_pad(decbin(substr($data,70,1)), 16, 0, STR_PAD_LEFT);

			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xManueleModeOk','xManueleModeOk',0),substr($Bools, 7, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAutoModeOK','xAutoModeOK',0),substr($Bools, 6, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xLockReq','xLockReq',0),substr($Bools, 5, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xUnlockReq','xUnlockReq',0),substr($Bools, 4, 1));

			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"Vermogen","Vermogen");
			SetValueFloat($this->CreateVariableByIdent($CategorieID,'rActueelVermogen','rActueelVermogen',2,"Power_Watt"),$this->StringTo32Float(substr($data, 72, 4)));
			SetValueFloat($this->CreateVariableByIdent($CategorieID,'rStroom','rStroom',2,"~Ampere"),$this->StringTo32Float(substr($data, 76, 4)));

 		}


		private function String2Hex2dec($String){
			$hex=$this->stringToHex($String);
			return hexdec($hex);
		}

		private function stringToHex($String){
			$hex='';
			for($i=0;$i<strlen($String);$i++){
				$ord=ord($string[$i]);
				$hexCode = dechex($ord);
				$hex .=substr('0'.$hexCode, -2);
			}
			return $hex;
		}


		private function StringTo32Float($String){
			$hex=$this->stringToHex($String);
			return $this->hexTo32Float($hex);
		}

		private function hexTo32Float($strHex){
			$v= hexdec($strHex);
			$x = ($v & ((1<<23)-1))+(1<<23)*($v>>31|1);
			$exp = ($v >> 23 & 0xFF) - 127;
			$float = $x * pow(2,$exp-23);
			return $float;
		}



		private function setUpdateEvent($variableId)
		{

				$eventId = $this->getUpdateEventId('UpdateZone');
				
				IPS_SetEventTrigger($eventId, 1, $variableId);
				IPS_SetEventActive($eventId, true);
				IPS_SetEventScript($eventId, "Zone_ReceiveValues(" . $this->InstanceID . ");");

		}

		Private function setUpdateS7Connection($id, $ident,$name, $AreaAddress,$Adress,$poller=0)
		{
			$OBJid = $this->CreateInstanceByIdent($id,$ident,$name,"{932076B1-B18E-4AB6-AB6D-275ED30B62DB}");

			$config = sprintf('{"DataType":10,"Area":7,"AreaAddress":%s,"Address":%s,"Bit":0,"Length":80,"Poller":%s,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}',$AreaAddress, $Adress,$poller);
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

		private function getS7ValueId($ident)
		{
			return @IPS_GetVariableIDByName("Value", $ident);
		}

	}