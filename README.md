## Lara Form - Laravel Form Package with Form Tampering protection

LaraForm is a Laravel Form wrapper with convenient methods, that includes **Form Tampering protection** and prevents double form submission.

## <a id="installation"></a>Installation
2. <a href="#installation">Installation</a>
3. <a href="#examples">Examples</a>
4. <a href="#security">Security</a>

## <a id="installation"></a>Installation

At `composer.json` of your Laravel installation, add the following require line:

``` json
{
    "require": {
        "omnicode/lara-form": "~4.0"
    }
}
```
##### laravel <  5.4 - 1.0
##### laravel    5.5 - 2.0
##### laravel    5.6 - 3.1
##### laravel    5.7 - 4.0

Run `composer update` to add the package to your Laravel app.

### Laravel << 5.4

At `config/app.php`, add the Service Provider and the Facade:

```php
    'providers' => [
        // ...
	'LaraForm\ServiceProvider\LaraFormServiceProvider'
    ]

	//...

    'aliases' => [
        'LaraForm' => 'LaraForm\Facades\LaraForm'
    ]
```

## <a id="quick-start"></a>Quick start

To create a simple form

The first argument must be the model object or null,
the second argument must be an array with options

`{!! LaraForm::create('model|null', array $options) !!}`

Action takes an array, the first parameter must be your controller and method or
method for current controller and `HomeController@index` or `index`
the other parameters are those parameters that need to be transmitted

`{!! LaraForm::create($model, [ 'action' => [] ]) !!}`

`url` must be valid url

`{!! LaraForm::create($model, [ 'url' => 'foo/bar' ]) !!}`

Route takes an array, the first parameter must be route name
and the other parameters are those parameters that need to be transmitted

`{!! LaraForm::create($model, [ 'route' => [] ]) !!}`

Parameter **method**  has default value **post**
and if a **model** is transmitted, then the value is **put**

`{!! LaraForm::create($model, ['method'=>'delete']) !!}`

Each form must necessarily be closed using the

`{!! LaraForm::end() !!}`

--------------------------------

Helpers

Displays all validation errors and displays it on the sheet
template name in config file **errorList** and **errorItem**

`{!! LaraForm::errorList() !!}`

Simply inserts a label tag and accepts the first parameter as text
the second parameter must be an array with options
 `['for' => 'foo']`
template name in config file **label**

`{!! LaraForm::label() !!}`






Available methods

**All the of methods take two arguments
the first argument must be name field
the second argument must be an array with options**

Method **input** default has text type,
but you can in date options and type if it's related to the input tag
template name in config file '_input_'

`{!! LaraForm::input() !!}`

Method input has **password** type,
template name in config file '_input_'

`{!! LaraForm::password() !!}`

Method input has **email** type,
template name in config file '_input_'

`{!! LaraForm::email() !!}`

Method input has **number** type,
template name in config file '_input_'

`{!! LaraForm::number() !!}`

Method input has **hidden** type,
template name in config file '_hiddenInput_'

`{!! LaraForm::hidden() !!}`

method **file** can be single and multiple
the input is displayed inside the label tag and has the style button
if you want the text not to be displayed, you can give in options ['label_text' => false]
if you want to insert icons, you can transfer to options 
`['icon' => 'icon name']`
template name in config file '_file_' and '_fileMultiple_'

`{!! LaraForm::file() !!}`

Method **checkbox** can be single and multiple, for this you can pass in the options ['multiple' => true], or
after the name give the characters []
before each checkbox or a group of checkbox is put a hidden input which matters 0,
if you do not want to insert it, you can give 
`['hidden' => false]`
If you want change default hidden value, you can give
`['hidden_value' => 'val']`
if you want the field to be checked,  you can give 
`['checked' => true]`
if you want the text not to be displayed, you can give in options `['label_text' => false]`
if you want to insert icons, you can transfer to options 
`['icon' => 'icon name']`

**{!! LaraForm::checkbox() !!}**

Method **radio**.
If you want the field to be checked,  you can give `['checked' => true]`.
If you want the text not to be displayed, you can give in options `['label_text' => false]`.
If you want to insert icons, you can transfer to options 
`['icon' => 'icon name']`.

`{!! LaraForm::radio() !!}`

Method **textarea**

Template name in config file '_textarea_'

`{!! LaraForm::textarea() !!}`

