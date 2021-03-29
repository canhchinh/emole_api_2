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
                var $this = $(this);
                e.preventDefault();
                if (confirm("この記録は復元されませんか？ 削除してもよろしいですか？")) {
                    $.post({
                        type: $this.data('method'),
                        url: $this.attr('href')
                    }).done(function (data) {
                        if (data.hasOwnProperty('success') && data.success) {
                            if (data.hasOwnProperty('redirectUrl') && data.redirectUrl) {
                                window.location.href = data.redirectUrl;
                                return;
                            }
                            window.location.reload();
                        }
                    });
                }
                return false;
            });
        }
    }
}();
