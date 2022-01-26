import 'jquery.cookie';
import 'block-ui';
import 'select2';
import Driver from 'driver.js';

import Test from './components/test.js';

global.cookie = require('jquery.cookie');
global.bootstrap = require('bootstrap');
global.md5 = require('md5');

global.Test = Test;

/**
 * Bootstrap Tooltip
 */
let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

/**
 * Z-Indexer
 */
let zIndexer = function(elements)
{
    let zindex = 0;

    $.each($(elements), function() {
        let __ = $(this);

        zindex = __.css('z-index') > zindex ? __.css('z-index') : zindex;
    })

    return parseInt(zindex)+1;
}

/**
 * To html
 */
let toHtml = function()
{
    $('[data-to-html]').on('keydown', function() {
        let __ = $(this);

        $('[data-id=' + __.data('to-html') + ']').html(__.val())
    })
}

/**
 * Looper
 */
let loopers = [];

let looper = function(id, _function, _delay)
{
    eval(_function)()

    if (typeof loopers[id] === 'undefined')
        loopers[id] = true;

    clearTimeout(loopers[id]);

    loopers[id] = window.setTimeout(function() {
        looper(id, _function, _delay)
    }, _delay ? _delay : 1000)
}

/**
 * Form Data
 */
let getFormData = function(items, target)
{
    var array = $('<article />');

    $.each(items, function(key, name) {
        let item = target
            ? elem(target + '->find([name=' + name + '])')
            : $('[name=' + name + ']');

        if (
            (
                item.attr('type') &&
                (
                    item.attr('type') == 'text' ||
                    item.attr('type') == 'datetime' ||
                    item.attr('type') == 'datetime-local' ||
                    item.attr('type') == 'email' ||
                    item.attr('type') == 'month' ||
                    item.attr('type') == 'date' ||
                    item.attr('type') == 'number' ||
                    item.attr('type') == 'range' ||
                    item.attr('type') == 'search' ||
                    item.attr('type') == 'tel' ||
                    item.attr('type') == 'time' ||
                    item.attr('type') == 'url' ||
                    item.attr('type') == 'week' ||
                    item.attr('type') == 'hidden' ||
                    item.attr('type') == 'password' ||
                    item.attr('type') == 'file' ||
                    item.attr('type') == 'color'
                )
            ) || item.is('textarea')
        )
        {
            let __ = target
                ? elem(target + '->find(input[name="' + name + '"])')
                : $('input[name="' + name + '"]'),
                arr = [];

            if (item.attr('multiple'))
            {
                for (let i = 0; i < __.length; i++)
                {
                    arr[i] = __.eq(i).val();
                    array.data(item.data('alias') ? item.data('alias') : name, arr);
                }
            }
            else
                array.data(item.data('alias') ? item.data('alias') : name, item.val());
        }
        else if (item.is('select'))
        {
            let options = target
                ? elem(target + '->find(select[name="' + name + '"])->children(option:checked)')
                : $('select[name="' + name + '"] > option:checked'),
                arr = [];

            if (item.attr('multiple'))
            {
                for (let i = 0; i < options.length; i++)
                {
                    arr[i] = options.eq(i).val();
                    array.data(item.data('alias') ? item.data('alias') : name, arr);
                }
            }
            else
            {
                array.data(
                    item.data('alias') ? item.data('alias') : name,
                    options.eq(0).val()
                );
            }
        }
        else if (item.attr('type') == 'radio')
        {
            array.data(
                item.data('alias') ? item.data('alias') : name,
                target
                ? elem(target + '->find(input[name="' + name + '"]:checked)').val()
                : $('input[name="' + name + '"]:checked').val()
            )
        }
        else if (item.attr('type') == 'checkbox')
        {
            let checkboxes = target
                ? elem(target + '->find(input[name="' + name + '"]:checked)')
                : $('input[name="' + name + '"]:checked'),
                arr = [];

            if (item.data('multiple') == true)
            {
                for (let i = 0; i < checkboxes.length; i++)
                {
                    arr[i] = checkboxes.eq(i).val();
                    array.data(item.data('alias') ? item.data('alias') : name, arr);
                }
            }
            else
            {
                if (checkboxes.length == 1)
                {
                    array.data(
                        item.data('alias') ? item.data('alias') : name,
                        target
                        ? elem(target + '->find(input[name="' + name + '"]:checked)').val()
                        : $('input[name="' + name + '"]:checked').val()
                    );
                }
                else
                {
                    for (i = 0; i < checkboxes.length; i++)
                    {
                        arr[i] = checkboxes.eq(i).val();
                        array.data(item.data('alias') ? item.data('alias') : name, arr);
                    }
                }
            }
        }
        else if (item.attr('type') == 'file')
            alert('N/A');
    })

    return array.data();
}

