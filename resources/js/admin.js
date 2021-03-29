$(function () {
    Admin.init();
});

var Admin = function () {
    return {
        init: function () {
            this.registerLinkWithDeleteMethod();

            var els = $('.selectizeSelect');
            els?.each(function () {
                $(this).selectize({
                    plugins: ['remove_button'],
                    persist: true,
                    maxItems: null,
                    valueField: 'id',
                    labelField: 'title',
                    searchField: 'title',
                    // options: $(this).data('json'),
                    create: false
                });
            });
        },
        registerLinkWithDeleteMethod: function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('click', 'a.js-click', function (e) {
                e.preventDefault();
                var $this = $(this);
                $.post({
                    type: $this.data('method'),
                    url: $this.attr('href')
                }).done(function (data) {
                    if (data.hasOwnProperty('url')) {
                        window.location.href = data.url;
                    }
                });
            });
        }
    }
}();
