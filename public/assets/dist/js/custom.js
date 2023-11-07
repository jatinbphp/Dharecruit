$(document).ready(function(){
    $('.assign').click(function(){
        alert('in');
        var user_id = $(this).attr('uid');
        var url = $(this).attr('url');
        var l = Ladda.create(this);
        l.start();
        $.ajax({
            url: url,
            type: "post",
            data: {'id': user_id},
            headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
            success: function(data){
                l.stop();
                $('#assign_remove_'+user_id).show();
                $('#assign_add_'+user_id).hide();
            }
        });
    });
    $('.unassign').click(function(){
        alert('out');
        var user_id = $(this).attr('ruid');
        var url = $(this).attr('url');
        var l = Ladda.create(this);
        l.start();
        $.ajax({
            url: url,
            type: "post",
            data: {'id': user_id,'_token' : $('meta[name=_token]').attr('content') },
            success: function(data){
                l.stop();
                $('#assign_remove_'+user_id).hide();
                $('#assign_add_'+user_id).show();
            }
        });
    });
});
