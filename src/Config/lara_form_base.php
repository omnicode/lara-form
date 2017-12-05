<?php
return [
    'charset' => 'utf-8',
    'seperator' => [
        'start' => '{%', // [..., {..., (...
        'end' => '%}',   // ...], ...}, ...)
    ],
    'label' => [
        'form_protection' => 'laraform_token',
        'select_empty' => '--Select--',
        'submit' => 'Save',
        'submit_btn' => 'default',
        'idPrefix' => false
    ],
    'default_value' => [
        'checkbox' => 1,
        'hidden' => 0
    ],
    'session' => [
        'pre_path' => 'laraforms',
        'request_interval' => 5, // by hour
        'max_time' => 2, // by hour
        'max_count' => false,
        'path_for' => [
            'check' => 'is_check',
            'unlock' => 'is_unlock',
        ]
    ],
    'except' => [
        'stripe/*',
        'user-settings/intro-pop-up'
    ],
    'css' => [
        'errorClass' => 'has-error',
        'inputClass' => 'form-control',
        'selectClass' => 'form-control',
        'checkboxClass' => false,
        'radioClass' => false,
        'fileClass' => false,
        'textareaClass' => 'form-control',
        'submitColor' => 'btn-default',
        'submitClass' => 'btn',
    ],
    'templates' => [
        // Used for button elements in button().
        'button' => '<button {%attrs%}>{%text%}</button>',
        // Used for checkboxes in checkbox() and multiCheckbox().
        'checkbox' => '<input type="checkbox" name="{%name%}" value="{%value%}" {%attrs%}/>',
        // Wrapper container for checkboxes.
        'checkboxContainer' => '<div class="checkbox {%type%}{%required%}{%class%}" {%containerAttrs%}>{%content%}</div>',
        // Container for error items.
        'errorList' => '<div class="alert alert-danger {%class%}">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <ul>{%content%}</ul>
                        </div>',
        // Error item wrapper.
        'errorItem' => '<li>{%text%}</li>',
        // File input used by file().
        'file' => '<input type="file" name="{%name%}" {%attrs%}/>',
        // File input used by file().
        'fileMultiple' => '<input type="file" name="{%name%}"  multiple="multiple" {%attrs%}/>',
        // Fieldset element used by allControls().
        'fieldset' => '<fieldset {%attrs%}>{%content%}</fieldset>',
        // Open tag used by create().
        'formStart' => '<form {%attrs%}>',
        // Close tag used by end().
        'formEnd' => '</form>',
        // General grouping container for control(). Defines input/label ordering.
        'formGroup' => '{%label%}{%input%}',
        // Generic input element.
        'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
        // hidden for default value
        'hiddenInput' => '<input type="hidden" name="{%name%}" value="{%value%}"/>',
        // Submit input element.
        'submit' => '<input type="{%type%}" value="{%name%}" {%attrs%}/>',
        // Container element used by control().
        'inputContainer' => '<div class="form-group {%type%}{%required%}{%class%}{%error%}" {%containerAttrs%}>{%label%}{%content%}{%help%}</div>',
        // Container element used by control() when a field has an error.
        'helpBlock' => '<span class="help-block {%class%}">{%text%}</span>',
        // Label element when inputs are not nested inside the label.
        'label' => '<label {%attrs%}>{%text%}</label>',
        // Label element used for radio and multi-checkbox inputs.
        'nestingLabel' => '{%hidden%}<label {%attrs%}>{%content%}{%text%}</label>',
        // Legends created by allControls()
        'legend' => '<legend>{%text%}</legend>',
        // Multi-Checkbox input set title element.
        'multicheckboxTitle' => '<legend>{%text%}</legend>',
        // Multi-Checkbox wrapping container.
        'multicheckboxWrapper' => '<fieldset {%attrs%}>{%content%}</fieldset>',
        // Option element used in select pickers.
        'option' => '<option value="{%value%}" {%attrs%}>{%text%}</option>',
        // Option group element used in select pickers.
        'optgroup' => '<optgroup label="{%label%}" {%attrs%}>{%content%}</optgroup>',
        // Select element,
        'select' => '<select name="{%name%}" {%attrs%}>{%content%}</select>',
        // Multi-select element,
        'selectMultiple' => '<select name="{%name%}{%%}" multiple="multiple" {%attrs%}>{%content%}</select>',
        // Radio input element,
        'radio' => '<input type="radio" name="{%name%}" value="{%value%}" {%attrs%}/>',
        // Wrapping container for radio input/label,
        'radioWrapper' => '{%label%}',
        // Textarea input element,
        'textarea' => '<textarea name="{%name%}" {%attrs%}>{%value%}</textarea>',
        // Container for submit buttons.
        'submitContainer' => '<div class="submit {%class%}">{%content%}</div>',
        //icon
        'icon' => '<i class="fa fa-{%name%} {%class%}"></i>'
    ]
];