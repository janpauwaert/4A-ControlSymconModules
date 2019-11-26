<?php
 IPSUtils_Include   ( "IPSLogger.inc.php" ,   "IPSLibrary::app::core::IPSLogger" ) ; 
class RoomTemperatureControl extends IPSModule
{
	public function Create()
	{
		// Never delete this line
		parent::Create();

		// Create Property
		$this -> RegisterPropertyInteger ( "RoomTemperaturePV" , 0.0 ) ;
		$this -> RegisterPropertyInteger ( "RoomTemperatureSP" , 0.0 ) ;

	}

	public function ApplyChanges()
	{
		// Never delete this line
		parent::ApplyChanges();


		//$this->setUpdateS7Connection();
		 //Validate if compatible instance id was selected and set update event 

 		$this->Aggregation(); 
 			

	}

	public function Destroy() {
		//Never delete this line!
		parent::Destroy();
	}


 	/** Processes sensor readings and updates the status variables 
	  * @return bool: true if successful, false on failure 
 	  */ 
 	public function Aggregation() 
 	{ 
 		switch (IPS_GetProperty($this->InstanceID, "AGType" )) {
		   	case 0:
 				$this->CreateVariableByIdent($this->InstanceID,'Value','Value',0) = this->OR();
 				break;
 			case 1:
 				$this->CreateVariableByIdent($this->InstanceID,'Value','Value',0)= this->AND();
 				break;
 		   	case 2:
 				$this->CreateVariableByIdent($this->InstanceID,'Value','Value',0)= this->XOR();
 				break;
 			case 3:
 				$this->CreateVariableByIdent($this->InstanceID,'Value','Value',2)= this->SUM();
 				break;
 			case 4:
 				$this->CreateVariableByIdent($this->InstanceID,'Value','Value',2)= this->AVR();
 				break;
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

	private function OR(){
		$SOr = 0;
		$Or= false;
		for ($i=1; $i < 11; $i++) { 
			$SOr = $SOr + IPS_GetProperty ( sprintf("VariableInstanceId%s",$i), 0 );
		}
		if ($SOr > 0){
			$Or = true;
		}
		return $Or;
	}
	private function XOR(){
		$SXOr = 0;
		$XOr= false;
		for ($i=1; $i < 11; $i++) { 
			$SXOr = $SXOr + IPS_GetProperty ( sprintf("VariableInstanceId%s",$i), 0 );
		}
		if ($SXOr = 1){
			$XOr = true;
		}
		return $XOr;
	}
	private function AND(){
		$SAnd = 0;
		$And= false;
		$Count = 0;
		for ($i=1; $i < 11; $i++) { 
			$SAnd = $SAnd + IPS_GetProperty ( sprintf("VariableInstanceId%s",$i), 0 );
			if (IPS_GetProperty ( sprintf("VariableInstanceId%s",$i)){
				$Count = $Count+1;
			}
		}
		if ($SAnd = $Count){
			$And = true;
		}
		return $And;
	}
	private function SUM(){
		$Sum = 0;
		for ($i=1; $i < 11; $i++) { 
			$Sum = $Sum + IPS_GetProperty ( sprintf("VariableInstanceId%s",$i), 0 );
		}
		return $Sum;
	}
	private function AVR(){
		$Avr = 0;
		$Count = 0;
		for ($i=1; $i < 11; $i++) { 
			$Avr = $Avr + IPS_GetProperty ( sprintf("VariableInstanceId%s",$i), 0 );
			if (IPS_GetProperty ( sprintf("VariableInstanceId%s",$i)){
				$Count = $Count+1;
			}
		}
		
		return $Avr/$Count;
	}



}