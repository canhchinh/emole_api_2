$(function () {
    Admin.init();
});

var Admin = function () {
    return {
        init: function () {
            this.initCommon();

            this.notificationListPage();
            this.userListPage();
            this.userPortfolioPage();
            this.doSendEmailToAllUser();

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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.btn-href').on('click', function (e) {
                e.preventDefault();
                window.location.href = $(this).data('href');
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
                startDate: '-120Y',
                endDate: 'today'
            });

            $('select.js-href-value').on('change', function () {
                window.location.href = $(this).val();
            });

            // Prevent double click form submit
            $('.once-click-disabled').on('click', function () {
                if (!$(this).is(":disabled")) {
                    $(this).attr('disabled', true);
                    $(this).closest('form').submit();
                }
            });
        },
        notificationListPage: function () {
            Admin.registerLinkWithDeleteMethod();

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
                            window.location.href = $this.data('redirect');
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
                modalEl.find('.username').html($(this).data('user-name'));
                modalEl.find('input[name="user_id"]').val($(this).data('user-id'));
                modalEl.find('input[name="email_subject"]').val('');
                modalEl.find('[name="email_content"]').val('');
                modalEl.find('label.error').remove();
                modalEl.find('.ajax-response').hide();
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
                            window.location.href = $this.data('redirect');
                        }
                        if (data.hasOwnProperty('success') && data.success == false) {
                            alert(data.message);
                        }
                    });
                }
                return false;
            });

            $("#sendEmailToUserForm").validate({
                submitHandler: function() {
                    Admin.doSendEmailToUser($('#sendEmailToUserForm'));
                }
            });

            $('#careersList').on('change', function () {
                var val = $(this).val();
                window.location.href = val;
            });

            $('#datepicker-birthday').on('change', function () {
                var val = $(this).val();
                if (val != $(this).data('current-value')) {
                    var url = $(this).data('url');
                    var newUrl = url.replace('birthdayValue', val);
                    window.location.href = newUrl;
                }
            });
        },
        doSendEmailToUser: function (form) {
            var btnSubmit = form.find('[type="submit"]');
            btnSubmit.attr('disabled', true);
            $.post({
                type: form.data('method'),
                data: form.serializeArray(),
                url: form.attr('action')
            }).done(function (data) {
                form.find('.ajax-response').hide();
                if (data.hasOwnProperty('success') && data.success) {
                    form.find('input[type="text"]').val('');
                    form.find('[name="email_content"]').val('');
                    form.find('.ajax-response.text-success').html(data.message).show();
                }
                if (data.hasOwnProperty('success') && data.success == false) {
                    form.find('.ajax-response.text-danger').html(data.message).show();
                }
                btnSubmit.attr('disabled', false);
            });
        },
        userPortfolioPage: function () {
            $(document).on('click', 'a.js-delete-portfolio', function (e) {
                var $this = $(this);
                e.preventDefault();
                if (confirm("本当に削除しますか？")) {
                    $.post({
                        type: $this.data('method'),
                        url: $this.attr('href')
                    }).done(function (data) {
                        if (data.hasOwnProperty('success') && data.success) {
                            window.location.href = $this.data('redirect');
                        }
                        if (data.hasOwnProperty('success') && data.success == false) {
                            alert(data.message);
                        }
                    });
                }
                return false;
            });

            $('.change-status-portfolio').on('change', function (e) {
                var $this = $(this);

                var val = $(this).val();
                $.ajax({
                    type: $this.data('method'),
                    data: {status: val},
                    url: $this.data('url-change-status')
                }).done(function (data) {
                    if (data.hasOwnProperty('success') && data.success) {
                        window.location.reload();
                    }
                });
            });

            $("#sendEmailToPortfolioForm").validate({
                submitHandler: function() {
                    Admin.doSendEmailToUser($('#sendEmailToPortfolioForm'));
                }
            });
            var modalEl = $('#email-content-portfolio');
            $(document).on('click', '.sendEmailPortfolio', function (e) {
                modalEl.find('.username').html($(this).data('user-name'));
                modalEl.find('input[name="user_id"]').val($(this).data('user-id'));
                modalEl.find('input[name="email_subject"]').val('');
                modalEl.find('[name="email_content"]').val('');
                modalEl.find('label.error').remove();
                modalEl.find('.ajax-response').hide();
                modalEl.modal('show');
            });
        },
        doSendEmailToAllUser: function () {
            var sendEmailToAllUser = '.sendEmailToAllUser';
            var modalEl = $('#send-email-to-all-users');

            if ($(sendEmailToAllUser).length) {
                $(sendEmailToAllUser).on('click', function (e) {
                    e.preventDefault();
                    modalEl.modal('show');
                });
            }

            $("#sendEmailToAllUserForm").validate({
                submitHandler: function () {
                    doSend($('#sendEmailToAllUserForm'));
                }
            });


            function doSend(form) {
                var btnSubmit = form.find('[type="submit"]');
                btnSubmit.attr('disabled', true);
                $.post({
                    type: form.data('method'),
                    data: form.serializeArray(),
                    url: form.attr('action')
                }).done(function (data) {
                    form.find('.ajax-response').hide();
                    if (data.hasOwnProperty('success') && data.success) {
                        form.find('input[type="text"]').val('');
                        form.find('[name="email_content"]').val('');
                        form.find('.ajax-response.text-success').html(data.message).show();
                    }
                    if (data.hasOwnProperty('success') && data.success == false) {
                        form.find('.ajax-response.text-danger').html(data.message).show();
                    }
                    btnSubmit.attr('disabled', false);
                });
            }
        }
    }
}();