/**
 * Select All
 */
$(document).on('dblclick', 'input[type=checkbox]', function() {
    $('input[name=' + $(this).attr('name') + '][type=checkbox]').each(function() {
        let __ = $(this);
            __.prop('checked', __.is(':checked') ? false : true)
    })
})

/**
 * Ajax
 */
let ajaxAction = function(__)
{
    try
    {
        let confirmation = __.data('confirmation');

        if (confirmation && !__.hasClass('confirmation-dialog'))
        {
            etsetraAlert(
                {
                    'id': 'confirmation-' + md5(confirmation),
                    'message': confirmation,
                    'data': __.data()
                }
            )
        }
        else
            etsetraAjax(__);
    }
    catch (e)
    {
        console.log(e)
    }
}

let etsetraAjax = function(__)
{
    if (__.attr('disabled'))
        return false;

    let old_target;
    let blockui;

    if (__.data('blockui'))
    {
        blockui = elem(__.data('blockui'));
        blockui.block({ message: '' })
    }

    if (__.data('action-target'))
    {
        old_target = __;
        __ = elem(__.data('action-target'));
    }

    if (old_target && old_target.data('reset'))
    {
        __.data('skip', 0)
    }

    if (__.is('form') && !__.attr('id'))
    {
        etsetraAlert({ 'id': 'error', 'message': messages['required_form_id'] })

        return false;
    }

    let method = 'POST',
        variables = {};

    if (__.is('form'))
    {
        let form = __.serializeArray(),
            items = '';

        __.find('button').attr('disabled', true)
        __.find('.is-invalid').removeClass('is-invalid');

        $.map(form, function(obj) {
            items = items == '' ? obj.name : items + ',' + obj.name;
        })

        variables = $.extend(
            variables,
            getFormData(items.split(','), '#' + __.attr('id'))
        );

        if (__.attr('method'))
            method = __.attr('method').toUpperCase();
    }
    else
    {
        if (__.is('input'))
            __.attr('disabled', true);

        if (__.is('input') && __.attr('type') == 'checkbox')
            $('input[name=' + __.attr('name') + ']').attr('disabled', true);

        if (old_target && old_target.is('a'))
            old_target.attr('disabled', true)

        if (__.data('method'))
            method = __.data('method').toUpperCase();
    }

    if (__.is('input') || __.is('select') || __.is('textarea'))
    {
        let this_arr = {};

        this_arr[__.data('alias') ? __.data('alias') : __.attr('name')] = __.val();

        variables = $.extend(variables, this_arr);
    }

    variables = $.extend(variables, __.data());

    delete variables['loading'];
    delete variables['more'];
    delete variables['blockui'];
    delete variables['alias'];
    delete variables['each'];
    delete variables['action'];
    delete variables['method'];
    delete variables['callback'];
    delete variables['toggle'];
    delete variables['headers'];

    if (__.data('include'))
    {
        variables = $.extend(
            variables,
            getFormData(__.data('include').split(','), null)
        );

        delete variables['include'];
    }

    var URL = __.data('action'),
        URL = method == 'GET' ? URL + '/?' + $.param(variables, true) : URL;

    let unloaded = false;

    $(window).bind('beforeunload', function() {
        unloaded = true;
    })

    let ajaxOptions = {};
    let headers = __.data('headers');

    if (headers)
    {
        ajaxOptions['headers'] = headers;
    }
    else
    {
        ajaxOptions['headers'] = {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        };
    }

    $.ajaxSetup(ajaxOptions);

    let _xhr = $.ajax({
        type: method,
        dataType: 'json',
        url: URL,
        crossDomain: URL.substring(0, 4) == 'http' ? true : false,
        data: method == 'GET' ? '' : variables,
        complete: function(obj)
        {
            etsetraAjaxComplete(__);

            if (__.data('loop'))
            {
                window.setTimeout(function() {
                    etsetraAjax(__)
                }, __.data('loop'))
            }

            if (__.is('form'))
            {
                if (typeof grecaptcha != 'undefined')
                    grecaptcha.reset()

                __.find('button').attr('disabled', false);
            }
            else
            {
                if (!__.is('input'))
                    __.attr('disabled', false);

                if (__.is('input') && __.attr('type') == 'checkbox')
                    $('input[name=' + __.attr('name') + ']').attr('disabled', false);

                if (old_target && old_target.is('a'))
                    old_target.attr('disabled', false)
            }

            if (blockui)
                blockui.unblock()

            let loading = __.data('loading');

            if (loading)
                elem(loading).addClass('d-none')
        },
        error: function(jqXHR)
        {
            if (unloaded === true)
                return;

            switch (jqXHR.status)
            {
                case 0:
                    toast(messages[0], 'warning')
                break;
                case 422:
                    if (typeof jqXHR.responseJSON !== 'undefined')
                    {
                        if (__.is('form'))
                            $.each(jqXHR.responseJSON.errors, function(name, value) {
                                let input = __.find('[name=' + name.replace(/(\.([0-9]+))/i, '') + ']');
                                    input.addClass('is-invalid')
                                let feedback = input.parent().children('.invalid-feedback');

                                if (feedback.length)
                                    feedback.html(value[0]);
                                else
                                    toast(value[0], 'danger')
                            })
                        else
                            $.each(jqXHR.responseJSON.errors, function(name, value) {
                                toast(value, 'danger')
                            })
                    }
                break;
                case 404:
                case 403:
                case 401:
                case 429:
                    etsetraAlert({ 'id': 'error', 'message': messages[jqXHR.status] })
                break;
                case 405:
                case 500:
                    if (env == 'production')
                    {
                        etsetraAlert({ 'id': 'error', 'message': messages['unknown'] })
                    }
                    else
                    {
                        let zIndex = zIndexer('.modal');

                        let errorModal = $('#errorModal').modal();
                            errorModal.find('.modal-title').html(jqXHR.responseJSON.exception);
                            errorModal.find('.modal-body').html([
                                $('<p />', { html: jqXHR.responseJSON.message }),
                                $('<p />', { html: jqXHR.responseJSON.file }),
                                $('<p />', {
                                    class: 'm-0',
                                    html: 'Line: ' + jqXHR.responseJSON.line,
                                }),
                            ])
                            errorModal.css('z-index', zIndex)

                            errorModal.modal('show');

                            $('.modal-backdrop:last').css('z-index', zIndex - 1)
                    }
                break;
            }
        },
        success: function(obj)
        {
            if (obj.success == 'ok')
            {
                let each = __.data('each');

                if (each)
                {
                    each = elem(each);

                    if (old_target && old_target.data('reset'))
                        each.find('.tmp').remove()

                    if (each.length && obj.data.length)
                    {
                        $.each(obj.data, function(key, o) {
                            let element = each.find('[data-id=' + o.id + ']');

                            if (!element.length)
                            {
                                let model = each
                                    .find('.each-model')
                                    .clone()
                                    .removeClass('each-model')
                                    .addClass('tmp')
                                    .attr('data-id', o.id);

                                model.find('[data-col]').each(function(ck, ci) {
                                    let x = $(this),
                                        value = o[$(ci).data('col')];

                                    if (x.is('input'))
                                        x.val(value)
                                    else if (x.is('img'))
                                        x.attr('src', value)
                                    else
                                        x.html(value)
                                })

                                eval('__' + each.attr('id'))(model, o, obj)

                                    model.appendTo(each)
                            }
                        })
                    }
                }

                let skip = __.data('skip');
                let take = __.data('take');
                let more = __.data('more');

                if (skip !== undefined && take && obj.data.length && obj.data.length == take)
                {
                    __.data('skip', skip + take)

                    if (more)
                        elem(more).removeClass('d-none')
                }
                else
                {
                    if (more)
                        elem(more).addClass('d-none')
                }

                let callback = __.data('callback');

                if (callback)
                    eval(callback)(__, obj)
            }

            if (obj.redirect)
                window.location.href = obj.redirect;

            if (obj.alert)
            {
                let alert = etsetraAlert(obj.alert);

                if (obj.alert.route)
                {
                    alert.find('[data-name=ok]')
                         .attr('href', routes[obj.alert.route])
                         .removeAttr('data-bs-dismiss')
                }
            }

            if (obj.javascript)
                eval(obj.javascript);

            if (obj.toast)
                toast(obj.toast.message, obj.toast.type)
        },
        timeout: __.data('timeout') ? __.data('timeout') : 60000,
    })
}

