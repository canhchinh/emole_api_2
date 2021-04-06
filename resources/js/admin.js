$(function () {
    Admin.init();
});

var Admin = function () {
    return {
        init: function () {
            this.initCommon();
            this.registerLinkWithDeleteMethod();
            this.notificationListPage();
            this.userListPage();

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
                $(this)[0].selectize.lock();
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

            var focusEls = $('[data-focus-to]');
            focusEls.on('click', function (e) {
                $('body').find($(this).data('focus-to')).focus();
            });

            // https://bootstrap-datepicker.readthedocs.io/en/latest/index.html#
            $('.js-datepicker').datepicker({
                language: 'ja',
                format: 'yyyy-mm-dd',
                clearBtn: true,
                startDate: '-180d',
                endDate: 'today'
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
                if (confirm("本当に削除しますか？")) {
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
        },
        userListPage: function () {
            var modalEl = $('#email-content');
            $(document).on('click', '.sendEmail', function (e) {
                modalEl.find('input[name="user-id"]').val($(this).data('user-id'));
                modalEl.find('input[name="email-subject"]').val('');
                modalEl.find('[name="email-content"]').val('');
                modalEl.modal('show');
            });
            $(document).on('click', 'a.js-delete', function (e) {
                var $this = $(this);
                e.preventDefault();
                if (confirm("本当に削除しますか？")) {
                    $.post({
                        type: $this.data('method'),
                        url: $this.attr('href')
                    }).done(function (data) {
                        if (data.hasOwnProperty('success') && data.success) {
                            // window.location.href = $('#filter-notify-status').val();
                        }
                        if (data.hasOwnProperty('success') && data.success == false) {
                            alert(data.message);
                        }
                    });
                }
                return false;
            });


            $('#email-content').on('click', '.send-email', function (e) {
                e.preventDefault();
                Admin.doSendEmailToUser($(this).closest('form'));
            });

            $('#careersList').on('change', function () {
                var val = $(this).val();
                window.location.href = val;
            });

            $('#datepicker-birthday').on('change', function () {
                var val = $(this).val();
                console.log(val);
                if (val != $(this).data('current-value')) {
                    var url = $(this).data('url');
                    var newUrl = url.replace('birthdayValue', val);
                    window.location.href = newUrl;
                }
            });
        },
        doSendEmailToUser: function (form) {
            $.post({
                type: form.data('method'),
                data: form.serializeArray(),
                url: form.attr('action')
            }).done(function (data) {
                if (data.hasOwnProperty('success') && data.success) {
                    // window.location.href = $('#filter-notify-status').val();
                }
                if (data.hasOwnProperty('success') && data.success == false) {
                    alert(data.message);
                }
            });
        }
    }
}();
