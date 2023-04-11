$(document).ready(function () {
    var bkgID = $('#bkgID').val();
    function getScrollButton() {
        $('#chatbox').find("#chats").animate({scrollTop: $('#chats').prop("scrollHeight")}, 1000);
    }
    $('#chatbox').on('click', '#loadchatboxMore', function (event) {
        var date = $(this).data('date');
        var _that = $(this);
        event.preventDefault();
        $.ajax({
            url: '/admin/book-logs/'+bkgID+'/' + date,
            cache: false
        }).done(function (data) {
            $('#chatbox').prepend(data);
            _that.remove();
        });
    });

    $('#loadchatbox').click(function () {
        $('#chatbox').load('/admin/book-logs/'+bkgID, getScrollButton).show();
    });

    $('#chatbox').on('click', '.see_more', function (event) {
        event.preventDefault();
        $.ajax({
            url: '/admin/book-logs/see-more/' + $(this).data('id'),
            cache: false
        })
                .done(function (data) {
                    var obj = $('#modal_seeLog');
                    obj.find('#msl_subj').text(data.subj);
                    obj.find('#msl_room').text(data.room);
                    obj.find('#msl_user').text(data.user);
                    obj.find('#msl_content').html(data.content);
                    obj.find('#msl_date').text(data.date);
                    obj.modal('show');
                });
    });
    $('#chatbox').on('click', '.see_more_mail', function (event) {
        event.preventDefault();
        $.ajax({
            url: '/admin/book-logs/see-more-mail/' + $(this).data('id'),
            cache: false
        })
                .done(function (data) {
                    var obj = $('#modal_seeLog');
                    obj.find('#msl_subj').text(data.subj);
                    obj.find('#msl_room').text(data.room);
                    obj.find('#msl_user').text(data.user);
                    obj.find('#msl_content').html(data.content);
                    obj.find('#msl_date').text(data.date);
                    obj.modal('show');
                });

    });
    $('.openFF').on('click', function (event) {
        event.preventDefault();
        var id = $(this).data('booking');
        $.post('/admin/forfaits/open', {_token: window.csrf_token, id: id}, function (data) {
            var formFF = $('#formFF');
            formFF.attr('action', data.link);
            formFF.find('#admin_ff').val(data.admin);
            formFF.submit();

        });
    });

    if (window.is_mobile == 0)
        setTimeout(function () {
            
            $('#chatbox').load('/admin/book-logs/'+bkgID, getScrollButton).show();         
        }, 1000);
});