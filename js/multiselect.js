var multiselect = {
    windowBinded : false,

    tpl : {
        main : '<div class="multiselect" placeholder="{{ placeholder }}" multiselect{% if count %} data-count="{{ count }}"{% endif %}>{{ options }}</div>',
        option :'<label class="multiselect_option"><input type="checkbox" name="{{ name }}[]" value="{{ value }}" multiselect-elem="option"{% if selected %} checked{% endif %}> {{ text }}</label>'
    },

    select : function($multiselect, $checkbox) {
        var $select = $multiselect.find('select');
        $select.find('option[value="' + $checkbox.attr('value') + '"]').prop('selected', $checkbox.is(':checked'));
        $select.change();
    },

    change : function($multiselect) {
        var value = $multiselect.find('select').val();
        if (value) {
            $multiselect.attr('data-count', value.length);
        } else {
            $multiselect.removeAttr('data-count');
        }
    },

    convert : function($select) {
        with (multiselect) {
            var options      = '',
                name         = $select.attr('name'),
                $options     = $select.find('option'),
                placeholder  = $select.attr('placeholder') || false,
                selectedVals = $select.val();

            for (var i=0; $options[i]; i++) {
                var $option = $($options[i]),
                    value = $option.attr('value');

                if (i==0 && !placeholder) {
                    placeholder = $option.text();
                    continue;
                }

                options += simpleTpl(tpl.option, {
                    name : name,
                    text : $option.text(),
                    value : value,
                    selected : (selectedVals.indexOf(value) >= 0)
                });
            }

            var $multiselect = simpleTpl(tpl.main, {
                name        : name,
                options     : options,
                placeholder : placeholder,
                count       : selectedVals.length
            }, true);
            $multiselect.insertBefore($select);
            $select.removeAttr('multiselect').appendTo($multiselect).addClass('h');
        }
    },

    init : function($select) {
        if (isMobile) {
            return false;
        }

        with (multiselect) {
            if (!$select) {
                $select = $('select[multiselect]');
            } else {
                $select = [$select];
            }
            if (!$select.length) {
                return false
            }
            for (var i=0; $select[i]; i++) {
                convert($($select));
            }

            if (windowBinded) {
                return true;
            }
            $(window).on('change', function(ev){
                var $el = $(ev.target),
                    $multiselect = $el.closest('[multiselect]');

                if ($multiselect.length) {
                    if ($el.is('[multiselect-elem="option"]')) {
                        multiselect.select($multiselect, $el);
                    } else if ($el.is('select')) {
                        multiselect.change($multiselect);
                    }
                }
            });
            windowBinded = true;
        }
    }
}

multiselect.init();
