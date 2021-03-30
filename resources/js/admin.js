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

            var els2 = $('.selectLocked');
            els2?.each(function () {
                $(this).[0].selectize.lock();
            });
        },
        initCommon: function () {
            $('.btn-href').on('click', function (e) {
                e.preventDefault();
                window.location.href = $(this).data('href');
            });

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
                        if (data.hasOwnProperty('success') && data.success == false) {
                            alert(data.message);
                        }
                    });
                }
                return false;
            });
        }
    }
}();