Method **select**. Select can be single and multiple, for this you can pass in the options `['multiple' => true]`, or
after the name give the characters `[]`.
In the second argument you can give an array for html tags '_option_' by key '_options_',
attribute disabled - you can give both to the entire field and to the tags '_option_' and '_optgroup_'
`['disabled' => true]` for entire field,
`'option_disabled' => [ key options  ]` for option,
`'group_ disabled' => [ key groups  ]` for group,
template name in config file '_select_' and '_selectMultiple_'

`{!! LaraForm::select() !!}`

Method **submit**
If you want to insert icons, you can transfer to options 
`['icon' => 'icon name']`.

`{!! LaraForm::submit() !!}`



Default type text but supports **email/password/number/date/hidden/file/checkbox/radio/button/submit/reset** and other types for input tag 

`{!! LaraForm::input('name') !!} `

`{!! LaraForm::input('email',['type' => 'email']) !!}`

`{!! LaraForm::input('password',['type' => 'password']) !!}`

`{!! LaraForm::input('number',['type' => 'number']) !!}`

`{!! LaraForm::input('hide',['type' => 'hidden']) !!}`

`{!! LaraForm::input('image',['type' => 'file']) !!}`

`{!! LaraForm::input('check',['type' => 'checkbox']) !!}`

`{!! LaraForm::input('radio',['type' => 'radio']) !!}`

`{!! LaraForm::input('button',['type' => 'button']) !!}`

`{!! LaraForm::input('submit',['type' => 'submit']) !!}`

`{!! LaraForm::input('reset',['type' => 'reset']) !!}`

--------------
You can control inserting a label from the configuration, or simply by giving parameters in the field

`{!! LaraForm::input('field_name_1',['label' => 'My label']) !!}`

`{!! LaraForm::input('field_name_2',['label' => false]) !!}`

-----------
You can control inserting a placeholder from the configuration, or simply by giving parameters in the field

`{!! LaraForm::input('field_name_1',[ 'placeholder' => 'My 'placeholder']) !!}`

`{!! LaraForm::input('field_name_2',[ 'placeholder' => false]) !!}`

`{!! LaraForm::input('disabled_field',['disabled' => true]) !!}`

`{!! LaraForm::input('required_field',['required' => true]) !!}`

`{!! LaraForm::input('required_field',['readonly' => true]) !!}`


