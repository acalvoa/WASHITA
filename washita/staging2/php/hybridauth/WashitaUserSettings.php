<?php
    include_once(dirname(__FILE__)."/../_helpers.php");
    require_once(dirname(__FILE__)."/WashitaUser.php");
    require_once(dirname(__FILE__)."/UserType.enum.php");
    require_once(dirname(__FILE__)."/../City.class.php");


class WashitaUserSettings{
     /**
     * @var string
     */    
    var $SuccessMessage;
     /**
     * @var string
     */
    var $ErrorMessage;
    
    
    /**
     * @param integer $id
     * @return string
     */
    public function GetHtmlForUserByUserId($id){
        $user = WashitaUser::GetUserById($id);
        return $this->Html($user);
    }

    /**
     * @param WashitaUser $user
     * @return string
     */
	public function Html(WashitaUser $user)
	{
        $html='
         <div>';

        if($this->SuccessMessage){
             $html.='<div class="alert alert-success">'.$this->SuccessMessage.'</div>';
         }
        if($this->ErrorMessage){
             $html.='<div class="alert alert-danger">'.$this->ErrorMessage.'</div>';
         }
         
         if(!$user->IsComplete){
             $html.='<div class="alert alert-info">
                        <strong>Welcome!</strong> Please fill in the required fields below and complete the registration.
                     </div>';
         }

         $html.=' 
            <div class="row item text-left">
                    <div class="col col-sm-6 input-group-vertical">
                        <p>Nombre*</p>
                        <input type="text" name="name" class="form-control" placeholder="Name" maxlength="124" required
                        value="'.$user->Name.'"
                        />
                    </div>
                    <div class="col col-sm-6 input-group-vertical">
                        <p>Lastname*</p>
                        <input type="text" name="lastname" class="form-control" placeholder="Lastname" maxlength="124" required
                        value="'.$user->Lastname.'"
                        />
                    </div>
                            
                    <div class="col col-sm-6 input-group-vertical">
                        <p>Ciudad*</p>
                        <select name="city_area_id" class="form-control" required>';
                                $cities = City::GetAllCititesWithAreas();
                                foreach ($cities as $city) {
                                    foreach ($city->Areas as $cityArea) {
                                         $isSelected = ($user->CityAreaId === $cityArea->Id);
                                         $html.= '<option value="'.$cityArea->Id.'" '.($isSelected? "selected" : "").'>'.$city->Name.' / '.$cityArea->Name.'</option>';
                                    }
                                }

                        $html.=' 
                        </select>
                    </div>
                    <div class="col col-sm-6 input-group-vertical">
                            <p>Dirección</p>
                            <input type="text" name="address" class="form-control" placeholder="Address" maxlength="1024"
                                value="'.(!empty($user->Address)? $user->Address: "").'"
                            />
                    </div>

                    <div class="col col-sm-6 input-group-vertical ">
                        <p>Notification Email*</p>
                        <input type="email" name="email" class="form-control" placeholder="email de contacto"
                        maxlength="124" required 
                        value="'.(!empty($user->NotificationEmail)? $user->NotificationEmail: "").'"
                        />
                    </div>
                    <div class="col col-sm-6 input-group-vertical">
                            <p>Whatsapp</p>
                            <input type="text" name="whatsapp" class="form-control" placeholder="ingresa tú whatsapp y te enviaremos actualización de tu orden"
                                value="'.(!empty($user->Phone)? $user->Phone: "").'"
                            maxlength="20" />
                    </div>

                    <div class="col col-sm-6 input-group-vertical ">
                        <p>código (opcional)</p>
                        <input type="text" name="registration_code" class="form-control" placeholder="code"
                        maxlength="30"  
                        value="'.($user->RegistrationCode != null? $user->RegistrationCode->Code: "").'"
                         '.($user->RegistrationCode != null? "readonly": "").'
                        />';
                        if($user->UserType == UserType::Influencer){
                            $html.=' 
                             <small class="text-muted">Puedes enviar tu código "'.$user->RegistrationCode->Code.'" a tus amigos para que ellos tengan descuento. Por cada vez que alguien use tu cupón, tú ganarás crédito que podrás utilizar en tus pedidos.</small>
                            ';
                        }
                    $html.='
                    </div>';


                    if($user->UserType == UserType::Influencer){
                        $html.='
                        <div class="col col-sm-6 input-group-vertical ">
                            <p>Personal discount amount</p>
                            <p>$'.$user->PersonalDiscountAmount.'</p>
                        </div>';
                    }

                    $html.='
                    
            </div>
        </div>
        ';

        return $html;
    }
    
}