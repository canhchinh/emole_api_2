$(function () {
    Admin.init();
});

var Admin = function () {
    return {
        init: function () {
            this.initCommon();
            this.registerLinkWithDeleteMethod();
            this.notificationListPage();

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
        initCommon: function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        },
        notificationListPage: function () {
            $('#filter-notify-status').on('change', function () {
                var val = $(this).val();
                window.location.href = val;
            });

            $('.change-status-notify').on('change', function (e) {
                var $this = $(this);

                var val = $(this).val();
                $.ajax({
                    type: $this.data('method'),
                    data: {status: val},
                    url: $this.data('url-change-status')
                }).done(function (data) {
                    if (data.hasOwnProperty('success') && data.success) {
                        window.location.href = $('#filter-notify-status').val();
                    }
                });
            });
        },
        registerLinkWithDeleteMethod: function () {
            $(document).on('click', 'a.js-click', function (e) {
                var $this = $(this);
                e.preventDefault();
                if (confirm("この記録は復元されませんか？ 削除してもよろしいですか？")) {
                    $.post({
                        type: $this.data('method'),
                        url: $this.attr('href')
                    }).done(function (data) {
                        if (data.hasOwnProperty('success') && data.success) {
                            window.location.href = $('#filter-notify-status').val();
                        }
                    });
                }
                return false;
            });
        }
    }
}();
