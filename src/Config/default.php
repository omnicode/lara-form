<?php
return [
    'charset' => 'utf-8',
    'token_name' => 'laraform_token',
    'separator' => [
        'start' => '{%', // [..., {..., (...
        'end' => '%}',   // ...], ...}, ...)
    ],
    'text' => [
        'select_empty' => '--Select--',
        'submit_name' => 'Save',
        'placeholder' => true,
        'label' => true,
    ],
    'css' => [
        'class_control' => true,
        'id_prefix' => false,
        'class' => [
            'error' => 'has-error',
            'text' => 'form-control',
            'password' => 'form-control',
            'email' => 'form-control',
            'number' => 'form-control',
            'select' => 'form-control',
            'checkbox' => false,
            'radio' => false,
            'file' => false,
            'textarea' => 'form-control',
            'submit' => 'btn',
            'submitColor' => 'btn-default',
        ]
    ],
    'session' => [
        'name' => 'laraforms',
        'lifetime' => '1 hours', //Parse about any English textual datetime description
        'max_count' => 50,
        'paths' => [
            'check' => 'is_check',
            'unlock' => 'is_unlock',
            'time' => 'created_time',
            'action' => 'action',
        ],
    ],
    'ajax_request' => [
        'url' => [

        ],
        'action' => [

        ],
        'route' => [

        ]
    ],
    'except' => [
        'url' => [

        ],
        'route' => [

        ],
        'field' => [

        ],
    ],
    'templates' => [
        // Used for button elements in button().
        'button' => '<button {%attrs%}>{%icon%}{%text%}</button>',
        // Used for checkboxes in checkbox() and multiCheckbox().
        'checkbox' => '<input type="checkbox" name="{%name%}" value="{%value%}" {%attrs%}/>',
        // Wrapper container for checkboxes.
        'checkboxContainer' => '<div class="checkbox {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>{%content%}{%help%}</div>',
        // Wrapper container for radio.
        'radioContainer' => '<div class="radio {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>{%content%}{%help%}</div>',
        // Container for error items.
        'errorList' => '<div class="alert alert-danger {%class%}">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <ul>{%content%}</ul>
                        </div>',
        // Error item wrapper.
        'errorItem' => '<li>{%text%}</li>',
        // File input used by file().
        'file' => '<input type="file" name="{%name%}" style="display: none" {%attrs%}/>',
        // File input used by file().
        'fileMultiple' => '<input type="file" name="{%name%}[]"  multiple="multiple" style="display: none" {%attrs%}/>',
        // Open tag used by create().
        'formStart' => '<form {%attrs%}>',
        // Close tag used by end().
        'formEnd' => '</form>',
        // Generic input element.
        'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
        // hidden for default value
        'hiddenInput' => '<input type="hidden" name="{%name%}" value="{%value%}"/>',
        // Submit input element.
        'submit' => '<input type="{%type%}" value="{%name%}" {%attrs%}/>',
        // Container element used by control().
        'inputContainer' => '<div class="form-group {%type%} {%required%} {%disabled%} {%class%} {%error%}" {%containerAttrs%}>{%label%}{%content%}{%help%}</div>',
        // Container element used by control() when a field has an error.
        'helpBlock' => '<span class="help-block {%class%}">{%text%}</span>',
        // Label element when inputs are not nested inside the label.
        'label' => '<label {%attrs%}>{%icon%}{%text%}</label>',
        // Label element used for radio and multi-checkbox inputs.
        'nestingLabel' => '{%hidden%}<label {%attrs%}>{%content%}{%icon%}{%text%}</label>',
        // Option element used in select pickers.
        'option' => '<option value="{%value%}" {%attrs%}>{%text%}</option>',
        // Option group element used in select pickers.
        'optgroup' => '<optgroup label="{%label%}" {%attrs%}>{%content%}</optgroup>',
        // Select element,
        'select' => '<select name="{%name%}" {%attrs%}>{%content%}</select>',
        // Multi-select element,
        'selectMultiple' => '<select name="{%name%}[]" multiple="multiple" {%attrs%}>{%content%}</select>',
        // Wrapper container for select tag.
        'selectContainer' => '<div class="select {%type%} {%required%} {%disabled%} {%class%} {%error%}">{%hidden%}{%label%}{%content%}{%help%}</div>',
        // Radio input element,
        'radio' => '<input type="radio" name="{%name%}" value="{%value%}" {%attrs%}/>',
        // Textarea input element,
        'textarea' => '<textarea name="{%name%}" {%attrs%}>{%value%}</textarea>',
        // Container for submit buttons.
        'submitContainer' => '<div class="submit {%class%}">{%content%}</div>',
        // Container for file inputs.
        'fileContainer' => '<div class="file {%type%} {%required%} {%disabled%} {%class%} {%error%}">{%label%}{%content%}{%help%}</div>',
        //icon
        'icon' => '<i class="fa fa-{%name%}"></i>'
    ]
];