let etsetraAlert = function(obj)
{
    let zIndex = zIndexer('.modal');

    let modal = $('#' + obj.id).length ? $('#' + obj.id) : $('.alert-modal').clone().removeClass('alert-modal').attr('id', obj.id).appendTo('body')
        modal.find('.modal-body').html(obj.message)
        modal.css('z-index', zIndex)

    if (obj.id && (obj.id).substr(0, 12) == 'confirmation')
    {
        let ok = modal.find('[data-name=ok]');
            ok.html(keywords['ok'])

        $.each(obj.data, function(key, value) {
            ok.attr('data-' + key, value)
        })

            ok.removeAttr('data-confirmation')

        modal.find('[data-name=cancel]').html(keywords['cancel']).removeClass('d-none')
    }
    else
        modal.find('[data-name=ok]').html(keywords['ok'])

        modal.modal('show')

    $('.modal-backdrop:last').css('z-index', zIndex - 1)

    return modal;
}

let etsetraAjaxComplete = function(__)
{
    if (__.is('form'))
        __.find('button').attr('disabled', false);
    else
        __.attr('disabled', false);

    if (__.data('progress'))
        $('[data-progress=' + __.data('progress') + ']').addClass('d-none');
}

$(document).on('click', '[data-action]:not(ul,ol,div,form,input,.load,select)', function() {
        ajaxAction($(this));
    })
    .on('submit', 'form[data-action]', function(e) {
        ajaxAction($(this));

        e.preventDefault()
    }).on('keydown', 'input[data-action]', function(e) {
        if (e.keyCode == 13)
        {
            ajaxAction($(this));

            e.preventDefault()
        }
    }).on('change', 'select[data-action],input[type=checkbox][data-action]', function(e) {
        ajaxAction($(this));

        e.preventDefault()
    })

