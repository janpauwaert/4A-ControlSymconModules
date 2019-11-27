<?php
	class Zone extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			// Create Property
			$this -> RegisterPropertyInteger ( "DBNummer" , 3001 ) ;
			$this -> RegisterPropertyInteger ( "StartAdress" , 0 ) ;


			// Create event

			if ($this->getUpdateEventId('UpdateZone') == false)
			{
				$eventId = IPS_CreateEvent(0);
				IPS_SetParent($eventId, $this->InstanceID);
				IPS_SetIdent($eventId, 'UpdateZone');
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

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			$this->DestroyObject();

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
			$Stringid =  $this->setUpdateS7Connection($this->InstanceID,'ZoneNaam','Zone_Naam',$DBnummer,$StartAdress,$poller);
 			
			$this->setUpdateEvent($this->getS7ValueId($Stringid));				

			$StringData	= GetValueString($this->getS7ValueId($Stringid)); 
			//$HexData = 

			$this-> StoreDataToIPS($StringData); //
				//$this->SetStatus(106); 
			

 			$success = true; 
 			}
 			return $success;
 		} 

		Private function StoreDataToIPS($data)
 		{
 			$CategorieID = $this->CreateCategorieByIdent($this->InstanceID,"ZoneInfo","Zone_info");
			$s_ZoneID = substr($data,0,40);
			$Bools = str_pad(decbin($bData), 16, 0, STR_PAD_LEFT)

			SetValueString($this->CreateVariableByIdent($CategorieID,'ZoneID','Zone ID',0,'xAlarm'),substr($data,0,40));

 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xToegankelijk','xToegankelijk',0,'xUit'),substr($Bools, 7, 1)); 
 			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAfgesloten','xAfgesloten',0,"xAan"),substr($Bools, 6, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVergrendeld','xVergrendeld',0,"xAan"),substr($Bools, 5, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAanvraagHerwapenen','xAanvraagHerwapenen',0,"xAan"),substr($Bools, 4, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xAanvraagToegang','xAanvraagToegang',0,"xAan"),substr($Bools, 3, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xPersoonAanwezig','xPersoonAanwezig',0,"xAan"),substr($Bools, 2, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xVerlicht','xVerlicht',0,"xAan"),substr($Bools, 1, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xPaniek','xPaniek',0,"xAan"),substr($Bools, 0, 1));
			SetValueBoolean($this->CreateVariableByIdent($CategorieID,'xInbraak','xInbraak',0,"xAan"),substr($Bools, 8, 1));

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
			
			$config = sprintf('{"DataType":10,"Area":7,"AreaAddress":%s,"Address":%s,"Bit":0,"Length":80,"Poller":%s,"ReadOnly":false,"EmulateStatus":true,"Factor":0.0}',$AreaAddress, $Address,$poller);
			IPS_SetConfiguration ( $OBJid , $config) ;
			IPS_ApplyChanges ( $OBJid ) ;  // accepteer nieuwe configuratie 

			return $OBJid;
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