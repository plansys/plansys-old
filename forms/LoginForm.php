<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends Form
{
	public $username;
	public $password;
    public $rememberMe = true;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
    public function getForm() {
        return array (
            'title' => 'LoginForm',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'options' => array (
                'class' => 'login-container',
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div class=\"login form\">
<h1>System Login</h1>',
            ),
            array (
                'label' => 'Username',
                'name' => 'username',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password',
                'name' => 'password',
                'fieldType' => 'password',
                'layout' => 'Vertical',
                'fieldWidth' => '12',
                'type' => 'TextField',
            ),
            array (
                'type' => 'Text',
                'value' => '<label style=\"display:none;float:right;margin-top:17px;font-weight:normal;font-size:12px;\">
<input type=\"checkbox\" name=\"LoginForm[rememberMe]\" /> Remember Me<br/>

<div style=\"margin-left:17px;font-size:10px;color:#999;\">(30 Days)</div>
</label>',
            ),
            array (
                'label' => 'Submit',
                'buttonType' => 'success',
                'buttonSize' => '',
                'buttonPosition' => 'left',
                'options' => array (
                    'class' => 'btn-block',
                ),
                'type' => 'SubmitButton',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}