<?php

class Application_Form_Join extends Zend_Form
{

    public function init()
    {
       $this->setName("login");
       $this->setMethod('post');
       $this->setAction('/join');

       
        $this->addElement('text', 'fname', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 70)),
                ),
                'required'   => true,
                'label'      => 'First Name:',
            ));
        
        $this->addElement('text', 'lname', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 70)),
                ),
                'required'   => true,
                'label'      => 'Last Name:',
        ));
        
       $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Username:',
        ));
       
       $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Email:',
        ));
       
   
       

        $this->addElement('text', 'phone', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 20)),
                ),
                'required'   => true,
                'label'      => 'Phone:',
        ));   
       
       
       
       
        $this->addElement('text', 'address', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 255)),
                ),
                'required'   => true,
                'label'      => 'Address:',
        ));   
       

        $this->addElement('text', 'city', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 100)),
                ),
                'required'   => true,
                'label'      => 'City:',
        ));   
       
        $this->addElement('select', 'state', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 2)),
                ),
                'multiOptions' =>Main_Forms_Data::AmericaStates(),
                'required'   => true,
                'label'      => 'State:',
        ));   
       

        $this->addElement('text', 'zip', array(
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array('StringLength', false, array(0, 14)),
                ),
                'required'   => true,
                'label'      => 'Zip:',
        ));  
       
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));    


 


        $captcha = $this->createElement('captcha', 'captcha',
        array('required' => true,
              'captcha' => array('captcha' => 'Image',
              'font' => APPLICATION_PATH.'/../public/fonts/thebeautifulones.ttf',
              'fontSize' => '24',
              'wordLen' => 5,
              'height' => '75',
              'width' => '150',
              'imgDir' => APPLICATION_PATH.'/../public/captcha',
              'imgUrl' => Zend_Controller_Front::getInstance()->getBaseUrl().
              '/captcha',
              'dotNoiseLevel' => 50,
              'lineNoiseLevel' => 5)));

        $captcha->setLabel('Please type the words shown:');

        $this->addElement($captcha);



        $this->addElement('submit', 'join', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Join',
        ));  

    }


}

