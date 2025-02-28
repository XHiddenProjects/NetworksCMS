<?php
include_once dirname(path: __DIR__).'/init.php';
$lang = [
    'name'=>'English',
    'abbr' => 'en-us',
    'lang'=>'Language',
    #Meta
    'projectName'=>'NetWorksCMS',
    'author'=>'XHiddenProjects',
    'description'=>'Networks is an open-source CMS and forum script that allows users to develop and design website 
    without coding',
    # Policies
    'TermsAndConditions'=>'Do you accept the <a href="{{ROOT}}/policies/terms-and-conditions" target="_blank">Terms and Conditions</a>',
    'PrivacyPolicy'=>'Do you accept the <a href="{{ROOT}}/policies/privacy-policy" target="_blank">Privacy Policy</a>',
    'CookiePolicy'=>'<a href="#" target="_blank">Cookie Policy</a>',
    # Buttons
    'accept'=>'Accept',
    'decline'=>'Decline',
    'cancel'=>'Cancel',
    'submit'=>'Submit',
    # Tabs
    'home'=>'Home',
    'install'=>'Install',
    'signup'=>'Sign Up',
    'login'=>'Login',
    'logout'=>'Logout',
    'dashboard'=>'Dashboard',
    'forum'=>'Forum',
    'security'=>'Security',
    'profile'=>'Profile',
    #Date/Time
    'date'=>'Date',
    'time'=>'Time',
    'datetime'=>'Datetime',
    'timezone'=>'Timezone',
    # Information
    'fname'=>'First Name',
    'mname'=>'Middle Name',
    'lname'=>'Last Name',
    'email'=>'Email Address',
    'username'=>'Username',
    'password'=>'Password',
    'confirm_password'=>'Confirm Password',
    'loginAuth'=>'Username/Email',
    'rememberMe'=>'Remember me',
    # Media
    'MediaSignup'=>'Sign up with:',
    'MediaLogin'=>'Login in with:',
    # Errors
    'noTableSelected'=>'No Table was selected',
    'invalidFormMethod'=>'Invalid form method',
    'invalidToken'=>'Invalid token',
    'requiredFname'=>'Enter First Name',
    'requiredLname'=>'Enter Last Name',
    'requiredUsername'=>'Enter Username',
    'requiredPsw'=>'Enter Password',
    'requiredConfirmPsw'=>'Confirm Password',
    'requiredLoginAuth'=>'You must enter a username/email',
    'requiredEmail'=>'Enter email address',
    'errMailFrom'=>'From should only contain 1 account',
    'errMailTo'=>'From should have at least 1 email.',
    'errMailFormat'=>'The array format must be [email=>name...]',
    'errIsRequired'=>'This field is required',
    'usernameExists'=>'Username already exists',
    'tokenExpired'=>'Token expired',
    'misMatchAuth'=>'Invalid credentials',
    # validations
    'psw_validation_8_chars'=>'Must contain 8+ characters',
    'psw_validation_uppercase'=>'Must contain a uppercase letter',
    'psw_validation_lowercase'=>'Must contain a lowercase letter',
    'psw_validation_numbers'=>'Must contain a number',
    'psw_validation_special_chars'=>'Must contain a special character',
    # Email
    'email_confirm_user_subject'=>'Confirmation user',
    'email_confirm_user_body'=>'Thank you signing with us, please click the button below to confirm your account
    <br/><br/>
    <a href="'.NW_DOMAIN.'/confirm" style="text-decoration: none; padding:1.5rem; border-radius: 5px; background-color:rgb(18, 105, 181); color: #ffffff; display: inline-block;">Confirm Account</a>',
    # Warnings
    'noInstallChange'=>"Warning: You cannot change this after installation"
];
?>