$('.load').each(function() {
    ajaxAction($(this));
})

/**
 * Toast
 */
let toast = function(message, type)
{
    let id = 'toast-' + md5(message);
    let toast_wrapper = $('.toast-wrapper');
    let toast = toast_wrapper.children('#' + id);
    let tmp = $('<div />', {
        'id': id,
        'class': 'toast w-100 shadow-sm border-0 text-white px-2 bg-' + type,
        'role': 'alert',
        'data-bs-autohide': 'true',
        'data-bs-delay': '3000',
        'aria-live': 'assertive',
        'aria-atomic': 'true',
        'html': $('<div />', {
            'class': 'toast-body',
            'html': message
        })
    })

    if (toast.length)
        toast.toast('show')
    else
        tmp.appendTo(toast_wrapper).toast('show')
}

/**
 * Element Selector
 */
let elem = function(m)
{
    var sp = m.split('->'),
        elem,
        selector = '';

    $.each(sp, function(key, val) {
        if (elem)
        {
            var brackets = val.split(/[(\)]/);

            selector = selector + '.' + brackets[0] + "('" + brackets[1] + "')";
        }
        else
            elem = "$('" + val + "')";
    })

    return eval(elem + selector);
}

/**
 * Number Format
 */
let numberFormat = function(number)
{
    number = parseInt(number).toFixed(0);

    if (typeof number === 'undefined')
        return 0;
    else
    {
        let separator = '';
        let thousand_separator = '.';
        let number_string = number.toString(),
            rest = number_string.length % 3,
            result = number_string.substr(0, rest),
            thousands = number_string.substr(rest).match(/\d{3}/gi);

        if (thousands)
        {
            separator = rest ? thousand_separator : '';
            result += separator + thousands.join(thousand_separator);
        }

        return result;
    }
}

/**
 * Copy
 */
