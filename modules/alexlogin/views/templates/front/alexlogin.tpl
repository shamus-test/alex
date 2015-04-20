<div class="clear clearfix"></div>
  <form method="post" action="" class="form-horizontal alexlogin-form-wrap">
    <div id="ajaxlogin-erro"></div>
    <!-- login form -->
    <div class="alexlogin-login-wrap">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-sp-12">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sp-12">
            <div class="alexlogin-box form-group">
              <h2>{l s='My account' mod='alexlogin'}</h2>
              <p>{l s='Sign in' mod='alexlogin'}</p>
            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='E-mail' mod='alexlogin'}</label>
              <input type="email" name="log_email" id="log_email" value="" placeholder="{l s='Enter your email' mod='alexlogin'}" class="form-control" />
            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='Password' mod='alexlogin'}</label>
              <input type="password" name="log_password" id="log_password" value="" placeholder="{l s='Enter your password' mod='alexlogin'}" class="form-control" />
            </div>
            <div class="alexlogin-box form-group">       
              <button type="button" name="" value="Sign in" onclick="alexLogin();" id="btn-login" />{l s='Sign in' mod='alexlogin'}</button>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sp-12">
            <div class="alexlogin-box form-group">
              <h2>{l s='Create new account' mod='alexlogin'}</h2>
              <p>{l s='New users' mod='alexlogin'}</p>
            </div>
            <div class="alexlogin-box form-group">       
              <button type="button" name="" value="" onclick="showRegisterPopup();" />{l s='Create account' mod='alexlogin'}</button>
            </div>
            <p>or login via facebook</p>
            <div class="alexlogin-box form-group"> 
              {hook h='fblogin' cookie=$cookie}      
              <button type="button" name="" value="" />{l s='login with facebook' mod='alexlogin'}</button>
            </div>          
          </div>
        </div>
      </div>
    </div>
    <!-- login form -->
    
    <!-- registration form -->
    <div class="alexlogin-register-wrap">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-sp-12">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sp-12">
            <div class="alexlogin-box form-group">
              <h2>{l s='Create new account' mod='alexlogin'}</h2>
              <p>{l s='Include your information in the following fields' mod='alexlogin'}</p>
            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='Name' mod='alexlogin'}</label>
              <input type="text" name="reg_name" id="reg_name" value="" placeholder="{l s='Enter your name' mod='alexlogin'}" class="form-control" />
            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='Password' mod='alexlogin'}</label>
              <input type="password" name="reg_password" id="reg_password" value="" placeholder="{l s='Enter your password' mod='alexlogin'}" class="form-control" />

            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='Confirm Password' mod='alexlogin'}</label>
              <input type="password" name="reg_cnf_password" id="reg_cnf_password" value="" placeholder="{l s='Enter your password' mod='alexlogin'}" class="form-control" />
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 col-sp-12">
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='Surname' mod='alexlogin'}</label>
              <input type="text" name="reg_surname" id="reg_surname" value="" placeholder="{l s='Enter your surname' mod='alexlogin'}" class="form-control" />
            </div>
            <div class="alexlogin-box form-group">
              <label class="control-label"><sup>*</sup> {l s='E-Mail' mod='alexlogin'}</label>
              <input type="email" name="reg_email" id="reg_email" value="" placeholder="{l s='Enter your email' mod='alexlogin'}" class="form-control" />
            </div>
            <div class="alexlogin-box form-group">       
              <button type="button" name="" value="OK" onclick="alexRegister();" id="btn-register" />{l s='OK' mod='alexlogin'}</button>
            </div>
            <div class="alexlogin-box form-group">   
              <div class="checkbox">    
                <input type="checkbox" name="reg_term_condition" id="reg_term_condition" value="1" /> {l s='I accept terms and conditions' mod='alexlogin'}
              </div> 
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- registration form -->
  </form>
{strip}
  {addJsDefL name=err_reg_name}{l s='Name is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_password}{l s='Password is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_cnf_password}{l s='Confirm password is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_valid_password}{l s='Both passowrd does not match' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_surname}{l s='Surname is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_email}{l s='Email is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_valid_email}{l s='Enter your valid email' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_reg_term_condition}{l s='Accept term and condition' mod='alexlogin'}{/addJsDefL}


  {addJsDefL name=err_log_email}{l s='Email is required' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_log_valid_email}{l s='Enter your valid email' mod='alexlogin'}{/addJsDefL}
  {addJsDefL name=err_log_password}{l s='Password is required' mod='alexlogin'}{/addJsDefL}
  
{/strip}
