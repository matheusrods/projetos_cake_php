(function($) {
	$(document).on('click', '.expand', function() {
        var root_selector = ['#', $(this).prop('id')].join('');
        var root_siblings = ['.', $(this).prop('id')].join('');
        var childrens_selector = ['.', 'child-', $(this).prop('id')].join('');
        
        $(root_selector).data('expanded', !$(root_selector).data('expanded'));
        
        var expanded = $(root_selector).data('expanded');
        var is_root = $(root_selector).hasClass('root');

        if (expanded) {
            if (is_root) {
                var queue = [];
                $(root_siblings).each(function() {
                    if ($(this).data('expanded')) {
                        queue.push(['.', 'child-', $(this).prop('id')].join(''));
                    }
                });
                $(queue.join(',')).show();
            }
            $(childrens_selector).show();
        } else {
            if (is_root) {
                $(root_siblings).hide();
            } else {
                $(childrens_selector).hide();
            }
        }
    });
})(jQuery);