let copy = function(input)
{
    let copyText = document.getElementById(input);
        copyText.select();
        copyText.setSelectionRange(0, 99999);

    document.execCommand("copy");

    let copied = $('#' + input).data('copied');

    toast(copied ? copied : keywords['copied'], 'success')
}

$(document).on('click', '[data-copy]', function() {
    copy($(this).data('copy'))
})

/**
 * Increment
 */
let increment = function(element, point)
{
    let current_value = element.text().replace('.', ''),
        new_value = parseInt(current_value) + point;

    element.text(numberFormat(new_value))
}

/**
 * Decrement
 */
let decrement = function(element, point)
{
    let current_value = element.text().replace('.', ''),
        new_value = parseInt(current_value) - point;

    element.text(numberFormat(new_value))
}

/**
 * Click Class
 */
$(document).on('click', '[data-class]', function() {
    let __ = $(this),
        target = $(__.data('class'));

    if (__.data('class-remove'))
        target.removeClass(__.data('class-remove'))

    if (__.data('class-add'))
        target.addClass(__.data('class-add'))

    if (__.data('class-toggle'))
        target.toggleClass(__.data('class-toggle'))
})

/**
 * Initialize
 */
$(window).on('load', function() {
    toHtml()

    $('[data-lazy-class]').each(function () {
        $(this).addClass(__.data('lazy-class'))
    })
}).scroll(function () {
    $('[data-lazy-class]').each(function () {
        let __ = $(this);

        let element = $(__.data('lazy-element')),
            element_height = element.height();

        let diff = element_height - __.height();

        let element_offset_top = element.offset().top - (diff * 2),
            element_offset_bottom = element.offset().top;
        let window_top = $(window).scrollTop();

        if (window_top >= element_offset_top && window_top <= element_offset_bottom)
            __.addClass(__.data('lazy-class'))
        else
            __.removeClass(__.data('lazy-class'))
    })
})

$(document)
    .on('click', 'a', function(e) {
        let __ = $(this),
            href= __.attr('href'),
            canonical = $('link[rel="canonical"]');

        if (href == canonical.attr('href') || href == '#')
            e.preventDefault();
    })

let changeWindowHistory = function(urlPath)
{
    window.history.pushState(
        {},
        '',
        urlPath
    );
}

$('.dropdown').on('show.bs.dropdown', function () {
    $('body').removeClass('drawer')
})

let info_fn;
let info = function(key, fn, save)
{
    etsetraAjax(
        $(
            '<div />',
            {
                'data-action': routes.info,
                'data-callback': '__info_driver',
                'data-save': save ? 'on' : '',
                'data-key': key
            }
        )
    )

    info_fn = fn;
}

let __info_driver = function(__, obj)
{
    if (!obj.data.have)
        eval(info_fn)()
}

let nl2br = function(str, is_xhtml)
{
    if (typeof str === 'undefined' || str === null)
        return '';

    let breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br /><br />' : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

let slug = function(str, splitter)
{
    str = str.toLowerCase();

    var specialChars = [
        ['ÄŸ', 'g'],
        ['Ã¼', 'u'],
        ['ÅŸ', 's'],
        ['Ä±', 'i'],
        ['Ã¶', 'o'],
        ['Ã§', 'c'],
        ['Äž', 'g'],
        ['Ãœ', 'u'],
        ['Åž', 's'],
        ['Ä°', 'i'],
        ['Ã–', 'o'],
        ['Ã‡', 'c'],
        ['-', ' ']
    ];

    for (var i = 0; i < specialChars.length; i++)
    {
        str = str.replace(eval('/' + specialChars[i][0] + '/ig'), specialChars[i][1]);
    }

    str = $.trim(str);

    return str.replace(/\s\s+/g, ' ').replace(/[^a-z0-9-\s]/gi, '').replace(/[^\w]/ig, splitter);
}

let app = (function() {
    return {
        looper,
        numberFormat,
        elem,
        toast,
        copy,
        etsetraAlert,
        etsetraAjax,
        changeWindowHistory,
        increment,
        decrement,
        zIndexer,
        nl2br,
        Driver,
        info,
        slug,
    }
})()

global.app = app;

if (typeof module !== 'undefined' && typeof module.exports !== 'undefined')
{
    module.exports = app;
}
