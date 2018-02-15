<?php

namespace Tests\LaraForm;

use LaraForm\FormProtection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class FormProtectionTest extends BaseTestCase
{

    /**
     * @var formProtection
     */
    protected $formProtection;

    /**
     * @var request
     */
    protected $request;

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->formProtection)) {
            $this->formProtection = $this->getMockBuilder(FormProtection::class)
                ->setConstructorArgs([])
                ->setMethods([ 'sessionPath'])
                ->getMock();
        };
        if (empty($this->request)) {
            $this->request = $this->getMockBuilder(Request::class)
                ->setMethods(['ajax', 'url', 'route', 'getAction', 'getName', 'getRequestUri', 'fullUrl'])->getMock();
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstruct()
    {
        $configSession = $this->getProtectedAttributeOf($this->formProtection, 'configSession');
        $ajax = $this->getProtectedAttributeOf($this->formProtection, 'ajax');
        $fields = $this->getProtectedAttributeOf($this->formProtection, 'fields');
        $this->assertEquals(config('lara_form.session'), $configSession);
        $this->assertEquals(config('lara_form.ajax_request'), $ajax);
        $this->assertEmpty($fields);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetToken()
    {
        $token = str_random(20);
        $this->formProtection->setToken($token);
        $result = $this->getProtectedAttributeOf($this->formProtection, 'token');
        $this->assertEquals($token, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetTime()
    {
        $this->formProtection->setTime(555);
        $result = $this->getProtectedAttributeOf($this->formProtection, 'created_time');
        $this->assertEquals(555, $result);
    }

    /**
     * @throws \Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testSetUnlockFields()
    {
        $formProtection = $this->newFormProtection(['processUnlockFields']);
        $this->methodWillReturn(555,'processUnlockFields',$formProtection);
        $formProtection->setUnlockFields(555);
        $result = $this->getProtectedAttributeOf($formProtection, 'unlockFields');
        $this->assertEquals(555, $result);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testValidateWhenTokenEmpty()
    {
        $returned = $this->formProtection->validate($this->request, []);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testValidateWhenEmptyTokenInSession()
    {
        $data = ['laraform_token' => 'token'];
        $this->methodWillReturn('customUrl','sessionPath', $this->formProtection);
        $returned = $this->formProtection->validate($this->request, $data);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testValidateWhenInvalidAction()
    {
        $added = $this->addToSession();
        $token = array_keys($added);
        $token = array_shift($token);
        $data = ['laraform_token' => $token];
        $formProtection = $this->newFormProtection(['isValidAction']);
        $this->methodWillReturnFalse('isValidAction', $formProtection);
        $returned = $formProtection->validate($this->request, $data);
        $this->assertEquals(false, $returned);
        $this->flushSession();
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testValidateWhenAjaxRequestValueFalse()
    {
        $this->validateWhenAjax();
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testValidateWhenRequestForgetSession()
    {
        $this->validateWhenAjax(true, false);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testValidateComparisonFieldsByKeys()
    {
        $added = $this->addToSession();
        $token = array_keys($added);
        $token = array_shift($token);
        $data = ['name' => '', 'email' => '', 'phone' => '', 'laraform_token' => $token];
        $formProtection = $this->newFormProtection(['isValidAction', 'removeUnlockFields', 'getCheckedFieldsBy']);
        $this->methodWillReturnTrue('isValidAction', $formProtection);
        $this->methodWillReturn($data, 'removeUnlockFields', $formProtection);
        $this->methodWillReturn(['name' => ''], 'getCheckedFieldsBy', $formProtection);
        $this->methodWillReturn(false,'ajax',$this->request);
        $returned = $formProtection->validate($this->request, $data);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testValidateFieldsByStringValue()
    {
        $added = $this->addToSession();
        $token = array_keys($added);
        $token = array_shift($token);
        $data = ['name' => '', 'email' => 'aaa@example.com', 'phone' => 155];
        $formProtection = $this->newFormProtection(['isValidAction', 'getCheckedFieldsBy']);
        $this->methodWillReturnTrue('isValidAction', $formProtection);
        $this->methodWillReturn($data, 'getCheckedFieldsBy', $formProtection);
        $this->methodWillReturn(false,'ajax',$this->request);
        $attr = ['name' => '', 'email' => '', 'phone' => 555, 'laraform_token' => $token];
        $returned = $formProtection->validate($this->request, $attr);

        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testValidateFieldsByArrayValue()
    {
        $added = $this->addToSession();
        $token = array_keys($added);
        $token = array_shift($token);
        $data = ['images' => ['large' => ['img1', 'img2']]];
        $formProtection = $this->newFormProtection(['isValidAction', 'getCheckedFieldsBy', 'removeUnlockFields']);
        $this->methodWillReturnTrue('isValidAction', $formProtection);
        $this->methodWillReturn($data, 'getCheckedFieldsBy', $formProtection);
        $attr = ['images' => ['large' => ['img1'=>'sss', 'img2'=>'sss']]];
        $this->methodWillReturn($attr, 'removeUnlockFields', $formProtection);
        $this->methodWillReturn(false,'ajax',$this->request);
        $returned = $formProtection->validate($this->request, $attr+['laraform_token' => $token]);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \ReflectionException
     */
   public function testVerificationForAjaxByUrl()
   {
       Config::set('lara_form.ajax_request.url',['foo/bar','bar/foo']);
       $this->methodWillReturn('foo/bar','url',$this->request);
       $formProtection = $this->newFormProtection();
       $returned = $this->getProtectedMethod($formProtection,'verificationForAjax',[$this->request]);
       $this->assertEquals(false, $returned);
   }

    /**
     * @throws \ReflectionException
     */
    public function testVerificationForAjaxByAction()
    {
        Config::set('lara_form.ajax_request.action',['CustomController@customMethod']);
        $this->methodWillReturn(['controller' => 'App\Http\Controllers\CustomController@customMethod'],'getAction',$this->request);
        $this->methodWillReturn($this->request,'route',$this->request);
        $formProtection = $this->newFormProtection();
        $returned = $this->getProtectedMethod($formProtection,'verificationForAjax',[$this->request]);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testVerificationForAjaxByRoute()
    {
        Config::set('lara_form.ajax_request.route',['user.store']);
        $this->methodWillReturn('user.store','getName',$this->request);
        $this->methodWillReturn($this->request,'route',$this->request);
        $formProtection = $this->newFormProtection();
        $returned = $this->getProtectedMethod($formProtection,'verificationForAjax',[$this->request]);
        $this->assertEquals(false, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testVerificationForAjax()
    {
        Config::set('lara_form.ajax_request.url',[]);
        Config::set('lara_form.ajax_request.action',[]);
        Config::set('lara_form.ajax_request.route',[]);
        $formProtection = $this->newFormProtection();
        $returned = $this->getProtectedMethod($formProtection,'verificationForAjax',[$this->request]);
        $this->assertEquals(true, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRemoveByTimeWhenLifetimeFalse()
    {
        Config::set('lara_form.session.lifetime', false);
        $result = $this->formProtection->removeByTime();
        $this->assertEquals(false, $result);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRemoveByTimeWhenSessionHistoryEmpty()
    {
        session()->forget(Config::get('lara_form.session.name'));
        $result = $this->formProtection->removeByTime();
        $this->assertEquals(false, $result);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testRemoveByTimeWhenLifetimeDate()
    {
        Config::set('lara_form.session.lifetime', '10 minutes');
        $added = $this->addToSession();
        $this->formProtection->removeByTime();
        $result = $this->removeFromSessionByTime($added);
        $data = session(config('lara_form.session.name'));
        $this->assertEquals($result, $data);
        $this->flushSession();
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testRemoveByCountWhenFalse()
    {
        Config::set('lara_form.session.max_count', false);
        $result = $this->formProtection->removeByCount();
        $this->assertEquals(false, $result);
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \RuntimeException
     */
    public function testRemoveByCountWhenNumber()
    {
        Config::set('lara_form.session.max_count', 5);
        $this->addToSession();
        $this->formProtection->removeByCount();
        $data = session(config('lara_form.session.name'));
        $this->assertCount(5, $data);
        $this->flushSession();
    }

    /**
     * @throws \Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testProcessUnlockFieldsByString()
    {
        $result = $this->formProtection->processUnlockFields('string');
        $this->assertEquals(['string'], $result);
    }

    /**
     * @throws \Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testProcessUnlockFieldsByArray()
    {
        $data = ['1', '2', 5, 'hello'];
        $result = $this->formProtection->processUnlockFields($data);
        $this->assertEquals($data, $result);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testProcessUnlockFieldsByOtherType()
    {
        $this->formProtection->processUnlockFields($this);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testAddFieldWhenOptionDisabled()
    {
        $data = ['disabled'=> true];
        $result = $this->formProtection->addField('field', $data);
        $this->assertFalse($result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddFieldWhenOptionUnlock()
    {
        $data = ['_unlock' => true];
        $this->formProtection->addField('field', $data);
        $result = $this->getProtectedAttributeOf($this->formProtection, 'unlockFields');
        $this->assertEmpty($data);
        $this->assertEquals(['field'], $result);

    }

    /**
     * @throws \ReflectionException
     */
    public function testAddFieldByStringField()
    {
        $this->formProtection->addField('input');
        $result = $this->getProtectedAttributeOf($this->formProtection, 'fields');
        $this->assertEquals(['input' => ''], $result);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testAddFieldByArrayFields()
    {
        $formProtection = $this->newFormProtection(['addArrayInputField']);
        $this->methodWillReturn(null,'addArrayInputField',$formProtection);
        $returned = $formProtection->addField('user[name]');
        $this->assertEquals(null,$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddArrayInputFieldWhenMultiDimensional()
    {
        $this->formProtection->addArrayInputField('user[name]');
        $result = $this->getProtectedAttributeOf($this->formProtection, 'fields');
        $data = ['user' => ['name' => '']];
        $this->assertEquals($data, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddArrayInputFieldWhenOnceDimensional()
    {
        $this->formProtection->addArrayInputField('user');
        $result = $this->getProtectedAttributeOf($this->formProtection, 'fields');
        $this->assertEquals(['user' => ''], $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testConfirm()
    {
        $this->formProtection->confirm();
        $fields = $this->getProtectedAttributeOf($this->formProtection, 'fields');
        $unlockFields = $this->getProtectedAttributeOf($this->formProtection, 'unlockFields');
        $path = $this->getProtectedMethod($this->formProtection, 'sessionPath');
        $sessionData = session($path);
        $this->flushSession();
        $this->assertEmpty($fields);
        $this->assertEmpty($unlockFields);
        $this->assertNotEmpty($sessionData);
    }


    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testIsValidAction()
    {
        $unlock = ['action', 'url', 'route'];
        Config::set('lara_form.session.paths.unlock',$unlock);
        $this->formProtection->expects($this->any())->method('sessionPath')->willReturn('customUrl');

        foreach ($unlock as $item) {
            $this->withSession(['customUrl' => [$item]]);
            $returned = $this->getProtectedMethod($this->formProtection,'isValidAction',[$this->request,'token']);
            $this->assertEquals(true, $returned);
            $this->flushSession();
        }
    }


    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testIsValidActionByUrls()
    {
        $urls = ['example.loc/user/store', 'user/store', 'user/store?params=555'];
        $this->request->expects($this->any())->method('url')->willReturn('user/store');
        $this->request->expects($this->any())->method('getRequestUri')->willReturn('user/store?params=555');
        $this->request->expects($this->any())->method('fullUrl')->willReturn('example.loc/user/store');
        $formProtection = $this->newFormProtection(['getAction']);
        foreach ($urls as $item) {
            $formProtection->expects($this->any())->method('getAction')->willReturn($item);
            $returned = $this->getProtectedMethod($formProtection,'isValidAction',[$this->request,'token']);
            $this->assertEquals(true, $returned);
            $this->flushSession();
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetAction()
    {
        $this->methodWillReturn('customUrl', 'sessionPath', $this->formProtection);
        $this->withSession(['customUrl' => 'api/foo/bar']);
        $returned = $this->getProtectedMethod($this->formProtection, 'getAction', ['token']);
        $this->assertEquals('api/foo/bar', $returned);
        $this->flushSession();

    }

    /**
     * @throws \ReflectionException
     */
    public function testSessionPathWhenEmptyPath()
    {
        $token = str_random(32);
        $formProtection = $this->newFormProtection();
        $formProtection->setToken($token);
        $returned = $this->getProtectedMethod($formProtection, 'sessionPath');
        $path = config('lara_form.session.name');
        $this->assertEquals($path . '.' . $token, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSessionPathWhenUsedPath()
    {
        $token = str_random(32);
        $formProtection = $this->newFormProtection();
        $formProtection->setToken($token);
        $returned = $this->getProtectedMethod($formProtection, 'sessionPath', ['custom']);
        $path = config('lara_form.session.name');
        $this->assertEquals($path . '.custom', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetCheckedFieldsBy()
    {
        $this->methodWillReturn('customUrl', 'sessionPath', $this->formProtection);
        $this->withSession(['customUrl' => ['user']]);
        $returned = $this->getProtectedMethod($this->formProtection, 'getCheckedFieldsBy', ['customUrl']);
        $this->assertEquals(['user'], $returned);
    }


    /**
     * @throws \ReflectionException
     */
    public function testRemoveUnlockFieldsWhenUseGlobal()
    {
        list($data, $unlockData) = $this->addUlockFields();
        Config::set('lara_form.except.field', ['phone']);
        $this->methodWillReturn('customUrl', 'sessionPath', $this->formProtection);
        $returned = $this->getProtectedMethod(
            $this->formProtection,
            'removeUnlockFields',
            [$data + $unlockData, 'customUrl']);
        unset($data['phone']);
        $this->assertEquals($data, $returned);

    }

    /**
     * @throws \ReflectionException
     */
    public function testRemoveUnlockFieldsWhenUseLocal()
    {
        list($data, $unlockData) = $this->addUlockFields();
        $this->methodWillReturn('customUrl', 'sessionPath', $this->formProtection);
        $returned = $this->getProtectedMethod($this->formProtection,
            'removeUnlockFields',
            [$data + $unlockData, 'customUrl']);
        $this->assertEquals($data, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetUrl()
    {
        $this->formProtection->setUrl('foo/bar');
        $url = $this->getProtectedAttributeOf($this->formProtection, 'url');
        $this->assertEquals('foo/bar', $url);
    }

    /**
     * @return array
     */
    private function addUlockFields()
    {
        $data = [
            'phone' => '',
            'images[]' => [
                'foo/bar',
                '/bar/foo'
            ]
        ];
        $unlockData = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];
        $sessionData = [
            'customUrl' => array_keys($unlockData)
        ];
        $this->withSession($sessionData);
        return [$data, $unlockData];
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    private function addToSession()
    {
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $data[str_random(32)] = [
                "is_check" => [],
                "is_unlock" => [
                    "_token",
                    "_method",
                    "laraform_token",
                    "images",
                ],
                "created_time" => time() - random_int(50, 800),
                "action" => "api/foo/bar",
            ];
        }
        $this->withSession([config('lara_form.session.name') => $data]);
        return $data;
    }

    /**
     * @param $formHistory
     * @return array
     */
    private function removeFromSessionByTime($formHistory)
    {
        $maxTime = config('lara_form.session.lifetime');
        $timeName = config('lara_form.session.paths.time');
        $maxSeccounds = strtotime('-' . $maxTime);
        $newHistory = array_filter($formHistory, function ($value) use ($timeName, $maxSeccounds) {
            if (isset($value[$timeName])) {
                if ($value[$timeName] > $maxSeccounds) {
                    return $value;
                }
            }
        });
        return $newHistory;
    }

    /**
     * @param bool $ajax
     * @param bool $empty
     * @throws \PHPUnit_Framework_Constraint
     */
    private function validateWhenAjax($ajax = true, $empty = true)
    {
        $added = $this->addToSession();
        $token = array_keys($added);
        $token = array_shift($token);
        $data = ['laraform_token' => $token];
        $path = config('lara_form.session.name').'.'.$token;
        $formProtection = $this->newFormProtection(['sessionPath', 'isValidAction', 'verificationForAjax', 'removeUnlockFields']);

        $formProtection->expects($this->any(2))->method('sessionPath')->willReturn($path);
        $this->methodWillReturnTrue('isValidAction', $formProtection);
        $this->methodWillReturn($empty, 'verificationForAjax', $formProtection);
        $this->methodWillReturn($data, 'removeUnlockFields', $formProtection);
        $this->methodWillReturn($ajax, 'ajax', $this->request);

        $formProtection->validate($this->request, $data);
        $sessionData = session($path);
        if ($empty) {
            $this->assertEmpty($sessionData);
        } else {
            $this->assertNotEmpty($sessionData);
        }
    }


    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    private function newFormProtection($methods = null)
    {
        return $this->newInstance(FormProtection::class,[],$methods);
    }
}