---------------
    {!! LaraForm::checkbox('single') !!}`
    {!! LaraForm::checkbox('multi_1[]',['value'=>1]) !!}
    {!! LaraForm::checkbox('multi_1[]',['value'=>2]) !!}
    {!! LaraForm::checkbox('multi_1[]',['value'=>3]) !!}
    {!! LaraForm::checkbox('multi_1[]',['value'=>3]) !!}
or

    {!! LaraForm::checkbox('multi_2',['multiple' => true, 'value' => 1]) !!}
    {!! LaraForm::checkbox('multi_2',['multiple' => true, 'value' => 2]) !!}
    {!! LaraForm::checkbox('multi_2',['multiple' => true, 'value' => 3]) !!}
    {!! LaraForm::checkbox('multi_2',['multiple' => true, 'value' => 3]) !!}

default checked
    
   `{!! LaraForm::checkbox('checked',['checked' => true]) !!}`
    
display only icon
   
   `{!! LaraForm::checkbox('checked',['label_text' => false, 'icon' => 'save']) !!}`

--------------

    {!! LaraForm::radio('radio',['value' => 1]) !!}
    {!! LaraForm::radio('radio',['value' => 2]) !!}

default checked

`{!! LaraForm::radio('radio',['checked' => true]) !!}`

display only icon

`{!! LaraForm::radio('radio',['label_text' => false, 'icon' => 'child']) !!}`

-------------
`{!! LaraForm::textarea('description') !!}`

------------
Supports types **button/submit/reset** 

Default name control from config file

    {!! LaraForm::submit() !!}
    {!! LaraForm::submit('add',['btn' => 'danger']) !!}
    {!! laraForm::submit('edit',['icon' => 'edit']) !!}

Does not display a name

`{!! laraForm::submit(false,['icon' => 'edit']) !!}`

------------------
Add label tag

`{!! LaraForm::label('username',['for' => ‘field_id’]) !!}`

-----------------
Display by style button

    {!! LaraForm::file('upload_image_1',['accept' => '.jpg, .png']) !!}
    {!! LaraForm::file('upload_image_2',['accept' => ['.jpg', '.gif']]) !!}

Display only icon

`{!! LaraForm::file('upload_image_3',['icon' => 'upload', 'label_text' => false]) !!}`

## <a id="examples"></a>Examples

-------------------
    
    @php
        $months = [
               'January',
               'February',
               'March',
               'April',
               'May',
               'June',
               'July',
               'August',
               'September',
               'October',
               'November',
               'December'
          ];
     $monthsKey = [
               'm-1'=>'January',
               'm-2'=>'February',
               'm-3'=>'March',
               'm-4'=>'April',
               'm-5'=>'May',
               'm-6'=>'June',
               'm-7'=>'July',
               'm-8'=>'August',
               'm-9'=>'September',
               'm-10'=>'October',
               'm-11'=>'November',
               'm-12'=>'December'
          ];
    @endphp
    {!! laraForm::select('months_1',['options' => $months]) !!}
    {!! laraForm::select('month_keys_1',['options' => $monthsKey]) !!}
    {!! LaraForm::select('months_2',['options' => $months, 'empty' => 'please select month']) !!}
    {!! laraForm::select('month_keys_2',['options' => $monthsKey, 'empty' => false]) !!}
    
    {!! laraForm::select('months_1',['options' => $months, 'disabled' => true]) !!}
    {!! laraForm::select('month_keys_3',['options' => $monthsKey, 'option_disabled' => ['m-1','m-3']]) !!}
    {!! laraForm::select('month_keys_4',['options' => $monthsKey, 'option_disabled' => 'm-1']) !!}
    {!! laraForm::select('month_keys_4',['options' => $monthsKey, ‘selected’ => 'm-4']) !!}


---------------
    
    @php
        $world  = [
               'asia' => [
                         'Afghanistan',
                         'Armenia',
                         'Bahrain',
                         'North Korea',
               ],
               'europe' => [
                           'Albania',
                           'France',
                           'Romania',
                           'Germany',
                           'Spain',
               ],
               'africa' => [
                       'Algeria',
                       'Morocco',
                       'South Africa',
                       'Egypt',
                       'Eritrea',
    
               ]
          ];
    @endphp
    {!! LaraForm::select('countries_1',['multiple' => true, 'options' => $world]) !!}
    {!! LaraForm::select('countries_2',['multiple' => true, 'options' => $world, 'selected'  => [2,3]]) !!}
    {!! LaraForm::select('countries_2[]',['options' => $world]) !!}
    {!! LaraForm::select('countries_2',['options' => $world,'group_disabled' => ['europe']]) !!}
    {!! LaraForm::select('countries_3',['options' => $world,'group_disabled' => 'europe'])!!}

----------------
    
    {!! LaraForm::input('name')
    ->id('user_name')
    ->class('foo','bar')
    ->attr(['data-url'=>'foo/bar'])
    ->class(['foo-2','bar-2'])
    ->attr('placeholder','User Name')
    ->data('id','sup')
    ->attr(['label'=>false])
    !!}

-------------

Bootstrap form style inline

    @php
        $templatesForSingleForm = [
              'formStart' => '<form class="form-inline" {%attrs%}>',
        ]
    @endphp
    {{ Laraform::setTemplate($templatesForSingleForm) }}
    {!! LaraForm::create(null,['url' => '/login']) !!}
    {!! LaraForm::email('email') !!}
    {!! LaraForm::password('password') !!}
    {!! LaraForm::submit('login',['div' => false]) !!}
    {!! Laraform::end() !!}

-------------

    {!! LaraForm::create(null,['url' => '/register']) !!}
    {!! LaraForm::input('user_name') !!}
    {!! LaraForm::email('email') !!}
    {!! LaraForm::password('password') !!}
    {!! LaraForm::submit('login',['div' => false]) !!}
    {!! Laraform::end() !!}

--------------
    
    @php
        $templatesForPage = [
              'formStart' => '<form class="form-inline" {%attrs%}>',
              '_options' => [
                 'global' => true,
               ]
        ]
    @endphp
    
    {{ Laraform::setTemplate($templatesForPage) }}
    {!! LaraForm::create(null,['url' => '/login']) !!}
    {!! LaraForm::email('email') !!}
    {!! LaraForm::password('password') !!}
    {!! LaraForm::submit('login',['div' => false]) !!}
    {!! Laraform::end() !!}

---------------
    
    {!! LaraForm::create(null,['url' => '/register']) !!}
    {!! LaraForm::input('user_name') !!}
    {!! LaraForm::email('email') !!}
    {!! LaraForm::password('password') !!}
    {!! LaraForm::submit('login',['div' => false]) !!}
    {!! Laraform::end() !!}

--------------

    @php
        $containerControlForSingleField = [
               'class' => 'col-md-9', // string/array
               'required' => 'yourClass',  // if the field required, the be added class
               'disabled' => 'yourClass',  // if the field disabled, the be added class
               'type' => 'typeInput/yourType' ,
               'data-id' => 7, // other html attributes
        ]
    @endphp
    {!! LaraForm::input('gitHubName',['div' => $containerControlForSingleField]) !!}


-------------
    @php
        $templateForSingleField = [
             'input' => '<input title="Full name" type="{%type%}" name="{%name%}" {%attrs%}/>',
             'inputContainer' => '<div class="form-group col-md-3 {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>{%label%}{%content%}{%help%}</div>'
        ]
    @endphp
    
    {!! LaraForm::input('full_name',['template' => $templateForSingleField]) !!}

----------------

    @php
        $templatesHorizontal = [
             'formStart' => '<form class="form-horizontal" {%attrs%}>',
             'inputContainer' => '<div class="form-group {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>
             {%label%}
             <div class="col-sm-10">
             {%content%}
             </div>
             {%help%}
             </div>',
    
        ];
     $labelAttr = [
         'class' => ['col-sm-2','control-label']
     ] ;
     $submitContainer = [
         'class' => ['col-sm-offset-2','col-sm-10']
     ]
    @endphp
    
    {{ Laraform::setTemplate($templatesHorizontal) }}
    {!! LaraForm::create(null,['url' => '/login']) !!}
    {!! LaraForm::email('email',['label' => $labelAttr]) !!}
    {!! LaraForm::password('password',['label' => $labelAttr]) !!}
    {!! LaraForm::submit('login',['div' => $submitContainer]) !!}
    {!! Laraform::end() !!}

------------------

    @php
        $templatesHorizontal = [
             'formStart' => '<form class="form-horizontal" {%attrs%}>',
             'inputContainer' => '<div class="form-group {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>
             {%label%}
             <div class="col-sm-10">
             {%content%}
             </div>
             {%help%}
             </div>',
             'submitContainer' => '<div class="submit col-sm-offset-2 col-sm-10 {%class%}">{%content%}</div>',
             'label' => '<label class="col-sm-2 control-label" {%attrs%}>{%icon%}{%text%}</label>'
        ];
    @endphp
    
    {{ Laraform::setTemplate($templatesHorizontal) }}
    {!! LaraForm::create(null,['url' => '/login']) !!}
    {!! LaraForm::email('email') !!}
    {!! LaraForm::password('password') !!}
    {!! LaraForm::submit('login') !!}
    {!! Laraform::end() !!}

---------------------

    @php
    
        $temp = [
         'inputContainer' => '<div class="input-group {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>
          <span class="input-group-addon">'
             .LaraForm::checkbox('sssss',['label_text' => false,'div'=>false]).
           '</span>
         {%label%}{%content%}{%help%}
         <span class="input-group-addon">'
             .LaraForm::radio('rad',['label_text' => false,'div'=>false]).
           '</span>
           </div>',
        ];
    $searchTemp = [
         'inputContainer' => '<div class="input-group {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>
         {%label%}{%content%}{%help%}
         <span class="input-group-btn">'
         .LaraForm::submit(false,['icon'=>'search','div'=>false]).
         '</span>  </div>',
        ]
    @endphp
    
    {!! LaraForm::input('search',['template' => $searchTemp,'label'=>false,'div' => ['class' => 'form-group']]) !!}
    {!! LaraForm::input('addres',['template' => $temp,'label'=>false]) !!}





## <a id="security"></a>Security

LaraForm has form tampering protection, this ensures that

- Unknown fields cannot be added to the form
- Existing fields cannot be removed from the form
- Values of hidden inputs cannot be changed

Please note, however, that it will not prevent adding new values to select dropdown or radio buttons - this information should be validated by Laravel Validations

It also prevents submitting the same form twice (server side implementation)




