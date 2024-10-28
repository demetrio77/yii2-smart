(function($) {
    $.fn.dateRangeSelector = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method ' + method + ' does not exist on dateRangeSelector');
            return false;
        }
    };

    var defaults = {
        name1: '',
        name2: '',
        min: '',
        max: ''
    };

    var globalObjects = {};

    var methods = {

        instance: function(){
            return globalObjects[$(this).attr('id')].obj;
        },

        init: function (options) {
            return this.each(function(){
                var $this = $(this);

                $this.settings = $.extend({}, defaults, options || {});

                $this.input1 = $this.find('[name="' + $this.settings.name1 + '"]');
                $this.input2 = $this.find('[name="' + $this.settings.name2 + '"]');
                $this.errorDiv = $this.find('.date-range-selector-error-wrapper');

                if ($this.settings.min) {
                    $this.settings.min = new Date($this.settings.min);
                }
                if ($this.settings.max) {
                    $this.settings.max = new Date($this.settings.max);
                }

                $this.input1.change(function () {
                    $this.isValid();
                });

                $this.input2.change(function () {
                    $this.isValid();
                });

                $this.getStart = function () {
                    return new Date($this.input1.val());
                };

                $this.getEnd = function () {
                    return new Date($this.input2.val());
                };

                $this.getStartDateAsString = function () {
                    return $this.getStart().toISOString().split('T')[0];
                }

                $this.getEndDateAsString = function () {
                    return $this.getEnd().toISOString().split('T')[0];
                }

                $this.isValid = function () {
                    let hasError = false;
                    let error = '';
                    let start = $this.getStart();
                    let end = $this.getEnd();

                    if (isNaN(start.getTime())) {
                        hasError = true;
                        error = 'Start Date is not a valid date';
                    } else if ($this.settings.min && start < $this.settings.min) {
                        hasError = true;
                        error = 'The date can\'t be less than ' + $this.settings.min.toLocaleDateString('en-US', {});
                    } else if (isNaN(end.getTime())) {
                        hasError = true;
                        error = 'Start Date is not a valid date';
                    } else if ($this.settings.max && end > $this.settings.max) {
                        hasError = true;
                        error = 'The date can\'t be greater than ' + $this.settings.max.toLocaleDateString('en-US', {});
                    } else if (start > end) {
                        hasError = true;
                        error = 'Start Date can\'t be greater than End Date';
                    }

                    if (hasError) {
                        $this.addClass('has-error');
                        $this.errorDiv.removeClass('hidden');
                        $this.errorDiv.text(error);
                    } else {
                        $this.removeClass('has-error');
                        $this.errorDiv.addClass('hidden');
                        $this.errorDiv.text('');
                    }

                    return ! hasError;
                }

                globalObjects[$this.attr('id')] = {obj: $this};
            });
        }
    }
})(jQuery);

function DateRangeSelector() {
    this.instance = function($id) {
        return $('#'+$id).dateRangeSelector('instance');
    }
}

var DATERANGESELECTOR = new DateRangeSelector();
